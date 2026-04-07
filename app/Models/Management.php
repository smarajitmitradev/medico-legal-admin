<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Management extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'image'
    ];

    protected $table = 'managements';

    public function submanagements()
    {
        return $this->hasMany(SubManagement::class);
    }
}
