<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','type'];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
