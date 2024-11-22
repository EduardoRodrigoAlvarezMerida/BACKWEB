<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $primaryKey = 'IdProducto'; 
    protected $fillable = [
        'Nombre',
        'Descripcion',
        'Precio',
        'NombreImagen',
        'IdCategoria',
    ];
    public $timestamps = false;

    public function categoria()
    {
        return $this->belongsTo(categoria::class,'IdCategoria');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'IdProducto'); // Relación con Inventario
    }
}
