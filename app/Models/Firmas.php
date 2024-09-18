<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Firmas extends Model
{
    use HasFactory;
    
    public $timestamps = false;  // Desactiva el manejo automático de timestamps

    protected $connection = 'mysql'; // Para la base de datos 'db-sistema-ap'

    protected $table = 'firmas'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'id',
        'presupuesto_id',
        'comercializacion',
        'fecha_comercializacion',
        'firmado_por_comercializacion',
        'auditoria',
        'fecha_auditoria',
        'firmado_por_auditoria',
        'direccion',
        'fecha_direccion',
        'firmado_por_direccion',
    ];

}