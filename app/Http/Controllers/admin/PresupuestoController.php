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
use App\Models\Profesional;
use App\Models\Archivo;
use App\Models\Convenio;
use App\Models\Convenio_actual;
use App\Models\Presupuestos_aprobados;
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
        // Inicializa la consulta de los presupuestos
        $query = Presupuesto::query(); // Usando Eloquent


        // Agregar filtros si están presentes en el request
        if ($request->filled('search_nro_presupuesto')) {
            $query->where('id', 'like', '%' . $request->input('search_nro_presupuesto') . '%');
        }

        if ($request->filled('search_paciente')) {
            $query->where('paciente', 'like', '%' . $request->input('search_paciente') . '%');
        }

        if ($request->filled('search_medico_tratante')) {
            $query->where('medico_tratante', 'like', '%' . $request->input('search_medico_tratante') . '%');
        }

        if ($request->filled('search_estado')) {
            $query->join('estado', 'presupuesto.estado', '=', 'estado.id')
                ->where('estado.nombre', 'like', '%' . $request->input('search_estado') . '%')
                ->select('presupuesto.*') // Selecciona solo las columnas de 'presupuesto'
                ->distinct(); // Evita duplicados si el join produce más de una coincidencia
        }

        if ($request->filled('search_detalle')) {
            $query->where('detalle', 'like', '%' . $request->input('search_detalle') . '%');
        }

        if ($request->filled('search_desde')) {
            $query->whereDate('fecha', '>=', $request->input('search_desde'));
        }

        if ($request->filled('search_hasta')) {
            $query->whereDate('fecha', '<=', $request->input('search_hasta'));
        }

        if ($request->filled('search_obra_social')) {
            $query->where('obra_social', 'like', '%' . $request->input('search_obra_social') . '%');
        }

        if ($request->filled('search_total_presupuesto')) {
            $query->where('total_presupuesto', 'like', '%' . $request->input('search_total_presupuesto') . '%');
        }

        // Paginar los resultados
        $presupuestos = $query->orderBy('id', 'desc')->paginate(20);


        // Mantener los filtros aplicados en la paginación
        $presupuestos->appends($request->all());
        $firmas = Firmas::all();
        //dd($firmas);


        return view('presupuestos.index', compact('presupuestos', 'firmas'));
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
        $presupuestoAprobado = Presupuestos_aprobados::where('presupuesto_id', $id)->first();
        $today = date('Y-m-d');
        //dd($anestesias);



        return view('presupuestos.firmar', compact('presupuesto', 'archivos', 'prestaciones', 'firmas', 'id', 'paciente', 'today', 'anestesias', 'proceso', 'cambiosPresupuestos', 'cambiosPrestaciones', 'cambiosAnestesias', 'presupuestoAprobado'));
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
        $profesionales = Profesional::orderBy('nombre', 'asc')->get();
        $obrasSociales = ObraSocial::getObrasSociales();
        $ultimoConvenio = Convenio_actual::orderBy('id', 'desc')->first();

        return view('presupuestos.create', compact('today', 'ultimoConvenio', 'profesionales'));
    }



    public function store(Request $request)
    {
        //dd($request->all());
        $validatedData = $request->validate([
            'detalle' => 'nullable|string',
            'input_obrasocial' => 'nullable',
            'input_especialidad' => 'nullable|string',
            'convenio' => 'nullable',
            'convenio_id' => 'nullable',
            'descripcion' => 'nullable|string',
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
        if ($request->has('toggleDescripcion')) {
            $presupuesto->descripcion = $validatedData['descripcion'];
        }

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

        $presupuesto->convenio = $validatedData['convenio_id'];
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

        if (!$request->has('toggleDescripcion')) {
            if ($request->has('anestesia')) {
                if (in_array("0", $request->input('anestesia_id', []))) {
                    $presupuesto->estado = 5;
                } else {
                    $presupuesto->estado = 8;
                }
            } else {
                $presupuesto->estado = 8;
            }
        } else {
            $presupuesto->estado = 6;
        }



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

        if ($request->has('anestesia')) {
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
        }

        $proceso = new Proceso();
        $proceso->presupuesto_id = $presupuesto->id;
        if (empty($anestesias)) {
            $proceso->anestesia = 1;
            $proceso->fecha_anestesia = now();
        }
        $proceso->save();

        $firma = new Firmas();
        $firma->presupuesto_id = $presupuesto_id;
        $firma->save();

        $prestacionesData = [];
        $prestacionKeys = preg_grep('/^codigo_\d+$/', array_keys($request->all())); // Encuentra todas las claves que siguen el patrón

        //dd($request->all());
        foreach ($prestacionKeys as $key) {
            // Extraer el número del índice, por ejemplo, para 'codigo_3', $index sería 3
            $index = explode('_', $key)[1];

            $prestacionInput = $request->input("prestacion_{$index}");

            if (is_numeric($prestacionInput)) {
                $prestacionesData[] = [
                    'presupuesto_id' => $presupuesto->id,
                    'codigo_prestacion' => $request->input("codigo_{$index}"),
                    'prestacion_salutte_id' => $prestacionInput,
                    'nombre_prestacion' => Prestacion::getPrestacionById($prestacionInput), // o dejarlo vacío
                    'modulo_total' => $request->input("modulo_total_{$index}"),
                    'cantidad' => $request->input("cantidad_{$index}"),
                    'creado_por' => auth()->user()->id,
                    'creado_fecha' => now(),
                    // Agrega otras columnas si es necesario
                ];
            } else {
                $prestacionesData[] = [
                    'presupuesto_id' => $presupuesto->id,
                    'codigo_prestacion' => $request->input("codigo_{$index}"),
                    'prestacion_salutte_id' => null,
                    'nombre_prestacion' => $prestacionInput,
                    'modulo_total' => $request->input("modulo_total_{$index}"),
                    'cantidad' => $request->input("cantidad_{$index}"),
                    'creado_por' => auth()->user()->id,
                    'creado_fecha' => now(),
                    // Agrega otras columnas si es necesario
                ];
            }
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
        $firmas = firmas::where('presupuesto_id', $id)->get();
        $firmaDireccion = $firmas[0]->direccion;
        $prestaciones = Prestaciones::where('presupuesto_id', $id)->get();
        $anestesias = Anestesia_p::where('presupuesto_id', $id)->get();
        $proceso = Proceso::where('presupuesto_id', $id)->get();
        $profesionales = Profesional::orderBy('nombre', 'asc')->get();
        $procesoFarmacia = $proceso[0]->farmacia;

        return view('presupuestos.edit', compact('presupuesto', 'archivos', 'prestaciones', 'anestesias', 'id', 'procesoFarmacia', 'firmaDireccion', 'profesionales'));
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
            'descripcion' => 'nullable|string',
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
        $procesoFarmacia = $request['procesoFarmacia'];
        $firmaDireccion = $request['firmaDireccion'];

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
        if ($request->has('toggleDescripcion')) {
            $presupuesto->descripcion = $validatedData['descripcion'];
        } else {
            $presupuesto->descripcion = "";
        }
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
        //dd($request->all());
        $prestacionNuevaSi= 0;
        $rowCount = 1;
        while ($request->has("codigo_{$rowCount}")) {
            $prestacionId = $request->input("prestacion_id_{$rowCount}");
            $prestacionSalutteId = $request->input("prestacion_salutte_id_{$rowCount}");
            $prestacionNombre = $request->input("prestacion_{$rowCount}"); // Nombre de la prestación

            // Si la prestación no existe (nuevo ID o sin ID), se crea una nueva
            if (!$prestacionId || !Prestaciones::find($prestacionId)) {
                $prestacion = new Prestaciones();
                $prestacion->codigo_prestacion = $request->input("codigo_{$rowCount}");
                if (is_numeric($prestacionNombre)) {
                    $prestacion->nombre_prestacion = Prestacion::getPrestacionById($prestacionNombre); // Asignar el nombre correctamente
                    $prestacion->prestacion_salutte_id = $prestacionNombre; // Guardar prestacion_salutte_id en el campo correcto
                } else {
                    $prestacion->nombre_prestacion = $prestacionNombre;
                }
                $prestacion->cantidad = (int) $request->input("cantidad_{$rowCount}");
                $prestacion->creado_por = auth()->user()->id;
                $prestacion->creado_fecha = now();

                // Validación de modulo_total (evitar NaN o valores vacíos)
                $moduloTotal = $request->input("modulo_total_{$rowCount}");
                $prestacion->modulo_total = is_numeric($moduloTotal) ? $moduloTotal : 0;

                $prestacion->presupuesto_id = $presupuesto->id; // Asigna el ID del presupuesto actual a la nueva prestación
                $prestacion->save();

                // Registrar la nueva creación en cambios_prestaciones
                cambios_prestaciones::create([
                    'presupuesto_id' => $presupuesto->id,
                    'campo' => "Nueva prestación {$rowCount}",
                    'valor_anterior' => null,
                    'valor_nuevo' => $prestacion->nombre_prestacion,
                    'fecha_cambio' => now(),
                    'usuario_id' => auth()->user()->id
                ]);
                $prestacionNuevaSi = 1;

            } else {
                // Actualizar la prestación existente
                $prestacion = Prestaciones::find($prestacionId);
                $prestacionOriginal = $prestacion->getOriginal(); // Guardar original
                $prestacion->codigo_prestacion = $request->input("codigo_{$rowCount}");
                $prestacion->nombre_prestacion = $prestacionNombre; // Asignar el nombre correctamente
                $prestacion->cantidad = (int) $request->input("cantidad_{$rowCount}");
                $prestacion->prestacion_salutte_id = $prestacionSalutteId; // Guardar prestacion_salutte_id en el campo correcto

                // Validación de modulo_total (evitar NaN o valores vacíos)
                $moduloTotal = $request->input("modulo_total_{$rowCount}");
                $prestacion->modulo_total = is_numeric($moduloTotal) ? $moduloTotal : $prestacion->modulo_total;

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
            $rowCount++;
        }





        // Manejar anestesias
        //dd($request->all());
        $anestesiaConIdCero = false; // Variable para controlar si hay alguna anestesia con ID 0
        $rowCountt = 1;
        while ($request->has("anestesia{$rowCountt}")) {
            $anestesiaId = $request->input("anestesia{$rowCountt}");
            $anestesiaTipo = $request->input("anestesia_id{$rowCountt}");
            $anestesia = Anestesia_p::find($anestesiaId);

            if ($anestesia) {
                $anestesiaOriginal = $anestesia->getOriginal(); // Guardar original

                // Actualizar los valores de la anestesia
                $anestesia->complejidad = $request->input("complejidad{$rowCountt}");
                $anestesia->precio = $request->input("precio_anestesia{$rowCountt}");
                $anestesia->anestesia_id = $request->input("anestesia_id{$rowCountt}");
                //dd($anestesiaTipo);
                if ($anestesiaTipo == 0) {
                    $anestesiaConIdCero = true; // Si es 0, actualizar la variable de control
                }

                // Comparar cambios en las anestesias
                $dirtyFieldsAnestesia = $anestesia->getDirty();

                // Guardar los cambios en la tabla `cambios_anestesias`
                foreach ($dirtyFieldsAnestesia as $campo => $nuevoValor) {
                    // Concatenar el número de anestesia al nombre del campo
                    $campoConNumero = $campo . ' ' . $rowCountt;
                    if (str_contains($campo, 'anestesia_id')) {
                        $campoConNumero = "tipo anestesia {$rowCountt}";
                    }

                    cambios_anestesias::create([
                        'presupuesto_id' => $presupuesto->id,
                        'campo' => $campoConNumero, // Usar el nombre del campo con el número de anestesia
                        'valor_anterior' => $anestesiaOriginal[$campo],
                        'valor_nuevo' => $nuevoValor,
                        'fecha_cambio' => now(),
                        'usuario_id' => auth()->user()->id
                    ]);
                }
                $anestesia->save();
            }

            // Incrementar el contador para la siguiente anestesia
            $rowCountt++;
        }
        $firmas = Firmas::where('presupuesto_id', $id)->first();


        if ($firmaDireccion == 1) {
            $presupuesto->estado = 3;
            $firmas->direccion = 0;
            $presupuesto->save();
            $firmas->save();
        }else if ($anestesiaConIdCero == true) {
            $presupuesto->estado = 5; // Si alguna anestesia tiene ID 0
            $presupuesto->save();
        } else if ($anestesiaConIdCero == false) {
                if ($prestacionNuevaSi == 1){
                    $presupuesto->estado = 8; // Si todas las anestesias tienen IDs diferentes de 0
                } else if($procesoFarmacia == 1){
                    $presupuesto->estado = 6;
                }else {$presupuesto->estado =8;}
            $presupuesto->save();
        }




        return redirect()->route('presupuestos.index')->with('success', 'Presupuesto actualizado con éxito.');
    }


    // Elimina un presupuesto de la base de datos
    public function destroy(Request $request, $id)
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

    // Actualiza la columna 'borrado_logico' y el estado en el presupuesto
    $presupuesto->borrado_logico = 1;
    $presupuesto->estado = 10;
    $presupuesto->especialidad = $request->input('razon'); // Guarda la razón
    $presupuesto->save();

    // Redirige con mensaje de éxito
    return redirect()->route('presupuestos.index')->with('success', 'Presupuesto y prestaciones marcados como eliminados con éxito.');
}


    public function deletePrestacion($id)
    {
        $prestacion = Prestaciones::findOrFail($id);

        // Opcional: registrar el cambio en cambios_prestaciones
        cambios_prestaciones::create([
            'presupuesto_id' => $prestacion->presupuesto_id,
            'campo' => 'eliminación',
            'valor_anterior' => $prestacion->nombre_prestacion,
            'valor_nuevo' => 'Eliminado',
            'fecha_cambio' => now(),
            'usuario_id' => auth()->user()->id
        ]);

        $prestacion->delete();

        return response()->json(['success' => true]);
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

        //dd($request->all(), $id);
        $rowCountt = 1;
        $faltanAnestesias = 0;
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
                    // Concatenar el número de anestesia al nombre del campo
                    $campoConNumero = $campo . ' ' . $rowCountt;
                    if (str_contains($campo, 'anestesia_id')) {
                        $campoConNumero = "tipo anestesia {$rowCountt}";
                    }

                    cambios_anestesias::create([
                        'presupuesto_id' => $presupuesto->id,
                        'campo' => $campoConNumero, // Usar el nombre del campo con el número de anestesia
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
                $faltanAnestesias = 1;
            }
        }
        if ($faltanAnestesias == 1) {
            $proceso = Proceso::where('presupuesto_id', $id)->firstOrFail();
            $proceso->anestesia = 0;
            $proceso->fecha_anestesia = now();
            $proceso->save();
        } else {
            $proceso = Proceso::where('presupuesto_id', $id)->firstOrFail();
            $proceso->anestesia = 1;
            $proceso->fecha_anestesia = now();
            $proceso->save();
        }

        if ($proceso->anestesia == 1 && $proceso->farmacia == 1) {
            $presupuesto->estado = 6;
        } else if ($proceso->anestesia == 1 && $proceso->farmacia != 1) {
            $presupuesto->estado = 8;
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
        //dd($request->all());
        // Encontrar el presupuesto
        $presupuesto = presupuesto::findOrFail($id);
        $presupuesto->total_presupuesto = $request->total_presupuesto;

        // Actualizar el proceso
        $proceso = Proceso::where('presupuesto_id', $id)->firstOrFail();
        $proceso->farmacia = 1;
        $proceso->fecha_farmacia = now();
        $proceso->save();


        $presupuesto->estado = 6;
        $presupuesto->save();

        $rowCount = 1;

        while ($request->has("codigo_{$rowCount}")) {
            $prestacionId = $request->input("prestacion_id_{$rowCount}");

            // Si el prestacionId no existe, es una nueva prestación que se agregará
            if (!$prestacionId) {
                // Crear una nueva prestación
                $newPrestacion = new Prestaciones();
                $newPrestacion->presupuesto_id = $presupuesto->id;
                $newPrestacion->codigo_prestacion = $request->input("codigo_{$rowCount}");
                $newPrestacion->creado_por = auth()->user()->id;
                $newPrestacion->creado_fecha = now();
                $newPrestacion->cantidad = 1;
                $prestacionInput = $request->input("prestacion_{$rowCount}");
                if (is_numeric($prestacionInput)) {
                    $newPrestacion->prestacion_salutte_id = $prestacionInput;
                    $newPrestacion->nombre_prestacion = Prestacion::getPrestacionById($newPrestacion->prestacion_salutte_id);
                } else {
                    $newPrestacion->prestacion_salutte_id = null;
                    $newPrestacion->nombre_prestacion = $prestacionInput;
                }
                $newPrestacion->modulo_total = $request->input("modulo_total_{$rowCount}");
                $newPrestacion->save();
            }
            $rowCount++;
        }

        return redirect()->route('presupuestos.index')->with('success', 'Presupuesto actualizado con éxito.');
    }

    public function guardarArchivo(Request $request, $id)
    {
        //dd($request->all());
        $request->validate([
            'archivo' => 'required|mimes:pdf,jpg,jpeg,png|max:2048', // Valida que sea un PDF o imagen y que no supere los 2MB
        ]);

        // Manejo del archivo subido
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $rutaArchivo = $archivo->storeAs('presupuestos_aprobados', $nombreArchivo, 'public');
            $valorAprobado = $request->valor_aprobado;

            // Guardar información en la tabla presupuestos_aprobados
            Presupuestos_aprobados::create([
                'presupuesto_id' => $id,
                'file_path' => $rutaArchivo,
                'valor_aprobado' => $valorAprobado,
            ]);

            $presupuesto = Presupuesto::findOrFail($id);
            $presupuesto->estado = 9;
            $presupuesto->save();

            return redirect()->back()->with('success', 'Archivo subido correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo subir el archivo.');
    }
}
