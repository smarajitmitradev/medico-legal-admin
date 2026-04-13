<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleContent extends Model
{
    protected $fillable = [
        'submanagement_id',
        'title',
        'description',
        'youtube_link',
        'pdf_file',
        'reading_time',
    ];


    public function sub()
    {
        return $this->belongsTo(SubManagement::class, 'submanagement_id');
    }

    protected $casts = [
        'reading_time' => 'integer',
    ];
}
