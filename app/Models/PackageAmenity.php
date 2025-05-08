<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageAmenity extends Model
{
    use HasFactory;
    protected $fillable = ['package_id','user_id', 'amenity_id', 'price', 'is_paid'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function amenity()
    {
        return $this->belongsTo(Amenity::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
