<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogInventario extends Model
{
    use HasFactory;
    protected $table = 'logs';
    protected $primaryKey = 'IdLogs'; 
    protected $fillable = [
        'Descripcion',
    ];
    public $timestamps = false;
}
