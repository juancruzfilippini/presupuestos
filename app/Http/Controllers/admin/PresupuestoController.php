<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Models\Presupuesto; // Importamos el modelo correcto
use App\Models\Prestacion; // Importamos el modelo correcto
use App\Models\ObraSocial; // Importamos el modelo correcto
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\Archivo;
use App\Models\Prestaciones;
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
            'convenio' => 'nullable|string',
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
        ]);



        //dd($validatedData);
        // Creación del nuevo presupuesto


        $os = $validatedData['obra_social'];
        $esp = $validatedData['especialidad'];

        if (!is_null($request->presupuesto_id)) {
            //dd($request->presupuesto_id);
            
        }

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
        $presupuesto->convenio = $validatedData['convenio'];
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

        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                // Genera un nombre único para el archivo y lo guarda en el sistema de archivos
                $filePath = $file->storeAs('archivos', $filename, 'public');

                // Crea una nueva instancia del modelo Archivo y guarda los detalles en la base de datos
                Archivo::create([
                    'presupuesto_id' => $presupuesto->id,
                    'file_path' => $filePath,
                ]);
            }
        }

        // Insertar las prestaciones en la base de datos
        DB::table('prestaciones')->insert($prestacionesData);

        // Redirigir a la vista de presupuestos o a otra vista que desees
        return redirect()->route('presupuestos.index')->with('success', 'Presupuesto creado exitosamente');

    }



    public function edit($id)
    {
        
        $presupuesto = Presupuesto::findOrFail($id);
        $archivos = Archivo::where('presupuesto_id', $id)->get();
        $prestaciones = Prestaciones::where('presupuesto_id', $id)->get();
        //dd($prestaciones);

        return view('presupuestos.edit', compact('presupuesto', 'archivos', 'prestaciones', 'id'));
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
