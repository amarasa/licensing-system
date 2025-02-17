<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'current_version'];

    public function licenses()
    {
        return $this->hasMany(License::class);
    }
}
