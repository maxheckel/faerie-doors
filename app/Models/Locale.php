<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    use HasFactory;

    public function wizards(){
        return $this->belongsToMany(User::class)->wherePivot('active', true);
    }
}
