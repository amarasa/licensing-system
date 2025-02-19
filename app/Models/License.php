<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    protected $fillable = ['plugin_id', 'license_key', 'status', 'purchased_at', 'expires_at'];

    protected $casts = [
        'purchased_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function plugin()
    {
        return $this->belongsTo(Plugin::class);
    }

    public function activations()
    {
        return $this->hasMany(Activation::class);
    }
}
