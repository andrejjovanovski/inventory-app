<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Group;
use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make("Active members", Member::query()->count())->icon(
                "heroicon-m-user",
            ),
            Stat::make("Active groups", Group::query()->count())->icon(
                "heroicon-m-user-group",
            ),
            Stat::make("Total events", Event::query()->count())->icon(
                "heroicon-m-calendar",
            ),
            Stat::make(
                "Active events",
                Event::query()->where("end_date", ">=", now())->count(),
            )->icon("heroicon-m-calendar-date-range"),
        ];
    }
}
