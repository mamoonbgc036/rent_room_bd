<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSection4 extends Model
{
    use HasFactory;
    protected $table = 'footer_section_4';
    protected $fillable = ['footer_id', 'section4_title', 'social_link_description', 'social_icon_class', 'social_link'];

    public function footer()
    {
        return $this->belongsTo(Footer::class);
    }
}
