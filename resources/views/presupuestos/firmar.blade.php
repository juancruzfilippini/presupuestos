@php
use App\Models\ObraSocial;
use App\Models\Convenio;
use App\Models\Prestacion;
use App\Models\Anestesia;
use App\Models\Estado;
use App\Models\Users;
use App\Helpers\NumberToWordsHelper;
use Carbon\Carbon;


@endphp
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<head>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<title>Sistema de Presupuestos</title>
<x-app-layout>
    <x-slot name="title">Ver Presupuesto</x-slot>




    <form method="GET" action="{{ route('presupuestos.index') }}"
        class="w-full mx-auto p-6 bg-white shadow-md rounded-lg" enctype="multipart/form-data">
        @csrf

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <input type="hidden" id="presupuesto_id" name="presupuesto_id" value="{{$id}}">

        <div class="d-flex mb-6">
            <h1 class="text-2xl font-bold">VER PRESUPUESTO {{$presupuesto->id}}</h1>

            @if($presupuesto->estado == 10)
                <div class="bg-red-500 text-white font-semibold p-2 rounded ml-4">
                    Este presupuesto fue anulado.
                </div>
            @endif
        </div>
        

        <label for="fecha" class="font-semibold">PEDIDO MÉDICO:</label>
        <p></p>

        @if ($archivos->count() > 0)
        <div class="mb-4">
            <label for="download_file" class="font-semibold">Archivo adjunto:</label>
            @foreach ($archivos as $archivo)
            <li>
                <a href="{{ asset('storage/' . $archivo->file_path) }}"
                    target="_blank" class="text-blue-500 hover:underline">
                    Ver {{ basename($archivo->file_path) }}
                </a>
            </li>
            @endforeach
        </div>
        @endif
        <div style="border-top: 1px solid #000; padding-top: 20px; margin-top: 20px;"></div>
        <!-- Linea divisora de secciones -->

        <div class="flex justify-between mb-4">
            <div>
                <label for="fecha" class="font-semibold">FECHA:</label>
                {{Carbon::parse($presupuesto->fecha)->format('d/m/Y') }}

            </div>
            <div>
                <label for="medico_solicitante" class="font-semibold">MEDICO SOLICITANTE:</label>
                {{$presupuesto->medico_solicitante}}
            </div>
            <div>
                <label for="medico_tratante" class="font-semibold">MEDICO TRATANTE:</label>
                {{$presupuesto->medico_tratante}}
            </div>
        </div>

        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>
        <!-- Linea divisora de secciones -->
        <h2 class="text-lg font-semibold mb-2">PACIENTE</h2>
        <div class="form-group">
            {{$presupuesto->paciente}}
            <br>
            Fecha de nacimiento: {{Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y');}}
            <br>
            Edad: {{ Carbon::parse($paciente->fecha_nacimiento)->age }}
            <br>
            Documento: {{number_format($paciente->documento, 0, '', '.');}}
            <br>
            Email: {{$presupuesto->email}}
            <br>
            <p></p>
            <div style="border-top: 1px solid #ddd; margin-top: 10px; margin-bottom: 10px;"></div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            
            <h6>Obra Social: {{ $presupuesto->obra_social }} @if(!empty($presupuesto->nro_afiliado))- Nro Afiliado: {{$presupuesto->nro_afiliado}} @endif </h6>
        </div>
        <div style="border-top: 1px solid #ddd; margin-top: 10px; margin-bottom: 10px;"></div>

        @if(!empty($presupuesto->detalle))
        <h6>Diagnóstico/Asunto:</h6>
        <p>
        {{$presupuesto->detalle}}
        </p>
        @endif
        <p></p>
        <p></p>
        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>
        <!-- Linea divisora de secciones -->
        <h2 class="text-lg font-semibold mb-2">PRESTACIONES</h2>

        <table class="w-auto bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b-2 border-gray-300 text-left">CÓDIGO</th>
                    <th class="px-4 py-2 border-b-2 border-gray-300 text-left" style="text-transform: uppercase;">DETALLE</th>
                    <th class="px-4 py-2 border-b-2 border-gray-300 text-left">MÓDULO TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prestaciones as $prestacion)
                <tr>
                    <td class="px-4 py-2 border-b border-gray-300">{{ $prestacion->codigo_prestacion }}</td>
                    <td class="px-4 py-2 border-b border-gray-300">
                        {{ $prestacion->nombre_prestacion }} @if($prestacion->cantidad != 1) (x {{ $prestacion->cantidad }}) @endif
                    </td>
                    <td class="px-4 py-2 border-b border-gray-300">$ {{ number_format((float) $prestacion->modulo_total, 2, ',', '.') }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>


        @if(!$anestesias->isEmpty())
        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>
        <h2 class="text-lg font-semibold mb-2">ANESTESIA</h2>
        

            <table class="w-auto bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border-b-2 border-gray-300 text-left">TIPO</th>
                        <th class="px-4 py-2 border-b-2 border-gray-300 text-left">COMPLEJIDAD</th>
                        <th class="px-4 py-2 border-b-2 border-gray-300 text-left">PRECIO</th>
                        @if ($presupuesto->edad >65 || $presupuesto->edad <3)
                            <th class="px-4 py-2 border-b-2 border-gray-300 text-left">ADICIONAL</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($anestesias as $anestesia)
                    <tr>
                        <td class="px-4 py-2 border-b border-gray-300">
                            @switch($anestesia->anestesia_id)
                            @case(0)
                            Sin especificar
                            @break
                            @case(1)
                            Anestesia Local
                            @break
                            @case(2)
                            Anestesia Regional
                            @break
                            @case(3)
                            Sedación Superficial
                            @break
                            @case(4)
                            Anestesia General
                            @break
                            @default
                            No especificado
                            @endswitch
                        </td>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $anestesia->complejidad }}</td>
                        @if(($presupuesto->edad>65) || ($presupuesto->edad<3))
                            <td class="px-4 py-2 border-b border-gray-300">$ {{number_format($anestesia->precio, 2, ',', '.');}} </td>
                            <td class="px-4 py-2 border-b border-gray-300">$ {{number_format(($anestesia->precio*1.2)-($anestesia->precio), 2, ',', '.');}} </td>
                        @else
                            <td class="px-4 py-2 border-b border-gray-300">$ {{number_format($anestesia->precio, 2, ',', '.');}} </td>
                        @endif
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($presupuesto->edad< 3 || $presupuesto->edad>65 )
                    <div style="text-align: left; margin-left: ">
                        <label id="adicional_anestesia" style="color: red;">*Adicional: 20% de recargo de anestesia por riesgo de edad*</label>
                    </div>
                @endif
            @endif
            
                
            

            <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>


            <div class="mb-6">
                <h4 for="total_presupuesto" class="mt-3 font-semibold" style="text-align: center;">
                    TOTAL PRESUPUESTO: $ {{ number_format($presupuesto->total_presupuesto, 2, ',', '.') }}
                </h4>
                <p style="text-align: center;">
                    {{ NumberToWordsHelper::convertir($presupuesto['total_presupuesto']) }}
                </p>
                
            </div>


            <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>

            @if($presupuesto->descripcion != '')
            <label class="ml-4 p-2 font-semibold">Cirugía Propuesta: </label>
            <ul class="ml-8 list-none"> <!-- Eliminamos los discos y agregamos asteriscos manualmente -->
                @foreach(explode('*', $presupuesto->descripcion) as $item)
                @if(trim($item) != '') {{-- Evitar ítems vacíos --}}
                <li class="font-semibold">* {{ trim($item) }}</li>
                @endif
                @endforeach
            </ul>
            <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
            @endif
            @if($presupuesto->condicion != '')
            <label class="ml-4 p-2 font-semibold">Condición: </label>
            <label class="ml-4 p-2">{{$presupuesto->condicion}}</label>
            @endif
            @if($presupuesto->incluye != '')
            <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
            <label class="ml-4 p-2 font-semibold">Incluye: </label>
            <label class="ml-4 p-2">{{$presupuesto->incluye}}</label>
            @endif
            @if($presupuesto->excluye != '')
            <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
            <label class="ml-4 p-2 font-semibold">Excluye: </label>
            <label class="ml-4 p-2">{{$presupuesto->excluye}}</label>
            @endif
            @if($presupuesto->adicionales != '')
            <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
            <label class="ml-4 p-2 font-semibold">Adicionales: </label>
            <ul class="ml-8 list-none"> <!-- Eliminamos los discos y agregamos asteriscos manualmente -->
                @foreach(explode('*', $presupuesto->adicionales) as $item)
                @if(trim($item) != '') {{-- Evitar ítems vacíos --}}
                <li class="font-semibold">* {{ trim($item) }}</li>
                @endif
                @endforeach
            </ul>
            @endif


            <div class="mb-6" style="margin-top: 50px">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-arrow-left-long"></i>
                    Volver
                </button>
                @if(Auth::user()->rol_id != 7)
                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#cambiosModal"><i class="fa-solid fa-eye"></i>
                    Ver Historial de Cambios
                </button>
                @endif
                @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 2)
                    @if($presupuesto->estado != 4 && $presupuesto->estado != 3 && $presupuesto->estado != 9 && $presupuesto->estado != 10)
                        <a href="{{ route('presupuestos.edit', $presupuesto->id) }}" class="btn btn-danger"><i class="fa-solid fa-edit"></i>
                            Editar
                        </a>
                    @endif
                @endif
                <!-- Lógica para mostrar el botón correcto (subir o ver archivo) -->
                 @if(Auth::user()->rol_id != 7)
                    @if($firmas->direccion == 1 && ($firmas->comercializacion == 1 || $firmas->auditoria == 1))
                        <div class="d-inline-block float-end">
                            <a href="{{ route('presupuestos.exportarDatos', $presupuesto->id) }}" target="_blank" class="btn btn-primary">
                                Generar Presupuesto
                            </a>
                            @if($presupuestoAprobado) <!-- Comprobar si hay un archivo subido -->
                            <!-- Botón para ver el archivo subido -->
                            <a href="{{ asset('storage/' . $presupuestoAprobado->file_path) }}" target="_blank" class="btn btn-secondary">
                                Ver Presupuesto Aprobado
                            </a>
                            <span class="input-group-text" style="margin-top: 5px; display: block; text-align: center;">
                                Aprobado por $ {{number_format($presupuestoAprobado->valor_aprobado, 2, ',', '.')}}
                            </span>

                            @else
                            <!-- Botón para abrir el modal de subir archivo si no hay archivo -->
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#archivoModal">
                                Subir Presupuesto Aprobado
                            </button>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
    </form>


    @if((Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 4) && 
   ($presupuesto->estado == 6 || $presupuesto->estado == 3 || $presupuesto->estado == 4 || $presupuesto->estado == 9))

    <div class="d-flex justify-content-between align-items-start" style="width: 100%;">
        <!-- Formulario de firmas -->
        <div class="d-inline-block">
            <form class="w-auto mx-auto p-6 bg-white shadow-md rounded-lg mr-2"
                method="GET"
                action="{{ route('presupuestos.sign', ['id' => $presupuesto->id, 'rol_id' => Auth::user()->rol_id]) }}"
                id="sign-form-{{ $presupuesto->id }}">
                @csrf
                <!-- Botones de firma según el rol -->
                <button type="button"
                    class="btn {{ $firmas->auditoria == 1 ? 'btn-secondary' : 'btn-success' }}"
                    {{ $firmas->auditoria == 1 || Auth::user()->rol_id != 1 ? 'disabled' : '' }}
                    onclick="confirmSign({{ $presupuesto->id }})">
                    {{ $firmas->auditoria == 1 ? 'Firmado por Auditoría' : 'Firmar Presupuesto como Auditoría' }}
                </button>

                <button type="button"
                    class="btn {{ $firmas->comercializacion == 1 ? 'btn-secondary' : 'btn-success' }}"
                    {{ $firmas->comercializacion == 1 || Auth::user()->rol_id != 2 ? 'disabled' : '' }}
                    onclick="confirmSign({{ $presupuesto->id }})">
                    {{ $firmas->comercializacion == 1 ? 'Firmado por Comercialización' : 'Firmar Presupuesto como Comercialización' }}
                </button>

                <button type="button"
                    class="btn {{ $firmas->direccion == 1 ? 'btn-secondary' : 'btn-success' }}"
                    {{ $firmas->direccion == 1 || Auth::user()->rol_id != 6 ? 'disabled' : '' }}
                    onclick="confirmSign({{ $presupuesto->id }})">
                    {{ $firmas->direccion == 1 ? 'Firmado por Dirección' : 'Firmar Presupuesto como Dirección' }}
                </button>
            </form>
        </div>
    </div>
    @endif

    @if(Auth::user()->rol_id == 4 && $presupuesto->estado != 10)
    <form method="POST" action="{{ route('presupuestos.destroy', $presupuesto->id) }}"
            id="delete-form-{{ $presupuesto->id }}">
            @csrf
            @method('DELETE')
            <button style="margin-top: 10px; margin-left: 25px" type="button" class="btn btn-danger"
                onclick="confirmDelete({{ $presupuesto->id }})"><i class="fa-solid fa-ban"></i>
                Anular Presupuesto
            </button>
        </form>
    @endif
    <!-- Modal para subir archivo -->
    <div class="modal fade" id="archivoModal" tabindex="-1" aria-labelledby="archivoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archivoModalLabel">Subir Archivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form method="POST" action="{{ route('presupuestos.guardarArchivo', ['id' => $presupuesto->id]) }}" enctype="multipart/form-data">
                        @csrf
                        <!-- Nuevo campo para el valor aprobado -->
                        <div class="mb-4">
                            <label for="valor_aprobado" class="form-label">Ingrese el valor aprobado</label>
                            <input type="number" step="0.01" name="valor_aprobado" id="valor_aprobado" class="form-control" placeholder="{{$presupuesto->total_presupuesto}}" required>
                        </div>
                        <div class="mb-4">
                            <label for="archivo" class="form-label">Seleccionar PDF o Imagen</label>
                            <input type="file" name="archivo" id="archivo" class="form-control" accept=".pdf, .jpg, .jpeg, .png" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Subir Archivo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    




    <!-- Modal REGISTRO AUDITORIA -->
    <div class="modal fade" id="cambiosModal" tabindex="-1" aria-labelledby="cambiosModalLabel" aria-hidden="true">
        <div class="modal-dialog custom-modal-width">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cambiosModalLabel">Historial de Cambios</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Tabla para Cambios en Presupuestos -->
                    @if($cambiosPresupuestos->isNotEmpty())
                    <h3>Cambios en Presupuesto</h3>
                    <table class="table table-striped" style="table-layout: auto; width: 100%;">
                        <thead>
                            <tr>
                                <th style="">Fecha</th>
                                <th>Campo</th>
                                <th>Valor Anterior</th>
                                <th>Valor Nuevo</th>
                                <th style="">Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cambiosPresupuestos as $cambio)
                            <tr>
                                <td>{{ Carbon::parse($cambio->fecha_cambio)->format('d/m/Y - H:i:s') }}</td>
                                <td>
                                    @if($cambio->campo== 'paciente_salutte_id')
                                    HC
                                    @elseif ($cambio->campo == 'medico_tratante')
                                    Medico tratante
                                    @else
                                    {{ $cambio->campo }}
                                    @endif
                                </td>
                                <td>
                                    @if($cambio->campo == 'estado')
                                    {{ Estado::getEstadoById($cambio->valor_anterior) }}
                                    @else
                                    {{ $cambio->valor_anterior }}
                                    @endif
                                </td>
                                <td>
                                    @if($cambio->campo == 'estado')
                                    {{ Estado::getEstadoById($cambio->valor_nuevo) }}
                                    @else
                                    {{ $cambio->valor_nuevo }}
                                    @endif
                                </td>
                                <td style="">{{ Users::getNameById($cambio->usuario_id) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                    <!-- Tabla para Cambios en Prestaciones -->
                    
                    @if($cambiosPrestaciones->isNotEmpty())
                        <h3>Cambios en Prestaciones</h3>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Campo</th>
                                    <th>Valor Anterior</th>
                                    <th>Valor Nuevo</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cambiosPrestaciones as $cambio)
                                <tr>
                                    <td>{{ Carbon::parse($cambio->fecha_cambio)->format('d/m/Y - H:i:s') }}</td>
                                    <td>
                                        @if($cambio->campo == 'modulo_total')
                                        precio prestacion
                                        @elseif($cambio->campo == 'codigo_prestacion')
                                        codigo prestacion
                                        @else
                                        {{ $cambio->campo }}
                                        @endif
                                    </td>
                                    <td>{{ $cambio->valor_anterior }} </td>
                                    <td>{{ $cambio->valor_nuevo }}</td>
                                    <td>{{ Users::getNameById($cambio->usuario_id) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <!-- Tabla para Cambios en Anestesias -->
                    @if($cambiosAnestesias->isNotEmpty())
                    <h3>Cambios en Anestesias</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Campo</th>
                                <th>Valor Anterior</th>
                                <th>Valor Nuevo</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cambiosAnestesias as $cambio)
                            <tr>
                                <td>{{ Carbon::parse($cambio->fecha_cambio)->format('d/m/Y - H:i:s') }}</td>
                                <td>
                                    @if($cambio->campo== 'anestesia_id%')
                                    tipo de anestesia
                                    @elseif($cambio->campo== 'precio_anestesia')
                                    precio anestesia
                                    @else
                                    {{ $cambio->campo }}
                                    @endif
                                </td>
                                <td>
                                    @if(str_contains($cambio->campo, 'tipo anestesia'))
                                    {{ Anestesia::getAnestesiaById($cambio->valor_anterior) }}
                                    @else
                                    {{ $cambio->valor_anterior }}
                                    @endif

                                </td>
                                <td>
                                    @if(str_contains($cambio->campo, 'tipo anestesia'))
                                    {{ Anestesia::getAnestesiaById($cambio->valor_nuevo) }}
                                    @else
                                    {{ $cambio->valor_nuevo }}
                                    @endif
                                </td>

                                <td>{{ Users::getNameById($cambio->usuario_id) }}</td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                    @endif

                    <h3>Detalle</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Prestacion</th>
                                <th>Creada por</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($prestaciones as $prestacion)
                            <tr>
                                <td>{{ Carbon::parse($prestacion->creado_fecha)->format('d/m/Y - H:i:s') }}</td>
                                <td> Prestacion {{ $loop->index + 1 }} </td>
                                <td>{{ Users::getNameById($prestacion->creado_por) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($firmas->comercializacion == 1 || $firmas->auditoria == 1 || $firmas->direccion == 1)
                    <h3>Firmas</h3>
                    @if($firmas->comercializacion == 1)
                    ► Firmado por {{ Users::getNameById($firmas->firmado_por_comercializacion) }} - Comercialización, el día: {{ Carbon::parse($firmas->fecha_comercializacion)->format('d/m/Y - H:i:s') }}
                    <br>
                    @endif
                    @if($firmas->auditoria == 1)
                    ► Firmado por {{ Users::getNameById($firmas->firmado_por_auditoria) }} - Auditoría, el día: {{ Carbon::parse($firmas->fecha_auditoria)->format('d/m/Y - H:i:s') }}
                    <br>
                    @endif
                    @if($firmas->direccion == 1)
                    ► Firmado por {{ Users::getNameById($firmas->firmado_por_direccion) }} - Dirección, el día: {{ Carbon::parse($firmas->fecha_direccion)->format('d/m/Y - H:i:s') }}
                    <br>
                    @endif
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
<!--  <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>  LINEA DIVISORA-->

<!--  route('presupuestos.finalize', ['id' => $presupuesto->id]) -->

<script>

    function confirmSign(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, firmarlo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('sign-form-' + id).submit();
            }
        });
    }

    function confirmDelete(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>

<style>
    .custom-modal-width {
        max-width: 90%;
        /* O el ancho que necesites, por ejemplo, 800px */
    }
</style>