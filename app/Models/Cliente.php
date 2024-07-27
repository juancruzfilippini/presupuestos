<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    // Define la tabla asociada
    protected $table = 'clientes';

    // Define los campos que se pueden rellenar (mass assignable)
    protected $fillable = [
        'nombres',
        'apellidos',
        'empresa',
        'mail',
        'telefono',
        'created_by',   // Agregar 'created_by' aquí
        'updated_by',
    ];

    // Si no estás usando el campo de timestamps 'created_at' y 'updated_at', desactiva el manejo automático de timestamps
    public $timestamps = true;

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
