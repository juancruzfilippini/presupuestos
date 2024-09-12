<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cambios_prestaciones extends Model
{
    use HasFactory;
    protected $table = 'cambios_prestaciones'; // Especifica la tabla
    protected $fillable = ['presupuesto_id', 'campo', 'valor_anterior', 'valor_nuevo', 'fecha_cambio', 'usuario_id'];
    public $timestamps = false; // Desactivar las marcas de tiempo
}
