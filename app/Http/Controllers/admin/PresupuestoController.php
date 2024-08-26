<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Models\Presupuesto; // Importamos el modelo correcto
use App\Models\Prestacion; // Importamos el modelo correcto
use App\Models\ObraSocial; // Importamos el modelo correcto
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Paciente;
use Illuminate\Support\Facades\DB;


class PresupuestoController extends Controller
{
    // Muestra una lista de todos los presupuestos
    public function index(Request $request)
    {
        $presupuestos = Presupuesto::all();
        return view('presupuestos.index', compact('presupuestos'));
    }


    // Muestra el formulario para crear un nuevo presupuesto
    public function create()
    {
        $today = date('Y-m-d');
        $prestaciones = Prestacion::all();
        $obrasSociales = ObraSocial::getObrasSociales();
        return view('presupuestos.create', compact('obrasSociales', 'today'));
    }

    /*
            $validatedData = $request->validate([
                'obra_social' => 'integer',
                'especialidad' => 'nullable|string',
                'condicion' => 'nullable|string',
                'incluye' => 'nullable|string',
                'excluye' => 'nullable|string',
                'adicionales' => 'nullable|string',
                'total_presupuesto' => 'nullable|string',
                'anestesia_id' => 'nullable|integer',
                'complejidad' => 'nullable|string',
                'precio_anestesia' => 'nullable|string',
                'fecha' => 'nullable|string',
                'paciente_salutte_id' => 'nullable|integer',
                'paciente' => 'nullable|string',
                'medico' => 'nullable|string',
            ]);
            */
    public function store(Request $request)
    {
        //dd($request->all());
        //$validatedData = $request->except('file'); // Excluye 'file' de la validación
        //dd($validatedData);


        //dd($request->all());

        $validatedData = $request->validate([
            'obra_social' => 'nullable|integer',
            'especialidad' => 'nullable|string',
            'condicion' => 'nullable|string',
            'incluye' => 'nullable|string',
            'excluye' => 'nullable|string',
            'adicionales' => 'nullable|string',
            'total_presupuesto' => 'nullable|string',
            'anestesia_id' => 'nullable|integer',
            'complejidad' => 'nullable|string',
            'precio_anestesia' => 'nullable|string',
            'fecha' => 'nullable|string',
            'paciente_salutte_id' => 'nullable|integer',
            'paciente' => 'nullable|string',
            'medico' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Validación del archivo
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('presupuestos', $filename, 'public');
            $validatedData['file_path'] = $path; // Agregar la ruta del archivo a los datos validados
        }

        //dd($validatedData);
        // Creación del nuevo presupuesto


        $presupuesto = new Presupuesto();
        $presupuesto->obra_social = $validatedData['obra_social'];  // Guarda el ID de la obra social
        $presupuesto->especialidad = $validatedData['especialidad'];
        $presupuesto->condicion = $validatedData['condicion'];
        $presupuesto->incluye = $validatedData['incluye'];
        $presupuesto->excluye = $validatedData['excluye'];
        $presupuesto->adicionales = $validatedData['adicionales'];
        $presupuesto->total_presupuesto = $validatedData['total_presupuesto'];
        $presupuesto->anestesia_id = $validatedData['anestesia_id'];
        $presupuesto->complejidad = $validatedData['complejidad'];
        $presupuesto->precio_anestesia = $validatedData['precio_anestesia'];
        $presupuesto->fecha = $validatedData['fecha'];
        $presupuesto->paciente_salutte_id = $validatedData['paciente_salutte_id'];
        $presupuesto->paciente = $validatedData['paciente'];
        $presupuesto->medico = $validatedData['medico'];
        $presupuesto->estado = 0;
        if ($request->hasFile('file')) {
            $presupuesto->file_path = $path; // Guardar la ruta del archivo en la base de datos
        }


        // Guardar en la base de datos
        $presupuesto->save();

        $prestacionesData = [];
        $rowCount = 1;  // Asume que las filas empiezan en 1

        while ($request->has("codigo_{$rowCount}")) {
            $prestacionesData[] = [
                'prestacion_salutte_id' => $request->input("prestacion_{$rowCount}"),
                'presupuesto_id' => $presupuesto->id,
                'codigo_prestacion' => $request->input("codigo_{$rowCount}"),
                'modulo_total' => $request->input("modulo_total_{$rowCount}"),
                // Agrega otras columnas si es necesario, como oxígeno, etc.
                'oxigeno' => $request->input("oxigeno_{$rowCount}"),
            ];
            $rowCount++;
        }

        // Insertar las prestaciones en la base de datos
        DB::table('prestaciones')->insert($prestacionesData);

        // Redirigir a la vista de presupuestos o a otra vista que desees
        return redirect()->route('presupuestos.index')->with('success', 'Presupuesto creado exitosamente');

    }




    /*
    public function store(Request $request)
    {
        try {
            // Validación de datos
            $validatedData = $request->validate([
                'anestesia_id' => 'string',
                'fecha' => 'date',
                'obra_social' => 'string',
                'especialidad' => 'string',
                'complejidad' => 'string',
                'precio_anestesia' => 'string',
                'total_presupuesto' => 'string',
                'condicion' => 'string',
                'incluye' => 'string',
                'excluye' => 'string',
                'adicionales' => 'string',
                'paciente_salutte_id' => 'integer',
            ]);

            // Creación del nuevo presupuesto
            $presupuesto = new Presupuesto();
            $presupuesto->paciente_salutte_id = $validatedData['paciente_salutte_id'];
            $presupuesto->anestesia_id = $validatedData['anestesia_id'];
            $presupuesto->fecha = $validatedData['fecha'];
            $presupuesto->obra_social = $validatedData['obra_social'];
            $presupuesto->especialidad = $validatedData['especialidad'];
            $presupuesto->complejidad = $validatedData['complejidad'];
            $presupuesto->precio_anestesia = $validatedData['precio_anestesia'];
            $presupuesto->total_presupuesto = $validatedData['total_presupuesto'];
            $presupuesto->condicion = $validatedData['condicion'];
            $presupuesto->incluye = $validatedData['incluye'];
            $presupuesto->excluye = $validatedData['excluye'];
            $presupuesto->adicionales = $validatedData['adicionales'];

            // Guardar en la base de datos
            $presupuesto->save();

            // Redirigir a la vista de presupuestos o a otra vista que desees
            return redirect()->route('presupuestos.index')->with('success', 'Presupuesto creado exitosamente');

        } catch (\Exception $e) {
            // Log del error para inspección
            \Log::error($e);

            // Puedes manejar el error como desees, por ejemplo, redirigiendo con un mensaje de error
            return redirect()->route('presupuestos.index')->with('error', 'Hubo un problema al crear el presupuesto');
        }
    }
    */



    // Muestra el formulario para editar un presupuesto existente
    public function edit($id)
    {
        $presupuesto = presupuesto::findOrFail($id);
        return view('presupuestos.edit', compact('presupuesto'));
    }

    // Actualiza un presupuesto en la base de datos
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

        // Encontrar el presupuesto por su ID
        $presupuesto = presupuesto::findOrFail($id);

        // Actualizar los datos del presupuesto incluyendo el campo 'updated_by'
        $presupuesto->update([
            'nombres' => $request->input('nombres'),
            'apellidos' => $request->input('apellidos'),
            'empresa' => $request->input('empresa'),
            'mail' => $request->input('mail'),
            'telefono' => $request->input('telefono'),
            'updated_by' => Auth::id(),  // Establecer el ID del usuario autenticado
        ]);

        // Redirigir con un mensaje de éxito
        return redirect()->route('presupuestos.index')->with('success', 'presupuesto actualizado con éxito.');
    }

    // Elimina un presupuesto de la base de datos
    public function destroy($id)
    {
        $presupuesto = presupuesto::findOrFail($id);
        $presupuesto->delete();

        return redirect()->route('presupuestos.index')->with('success', 'presupuesto eliminado con éxito.');
    }

    public function searchPatient(Request $request)
    {
        $searchTerm = $request->input('search');

        // Llama al método search del modelo Paciente
        $patients = Paciente::search($searchTerm);

        return response()->json($patients);
    }



}
