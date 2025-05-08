<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'person_name', 'passport', 'nid_or_other', 'payslip', 'student_card', 'photo'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
