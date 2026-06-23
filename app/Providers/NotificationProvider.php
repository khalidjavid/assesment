<?php
namespace App\Providers;

use App\Models\System\Notification;

class NotificationProvider {

    public static function getNotification($user){
        $query = Notification::query();
        switch ($user['role']) {
            case 'agency':
                // no filter → sees everything
                break;
            case 'building_manager':
                $query->where('created_by', $user['id']);
                break;
            case 'resident':
                $query->where('user_id', $user['id']);
                break;
            default:
                return collect();
        }
        return $query
            ->latest()
            ->limit(20)
            ->get();
    }
}