<?php


namespace App\Http\Controllers;


use App\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class PostController
{
    public function index()
    {
        //for admin we will show all posts
        if (Auth::check()) {
            $posts = Post::orderBy('posts.created_at', 'desc')
                ->withCount('likes')
                ->with('author')
                ->paginate(20);
        } else {
            $posts = Post::where('status', 0)
                ->withCount('likes')
                ->orderBy('posts.created_at', 'desc')
                ->with('author')
                ->paginate(20);
        }

        return view('pages.index')->with('posts', $posts);
    }

    public function showBySlug(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->withCount('likes')
            ->with('author')
            ->firstOrFail();

        return $this->show($post);
    }

    public function showById(int $postId)
    {
        $post = Post::where('id', $postId)
            ->withCount('likes')
            ->with('author')
            ->firstOrFail();

        return $this->show($post);
    }

    public function show(Post $post)
    {
        $postId = $post->id;

        $aphorism = $this->getAphorismByPostId($postId);
        $previous = $post->getPrevious();
        $next = $post->getNext();
        $related = $post->related();

        return view('pages.show', compact('post', 'aphorism', 'previous', 'next', 'related'));
    }

    private function getAphorismByPostId(int $postId)
    {
        $aphorismsCount = DB::table('aphorism')->count();
        $needleAphorismId = $aphorismsCount - 365 - $postId + date('d');

        return DB::table('aphorism')
            ->where('aphorism.id', '=', $needleAphorismId)
            ->first();
    }

    public static function isLiked(int $post_id): bool
    {
        return Cookie::get('likedPostToday' . $post_id) ? true : false;
    }
}
