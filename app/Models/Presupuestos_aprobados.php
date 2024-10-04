<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presupuestos_aprobados extends Model
{
    use HasFactory;
    
    public $timestamps = false;  // Desactiva el manejo automático de timestamps

    protected $connection = 'mysql'; // Para la base de datos 'db-sistema-ap'

    protected $table = 'presupuestos_aprobados'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'id',
        'presupuesto_id',
        'file_path',
    ];

}