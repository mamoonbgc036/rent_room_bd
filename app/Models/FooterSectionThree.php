<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSectionThree extends Model
{
    use HasFactory;
    protected $fillable = ['footer_id', 'title', 'link'];
    public function footer()
    {
        return $this->belongsTo(Footer::class);
    }
}
