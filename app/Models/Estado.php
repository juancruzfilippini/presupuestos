<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;
    
    public $timestamps = false;  // Desactiva el manejo automático de timestamps

    protected $connection = 'mysql'; // Para la base de datos 'db-sistema-ap'

    protected $table = 'estado'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'id',
        'nombre'
    ];

    public static function getEstadoById($id){
        $estado = self::find($id);
        return $estado ? $estado->nombre : null;
    }

    public static function getEstadoByNombre($estado){
        if ($estado = 'completado') {
            return 4;
        }
        if ($estado = 'esperando anestesia') {
            return 5;
        }
        if ($estado = 'esperando firmas') {
            return 6;
        }
        if ($estado = 'esperando anestesia y farmacia') {
            return 7;
        }
        if ($estado = 'esperando farmacia') {
            return 8;
        }
        if ($estado = 'creado') {
            return 0;
        }
        if ($estado = 'editado') {
            return 1;
        }
        if ($estado = 'informando') {
            return 2;
        }
        if ($estado = 'firmando') {
            return 3;
        }
        if ($estado = 'anulado') {
            return 10;
        }
    }

}