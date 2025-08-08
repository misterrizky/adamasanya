<?php

namespace App\Multipliers;

use Carbon\Carbon;
use LevelUp\Experience\Contracts\Multiplier;

class IsWeekDay implements Multiplier
{
    public bool $enabled = true;

    public function qualifies(array $data): bool
    {
        $now = now();
        $dayOfWeek = $now->dayOfWeek;
        $isWeekend = in_array($dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY]);
        return !$isWeekend;
        //
    }

    public function setMultiplier(): int
    {
        return 2;
    }
}
