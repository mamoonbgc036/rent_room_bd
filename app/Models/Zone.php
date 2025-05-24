<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;
    protected $fillable = ['area_id', 'city_id', 'name'];

    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}
