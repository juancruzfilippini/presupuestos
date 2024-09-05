@php
    use App\Models\ObraSocial;
    use App\Models\Convenio;
    use App\Models\Prestacion;
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
            <p></p>
        </div>


        <div class="d-flex justify-content-between align-items-center">

            @if(is_numeric($presupuesto->obra_social))
                Obra Social: {{ ObraSocial::getObraSocialById($presupuesto->obra_social) }} 
            @else
                Obra Social: {{ $presupuesto->obra_social }} 
            @endif


            @if(is_numeric($presupuesto->convenio))
                -  {{ Convenio::getConvenioById($presupuesto->convenio) }}
            @else
                @if (!is_null($presupuesto->convenio))
                -  {{ $presupuesto->convenio }}
                @endif
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
                    <th class="px-4 py-2 border-b-2 border-gray-300 text-left">NOMBRE</th>
                    <th class="px-4 py-2 border-b-2 border-gray-300 text-left">MÓDULO TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prestaciones as $prestacion)
                    <tr>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $prestacion->codigo_prestacion }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $prestacion->nombre_prestacion }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $prestacion->modulo_total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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
            
        @if($presupuesto->detalle_anestesia != "" && $presupuesto->anestesia_id != 0)
        <label class="ml-4 p-2 font-semibold"> Observación Anestesia: </label> {{$presupuesto->detalle_anestesia}}
        @endif
       
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
                        <td class="px-4 py-2 border-b border-gray-300">{{ $presupuesto->precio_anestesia }}</td>
                    </tr>
            </tbody>
        </table>

        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>

        
        <div class="mb-6">
            <h4 for="total_presupuesto" class="mt-3 font-semibold" style="text-align: center;">TOTAL PRESUPUESTO: $ {{$presupuesto->total_presupuesto}}</h4>
        </div>

        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>

        @if(!is_null($presupuesto->condicion))
        <label class="ml-4 p-2 font-semibold">Condición: </label> 
        <label class="ml-4 p-2">{{$presupuesto->condicion}}</label> 
        @endif
        @if(!is_null($presupuesto->incluye))
        <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
        <label class="ml-4 p-2 font-semibold">Incluye: </label> 
        <label class="ml-4 p-2">{{$presupuesto->incluye}}</label> 
        @endif
        @if(!is_null($presupuesto->excluye))
        <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
        <label class="ml-4 p-2 font-semibold">Excluye: </label> 
        <label class="ml-4 p-2">{{$presupuesto->excluye}}</label> 
        @endif
        @if(!is_null($presupuesto->adicionales))
        <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
        <label class="ml-4 p-2 font-semibold">Adicionales: </label>
        <label class="ml-4 p-2 font-semibold">{{$presupuesto->adicionales}}</label> 
        @endif
    </form>

    
    


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