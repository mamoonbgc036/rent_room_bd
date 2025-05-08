<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmenityType extends Model
{
    use HasFactory;
    protected $fillable = [ 'type', 'user_id'];

    public function amenities()
    {
        return $this->hasMany(Amenity::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
