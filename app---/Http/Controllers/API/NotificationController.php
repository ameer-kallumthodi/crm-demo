<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Return notifications for the authenticated API user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $baseQuery = Notification::forUser($user->id, $user->role_id)
            ->with(['role', 'user', 'creator'])
            ->orderBy('created_at', 'desc');

        $latestNotifications = (clone $baseQuery)
            ->limit(10)
            ->get();

        // Mark fetched notifications as read if they are currently unread
        $latestNotifications->each(function ($notification) use ($user) {
            if (!$notification->isReadBy($user->id)) {
                $notification->markAsReadBy($user->id);
            }
        });

        $notifications = $latestNotifications->map(function ($notification) use ($user) {
            // Clean up HTML message: remove \r\n, normalize whitespace, but keep HTML structure
            $cleanedMessage = $notification->message;
            if ($cleanedMessage) {
                // Remove carriage return and line feed characters
                $cleanedMessage = str_replace(["\r\n", "\r", "\n"], '', $cleanedMessage);
                // Remove extra whitespace between HTML tags
                $cleanedMessage = preg_replace('/>\s+</', '><', $cleanedMessage);
                // Remove leading/trailing whitespace
                $cleanedMessage = trim($cleanedMessage);
            }
            
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $cleanedMessage,
                'type' => $notification->type,
                'is_read' => $notification->isReadBy($user->id),
                'created_at' => $notification->created_at->toIso8601String(),
                'created_at_human' => $notification->created_at->diffForHumans(),
                'created_by' => $notification->creator->name ?? 'System',
            ];
        });

        $unreadCount = (clone $baseQuery)
            ->get()
            ->filter(function ($notification) use ($user) {
                return !$notification->isReadBy($user->id);
            })
            ->count();

        return response()->json([
            'status' => true,
            'data' => [
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ],
        ]);
    }
}

