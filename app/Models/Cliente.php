<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Asegúrate de importar esta clase
use Illuminate\Database\Eloquent\Model;

class Cliente extends Authenticatable // Cambia de Model a Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'IdCliente'; // Si tienes una columna primaria diferente, defínela aquí
    protected $fillable = [
        'Nombre',
        'Apellido',
        'Correo',
        'Contraseña',
        'NIT',
        'IdRol',
    ];
    
    public $timestamps = false;

    // Relación con el modelo Rol
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'IdRol');
    }
}
