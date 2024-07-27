<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClienteController extends Controller
{
    // Muestra una lista de todos los clientes
    public function index(Request $request)
    {
        // Obtener el término de búsqueda del request
        $search = $request->input('search');

        // Consultar la base de datos, filtrando por nombres y apellidos si se ha ingresado un término de búsqueda
        $clientesQuery = Cliente::query();

        // Si hay un término de búsqueda, agregar condiciones para filtrar
        if ($search) {
            $clientesQuery->where(function($query) use ($search) {
                $query->where('nombres', 'LIKE', "%{$search}%")
                    ->orWhere('apellidos', 'LIKE', "%{$search}%");
            });
        }

        // Paginación de clientes
        $clientes = $clientesQuery->paginate(10); // Puedes ajustar el número de resultados por página según lo desees

        return view('clientes.index', compact('clientes', 'search'));
    }

    // Muestra el formulario para crear un nuevo cliente
    public function create()
    {
        return view('clientes.create');
    }

    // Almacena un nuevo cliente en la base de datos
    public function store(Request $request)
    {
        // Validar los datos del request
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'empresa' => 'nullable|string|max:100',
            'mail' => 'required|email|max:100',
            'telefono' => 'nullable|string|max:20',
        ]);

        // Crear el cliente con el ID del usuario autenticado
        Cliente::create([
            'nombres' => $request->input('nombres'),
            'apellidos' => $request->input('apellidos'),
            'empresa' => $request->input('empresa'),
            'mail' => $request->input('mail'),
            'telefono' => $request->input('telefono'),
            'created_by' => Auth::id(),  // Obtener el ID del usuario autenticado
        ]);

        // Redirigir con un mensaje de éxito
        return redirect()->route('clientes.index')->with('success', 'Cliente creado con éxito.');
    }
    
    // Muestra el formulario para editar un cliente existente
    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    // Actualiza un cliente en la base de datos
    public function update(Request $request, $id)
    {
        // Validar los datos del request
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'empresa' => 'nullable|string|max:100',
            'mail' => 'required|email|max:100',
            'telefono' => 'nullable|string|max:20',
        ]);

        // Encontrar el cliente por su ID
        $cliente = Cliente::findOrFail($id);

        // Actualizar los datos del cliente incluyendo el campo 'updated_by'
        $cliente->update([
            'nombres' => $request->input('nombres'),
            'apellidos' => $request->input('apellidos'),
            'empresa' => $request->input('empresa'),
            'mail' => $request->input('mail'),
            'telefono' => $request->input('telefono'),
            'updated_by' => Auth::id(),  // Establecer el ID del usuario autenticado
        ]);

        // Redirigir con un mensaje de éxito
        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado con éxito.');
    }

    // Elimina un cliente de la base de datos
    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado con éxito.');
    }
}
