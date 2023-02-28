<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoticiaImagen extends Model
{
    use HasFactory;
    protected $table = 'noticia_imagen';
    public $timestamps = false;
}
