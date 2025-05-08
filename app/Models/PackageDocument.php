<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDocument extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'path', 'expires_at'];
    protected $dates = ['expires_at'];
    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function updatedBy()
{
    return $this->belongsTo(User::class, 'updated_by');
}
}
