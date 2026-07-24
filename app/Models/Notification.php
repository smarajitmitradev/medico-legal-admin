<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'type',
        'module_content_id'
    ];

    public function content()
    {
        return $this->belongsTo(ModuleContent::class, 'module_content_id');
    }
}