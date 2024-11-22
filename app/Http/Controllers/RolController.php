<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Rol;

class RolController extends Controller
{
    public function index()
    {
        try {
            $rol = Rol::all();
            return response()->json($rol, 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener los roles: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al obtener los roles',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    
}
