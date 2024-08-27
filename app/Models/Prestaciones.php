<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestaciones extends Model
{
    use HasFactory;
    
    public $timestamps = false;  // Desactiva el manejo automático de timestamps

    protected $connection = 'mysql'; // Para la base de datos 'db-sistema-ap'

    protected $table = 'prestaciones'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'id',
        'prestacion_salutte_id',
        'presupuesto_id',
        'codigo_prestacion',
        'nombre_prestacion',
        'modulo_total',
    ];

}