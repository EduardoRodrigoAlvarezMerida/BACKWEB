<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;
    protected $primaryKey = 'IdInventario'; 
    protected $fillable = [
        'Cantidad',
        'IdTienda',
        'IdProducto'
    ];
    public $timestamps = false;


    public function tienda()
    {
        return $this->belongsTo(Tienda::class, 'IdTienda');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'IdProducto');
    }


}
