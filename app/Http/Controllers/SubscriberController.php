<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Email\SubscriberEmailController;
use App\Subscriber;
use App\Validators\SubscriberValidator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SubscriberController extends Controller
{
    /**
     * @var SubscriberValidator
     */
    protected $validator;

    public function __construct(SubscriberValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Create a new subscriber and send them an email to confirm their address.
     *
     * @param Request $request
     * @return array
     */
    public function subscribe(Request $request)
    {
        $data = [
            'email' => $request->email,
            'token' => md5(uniqid($request->email, true)),
            'active' => false,
            'token_expires_at' => Carbon::now()->addHours(24),
        ];

        $subscriptionSuccess = true;

        /**
         * Check if there is already a subscriber with the given email
         */
        /** @var Subscriber $subscriber */
        $subscriber = Subscriber::findByEmail($data['email']);

        if (!$subscriber) {
            if ($this->validator->with($data)->passes()) {
                $subscriber = new Subscriber();
                $subscriber->fill($data);
                $subscriber->save();
            } else {
                $subscriptionSuccess = false;
            }
        } else {
            if ($subscriber->active || $subscriber->isPendingConfirmation()) {
                $subscriptionSuccess = false;
            } else if ((!$subscriber->active && $subscriber->tokenHasExpired()) || $subscriber->isUnsubscribed()) {
                if ($this->validator->update($subscriber->id)->with($data)->passes()) {
                    $subscriber->update($data);
                } else {
                    $subscriptionSuccess = false;
                }
            }
        }

        if ($subscriptionSuccess) {
            SubscriberEmailController::sendConfirmationEmail($subscriber);
            return [
                'error' => 0,
                'message' => trans('public.email_subscription_email_confirmation_sent'),
            ];
        } else {
            return [
                'error' => 1,
                'message' => trans('public.subscription_email_not_valid')
            ];
        }
    }

    /**
     * Confirm the subscription of an email.
     *
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function confirmSubscription(string $token)
    {
        $subscriber = Subscriber::findByToken($token);

        if ($subscriber && $subscriber->isPendingConfirmation()) {
            $attributes = [
                'active' => true,
                'token_expires_at' => Carbon::now(),
                'activated_at' => Carbon::now(),
            ];
            $subscriber->update($attributes);
            SubscriberEmailController::sendSubscriptionConfirmedEmail($subscriber);
        }

        return redirect('/');
    }

    /**
     * Unsubscribe an email.
     *
     * @param string $token
     */
    public function unsubscribe(string $token)
    {
        $subscriber = Subscriber::findByToken($token);

        if ($subscriber && $subscriber->active) {
            $attributes = [
                'active' => false,
                'token_expires_at' => Carbon::now(),
                'unsubscribed_at' => Carbon::now(),
            ];
            $subscriber->update($attributes);
        }

        return redirect('/');
    }
}
