<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    use HasFactory;

    public $timestamps = false;  // Desactiva el manejo automático de timestamps

    protected $connection = 'mysql'; // Para la base de datos 'db-sistema-ap'

    protected $table = 'presupuesto'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'obra_social',
        'convenio',
        'especialidad',
        'condicion',
        'incluye',
        'excluye',
        'adicionales',
        'total_presupuesto',
        'anestesia_id',
        'anestesia_detalle',
        'complejidad',
        'precio_anestesia',
        'fecha',
        'paciente_salutte_id',
        'paciente',
        'medico_tratante',
        'medico_solicitante',
        'file_path',
        'borrado_logico',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }


}

/*
protected $fillable = [
        'paciente_salutte_id',
        'profesional_id',
        'anestesia_id',
        'fecha',
        'obra_social',
        'especialidad',
        'complejidad',
        'precio_anestesia',
        'total_presupuesto',
        'condicion',
        'incluye',
        'excluye',
        'adicionales',
    ];
    */