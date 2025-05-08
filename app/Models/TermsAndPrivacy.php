<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsAndPrivacy extends Model
{
    use HasFactory;
    protected $fillable = ['terms_title', 'terms_link', 'privacy_title', 'privacy_link', 'rights_reserves_text'];
}
