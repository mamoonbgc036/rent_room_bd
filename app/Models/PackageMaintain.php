<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageMaintain extends Model
{
    use HasFactory;
    protected $fillable = ['package_id','user_id', 'maintain_id', 'price', 'is_paid'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function maintain()
    {
        return $this->belongsTo(Maintain::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
