<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Prestacion extends Model
{
    // Usa la conexión por defecto (db-sistema-ap)
    protected $connection = 'db2'; // Para la base de datos ''
    protected $table = 'prestacion'; // Nombre de la tabla en la base de datos

    

    protected $fillable = [
        'id',
        'nombre',
        'codigo',
    ];

    public static function getPrestacionById($id)
    {
        $prestacion = self::where('id', $id)->first();
        
        // Devolver el nombre si el registro existe, de lo contrario null
        return $prestacion ? $prestacion->nombre : null;
    }

}