<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use App\Models\Team;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel\Concerns\HasAvatars;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Support\Facades\Storage;
use Laravelcm\Subscriptions\Traits\HasPlanSubscriptions;




class User extends Authenticatable implements HasTenants, FilamentUser, HasAvatar

{
    use HasFactory, Notifiable, HasRoles, HasPanelShield, HasAvatars,  HasPlanSubscriptions;

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null ;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'password', 'role','countries_id','cities_id','sertif','phone','address','avatar_url'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function battreis()
    {
        return $this->hasMany(Battrei::class, 'owner_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id');
    }
 
    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }
 
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams()->whereKey($tenant)->exists();
    }
    public function countries(){
        return $this->belongsTo(countrie::class);
    }
    public function cities(){
        return $this->belongsTo(citie::class);
    }
    public function fligh(){
        return $this->hasMany(fligh::class, 'users_id');
    }
    
    //menginjinkan akses login ke seluruh role yang ada
    public function canAccessPanel(Panel $panel): bool
    {
        $allowedRoles = $this->getAllowedRoles();

        return $this->hasAnyRole($allowedRoles);
    }
    protected function getAllowedRoles(): array
    {
        return \Spatie\Permission\Models\Role::where('guard_name', 'web')
            ->pluck('name')
            ->toArray();
    }
     //End menginjinkan akses login ke seluruh role yang ada
}
