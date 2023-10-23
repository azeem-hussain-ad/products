<?php

namespace App\Http\Controllers;

use App\Models\NotifyUsers;
use Illuminate\Http\Request;

class NotifyUsersController extends Controller
{
    public static function addNotiyUser($userId, $productId){

        $notification = new NotifyUsers();
        $notification->user_id = $userId;
        $notification->product_id = $productId;

        $notification->save();

        return $notification;
    }

    public static function checkNotifiable($userId, $productId){

        $notification = NotifyUsers::where('product_id', $productId)->where('user_id', $userId)->where('is_notified', false)->get();

        return $notification;
    }

    public static function getNotieables( $productId){

        $notification = NotifyUsers::where('product_id', $productId)->where('is_notified', false)->get();

        return $notification;
    }

    public static function updateNotifieables( $productId){

        $notification = NotifyUsers::where('product_id', $productId)->where('is_notified', false)->update(
            [
                'is_notified' => true,
            ]
        );

        return $notification;
    }
}
