<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubManagement extends Model
{
    protected $fillable = [
        'id',
        'management_id',
        'name',
        'slug',
        'is_video_pdf',
    ];

    protected $table = 'submanagements';


    public function contents()
    {
        return $this->hasMany(ModuleContent::class, 'submanagement_id');
    }

    public function management()
    {
        return $this->belongsTo(Management::class, 'management_id');
    }
}
