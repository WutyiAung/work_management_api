<?php

namespace App\Http\Controllers;

use App\Models\AssignedTask;
use App\Models\ContentManagements;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // public function index(Request $request)
    // {
    //     $date = $request->query('date');
    //     $query = AssignedTask::with('contentManagement');
    //     if ($date && $date !== 'undefined' && $date !== 'null') {
    //         $query->whereHas('contentManagement', function ($query) use ($date) {
    //             $query->where('notify_date', $date);
    //         });
    //     }
    //     $notifications = $query->get();
    //     return response()->json([
    //         'status' => 'success',
    //         'notifications' => $notifications
    //     ]);
    // }
    // public function index(Request $request)
    // {
    //     $date = $request->query('date');
    //     $query = AssignedTask::with('contentManagement');
    //     if ($date && $date !== 'undefined' && $date !== 'null') {
    //         $query->whereHas('contentManagement', function ($query) use ($date) {
    //             $query->where('notify_date', $date);
    //         });
    //     }
    //     $notifications = $query->get();

    //     // Transform content_management array to object and remove array
    //     $notifications->each(function ($notification) {
    //         if ($notification->contentManagement->count() > 0) {
    //             $notification->contentManagement = $notification->contentManagement->first();
    //         } else {
    //             $notification->contentManagement = null;
    //         }
    //         unset($notification->content_management); // Remove the array form
    //     });

    //     return response()->json([
    //         'status' => 'success',
    //         'notifications' => $notifications
    //     ]);
    // }
    // public function index(Request $request)
    // {
    //     $date = $request->query('date');
    //     $query = AssignedTask::with('contentManagement');
    //     if ($date && $date !== 'undefined' && $date !== 'null') {
    //         $query->whereHas('contentManagement', function ($query) use ($date) {
    //             $query->where('notify_date', $date);
    //         });
    //     }
    //     $notifications = $query->get();

    //     // Transform content_management array to object and hide array
    //     $notifications->each(function ($notification) {
    //         if ($notification->contentManagement->count() > 0) {
    //             $notification->contentManagement = $notification->contentManagement->first();
    //         } else {
    //             $notification->contentManagement = null;
    //         }
    //         $notification->makeHidden('content_management'); // Hide the array form
    //     });

    //     return response()->json([
    //         'status' => 'success',
    //         'notifications' => $notifications
    //     ]);
    // }
    public function index(Request $request)
    {
        $date = $request->query('date');
        $time = $request->query('time');
        $query = AssignedTask::with('contentManagement');
        if ($date && $date !== 'undefined' && $date !== 'null') {
            $query->whereHas('contentManagement', function ($query) use ($date) {
                $query->where('notify_date', $date);
            });
            if($time && $time !== 'undefined' && $time !== 'null'){
                $query->whereHas('contentManagement', function ($query) use ($time) {
                    $query->where('notify_time', $time);
                });
            }
        }
        $notifications = $query->get();

        // Transform the response to hide the content_management array
        $transformedNotifications = $notifications->map(function ($notification) {
            $notificationArray = $notification->toArray();
            if (!empty($notificationArray['content_management'])) {
                $notificationArray['contentManagement'] = $notificationArray['content_management'][0];
                unset($notificationArray['content_management']);
            } else {
                $notificationArray['contentManagement'] = null;
            }
            return $notificationArray;
        });

        return response()->json([
            'status' => 'success',
            'notifications' => $transformedNotifications
        ]);
    }
    public function store($id)
    {
        $data = ContentManagements::findOrFail($id);
        $data->is_seen = 1;
        $data->save();
        return response()->json([
            'status' => 'success',
        ]);
    }
    public function isClose($id)
    {
        $data = ContentManagements::findOrFail($id);
        $data->is_close = 1;
        $data->save();
        return response()->json([
            'status' => 'success'
        ]);
    }
}
