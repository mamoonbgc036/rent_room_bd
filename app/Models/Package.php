<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    use HasFactory;
    protected $fillable = [
        'country_id',
        'user_id',
        'city_id',
        'area_id',
        'property_id',
        'zone_id',
        'name',
        'address',
        'map_link',
        'number_of_rooms',
        'number_of_kitchens',
        'seating',
        'details',
        'video_link',
        'common_bathrooms',
        'status',
        'expiration_date',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'property_type_id'
    ];
    protected $dates = ['assigned_at'];
    protected $casts = [
        'assigned_at' => 'datetime',
    ];


    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function maintains(): BelongsToMany
    {
        return $this->belongsToMany(Maintain::class, 'package_maintains')
            ->withPivot('is_paid', 'price');
    }


    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'package_amenities')
            ->withPivot('is_paid', 'price');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function entireProperty()
    {
        return $this->hasOne(EntireProperty::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function userDetails()
    {
        return $this->hasMany(UserDetail::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, UserDetail::class, 'package_id', 'id', 'id', 'user_id');
    }


    public function instructions()
    {
        return $this->hasMany(PackageInstruction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedPartner()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function getSlugAttribute()
    {
        return str_replace(' ', '-', strtolower($this->name));
    }

    public function getShowUrl()
    {
        // Check for assigned partner first
        if ($this->assignedPartner) {
            $partnerSlug = str_replace(' ', '-', strtolower($this->assignedPartner->phone));
        }
        // If not assigned, use the creator's phone
        elseif ($this->creator) {
            $partnerSlug = str_replace(' ', '-', strtolower($this->creator->phone));
        }
        // If neither exists (shouldn't happen, but just in case)
        else {
            return '#';
        }

        // Include ID in the package slug for better identification
        $packageSlug = $this->id . '-' . str_replace(' ', '-', strtolower($this->name));
        return route('package.show', [
            'partnerSlug' => $partnerSlug,
            'packageSlug' => $packageSlug
        ]);
    }

    public function documents()
    {
        return $this->hasMany(PackageDocument::class);
    }
}
