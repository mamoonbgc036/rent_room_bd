<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeDataItem extends Model
{
    use HasFactory;
    protected $fillable = ['home_data_id', 'item_image', 'item_title', 'item_des'];

    public function homeData()
    {
        return $this->belongsTo(HomeData::class);
    }
}
