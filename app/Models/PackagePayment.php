<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagePayment extends Model
{
    use HasFactory;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'details',
        'amount',
        'payment_date',
        'payment_status'
    ];

    // Define relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define relationship with Package
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
