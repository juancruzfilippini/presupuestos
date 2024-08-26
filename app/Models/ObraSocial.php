<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraSocial extends Model
{
    use HasFactory;

    protected $connection = 'db2'; // Para la base de datos 'db-sistema-ap'
    protected $table = 'obra_social'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'id',
        'nombre',
    ];

    public static function getObrasSociales()
    {
        // Obtener todas las obras sociales
        $obrasSociales = self::select('id', 'nombre')->get();
        // Convertir la colección a un array
        return $obrasSociales->toArray();
    }

    public static function getObraSocialById($id)
    {
        // Buscar el registro por ID
        $obraSocial = self::where('id', $id)->first();
        
        // Devolver el nombre si el registro existe, de lo contrario null
        return $obraSocial ? $obraSocial->nombre : null;
    }
    
}