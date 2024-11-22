<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCotizacion extends Model
{
    use HasFactory;
    protected $primaryKey = 'IdDetalle';


    protected $fillable = [
        'Cantidad',
        'Precio',
        'IdInventario', 
        'IdCotizacion'       
    ];

    public $timestamps = false;

    protected $table = 'detalle_cotizaciones';

  
    public function venta()
    {
        return $this->belongsTo(Cotizacion::class, 'IdCotizacion');
    }

    
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'IdInventario');
    }


}
