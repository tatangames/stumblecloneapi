<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoticiaVideos extends Model
{
    use HasFactory;
    protected $table = 'noticia_videos';
    public $timestamps = false;
}
