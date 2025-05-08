<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    use HasFactory;
    protected $fillable = ['footer_section_four_id', 'icon_class', 'link'];

    public function footerSectionFour()
    {
        return $this->belongsTo(FooterSectionFour::class);
    }
}
