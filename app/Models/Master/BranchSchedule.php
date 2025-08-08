<?php

namespace App\Models\Master;

use WendellAdriel\Lift\Lift;
use Illuminate\Database\Eloquent\Model;

class BranchSchedule extends Model
{
    use Lift;
    protected $fillable = [
        'branch_id', 'day_of_week', 'open_time', 'end_time', 'is_open'
    ];
}
