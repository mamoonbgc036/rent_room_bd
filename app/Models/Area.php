<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function city()
    {
        return $this->belongsTo(City::class, 'district_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
