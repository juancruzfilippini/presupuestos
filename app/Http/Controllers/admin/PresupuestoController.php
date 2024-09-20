<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Models\Presupuesto; // Importamos el modelo correcto
use App\Models\Prestacion; // Importamos el modelo correcto
use App\Models\ObraSocial; // Importamos el modelo correcto
use App\Models\Firmas; // Importamos el modelo correcto
use App\Models\Proceso; // Importamos el modelo correcto
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ConvenioController;
use App\Models\Paciente;
use App\Models\Archivo;
use App\Models\Convenio;
use App\Models\Prestaciones;
use App\Models\Anestesia_p;
use Illuminate\Support\Facades\DB;
use App\Models\cambios_presupuestos;
use App\Models\cambios_prestaciones;
use App\Models\cambios_anestesias;


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
        $proceso = proceso::where('presupuesto_id', $id)->first();
        $anestesias = Anestesia_p::where('presupuesto_id', $id)->get();
        $paciente = Paciente::findById($presupuesto->paciente_salutte_id);
        $cambiosPresupuestos = cambios_presupuestos::where('presupuesto_id', $id)->orderBy('fecha_cambio', 'desc')->get();
        $cambiosPrestaciones = cambios_prestaciones::where('presupuesto_id', $id)->orderBy('fecha_cambio', 'desc')->get();
        $cambiosAnestesias = cambios_anestesias::where('presupuesto_id', $id)->orderBy('fecha_cambio', 'desc')->get();
        $today = date('Y-m-d');
        //dd($cambiosPrestaciones);



        return view('presupuestos.firmar', compact
        ('presupuesto', 'archivos', 'prestaciones', 'firmas', 'id', 'paciente', 'today', 'anestesias', 'proceso', 'cambiosPresupuestos', 'cambiosPrestaciones', 'cambiosAnestesias'));
    }

    public function sign($id, $rol_id)
    {

        $firmas = Firmas::where('presupuesto_id', $id)->first();

        //dd($id, $rol_id, $firmas->auditoria);

        switch ($rol_id) {
            case 1:
                $firmas->auditoria = 1;
                $firmas->fecha_auditoria = now();
                $firmas->firmado_por_auditoria = auth()->user()->id;
                break;
            case 2:
                $firmas->comercializacion = 1;
                $firmas->fecha_comercializacion = now();
                $firmas->firmado_por_comercializacion = auth()->user()->id;
                break;
            case 6:
                $firmas->direccion = 1;
                $firmas->fecha_direccion = now();
                $firmas->firmado_por_direccion = auth()->user()->id;
                break;
            default:
                return redirect()->back()->with('error', 'Rol no autorizado para firmar el presupuesto.');
        }


        $firmas->save();

        $presupuesto = Presupuesto::find($id);

        if ($presupuesto) {
            if ($firmas->direccion == 1 && $firmas->comercializacion == 1 && $firmas->auditoria == 1) {
                $presupuesto->estado = 4; // Cambia el estado a 4 (completado)
            } elseif ($firmas->direccion == 1 || $firmas->comercializacion == 1 || $firmas->auditoria == 1) {
                $presupuesto->estado = 3; // Cambia el estado a 3 (firmando)
            } else {
                // Maneja el caso en el que ninguna firma está en 1 si es necesario
            }
            $presupuesto->save(); // Guarda los cambios en la base de datos
        }
        return redirect()->route('presupuestos.firmar', $id)->with('success', 'El presupuesto ha sido firmado por ' . Auth::user()->name);
    }





    // Muestra el formulario para crear un nuevo presupuesto
    public function create()
    {
        $today = date('Y-m-d');
        $prestaciones = Prestacion::all();
        $obrasSociales = ObraSocial::getObrasSociales();
        $convenios = Convenio::getConvenios();
        //dd($convenios);
        return view('presupuestos.create', compact( 'today', 'convenios'));
    }


    public function store(Request $request)
    {
        //$validatedData = $request->except('file'); // Excluye 'file' de la validación
        //dd($validatedData);

        //dd($request->all());



        $validatedData = $request->validate([
            'detalle' => 'nullable|string',
            'input_obrasocial' => 'nullable',
            'input_especialidad' => 'nullable|string',
            'condicion' => 'nullable|string',
            'incluye' => 'nullable|string',
            'excluye' => 'nullable|string',
            'adicionales' => 'nullable|string',
            'total_presupuesto' => 'nullable|string',
            'fecha' => 'nullable|string',
            'paciente_salutte_id' => 'nullable|integer',
            'paciente' => 'nullable|string',
            'edad' => 'nullable',
            'medico_tratante' => 'nullable|string',
            'medico_solicitante' => 'nullable|string',
            'telefono' => 'nullable|string',
            'email' => 'nullable|string',
            'nro_afiliado' => 'nullable|string',
        ]);



        //dd($validatedData);
        // Creación del nuevo presupuesto


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
        
        $presupuesto->obra_social = $validatedData['input_obrasocial'];
        $presupuesto->detalle = $validatedData['detalle'];
        $presupuesto->total_presupuesto = $validatedData['total_presupuesto'];
        $presupuesto->fecha = $validatedData['fecha'];
        $presupuesto->paciente_salutte_id = $validatedData['paciente_salutte_id'];
        $presupuesto->paciente = $validatedData['paciente'];
        $presupuesto->edad = $validatedData['edad'];
        $presupuesto->medico_tratante = $validatedData['medico_tratante'];
        $presupuesto->medico_solicitante = $validatedData['medico_solicitante'];
        $presupuesto->nro_afiliado = $validatedData['nro_afiliado'];
        $presupuesto->telefono = $validatedData['telefono'];
        $presupuesto->email = $validatedData['email'];
        $presupuesto->estado = 7;


        // Guardar en la base de datos
        $presupuesto->save();
        $presupuesto_id = $presupuesto->id;

        $anestesias = $request->anestesia_id;
        $precios = $request->precio_anestesia;
        $complejidades = $request->complejidad;

        // Asegurarse de que las variables sean arrays
        $anestesias = is_array($anestesias) ? $anestesias : [];
        $precios = is_array($precios) ? $precios : [];
        $complejidades = is_array($complejidades) ? $complejidades : [];

        if (count($anestesias) == count($precios) && count($precios) == count($complejidades) && count($precios) > 0) {
            foreach ($anestesias as $index => $anestesiaId) {
                DB::table('anestesia_p')->insert([
                    'presupuesto_id' => $presupuesto_id,
                    'anestesia_id' => $anestesiaId,
                    'precio' => $precios[$index],
                    'complejidad' => $complejidades[$index],
                ]);
                if ($anestesiaId == 0) {
                    $presupuesto->estado = 5;
                    $presupuesto->save();
                }
            }
        }

        $proceso = new Proceso();
        $proceso->presupuesto_id = $presupuesto->id;
        $proceso->save();

        $firma = new Firmas();
        $firma->presupuesto_id = $presupuesto_id;
        $firma->save();



        $prestacionesData = [];
        $rowCount = 1;  // Asume que las filas empiezan en 1
        //dd($request->all());
        while ($request->has("codigo_{$rowCount}")) {
            $prestacionInput = $request->input("prestacion_{$rowCount}");

            if (is_numeric($prestacionInput)) {
                $prestacionesData[] = [
                    'presupuesto_id' => $presupuesto->id,
                    'codigo_prestacion' => $request->input("codigo_{$rowCount}"),
                    'prestacion_salutte_id' => $prestacionInput,
                    'nombre_prestacion' => Prestacion::getPrestacionById($prestacionInput), // o dejarlo vacío
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
            'edad' => 'nullable',
            'medico_tratante' => 'nullable|string',
            'medico_solicitante' => 'nullable|string',
            'nro_afiliado' => 'nullable|string',
            'email' => 'nullable|string',
            'telefono' => 'nullable|string',
        ]);

        $os = $validatedData['obra_social'];

        // Encontrar el presupuesto por su ID
        $presupuesto = presupuesto::findOrFail($id);

        // Guardar una copia del presupuesto original para registrar los cambios
        $presupuestoOriginal = $presupuesto->getOriginal();

        // Actualizar el presupuesto

        if (is_numeric($os)) {
            $presupuesto->obra_social = $validatedData['obra_social'];  // Guarda el ID de la obra social
        } else {
            $presupuesto->obra_social = $validatedData['input_obrasocial'];  // Guarda el Nombre de la obra social
        }
        $presupuesto->especialidad = $validatedData['input_especialidad'];
        if ($request->has('toggleCondicion')) {
            $presupuesto->condicion = $validatedData['condicion'];
        } else {
            $presupuesto->condicion = "";
        }
        if ($request->has('toggleIncluye')) {
            $presupuesto->incluye = $validatedData['incluye'];
        } else {
            $presupuesto->incluye = "";
        }
        if ($request->has('toggleExcluye')) {
            $presupuesto->excluye = $validatedData['excluye'];
        } else {
            $presupuesto->excluye = "";
        }
        if ($request->has('toggleAdicionales')) {
            $presupuesto->adicionales = $validatedData['adicionales'];
        } else {
            $presupuesto->adicionales = "";
        }
        if ($validatedData['paciente_salutte_id'] != '') {
            $presupuesto->paciente_salutte_id = $validatedData['paciente_salutte_id'];
        }
        if ($validatedData['edad'] != '') {
            $presupuesto->edad = $validatedData['edad'];
        }
        $presupuesto->detalle = $validatedData['detalle'];
        $presupuesto->total_presupuesto = $validatedData['total_presupuesto'];
        $presupuesto->fecha = $validatedData['fecha'];
        $presupuesto->paciente = $validatedData['paciente'];
        $presupuesto->medico_tratante = $validatedData['medico_tratante'];
        $presupuesto->medico_solicitante = $validatedData['medico_solicitante'];
        $presupuesto->telefono = $validatedData['telefono'];
        $presupuesto->nro_afiliado = $validatedData['nro_afiliado'];
        $presupuesto->email = $validatedData['email'];
        $presupuesto->estado = 5;

        // Guardar el presupuesto actualizado

        // Comparar cambios en el presupuesto
        $dirtyFieldsPresupuesto = $presupuesto->getDirty();
        //dd($dirtyFieldsPresupuesto);

        // Registrar los cambios en cambios_presupuestos usando el modelo
        foreach ($dirtyFieldsPresupuesto as $campo => $nuevoValor) {
            cambios_presupuestos::create([
                'presupuesto_id' => $presupuesto->id,
                'campo' => $campo,
                'valor_anterior' => $presupuestoOriginal[$campo],
                'valor_nuevo' => $nuevoValor,
                'fecha_cambio' => now(),
                'usuario_id' => auth()->user()->id
            ]);
        }

        $presupuesto->save();

        // Manejar prestaciones
        $rowCount = 1;
        while ($request->has("codigo_{$rowCount}")) {
            $prestacionId = $request->input("prestacion_id_{$rowCount}");
            $prestacion = Prestaciones::find($prestacionId);

            if ($prestacion) {
                $prestacionOriginal = $prestacion->getOriginal(); // Guardar original
                $prestacion->codigo_prestacion = $request->input("codigo_{$rowCount}");
                $prestacionInput = $request->input("prestacion_{$rowCount}");
                $prestacionSalutteId = $request->input("prestacion_salutte_id_{$rowCount}");
                $prestacion->prestacion_salutte_id = $prestacionSalutteId;
                $prestacion->nombre_prestacion = $prestacionInput;

                $prestacion->modulo_total = $request->input("modulo_total_{$rowCount}");

                // Comparar cambios en las prestaciones
                $dirtyFieldsPrestacion = $prestacion->getDirty();
                //dd($dirtyFieldsPrestacion);
                foreach ($dirtyFieldsPrestacion as $campo => $nuevoValor) {
                    cambios_prestaciones::create([
                        'presupuesto_id' => $presupuesto->id,
                        'campo' => $campo . " {$rowCount}",
                        'valor_anterior' => $prestacionOriginal[$campo],
                        'valor_nuevo' => $nuevoValor,
                        'fecha_cambio' => now(),
                        'usuario_id' => auth()->user()->id
                    ]);
                }
                $prestacion->save();

            }
            $rowCount++;
        }



        // Manejar anestesias
        $rowCountt = 1;
        while ($request->has("anestesia{$rowCountt}")) {
            $anestesiaId = $request->input("anestesia{$rowCountt}");
            $anestesia = Anestesia_p::find($anestesiaId);

            if ($anestesia) {
                $anestesiaOriginal = $anestesia->getOriginal(); // Guardar original

                $anestesia->complejidad = $request->input("complejidad{$rowCountt}");
                $anestesia->precio = $request->input("precio_anestesia{$rowCountt}");
                $anestesia->anestesia_id = $request->input("anestesia_id{$rowCountt}");

                if ($anestesiaId == 0) {
                    $presupuesto->estado = 5;
                }

                // Comparar cambios en las anestesias
                $dirtyFieldsAnestesia = $anestesia->getDirty();
                //dd($dirtyFieldsAnestesia);
                foreach ($dirtyFieldsAnestesia as $campo => $nuevoValor) {
                    cambios_anestesias::create([
                        'presupuesto_id' => $presupuesto->id,
                        'campo' => $campo,
                        'valor_anterior' => $anestesiaOriginal[$campo],
                        'valor_nuevo' => $nuevoValor,
                        'fecha_cambio' => now(),
                        'usuario_id' => auth()->user()->id
                    ]);
                }
                $anestesia->save();

            }
            $rowCountt++;
        }

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

    public function anestesia($id)
    {
        $presupuesto = Presupuesto::findOrFail($id);
        $archivos = Archivo::where('presupuesto_id', $id)->get();
        $prestaciones = Prestaciones::where('presupuesto_id', $id)->get();
        $anestesias = Anestesia_p::where('presupuesto_id', $id)->get();
        //dd($anestesias);
        return view('presupuestos.anestesia', compact('presupuesto', 'archivos', 'prestaciones', 'anestesias', 'id'));
    }

    public function updateAnestesia(Request $request, $id)
    {

        $presupuesto = presupuesto::findOrFail($id);
        $presupuesto->estado = 6;

        $proceso = Proceso::where('presupuesto_id', $id)->firstOrFail();
        //dd($proceso);
        $proceso->anestesia = 1;
        $proceso->fecha_anestesia = now();
        $proceso->save();

        //dd($request->all(), $id);
        $rowCountt = 1;
        while ($request->has("anestesia{$rowCountt}")) {
            $anestesiaId = $request->input("anestesia{$rowCountt}");
            $anestesia = Anestesia_p::find($anestesiaId);


            if ($anestesia) {
                $anestesiaOriginal = $anestesia->getOriginal(); // Guardar original

                $anestesia->complejidad = $request->input("complejidad{$rowCountt}");
                $anestesia->precio = $request->input("precio_anestesia{$rowCountt}");
                $anestesia->anestesia_id = $request->input("anestesia_id{$rowCountt}");

                // Comparar cambios en las anestesias
                $dirtyFieldsAnestesia = $anestesia->getDirty();
                //dd($dirtyFieldsAnestesia);
                foreach ($dirtyFieldsAnestesia as $campo => $nuevoValor) {
                    cambios_anestesias::create([
                        'presupuesto_id' => $id,
                        'campo' => $campo,
                        'valor_anterior' => $anestesiaOriginal[$campo],
                        'valor_nuevo' => $nuevoValor,
                        'fecha_cambio' => now(),
                        'usuario_id' => auth()->user()->id
                    ]);
                }
                $anestesia->save();
                //dd($anestesia);
            }
            $rowCountt++;
            if ($anestesia->anestesia_id == 0) {
                $presupuesto->estado = 5;
            }
        }

        $presupuesto->save();

        return redirect()->route('presupuestos.index')->with('success', 'Presupuesto actualizado con éxito.');

    }

    public function farmacia($id)
    {
        $presupuesto = Presupuesto::findOrFail($id);
        $archivos = Archivo::where('presupuesto_id', $id)->get();
        $prestaciones = Prestaciones::where('presupuesto_id', $id)->get();
        $anestesias = Anestesia_p::where('presupuesto_id', $id)->get();
        //dd($anestesias);  
        return view('presupuestos.farmacia', compact('presupuesto', 'archivos', 'prestaciones', 'anestesias', 'id'));
    }
    public function updateFarmacia(Request $request, $id)
    {
        // Encontrar el presupuesto
        $presupuesto = presupuesto::findOrFail($id);
        $presupuesto->total_presupuesto = $request->total_presupuesto;
        $presupuesto->save();

        // Actualizar el proceso
        $proceso = Proceso::where('presupuesto_id', $id)->firstOrFail();
        $proceso->farmacia = 1;
        $proceso->fecha_farmacia = now();
        $proceso->save();

        $rowCount = 1;

        while ($request->has("codigo_{$rowCount}")) {
            $prestacionId = $request->input("prestacion_id_{$rowCount}");

            // Si el prestacionId no existe, es una nueva prestación que se agregará
            if (!$prestacionId) {
                // Crear una nueva prestación
                $newPrestacion = new Prestaciones();
                $newPrestacion->presupuesto_id = $presupuesto->id;
                $newPrestacion->codigo_prestacion = $request->input("codigo_{$rowCount}");
                $prestacionInput = $request->input("prestacion_{$rowCount}");
                if (is_numeric($prestacionInput)) {
                    $newPrestacion->prestacion_salutte_id = $prestacionInput;
                    $newPrestacion->nombre_prestacion = null;
                } else {
                    $newPrestacion->prestacion_salutte_id = null;
                    $newPrestacion->nombre_prestacion = $prestacionInput;
                }
                $newPrestacion->modulo_total = $request->input("modulo_total_{$rowCount}");
                $newPrestacion->save();
            } else {
                // Si el prestacionId existe, actualiza la prestación existente
                $prestacion = Prestacion::find($prestacionId);

                if ($prestacion) {
                    $prestacionOriginal = $prestacion->getOriginal(); // Guardar original
                    $prestacion->codigo_prestacion = $request->input("codigo_{$rowCount}");
                    $prestacionInput = $request->input("prestacion_{$rowCount}");
                    if (is_numeric($prestacionInput)) {
                        $prestacion->prestacion_salutte_id = $prestacionInput;
                        $prestacion->nombre_prestacion = null;
                    } else {
                        $prestacion->prestacion_salutte_id = null;
                        $prestacion->nombre_prestacion = $prestacionInput;
                    }
                    $prestacion->modulo_total = $request->input("modulo_total_{$rowCount}");

                    // Comparar cambios en las prestaciones
                    $dirtyFieldsPrestacion = $prestacion->getDirty();
                    foreach ($dirtyFieldsPrestacion as $campo => $nuevoValor) {
                        cambios_prestaciones::create([
                            'presupuesto_id' => $presupuesto->id,
                            'campo' => $campo . " {$rowCount}",
                            'valor_anterior' => $prestacionOriginal[$campo],
                            'valor_nuevo' => $nuevoValor,
                            'fecha_cambio' => now(),
                            'usuario_id' => auth()->user()->id
                        ]);
                    }
                    $prestacion->save();
                }
            }

            $rowCount++;
        }

        return redirect()->route('presupuestos.index')->with('success', 'Presupuesto actualizado con éxito.');
    }


}
