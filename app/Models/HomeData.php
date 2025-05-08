<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeData extends Model
{
    use HasFactory;
    
    protected $fillable = ['section_title'];
    public function items()
    {
        return $this->hasMany(HomeDataItem::class);
    }

    public function homeDataItems()
    {
        return $this->hasMany(HomeDataItem::class);
    }
}
