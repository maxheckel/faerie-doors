<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaerieTemplate extends Model
{
    use HasFactory;

    public function faeries(){
        return $this->hasMany(Faerie::class);
    }
}
