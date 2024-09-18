@php
    use App\Models\ObraSocial;
    use App\Models\Convenio;
    use App\Models\Prestacion;
    use App\Helpers\NumberToWordsHelper;
@endphp

<header style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
    <img src="{{ public_path('storage/img/header.jpg') }}" alt="Logo" style="width: 200px;">
    <h2 style="flex-grow: 1; text-align: center; margin: 0;">PRESUPUESTO</h2>
</header>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<head>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        @page {
            margin: 100px 25px;
        }

        body {
            margin-bottom: 80px; /* Espacio para el footer */
        }

        .footer {
            position: absolute;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 12px;
            color: #555;
            line-height: 50px;
        }

    </style>
</head>

    <x-slot name="title">Ver Presupuesto</x-slot>
    
    <body>
    
    <form method="GET" action="{{ route('presupuestos.index') }}"
    
        class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg" enctype="multipart/form-data">
        @csrf
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <input type="hidden" id="presupuesto_id" name="presupuesto_id" value="{{ $presupuesto['id'] }}">
        
        <!-- <div style="border-top: 1px solid #000; padding-top: 20px; margin-top: 20px;"></div> -->
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
            Email: {{$presupuesto['email']}}
            <br>
            <div style="border-top: 1px solid #ddd; margin-top: 10px; margin-bottom: 10px;"></div>
        </div>


        <div style="margin-bottom: 5px;" class="d-flex justify-content-between align-items-center">

            @if(is_numeric($presupuesto['obra_social']))
                Obra Social: {{ ObraSocial::getObraSocialById($presupuesto['obra_social']) }} - Nro Afiliado:  {{ $presupuesto['nro_afiliado'] }}
            @else
                Obra Social: {{ $presupuesto['obra_social'] }} - Nro Afiliado:  {{ $presupuesto['nro_afiliado'] }}
            @endif
            <br>
        </div>
        <div style="border-top: 1px solid #ddd; margin-top: 10px; margin-bottom: 10px;"></div>

        <!-- Linea divisora de secciones -->

        <!-- NECESITO MOSTRAR AQUI MIS PRESTACIONES -->
        <div style="font-size: 1rem; font-weight: 600; text-align: center;">PRESTACIONES</div>
        <div style="margin-bottom: 5px;"></div>

        

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
                        <td>$ {{number_format($prestacion->modulo_total, 2, ',', '.');}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>

        @if(!empty($anestesias))
        <div style="font-size: 1rem; font-weight: 600; text-align: center;">ANESTESIA</div>

            <div style="margin-bottom: 5px;"></div>
                
            <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border-b-2 border-gray-300 text-center">TIPO</th>
                            <th class="px-4 py-2 border-b-2 border-gray-300 text-center">COMPLEJIDAD</th>
                            <th class="px-4 py-2 border-b-2 border-gray-300 text-center">PRECIO</th>
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
                            </td>
                            <td class="px-4 py-2 border-b border-gray-300">{{ $anestesia->complejidad }}</td>
                            <td class="px-4 py-2 border-b border-gray-300">$ {{number_format($anestesia->precio, 2, ',', '.');}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
        @endif


        
        <div class="" style="margin-top: 10px;">
    <div style="font-size: 1rem; font-weight: 600; text-align: center; margin-bottom: 5px;">
        TOTAL PRESUPUESTO: $ {{ number_format($presupuesto['total_presupuesto'], 2, ',', '.') }}
    </div>
    <p style="text-align: center; margin-top: 0;">
    {{ NumberToWordsHelper::convertir($presupuesto['total_presupuesto']) }}
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
        @if($presupuesto['adicionales'] != '')
            <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
            <label class="ml-4 p-2 font-semibold" style="font-size: 12px;">Adicionales: </label>
            <ul class="ml-4 list-none" style="font-size: 12px;"> <!-- Aplicamos el mismo tamaño de fuente -->
                @foreach(explode('*',$presupuesto['adicionales']) as $item)
                    @if(trim($item) != '') {{-- Evitar ítems vacíos --}}
                        <li class="font-semibold" style="font-size: 12px; font-weight: bold;">* {{ trim($item) }}</li> <!-- Negrita y tamaño -->
                    @endif
                @endforeach
            </ul>
        @endif

        <div class="footer">
            Firmado electrónicamente por Comercialización |
            Firmado electronicamente por Auditoría |
            Firmado electronicamente por Dirección 
            <br>
            Paso de los Andes 3051, Ciudad de Mendoza / www.hospital.uncu.edu.ar / Informes: 261 4494220 / internacion@hospital.uncu.edu.ar
        </div>
    </body>

    </form>

<!--  <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>  LINEA DIVISORA-->
<!--  route('presupuestos.finalize', ['id' => $presupuesto->id]) -->

