<?php
namespace User;

use Core\View;
use Core\DB;
use Core\Request;
use Core\Auth;
use Core\Helper;

class Notifications {
    /**
     * All Notifications
     *
     * @version 7.7
     * @return void
     */
    public function index(Request $request){
        
        View::set('title', e('Notifications'));

        $user = Auth::user();
        $notifications = [];

        // Fetch all notifications for the user
        $query = DB::appevents()
            ->whereRaw("type='notification' AND (userid IS NULL OR userid = '{$user->id}') AND (planid IS NULL OR planid = '".($user->planid ?? '0')."') AND (expires_at IS NULL OR DATE(expires_at) > DATE('".Helper::dtime()."'))")
            ->orderByDesc('id');

        // Paginate notifications
        $db = $query->paginate(20);
        foreach($db as $notification){
            $data = json_decode($notification->data);
            $notification->content = $data->content ?? '';
            $notification->title = $data->title ?? '';
            $notification->date = $notification->created_at ? Helper::timeago($notification->created_at) : null;
            $notification->dateFull = $notification->created_at ? date('M d, Y h:i A', strtotime($notification->created_at)) : null;
            $notifications[] = $notification;
        }
        
        return View::with('user.notifications', compact('notifications'))->extend('layouts.dashboard');
    }
}
