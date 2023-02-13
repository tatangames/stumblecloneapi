<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioRecursos extends Model
{
    use HasFactory;
    protected $table = 'usuario_recursos';
    public $timestamps = false;
}
