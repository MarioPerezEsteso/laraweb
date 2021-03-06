<?php

namespace Tests\Functional;

use App\Subscriber;
use Tests\TestCase;

class SubscriberControllerTest extends TestCase
{
    /**
     * Test subscribe without confirmation ok.
     */
    public function testSubscribeWithoutConfirmOk()
    {
        $faker = \Faker\Factory::create();
        $email = $faker->email;

        $response = $this->call('POST', 'newsletter/subscribe', [
            'email' => $email,
        ]);

        $response->assertExactJson([
            'error' => 0,
            'message' => trans('public.email_subscription_email_confirmation_sent'),
        ]);

        $this->assertDatabaseHas('subscribers', [
            'email' => $email,
            'active' => false,
            'activated_at' => null,
            'unsubscribed_at' => null,
        ]);

        $this->assertDatabaseMissing('subscribers', [
            'email' => $email,
            'token_expires_at' => null,
        ]);
    }

    /**
     * Test subscription error if email already exists.
     */
    public function testSubscribeEmailNotConfirmedAndTokenHasNotExpiredError()
    {
        $subscriber = factory(Subscriber::class)->create([
            'active' => false,
            'token_expires_at' => \Carbon\Carbon::now()->addHours(20),
            'activated_at' => null,
            'unsubscribed_at' => null,
        ]);

        $response = $this->call('POST', 'newsletter/subscribe', [
            'email' => $subscriber->email,
        ]);

        $response->assertExactJson([
            'error' => 1,
            'message' => trans('public.subscription_email_not_valid'),
        ]);
    }

    /**
     * Test token is renewed if the user has subscribed before but not confirmed the email and the token has expired.
     */
    public function testSubscribeEmailNotConfirmedAndTokenExpiredOk()
    {
        $subscriber = factory(Subscriber::class)->create([
            'active' => false,
            'token_expires_at' => \Carbon\Carbon::now()->subHours(20),
            'activated_at' => null,
            'unsubscribed_at' => null,
        ]);

        $response = $this->call('POST', 'newsletter/subscribe', [
            'email' => $subscriber->email,
        ]);

        $response->assertExactJson([
            'error' => 0,
            'message' => trans('public.email_subscription_email_confirmation_sent'),
        ]);

        $this->assertDatabaseHas('subscribers', [
            'email' => $subscriber->email,
            'active' => false,
            'activated_at' => null,
            'unsubscribed_at' => null,
        ]);

        $this->assertDatabaseMissing('subscribers', [
            'email' => $subscriber->email,
            'token' => $subscriber->token,
            'token_expires_at' => null,
        ]);
    }

    /**
     * Test subscribe of a previously unsubscribed user.
     */
    public function testIsUnsubscribedAndSubscribesAgain()
    {
        $subscriber = factory(Subscriber::class)->create([
            'active' => false,
            'token_expires_at' => \Carbon\Carbon::now()->subDays(2),
            'activated_at' => \Carbon\Carbon::now()->subDays(2),
            'unsubscribed_at' => \Carbon\Carbon::now()->subDays(1),
        ]);

        $response = $this->call('POST', 'newsletter/subscribe', [
            'email' => $subscriber->email,
        ]);

        $response->assertExactJson([
            'error' => 0,
            'message' => trans('public.email_subscription_email_confirmation_sent'),
        ]);

        $this->assertDatabaseMissing('subscribers', [
            'email' => $subscriber->email,
            'token' => $subscriber->token,
            'token_expires_at' => $subscriber->token_expires_at,
        ]);

        $this->assertDatabaseMissing('subscribers', [
            'email' => $subscriber->email,
            'token_expires_at' => null,
        ]);
    }

    /**
     * Test subscription confirmation.
     */
    public function testSubscriptionConfirmationOk()
    {
        $subscriber = factory(Subscriber::class)->create([
            'active' => false,
            'activated_at' => null,
            'token_expires_at' => \Carbon\Carbon::now()->addHours(24),
        ]);

        $response = $this->call('GET', 'newsletter/confirm/' . $subscriber->token);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('subscribers', [
            'email' => $subscriber->email,
            'token' => $subscriber->token,
            'active' => true,
            'unsubscribed_at' => null,
        ]);

        $this->assertDatabaseMissing('subscribers', [
            'email' => $subscriber->email,
            'token_expires_at' => null,
            'activated_at' => null,
        ]);
    }

    /**
     * Test that the subscriber is redirected to the homepage on subscription confirmation if they have been
     * previously activated and the token is still valid.
     */
    public function testRedirectToHomePageOnSubscriptionConfirmationIfUserHasBeenPreviouslyActivatedAndTokenIsStillValid()
    {
        $subscriber = factory(Subscriber::class)->create([
            'active' => true,
            'token_expires_at' => \Carbon\Carbon::now()->addHours(23),
            'activated_at' => \Carbon\Carbon::now()->subMinutes(30),
            'unsubscribed_at' => null,
        ]);

        $response = $this->call('GET', 'newsletter/confirm/' . $subscriber->token);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('subscribers', $subscriber->getAttributes());
    }

    /**
     * Test that the subscriber is redirected to the homepage on subscription confirmation if they have been
     * previously activated and the token has expired.
     */
    public function testRedirectToHomePageOnSubscriptionConfirmationIfUserHasBeenPreviouslyActivatedAndTokenHasExpired()
    {
        $subscriber = factory(Subscriber::class)->create([
            'active' => true,
            'token_expires_at' => \Carbon\Carbon::now()->subHour(10),
            'activated_at' => \Carbon\Carbon::now()->subHour(15),
            'unsubscribed_at' => null,
        ]);

        $response = $this->call('GET', 'newsletter/confirm/' . $subscriber->token);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('subscribers', $subscriber->getAttributes());
    }

    /**
     * Test that the subscriber is redirected to the homepage if the token does not exist.
     */
    public function testRedirectToHomePageIfTokenDoesNotExist()
    {
        $unexistentToken = '123456789987654321';
        $response = $this->call('GET', 'newsletter/confirm/' . $unexistentToken);

        $response->assertRedirect('/');
    }

    /**
     * Test that the user is unsubscribed.
     */
    public function testUnsubscribeUser()
    {
        $subscriber = factory(Subscriber::class)->create([
            'active' => true,
            'token_expires_at' => \Carbon\Carbon::now()->subHour(10),
            'activated_at' => \Carbon\Carbon::now()->subHour(15),
            'unsubscribed_at' => null,
        ]);

        $response = $this->call('GET', 'newsletter/unsubscribe/' . $subscriber->token);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('subscribers', [
            'email' => $subscriber->email,
            'token' => $subscriber->token,
            'active' => false,
            'token_expires_at' => \Carbon\Carbon::now(),
            'activated_at' => $subscriber->activated_at,
            'unsubscribed_at' => \Carbon\Carbon::now(),
        ]);
    }

    /**
     * Test that the subscriber is redirected to the home page if the token does not exist.
     */
    public function testRedirectToHomePageOnUnsubscriptionIfTokenDoesNotExist()
    {
        $unexistentToken = '123456789987654321';
        $response = $this->call('GET', 'newsletter/unsubscribe/' . $unexistentToken);

        $response->assertRedirect('/');
    }

    /**
     * Test redirect to homepage on unsubscription if a user has been previously unsubscribed.
     */
    public function testRedirectToHomePageIfUserHasBeenPreviouslyUnsubscribed()
    {
        $subscriber = factory(Subscriber::class)->create([
            'active' => false,
            'token_expires_at' => \Carbon\Carbon::now()->subHour(10),
            'activated_at' => \Carbon\Carbon::now()->subHour(15),
            'unsubscribed_at' => \Carbon\Carbon::now(),
        ]);

        $response = $this->call('GET', 'newsletter/unsubscribe/' . $subscriber->token);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('subscribers', $subscriber->getAttributes());
    }
}
