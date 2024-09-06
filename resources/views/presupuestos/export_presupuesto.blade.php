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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>


    <x-slot name="title">Ver Presupuesto</x-slot>

    <form method="GET" action="{{ route('presupuestos.index') }}"
        class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg" enctype="multipart/form-data">
        @csrf

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <input type="hidden" id="presupuesto_id" name="presupuesto_id" value="{{ $presupuesto['id'] }}">


        <h2 class="text-center">PRESUPUESTO</h2>
        
        <div style="border-top: 1px solid #000; padding-top: 20px; margin-top: 20px;"></div>
        <!-- Linea divisora de secciones -->

        <div style="width: 100%; display: table; margin-bottom: 16px;">
            <div style="display: table-cell; text-align: left; vertical-align: middle;">
                <label for="medico_tratante" style="">MEDICO TRATANTE: {{$presupuesto['medico_tratante']}}</label>
            </div>
            <div style="display: table-cell; text-align: right; vertical-align: middle;">
                <label for="fecha" style="">FECHA: {{ \Carbon\Carbon::parse($presupuesto['fecha'])->format('d/m/Y') }}</label>
            </div>
        </div>
        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>

        <!-- Linea divisora de secciones -->
        <div style="font-size: 1rem; font-weight: 600; text-align: center;">PACIENTE</div>

        <div class="form-group">
            Nombre: {{$presupuesto['paciente']}}
            <br>
            Fecha de nacimiento: {{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y');}}
            <br>
            Edad: {{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age }}
            <br>
            Documento: {{number_format($paciente->documento, 0, '', '.');}}
            <br>
            <div style="border-top: 1px solid #ddd; margin-top: 10px; margin-bottom: 10px;"></div>
        </div>


        <div style="margin-bottom: 5px;" class="d-flex justify-content-between align-items-center">

            @if(is_numeric($presupuesto['obra_social']))
                Obra Social: {{ ObraSocial::getObraSocialById($presupuesto['obra_social']) }} - Nro Afiliado:  
            @else
                Obra Social: {{ $presupuesto['obra_social'] }} - Nro Afiliado:  
            @endif
            <br>
        </div>
        <!-- Linea divisora de secciones -->

        <!-- NECESITO MOSTRAR AQUI MIS PRESTACIONES -->
                <table>
            <thead>
                <tr>
                    <th>CÓDIGO</th>
                    <th style="text-transform: uppercase;">{{$presupuesto['especialidad']}}</th>
                    <th>MÓDULO TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prestaciones as $prestacion)
                    <tr>
                        <td>{{ $prestacion->codigo_prestacion }}</td>
                        <td class="text-left">{{ $prestacion->nombre_prestacion }}</td>
                        <td>$ {{number_format($prestacion->modulo_total, 0, '', '.');}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>

        @if($presupuesto['anestesia'] != "Sin anestesia")
            <div style="margin-bottom: 5px;">
            </div>
                
                <table class="min-w-full bg-white border border-gray-200" style="margin-bottom: 5px;">
                    <thead>
                        <tr>
                            <th>COMPLEJIDAD</th>
                            <th>PRECIO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2 border-b border-gray-300">{{ $presupuesto['complejidad'] }}</td>
                            <td class="px-4 py-2 border-b border-gray-300">$ {{number_format($presupuesto['precio_anestesia'], 0, '', '.');}}</td>
                        </tr>
                    </tbody>
                </table>
        
                <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>
        @endif


        
        <div class="mb-6">
        <div style="font-size: 1rem; font-weight: 600; text-align: center;">TOTAL PRESUPUESTO: $ {{number_format($presupuesto['total_presupuesto'], 0, '', '.');}}</div>
            <p style="text-align: center;">
    {{ NumberToWordsHelper::convertir($presupuesto['total_presupuesto']) }} pesos
            </p>
        </div>

        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>

        @if($presupuesto['condicion'])
            <label class="ml-4 p-2 font-semibold" style="font-size: 12px;">Condición: </label> 
            <label class="ml-4 p-2" style="font-size: 12px;">{{$presupuesto['condicion']}}</label> 
        @endif
        @if($presupuesto['incluye'])
            <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
            <label class="ml-4 p-2 font-semibold" style="font-size: 12px;">Incluye: </label> 
            <label class="ml-4 p-2" style="font-size: 12px;">{{$presupuesto['incluye']}}</label> 
        @endif
        @if($presupuesto['excluye'])
            <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
            <label class="ml-4 p-2 font-semibold" style="font-size: 12px;">Excluye: </label> 
            <label class="ml-4 p-2" style="font-size: 12px;">{{$presupuesto['excluye']}}</label> 
        @endif
        @if($presupuesto['adicionales'])
            <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
            <label class="ml-4 p-2 font-semibold" style="font-size: 12px; font-weight: bold;">Adicionales: </label>
            <label class="ml-4 p-2" style="font-size: 12px; font-weight: bold;">{{$presupuesto['adicionales']}}</label> 
        @endif 

    </form>
<!--  <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>  LINEA DIVISORA-->
<!--  route('presupuestos.finalize', ['id' => $presupuesto->id]) -->

<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    th, td {
        border: 1px solid #000;
        padding: 8px;
        text-align: center;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .text-left {
        text-align: left;
    }
    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
</style>
