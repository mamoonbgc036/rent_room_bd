<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'user_id', 'symbol', 'currency', 'photo'];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
