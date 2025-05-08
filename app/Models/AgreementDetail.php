<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgreementDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'agreement_type',
        'duration',
        'amount',
        'deposit',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
