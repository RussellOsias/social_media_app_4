<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Store a new notification.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'type' => 'required|string',
            'post_id' => 'required|exists:posts,id',
            'actor_id' => 'required|exists:users,id',
        ]);
        
        // Get the post owner
        $postOwner = Post::find($request->post_id)->user;

        // Create the notification if the post owner exists
        if ($postOwner) {
            $notification = Notification::create([
                'user_id' => $postOwner->id,
                'post_id' => $request->post_id,
                'type' => $request->type,
                'is_read' => false,
                'actor_id' => $request->actor_id,
            ]);
            return response()->json($notification, 201);
        }

        return response()->json(['error' => 'Post owner not found.'], 400);
    }

    /**
     * Display notifications for the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $notifications = Notification::with('user', 'actor')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Optionally, you could filter out read notifications here
        // $notifications = $notifications->where('is_read', false);
    
        return view('notifications.index', compact('notifications'));
    }
    /**
     * Fetch unread notifications for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnread()
    {
        $unreadNotifications = Notification::with(['user', 'actor'])
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($unreadNotifications);
    }

    /**
     * Mark a notification as read.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        
        // Mark as read and save
        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'Notification marked as read.']);
    }

    /**
     * Delete a notification.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        
        // Delete the notification
        $notification->delete();

        return response()->json(['message' => 'Notification deleted.']);
    }
}
