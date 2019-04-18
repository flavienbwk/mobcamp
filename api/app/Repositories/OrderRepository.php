<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

use App\UserItem;
use App\Schedule;
use App\TourSchedule;
use App\OrderUserItem;

class OrderRepository
{

    public static function getCooperativeSchedule($cooperative_id, $schedule_id)
    {
        return Schedule::find($schedule_id)
            ->join("tour_schedule", "tour_schedule.schedule_id", "=", "schedule.id")
            ->join("tour", "tour.id", "=", "tour_schedule.schedule_id")
            ->where("tour.cooperative_id", $cooperative_id);
    }

    public static function getItemQuantityOrdered($user_item_id)
    {
        $query = OrderUserItem::
        join("order", "order.id", "=", "order_user_item.order_id")
        ->where([
            ["order_user_item.user_item_id", $user_item_id],
            ["order.type", "buy"],
        ])
            ->sum('quantity');
        return $query;
    }

    public static function getItemQuantityTotal($user_item_id)
    {
        $query = UserItem::find($user_item_id);
        if ($query)
            if ($query->first())
                return intval($query->first()->toArray()["quantity"]);
        return 0;
    }

    public static function getTourIdByScheduleId($schedule_id)
    {
        $TourSchedule = TourSchedule::select("tour.id")
            ->join("schedule", "schedule.id", "=", "tour_schedule.schedule_id")
            ->join("tour", "tour.id", "=", "tour_schedule.tour_id")
            ->where("schedule.id", $schedule_id);
        if ($TourSchedule && $TourSchedule->first()) {
            return $TourSchedule->first()->toArray()["id"];
        } else {
            return null;
        }
    }
}
