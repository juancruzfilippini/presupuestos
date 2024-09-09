@php
    use App\Models\ObraSocial;
    use App\Models\Convenio;
    use App\Models\Prestacion;
    use App\Helpers\NumberToWordsHelper;
@endphp

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<head>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<x-app-layout>
    <x-slot name="title">Ver Presupuesto</x-slot>

    <form method="GET" action="{{ route('presupuestos.index') }}"
        class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg" enctype="multipart/form-data">
        @csrf

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <input type="hidden" id="presupuesto_id" name="presupuesto_id" value="{{$id}}">

        <h1 class="text-2xl font-bold mb-6">VER PRESUPUESTO {{$presupuesto->id}}</h1>

        <label for="fecha" class="font-semibold">PEDIDO MÉDICO:</label>
        <p></p>

        @if ($archivos->count() > 0)
            <div class="mb-4">
                <label for="download_file" class="font-semibold">Archivo adjunto:</label>
                @foreach ($archivos as $archivo)
                    <li>
                        <a href="{{ Storage::url($archivo->file_path) }}" class="text-blue-500 hover:underline" download>
                            Descargar {{ basename($archivo->file_path) }}
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
            {{ \Carbon\Carbon::parse($presupuesto->fecha)->format('d/m/Y') }}

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
            Fecha de nacimiento: {{\Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y');}}
            <br>
            Edad: {{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age }}
            <br>
            Documento: {{number_format($paciente->documento, 0, '', '.');}}
            <br>
            <p></p>
            <div style="border-top: 1px solid #ddd; margin-top: 10px; margin-bottom: 10px;"></div>
        </div>


        <div class="d-flex justify-content-between align-items-center">

            @if(is_numeric($presupuesto->obra_social))
                Obra Social: {{ ObraSocial::getObraSocialById($presupuesto['obra_social']) }} - Nro Afiliado:  {{$presupuesto->nro_afiliado}}
            @else
                Obra Social: {{ $presupuesto->obra_social }} - Nro Afiliado: {{$presupuesto->nro_afiliado}}
            @endif

        </div>


        <p></p>
        <p></p>
        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>
        <!-- Linea divisora de secciones -->
        <h2 class="text-lg font-semibold mb-2">PRESTACIONES</h2>

        <!-- NECESITO MOSTRAR AQUI MIS PRESTACIONES -->
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b-2 border-gray-300 text-left">CÓDIGO</th>
                    <th class="px-4 py-2 border-b-2 border-gray-300 text-left" style="text-transform: uppercase;">{{$presupuesto['especialidad']}}</th>
                    <th class="px-4 py-2 border-b-2 border-gray-300 text-left">MÓDULO TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prestaciones as $prestacion)
                    <tr>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $prestacion->codigo_prestacion }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $prestacion->nombre_prestacion }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">$ {{number_format($prestacion->modulo_total, 0, '', '.');}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        
        @if($presupuesto->anestesia_id != 0)
        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>
            <label class="p-2 font-semibold">Anestesia: </label>

                @switch($presupuesto->anestesia_id)
                    @case(0)
                        Sin anestesia
                        @break
                    @case(1)
                        Local
                        @break
                    @case(2)
                        Periferica
                        @break
                    @case(3)
                        Central
                        @break
                    @case(4)
                        Total
                        @break
                    @default
                        No especificado
                @endswitch
                
                
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border-b-2 border-gray-300 text-left">COMPLEJIDAD</th>
                            <th class="px-4 py-2 border-b-2 border-gray-300 text-left">PRECIO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2 border-b border-gray-300">{{ $presupuesto->complejidad }}</td>
                            <td class="px-4 py-2 border-b border-gray-300">$ {{number_format($presupuesto->precio_anestesia, 0, '', '.');}}</td>
                        </tr>
                    </tbody>
                </table>
                @if($presupuesto->detalle_anestesia != "" && $presupuesto->anestesia_id != 0)
                <br>
                <label class="p-2 font-semibold">Observación Anestesia: </label> {{$presupuesto->detalle_anestesia}}
                @endif
        @endif

        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>

        
        <div class="mb-6">
            <h4 for="total_presupuesto" class="mt-3 font-semibold" style="text-align: center;">TOTAL PRESUPUESTO: $ {{number_format($presupuesto->total_presupuesto, 0, '', '.');}}</h4>
            <p style="text-align: center;">
    {{ NumberToWordsHelper::convertir($presupuesto['total_presupuesto']) }} pesos
            </p>
        </div>

        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>

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
        <label class="ml-4 p-2 font-semibold">{{$presupuesto->adicionales}}</label> 
        @endif

        <div class="mb-6" style="margin-top: 50px">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-arrow-left-long"></i> 
                 Volver
            </button>
        </div>
    </form>

    
    @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 4)
    <div class="d-flex justify-content-between" style="width: 100%;">
        <div>
            <form class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg mr-2" 
                  method="GET" 
                  action="{{ route('presupuestos.sign', ['id' => $presupuesto->id, 'rol_id' => Auth::user()->rol_id]) }}"
                  id="sign-form-{{ $presupuesto->id }}">
                @csrf
                <!-- Botón para Auditoría (Rol 1) -->
                <button type="button" 
                        class="btn {{ $firmas->auditoria == 1 ? 'btn-secondary' : 'btn-success' }}" 
                        {{ $firmas->auditoria == 1 || Auth::user()->rol_id != 1 ? 'disabled' : '' }}
                        onclick="confirmSign({{ $presupuesto->id }})">
                    {{ $firmas->auditoria == 1 ? 'Firmado por Auditoría' : 'Firmar Presupuesto como Auditoría' }}
                </button>

                <!-- Botón para Comercialización (Rol 2) -->
                <button type="button" 
                        class="btn {{ $firmas->comercializacion == 1 ? 'btn-secondary' : 'btn-success' }}" 
                        {{ $firmas->comercializacion == 1 || Auth::user()->rol_id != 2 ? 'disabled' : '' }}
                        onclick="confirmSign({{ $presupuesto->id }})">
                    {{ $firmas->comercializacion == 1 ? 'Firmado por Comercialización' : 'Firmar Presupuesto como Comercialización' }}
                </button>

                <!-- Botón para Dirección (Rol 6) -->
                <button type="button" 
                        class="btn {{ $firmas->direccion == 1 ? 'btn-secondary' : 'btn-success' }}" 
                        {{ $firmas->direccion == 1 || Auth::user()->rol_id != 6 ? 'disabled' : '' }}
                        onclick="confirmSign({{ $presupuesto->id }})">
                    {{ $firmas->direccion == 1 ? 'Firmado por Dirección' : 'Firmar Presupuesto como Dirección' }}
                </button>
            </form>
        </div>

        <!-- Botón adicional que aparece solo si todas las partes han firmado -->
        @if($firmas->auditoria == 1 && $firmas->comercializacion == 1 && $firmas->direccion == 1)
            <form class="max-w-4xl p-6 bg-white shadow-md rounded-lg mr-2" method="GET" action="{{ route('presupuestos.exportarDatos', ['id' => $presupuesto->id]) }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                    Generar Presupuesto
                </button>
            </form>
        @endif
    </div>
@endif


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

    
</script>