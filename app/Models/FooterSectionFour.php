<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSectionFour extends Model
{
    use HasFactory;
    protected $fillable = ['footer_id', 'title', 'description'];

    public function socialLinks()
    {
        return $this->hasMany(SocialLink::class);
    }
}
