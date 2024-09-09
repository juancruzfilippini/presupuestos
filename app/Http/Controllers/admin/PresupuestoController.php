<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Models\Presupuesto; // Importamos el modelo correcto
use App\Models\Prestacion; // Importamos el modelo correcto
use App\Models\ObraSocial; // Importamos el modelo correcto
use App\Models\Firmas; // Importamos el modelo correcto
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\Archivo;
use App\Models\Prestaciones;
use App\Models\Anestesia_p;
use Illuminate\Support\Facades\DB;


class PresupuestoController extends Controller
{
    // Muestra una lista de todos los presupuestos
    public function index(Request $request)
    {
        // Filtra los presupuestos donde 'borrado_logico' es null o 0
        $presupuestos = Presupuesto::where('borrado_logico', 0)
            ->orWhereNull('borrado_logico')
            ->get();

        return view('presupuestos.index', compact('presupuestos'));
    }

    public function firmar($id)
    {
        $presupuesto = Presupuesto::findOrFail($id);
        $archivos = Archivo::where('presupuesto_id', $id)->get();
        $prestaciones = Prestaciones::where('presupuesto_id', $id)->get();
        $firmas = Firmas::where('presupuesto_id', $id)->first();
        $paciente = Paciente::findById($presupuesto->paciente_salutte_id);
        $today = date('Y-m-d');
        //dd($paciente);

        return view('presupuestos.firmar', compact('presupuesto', 'archivos', 'prestaciones', 'firmas', 'id', 'paciente', 'today'));
    }

    public function sign($id, $rol_id)
    {

        $firmas = Firmas::where('presupuesto_id', $id)->first();

        //dd($id, $rol_id, $firmas->auditoria);

        switch ($rol_id) {
            case 1:
                $firmas->auditoria = 1;
                break;
            case 2:
                $firmas->comercializacion = 1;
                break;
            case 6:
                $firmas->direccion = 1;
                break;
            default:
                return redirect()->back()->with('error', 'Rol no autorizado para firmar el presupuesto.');
        }

        $firmas->save();

        return redirect()->route('presupuestos.firmar', $id)->with('success', 'El presupuesto ha sido firmado por ' . Auth::user()->name);
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
            'fecha' => 'nullable|string',
            'paciente_salutte_id' => 'nullable|integer',
            'paciente' => 'nullable|string',
            'medico_tratante' => 'nullable|string',
            'medico_solicitante' => 'nullable|string',
            'telefono' => 'nullable|string',
            'email' => 'nullable|string',
            'nro_afiliado' => 'nullable|string',
        ]);



        //dd($validatedData);
        // Creación del nuevo presupuesto


        $os = $validatedData['obra_social'];
        $esp = $validatedData['especialidad'];

        if (!is_null($request->presupuesto_id)) {
            // Buscar el presupuesto existente
            $presupuesto = Presupuesto::find($request->presupuesto_id);

            // Si el presupuesto no existe, puedes manejarlo como creas conveniente
            if (!$presupuesto) {
                return redirect()->back()->withErrors(['error' => 'Presupuesto no encontrado.']);
            }
        } else {
            // Si no hay presupuesto_id, crea uno nuevo
            $presupuesto = new Presupuesto();
        }




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
        $presupuesto->fecha = $validatedData['fecha'];
        $presupuesto->paciente_salutte_id = $validatedData['paciente_salutte_id'];
        $presupuesto->paciente = $validatedData['paciente'];
        $presupuesto->medico_tratante = $validatedData['medico_tratante'];
        $presupuesto->medico_solicitante = $validatedData['medico_solicitante'];
        $presupuesto->nro_afiliado = $validatedData['nro_afiliado'];
        $presupuesto->telefono = $validatedData['telefono'];
        $presupuesto->email = $validatedData['email'];
        $presupuesto->estado = 0;


        // Guardar en la base de datos
        $presupuesto->save();

        $presupuesto_id = $presupuesto->id;

        $anestesias = $request->anestesia_id;
        $precios = $request->precio_anestesia;
        $complejidades = $request->complejidad;

        // Asegúrate de que los arrays tengan la misma cantidad de elementos
        if (count($anestesias) == count($precios) && count($precios) == count($complejidades)) {
            foreach ($anestesias as $index => $anestesiaId) {
                DB::table('anestesia_p')->insert([
                    'presupuesto_id' => $presupuesto_id,
                    'anestesia_id' => $anestesiaId,
                    'precio' => $precios[$index],
                    'complejidad' => $complejidades[$index],
                ]);
            }
        } else {
            return back()->withErrors(['message' => 'Los arrays de anestesia, precio y complejidad deben tener la misma longitud.']);
        }
    
        $firma = new Firmas();
        $firma->presupuesto_id = $presupuesto_id;
        $firma->save();



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
        $anestesias = Anestesia_p::where('presupuesto_id', $id)->get();
        //dd($anestesias);

        return view('presupuestos.edit', compact('presupuesto', 'archivos', 'prestaciones', 'anestesias', 'id'));
    }


    // Actualiza un presupuesto en la base de datos
    public function update(Request $request, $id)
    {

        //dd($request->all());

        // Validar los datos del request
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
            'fecha' => 'nullable|string',
            'paciente_salutte_id' => 'nullable|integer',
            'paciente' => 'nullable|string',
            'medico_tratante' => 'nullable|string',
            'medico_solicitante' => 'nullable|string',
            'nro_afiliado' => 'nullable|string',
            'email' => 'nullable|string',
            'telefono' => 'nullable|string',
        ]);


        // Encontrar el presupuesto por su ID
        $presupuesto = presupuesto::findOrFail($id);





        $presupuesto->especialidad = $validatedData['input_especialidad'];
        // Verificar si el toggle de 'condicion' está activado antes de asignar
        if ($request->has('toggleCondicion')) {
            $presupuesto->condicion = $validatedData['condicion'];
        } else {
            $presupuesto->condicion = "";
        }

        // Verificar si el toggle de 'incluye' está activado antes de asignar
        if ($request->has('toggleIncluye')) {
            $presupuesto->incluye = $validatedData['incluye'];
        } else {
            $presupuesto->incluye = "";
        }

        // Verificar si el toggle de 'excluye' está activado antes de asignar
        if ($request->has('toggleExcluye')) {
            $presupuesto->excluye = $validatedData['excluye'];
        } else {
            $presupuesto->excluye = "";
        }

        // Verificar si el toggle de 'adicionales' está activado antes de asignar
        if ($request->has('toggleAdicionales')) {
            $presupuesto->adicionales = $validatedData['adicionales'];
        } else {
            $presupuesto->adicionales = "";
        }
        if ($validatedData['paciente_salutte_id'] != '') {
            $presupuesto->paciente_salutte_id = $validatedData['paciente_salutte_id'];
        }

        $presupuesto->detalle = $validatedData['detalle'];
        $presupuesto->convenio = $validatedData['convenio'];
        $presupuesto->total_presupuesto = $validatedData['total_presupuesto'];
        $presupuesto->fecha = $validatedData['fecha'];
        $presupuesto->paciente = $validatedData['paciente'];
        $presupuesto->medico_tratante = $validatedData['medico_tratante'];
        $presupuesto->medico_solicitante = $validatedData['medico_solicitante'];
        $presupuesto->telefono = $validatedData['telefono'];
        $presupuesto->nro_afiliado = $validatedData['nro_afiliado'];
        $presupuesto->email = $validatedData['email'];
        $presupuesto->estado = 1;

        //'updated_by' => Auth::id(),  // Establecer el ID del usuario autenticado

        // Guardar en la base de datos
        $presupuesto->save();



        $rowCount = 1;  // Asume que las filas empiezan en 1

        while ($request->has("codigo_{$rowCount}")) {
            $prestacionId = $request->input("prestacion_id_{$rowCount}");
            $prestacionInput = $request->input("prestacion_{$rowCount}");

            // Buscar la prestación por ID
            $prestacion = Prestaciones::find($prestacionId);

            if ($prestacion) {
                // Actualizar los campos de la prestación
                $prestacion->codigo_prestacion = $request->input("codigo_{$rowCount}");

                if (is_numeric($prestacionInput)) {
                    $prestacion->prestacion_salutte_id = $prestacionInput;
                    $prestacion->nombre_prestacion = null;  // O dejarlo vacío
                } else {
                    $prestacion->prestacion_salutte_id = null;
                    $prestacion->nombre_prestacion = $prestacionInput;
                }

                $prestacion->modulo_total = $request->input("modulo_total_{$rowCount}");
                // Actualizar otros campos según sea necesario

                // Guardar los cambios en la base de datos
                $prestacion->save();
            } else {
                // Manejar el caso donde la prestación no existe
                // Puedes lanzar una excepción, ignorarlo, o crear una nueva prestación
                // throw new Exception("Prestación con ID {$prestacionId} no encontrada.");
            }

            $rowCount++;
        }
        $rowCountt = 1;  // Asume que las filas empiezan en 1

        while ($request->has("anestesia{$rowCountt}")) {
            $anestesia = $request->input("anestesia{$rowCountt}");

            // Buscar la prestación por ID
            $anestesia = Anestesia_p::find($anestesia);

            if ($anestesia) {
                // Actualizar los campos de la prestación
                $anestesia->complejidad = $request->input("complejidad{$rowCountt}");
                $anestesia->precio = $request->input("anestesia_precio{$rowCountt}");
                $anestesia->anestesia_id = $request->input("anestesia_id{$rowCountt}");
                $anestesia->save();
            }
            $rowCountt++;
        }

        // Redirigir con un mensaje de éxito
        return redirect()->route('presupuestos.index')->with('success', 'Presupuesto actualizado con éxito.');
    }

    // Elimina un presupuesto de la base de datos
    public function destroy($id)
    {
        // Encuentra el presupuesto por ID o falla si no existe
        $presupuesto = Presupuesto::findOrFail($id);

        // Obtén el ID del presupuesto para actualizar las prestaciones asociadas
        $presupuestoId = $presupuesto->id;

        // Actualiza la columna 'borrado_logico' a 1 en las prestaciones asociadas
        Prestaciones::where('presupuesto_id', $presupuestoId)
            ->update(['borrado_logico' => 1]);

        Archivo::where('presupuesto_id', $presupuestoId)
            ->update(['borrado_logico' => 1]);

        // Actualiza la columna 'borrado_logico' a 1 en el presupuesto
        $presupuesto->update(['borrado_logico' => 1]);

        // Redirige con mensaje de éxito
        return redirect()->route('presupuestos.index')->with('success', 'Presupuesto y prestaciones marcados como eliminados con éxito.');
    }



    public function searchPatient(Request $request)
    {
        $searchTerm = $request->input('search');

        // Llama al método search del modelo Paciente
        $patients = Paciente::search($searchTerm);

        return response()->json($patients);
    }



}
