<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'fb_id',
        'password',
        'partner_bank_details',
        'proof_type_1',
        'proof_path_1',
        'proof_type_2',
        'proof_path_2',
        'proof_type_3',
        'proof_path_3',
        'proof_type_4',
        'proof_path_4',
    ];

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

    public function countries()
    {
        return $this->hasMany(Country::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function propertyTypes()
    {
        return $this->hasMany(PropertyType::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function maintains()
    {
        return $this->hasMany(Maintain::class);
    }

    public function amenities()
    {
        return $this->hasMany(Amenity::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'Super Admin';
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function documents()
    {
        return $this->hasMany(UserDocument::class);
    }
    public function agreementDetail()
    {
        return $this->hasOne(AgreementDetail::class);
    }
    public function bankDetail()
    {
        return $this->hasOne(BankDetail::class);
    }
    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function package()
    {
        return $this->hasOne(Package::class, 'user_id');
    }

    public function packagePayments()
    {
        return $this->hasMany(PackagePayment::class);
    }
}
