<?php

namespace App\Repositories;

use App\User;
use App\Role;
use App\Cooperative;
use App\TourSchedule;
use App\Schedule;
use App\Tour;

class TourRepository
{

    public static function inCooperative($tour_id, $cooperative_id)
    {
        return (Tour::where([
            ["id", $tour_id],
            ["cooperative_id", $cooperative_id]
        ])->count()) ? true : false;
    }

    public static function getTourSchedules($tour_id, $cooperative_id)
    {
        return TourSchedule::select("schedule.from", "schedule.to", "tour_schedule.place")
            ->join("tour", "tour.id", "=", "tour_schedule.tour_id")
            ->join("schedule", "schedule.id", "=", "tour_schedule.schedule_id")
            ->where([
                ["tour.cooperative_id", $cooperative_id],
                ["tour.id", $tour_id]
            ])
            ->get();
    }
    
}
