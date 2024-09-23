<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Comment;
use App\Events\PostCreated;
use App\Events\PostLiked;
use App\Events\CommentAdded;

class PostController extends Controller
{
    // Store a new post
    public function store(Request $request) {
        $request->validate([
            'content' => 'required|max:255',
        ]);

        // Create a new post
        $post = new Post();
        $post->content = $request->content;
        $post->user_id = Auth::id(); // Set the current user as the author
        $post->save();

        // Load the user relationship so it's included in the response
        $post->load('user');

        // Fire the PostCreated event
        event(new PostCreated($post));

        return response()->json($post, 201);
    }

    // Retrieve all posts with related data
    public function index() {
        $posts = Post::with(['user', 'comments.user'])->latest()->get(); 
        
        foreach ($posts as $post) {
            $post->userHasLiked = $post->likes()->where('user_id', Auth::id())->exists();
        }

        return response()->json($posts);
    }
    
    // Delete a post
    public function destroy($id) {
        // Find the post by ID
        $post = Post::findOrFail($id);

        // Check if the authenticated user is the post owner
        if ($post->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete the post
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    // Like or unlike a post
    public function likePost(Post $post) {
        // Check if the user has already liked the post
        $existingLike = $post->likes()->where('user_id', Auth::id())->first();

        if ($existingLike) {
            // Unlike the post if it was previously liked
            $existingLike->delete();
            $post->decrement('likes_count'); // Decrement the like count

            // Optionally, create a notification for the post owner
            return response()->json(['message' => 'Post unliked']);
        } else {
            // Like the post if not liked yet
            $post->likes()->create(['user_id' => Auth::id()]);
            $post->increment('likes_count'); // Increment the like count

            // Fire the PostLiked event
            event(new PostLiked($post));

            // Optionally, create a notification for the post owner
            app(NotificationController::class)->store(new Request([
                'type' => 'like',
                'post_id' => $post->id,
                'actor_id' => Auth::id(), // Pass the current user ID as actor
            ]));

            return response()->json(['message' => 'Post liked']);
        }
    }

    // Add a comment to a post
    public function addComment(Request $request, Post $post) {
        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        // Create a new comment
        $comment = new Comment();
        $comment->comment = $request->comment;
        $comment->user_id = Auth::id();  // The user adding the comment
        $comment->post_id = $post->id;    // The post the comment belongs to
        $comment->save();

        // Fire the CommentAdded event
        event(new CommentAdded($comment));

        // Optionally, create a notification for the post owner
        app(NotificationController::class)->store(new Request([
            'type' => 'comment',
            'post_id' => $post->id,
            'actor_id' => Auth::id(), // Pass the current user ID as actor
        ]));

        return response()->json($comment, 201);
    }
}
