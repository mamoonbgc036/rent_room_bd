<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntireProperty extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'package_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function roomPrices()
    {
        return $this->hasMany(RoomPrice::class);
    }
    public function prices()
    {
        return $this->hasMany(RoomPrice::class);
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
