<?php

namespace App\Models;

use App\Models\Region\City;
use App\Models\Region\State;
use WendellAdriel\Lift\Lift;
use App\Models\Region\Country;
use App\Models\Region\Village;
use App\Models\Region\Subdistrict;
use Illuminate\Database\Eloquent\Model;
use WendellAdriel\Lift\Attributes\Relations\BelongsTo;

#[BelongsTo(User::class)]
#[BelongsTo(Country::class)]
#[BelongsTo(State::class)]
#[BelongsTo(City::class)]
#[BelongsTo(Subdistrict::class)]
#[BelongsTo(Village::class)]
class UserAddress extends Model
{
    use Lift;
}
