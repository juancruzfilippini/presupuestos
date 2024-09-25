<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convenio_actual extends Model
{
    use HasFactory;
    public $timestamps = false;  // Desactiva el manejo automático de timestamps
    protected $connection = 'mysql'; // Para la base de datos 'db-sistema-ap'
    protected $table = 'convenio_actual'; // Nombre de la tabla en la base de datos
    protected $fillable = [
        'id',
        'convenio_id',
        'nombre_convenio',
    ];
}