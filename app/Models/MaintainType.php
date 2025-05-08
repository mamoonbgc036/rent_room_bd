<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintainType extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','type'];

    public function maintains()
    {
        return $this->hasMany(Maintain::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
