<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Repositories\CommentRepository;
use App\Repositories\PostRepository;
use App\User;
use Illuminate\Http\Request;
use Auth;

class CommentController extends Controller
{
    private $commentRepository;

    private $postRepository;

    /**
     * CommentController constructor.
     */
    public function __construct(CommentRepository $commentRepository, PostRepository $postRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $data = array(
            'post_id' => $request->postId,
            'author_name' => $request->authorName,
            'author_email' => $request->authorEmail,
            'author_url' => $request->authorUrl,
            'body' => $request->body,
        );

        if (!empty($request->parent)) {
            $data['parent'] = $request->parent;
        }

        /** @var User $user */
        $user = Auth::user();
        if ($user !== null) {
            $data['user_id'] = $user->getAttribute('id');
        }

        $spam = false;
        $data['spam'] = $spam;

        $approved = !$spam;
        $data['approved'] = $approved;

        $data['ip'] = getClientIPAddress();

        $valid = true;
        if (!$valid) {

            return array(
                'error' => 1,
                'message' => trans('public.error_creating_comment'),
            );
        } else {
            $this->commentRepository->create($data);
            $comment = new Comment();
            $comment->setRawAttributes($data);

            return array(
                'error' => 0,
                'spam' => $data['spam'] ? 1 : 0,
                'html' => view('themes.vortex.partials.blog.singleComment', compact('comment'))->render(),
            );
        }
    }

    /**
     * Get the form to send a new comment.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getForm(Request $request)
    {
        $data = array(
            'parent' => $request->parent,
        );

        $comment = $this->commentRepository->find($data['parent']);

        if ($comment === null) {
            return ['error' => 1];
        }

        $post = $this->postRepository->find($comment->post_id);
        $commentParent = $data['parent'];

        return view('themes.vortex.partials.blog.commentForm', compact('post', 'commentParent'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Sort array of comments.
     *
     * @param $comments
     * @param bool $addChildToParent
     * @return array
     */
    public static function sortByParent($comments, $addChildToParent = true)
    {
        if (!$addChildToParent) {
            return $comments;
        }

        $sort = [];
        foreach ($comments as $comment) {
            $comment->children = [];
            $sort[$comment->id] = $comment;
        }

        foreach ($sort as $key => $comment) {
            if ($comment->parent !== null) {
                $children = $sort[$comment->parent]->getAttribute('children');
                $children[$comment->id] = $comment;
                $sort[$comment->parent]->setAttribute('children', $children);
            }
        }

        foreach ($sort as $key => $comment) {
            if ($comment->parent !== null) {
                unset($sort[$key]);
            }
        }

        return $sort;
    }

    /**
     * Show comments ordered.
     *
     * @param array $comments
     */
    public static function showCommentsOrdered($comments)
    {
        foreach ($comments as $comment) {
            /** @var Comment $comment */
            print view('themes.vortex.partials.blog.singleComment', compact('comment'));
        }
    }

    /**
     * Get gravatar or the author.
     *
     * @param $comment
     * @return String
     */
    public static function getAuthorAvatar($comment)
    {
        if ($comment->user_id !== null) {
            $avatar = getGravatar($comment->user->email);
        } else {
            $avatar = getGravatar($comment->author_email);
        }
        return $avatar;
    }
}
