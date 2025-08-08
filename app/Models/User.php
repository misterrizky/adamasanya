<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravolt\Avatar\Avatar;
use WendellAdriel\Lift\Lift;
use App\Models\Master\Branch;
use Bavix\Wallet\Traits\CanPay;
use App\Models\Transaction\Cart;
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWallets;
use Cog\Laravel\Ban\Traits\Bannable;
use Bavix\Wallet\Interfaces\Customer;
use Spatie\Permission\Traits\HasRoles;
use Cmgmyr\Messenger\Traits\Messagable;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use LevelUp\Experience\Concerns\GiveExperience;
use LevelUp\Experience\Concerns\HasAchievements;
use WendellAdriel\Lift\Attributes\Relations\HasOne;
use Cog\Contracts\Ban\Bannable as BannableInterface;
use WendellAdriel\Lift\Attributes\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use NotificationChannels\WebPush\HasPushSubscriptions;
use WendellAdriel\Lift\Attributes\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[BelongsTo(Branch::class)]
#[HasMany(Cart::class)]
#[HasMany(Rent::class)]
#[HasMany(Sale::class)]
#[HasOne(UserAddress::class)]
#[HasMany(UserAddress::class)]
#[HasMany(UserBank::class)]
#[HasMany(UserFamily::class)]
#[HasMany(UserMeter::class)]
#[HasMany(UserTag::class)]
#[HasMany(Session::class)]
class User extends Authenticatable implements BannableInterface, Customer, Wallet
{
    use Bannable, CanPay, GiveExperience, HasAchievements, HasFactory, HasPushSubscriptions, HasRoles, HasWallet, HasWallets, Lift, Messagable, Notifiable, SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            })
            ->orWhereHas('profile', function($query) use ($keyword) {
                $query->where('nik', 'like', "%{$keyword}%");
            });
    }
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'deleted_at' => 'datetime',
            'verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function activityLogs()
    {
        return $this->hasMany(Activity::class, 'causer_id');
    }
    public function profile()
    {
        return $this->belongsTo(UserProfile::class, 'id', 'user_id');
    }
    public function getImageAttribute()
    {
        if(!$this->avatar){
            $avatar = new Avatar();
            return $avatar->create($this->name)->toBase64();
        }else{
            if(file_exists(public_path('storage/'.$this->avatar))){
                return asset('storage/'.$this->avatar);
            }else{
                return asset('media/avatars/blank.png');
            }
        }
    }
    public function getStatusAttribute()
    {
        if($this->isBanned()){
            return [
                'class' => 'danger',
                'text' => 'DPO'
            ];
        }elseif($this->deleted_at){
            return [
                'class' => 'dark',
                'text' => 'Hapus Akun'
            ];
        }elseif ($this->st == "verified") {
            return [
                'class' => 'success',
                'text' => 'Terverifikasi'
            ];
        } elseif ($this->st == "unverified") {
            return [
                'class' => 'info',
                'text' => 'Tidak Terverifikasi'
            ];
        } elseif ($this->st == "suspend") {
            return [
                'class' => 'warning',
                'text' => 'Ditangguhkan'
            ];
        }
        
        return [
            'class' => 'primary',
            'text' => 'Menunggu Verifikasi'
        ];
    }
    public function showFormattedPhoneNumber($phone)
    {
        $phoneNumber = $phone;
        $formattedPhoneNumber = $this->formatPhoneNumber($phoneNumber);

        return $formattedPhoneNumber;
    }

    private function formatPhoneNumber($phoneNumber)
    {
        if (substr($phoneNumber, 0, 1) === '0') {
            return '+62' . substr($phoneNumber, 1);
        }
        return $phoneNumber;
    }
}
