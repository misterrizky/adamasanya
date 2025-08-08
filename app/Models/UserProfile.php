<?php

namespace App\Models;

use Carbon\Carbon;
use Laravolt\Avatar\Avatar;
use WendellAdriel\Lift\Lift;
use Illuminate\Database\Eloquent\Model;
use WendellAdriel\Lift\Attributes\Relations\BelongsTo;

#[BelongsTo(User::class)]
class UserProfile extends Model
{
    use Lift;
    protected function casts(): array
    {
        return [
            'bod' => 'date',
            'start_at' => 'date',
            'end_at' => 'date',
        ];
    }
    public function getImageAttribute()
    {
        if(!$this->selfie){
            $avatar = new Avatar();
            return $avatar->create($this->user->name)->toBase64();
        }else{
            if(file_exists(public_path('storage/'.$this->selfie))){
                return asset('storage/'.$this->selfie);
            }else{
                return asset('media/avatars/blank.png');
            }
        }
    }
    public function usia()
    {
        $now = Carbon::now(); // Tanggal sekarang
        $b_day = Carbon::parse($this->bod); // Tanggal Lahir
        $age = $b_day->diffInYears($now);  // Menghitung umur
        return $age;
    }
}
