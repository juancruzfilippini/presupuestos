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


    public function store(Request $request)
    {
        //$validatedData = $request->except('file'); // Excluye 'file' de la validación
        //dd($validatedData);


        //dd($request->all());

        $validatedData = $request->validate([
            'detalle' => 'nullable|string',
            'obra_social' => 'nullable|integer',
            'input_obrasocial' => 'nullable|string',
            'especialidad' => 'nullable|string',
            'input_especialidad' => 'nullable|string',
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
            'medico_tratante' => 'nullable|string',
            'medico_solicitante' => 'nullable|string',
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


        $os = $validatedData['obra_social'];
        $esp = $validatedData['especialidad'];

        $presupuesto = new Presupuesto();
        if (is_numeric($os)) {
            $presupuesto->obra_social = $validatedData['obra_social'];  // Guarda el ID de la obra social
        } else {
            $presupuesto->obra_social = $validatedData['input_obrasocial'];  // Guarda el Nombre de la obra social
        }

        if ($esp) {
            $presupuesto->especialidad = $esp;
        } else {
            $presupuesto->especialidad = $validatedData['input_especialidad'];
        }

        // Verificar si el toggle de 'condicion' está activado antes de asignar
        if ($request->has('toggleCondicion')) {
            $presupuesto->condicion = $validatedData['condicion'];
        }

        // Verificar si el toggle de 'incluye' está activado antes de asignar
        if ($request->has('toggleIncluye')) {
            $presupuesto->incluye = $validatedData['incluye'];
        }

        // Verificar si el toggle de 'excluye' está activado antes de asignar
        if ($request->has('toggleExcluye')) {
            $presupuesto->excluye = $validatedData['excluye'];
        }

        // Verificar si el toggle de 'adicionales' está activado antes de asignar
        if ($request->has('toggleAdicionales')) {
            $presupuesto->adicionales = $validatedData['adicionales'];
        }

        $presupuesto->detalle = $validatedData['detalle'];
        $presupuesto->total_presupuesto = $validatedData['total_presupuesto'];
        $presupuesto->anestesia_id = $validatedData['anestesia_id'];
        $presupuesto->complejidad = $validatedData['complejidad'];
        $presupuesto->precio_anestesia = $validatedData['precio_anestesia'];
        $presupuesto->fecha = $validatedData['fecha'];
        $presupuesto->paciente_salutte_id = $validatedData['paciente_salutte_id'];
        $presupuesto->paciente = $validatedData['paciente'];
        $presupuesto->medico_tratante = $validatedData['medico_tratante'];
        $presupuesto->medico_solicitante = $validatedData['medico_solicitante'];
        $presupuesto->estado = 0;
        if ($request->hasFile('file')) {
            $presupuesto->file_path = $path; // Guardar la ruta del archivo en la base de datos
        }


        // Guardar en la base de datos
        $presupuesto->save();

        $prestacionesData = [];
        $rowCount = 1;  // Asume que las filas empiezan en 1

        while ($request->has("codigo_{$rowCount}")) {
            $prestacionInput = $request->input("prestacion_{$rowCount}");

            if (is_numeric($prestacionInput)) {
                $prestacionesData[] = [
                    'presupuesto_id' => $presupuesto->id,
                    'codigo_prestacion' => $request->input("codigo_{$rowCount}"),
                    'prestacion_salutte_id' => $prestacionInput,
                    'nombre_prestacion' => null, // o dejarlo vacío
                    'modulo_total' => $request->input("modulo_total_{$rowCount}"),
                    // Agrega otras columnas si es necesario, como oxígeno, etc.
                ];
            } else {
                $prestacionesData[] = [
                    'presupuesto_id' => $presupuesto->id,
                    'codigo_prestacion' => $request->input("codigo_{$rowCount}"),
                    'prestacion_salutte_id' => null,
                    'nombre_prestacion' => $prestacionInput,
                    'modulo_total' => $request->input("modulo_total_{$rowCount}"),
                    // Agrega otras columnas si es necesario, como oxígeno, etc.
                ];
            }

            $rowCount++;
        }
/*
        $ArchivosData = [];
        $archivosCount = 1;  // Asume que las filas empiezan en 1

        while ($request->has("codigo_{$rowCount}")) {
            $prestacionInput = $request->input("prestacion_{$rowCount}");

            if (is_numeric($prestacionInput)) {
                $prestacionesData[] = [
                    'presupuesto_id' => $presupuesto->id,
                    'codigo_prestacion' => $request->input("codigo_{$rowCount}"),
                    'prestacion_salutte_id' => $prestacionInput,
                    'nombre_prestacion' => null, // o dejarlo vacío
                    'modulo_total' => $request->input("modulo_total_{$rowCount}"),
                    // Agrega otras columnas si es necesario, como oxígeno, etc.
                ];
            }*/

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
