<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserRole;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for admin management.
     */
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $notifications = Notification::with(['role', 'user', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $roles = UserRole::where('is_active', true)->whereNotIn('id', [1, 2])->get();
        $users = User::where('is_active', true)->with('role')->get();

        return view('admin.notifications.create', compact('roles', 'users'));
    }

    /**
     * Store a newly created notification.
     */
    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error',
            'target_type' => 'required|in:all,role,user',
            'role_id' => 'required|exists:user_roles,id',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $notification = Notification::create([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'target_type' => $request->target_type,
            'role_id' => $request->role_id,
            'user_id' => $request->target_type === 'user' ? $request->user_id : null,
            'created_by' => AuthHelper::getCurrentUserId()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification created successfully.',
            'data' => $notification->load(['role', 'user', 'creator'])
        ]);
    }

    /**
     * Display the specified notification.
     */
    public function show($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $notification = Notification::with(['role', 'user', 'creator', 'reads.user'])
            ->findOrFail($id);

        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified notification.
     */
    public function edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $notification = Notification::findOrFail($id);
        $roles = UserRole::where('is_active', true)->whereNotIn('id', [1, 2])->get();
        $users = User::where('is_active', true)->with('role')->get();

        return view('admin.notifications.edit', compact('notification', 'roles', 'users'));
    }

    /**
     * Update the specified notification.
     */
    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $notification = Notification::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error',
            'target_type' => 'required|in:all,role,user',
            'role_id' => 'required|exists:user_roles,id',
            'user_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean'
        ]);

        $notification->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'target_type' => $request->target_type,
            'role_id' => $request->role_id,
            'user_id' => $request->target_type === 'user' ? $request->user_id : null,
            'is_active' => $request->has('is_active') ? true : false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification updated successfully.',
            'data' => $notification->load(['role', 'user', 'creator'])
        ]);
    }

    /**
     * Remove the specified notification.
     */
    public function destroy($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully.'
        ]);
    }

    /**
     * Get notifications for the current user (for topbar).
     */
    public function getUserNotifications()
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (!$currentUser) {
            return response()->json(['notifications' => []]);
        }

        $notifications = Notification::forUser($currentUser->id, $currentUser->role_id)
            ->with(['role', 'user', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) use ($currentUser) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => $notification->isReadBy($currentUser->id),
                    'created_at' => $notification->created_at->diffForHumans(),
                    'created_by' => $notification->creator->name ?? 'System'
                ];
            });

        return response()->json(['notifications' => $notifications]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead($id)
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $notification = Notification::findOrFail($id);
        
        // Check if user should see this notification
        $userNotifications = Notification::forUser($currentUser->id, $currentUser->role_id)
            ->where('id', $id)
            ->exists();

        if (!$userNotifications) {
            return response()->json(['error' => 'Notification not found.'], 404);
        }

        $notification->markAsReadBy($currentUser->id);

        return response()->json(['success' => true]);
    }

    /**
     * View all notifications page for users.
     */
    public function viewAll()
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (!$currentUser) {
            return redirect()->route('login');
        }

        $notifications = Notification::forUser($currentUser->id, $currentUser->role_id)
            ->with(['role', 'user', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.view-all', compact('notifications'));
    }
}
