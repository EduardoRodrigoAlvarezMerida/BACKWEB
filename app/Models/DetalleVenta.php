<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $primaryKey = 'IdDetalle';

    protected $fillable = [
        'Cantidad',
        'Precio',
        'IdInventario', 
        'IdVenta'       
    ];

   
    public $timestamps = false;

    protected $table = 'detalle_ventas';

  
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'IdVenta');
    }

    
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'IdInventario');
    }
}
