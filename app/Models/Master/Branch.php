<?php

namespace App\Models\Master;

use App\Models\User;
use App\Models\Region\City;
use App\Models\Region\State;
use WendellAdriel\Lift\Lift;
use App\Models\Region\Country;
use App\Models\Region\Village;
use App\Models\Region\Subdistrict;
use Illuminate\Database\Eloquent\Model;
use WendellAdriel\Lift\Attributes\Relations\HasMany;
use WendellAdriel\Lift\Attributes\Relations\BelongsTo;

#[BelongsTo(Country::class)]
#[BelongsTo(State::class)]
#[BelongsTo(City::class)]
#[BelongsTo(Subdistrict::class)]
#[BelongsTo(Village::class)]
#[HasMany(User::class)]
#[HasMany(BranchSchedule::class)]

class Branch extends Model
{
    use Lift;
    public function getStatusAttribute()
    {
        if ($this->st == "a") {
            return [
                'class' => 'badge-light-success',
                'text' => 'Aktif'
            ];
        } elseif ($this->st == "i") {
            return [
                'class' => 'badge-light-danger',
                'text' => 'Tidak Aktif'
            ];
        }
        
        return [
            'class' => 'badge-light-primary',
            'text' => 'Belum Ditetapkan'
        ];
    }
}
