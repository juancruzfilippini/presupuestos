@php
    use App\Models\ObraSocial;
    use App\Models\Users;
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
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            margin-bottom: 20mm; /* Asegura espacio para el footer */
        }

        .table-container {
            page-break-inside: auto;
            page-break-after: auto;
        }

        .content {
            margin-bottom: 25mm;
            /* Espacio para el footer */
        }
        /* Estilos para el footer */
        .footer {
            width: 100%;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 10px;
            line-height: 1.2;
            border-top: 1px solid #000;
            padding-top: 5px;
            background-color: #fff;
        }
        @page {
            margin-top: 10mm;
            margin-bottom: 20mm; /* Deja espacio suficiente para el footer en todas las páginas */
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<div class="footer" style="text-align: center; line-height: 1.2;">
            <br>
            @if ($firmas->comercializacion == 1)
                <div>Firmado electrónicamente por {{ Users::getNameById($firmas->firmado_por_comercializacion) }} - Área de Comercialización</div>
            @endif
            @if ($firmas->auditoria == 1)
                <div>Firmado electrónicamente por {{ Users::getNameById($firmas->firmado_por_auditoria) }} - Auditoría Médica</div>
            @endif
            @if ($firmas->direccion == 1)
                <div>Firmado electrónicamente por {{ Users::getNameById($firmas->firmado_por_direccion) }} - Área de Dirección Administrativa</div>
            @endif
            <div style="color: black; font-size: 10px; margin-top: 10px">Validado según art. 5 de la Ley 25.506 "Firma Digital"</div>
            <br>
            
            <span>Paso de los Andes 3051, Ciudad de Mendoza</span><br>
            <span>www.hospital.uncu.edu.ar / Informes: 261 4494220 / internacion@hospital.uncu.edu.ar</span>
        </div>
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

        <div style="width: 100%; display: table; margin-bottom: 16px;">
            <div style="display: table-cell; text-align: left; vertical-align: middle;">
                <label for="medico_tratante" style="">MEDICO TRATANTE: {{$presupuesto['medico_tratante']}}</label>
            </div>
            <div style="display: table-cell; text-align: right; vertical-align: middle;">
                <label for="fecha" style="">FECHA: {{ \Carbon\Carbon::parse($firmas->fecha_direccion)->format('d/m/Y') }}</label>
            </div>
        </div>
        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>

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

                Obra Social: {{ $presupuesto['obra_social'] }} @if($presupuesto['nro_afiliado'] != '') - Nro Afiliado:  {{ $presupuesto['nro_afiliado'] }} @endif
        
            <br>
        </div>
        <div style="border-top: 1px solid #ddd; margin-top: 10px; margin-bottom: 10px;"></div>

        <div class="" style="font-size: 1rem; font-weight: 600; text-align: center;">PRESTACIONES</div>
        <div style="margin-bottom: 5px;"></div>
            <table>
                <thead>
                    <tr>
                        <th>CÓDIGO</th>
                        <th>DETALLE</th>
                        <th>MÓDULO TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestaciones as $prestacion)
                        <tr>
                            <td>{{ $prestacion->codigo_prestacion }}</td>
                            <td class="text-left">
                                {{ $prestacion->nombre_prestacion }} @if($prestacion->cantidad != 1) (x {{ $prestacion->cantidad }}) @endif
                            </td>
                            <td>$ {{number_format($prestacion->modulo_total, 2, ',', '.');}}</td>
                        </tr>
                    @endforeach
                </tbody>        
            </table>
        <br>
        @if(!$anestesias->isEmpty())
        <div style="font-size: 1rem; font-weight: 600; text-align: center;">ANESTESIA</div>

            <div style="margin-bottom: 5px;"></div>
            
                
            <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border-b-2 border-gray-300 text-center">TIPO</th>
                            <th class="px-4 py-2 border-b-2 border-gray-300 text-center">COMPLEJIDAD</th>
                            <th class="px-4 py-2 border-b-2 border-gray-300 text-center">PRECIO</th>
                            @if ($presupuesto['edad'] >65 || $presupuesto['edad'] <3)
                            <th class="px-4 py-2 border-b-2 border-gray-300 text-center">ADICIONAL</th>
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
                            @if(($presupuesto['edad']>65) || ($presupuesto['edad']<3))
                            <td class="px-4 py-2 border-b border-gray-300">$ {{number_format($anestesia->precio, 2, ',', '.');}} </td>
                            <td class="px-4 py-2 border-b border-gray-300">$ {{number_format(($anestesia->precio*1.2)-($anestesia->precio), 2, ',', '.');}} </td>
                        @else
                            <td class="px-4 py-2 border-b border-gray-300">$ {{number_format($anestesia->precio, 2, ',', '.');}} </td>
                        @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if (isset($presupuesto['edad']) && ($presupuesto['edad'] < 3 || $presupuesto['edad'] > 65))
                    <div style="margin-bottom: 5px; text-align: center;">
                            <label id="adicional_anestesia" style="font-size: 12px; color: red;">*Adicional: 20% de recargo de anestesia por riesgo de edad*</label>
                    </div>
                @endif
        @endif

    <div style="font-size: 1.1rem; font-weight: 600; text-align: center; margin-bottom: 5px; margin-top: 7px;">
        TOTAL PRESUPUESTO: $ {{ number_format($presupuesto['total_presupuesto'], 2, ',', '.') }}
    </div>
    <p style="text-align: center; margin-top: 0;">
    {{ NumberToWordsHelper::convertir($presupuesto['total_presupuesto']) }}
    </p>
        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 5px;"></div>

        @if($presupuesto['condicion'])
            <label class="ml-4 p-2 font-semibold" style="font-size: 10px;">Condición: </label> 
            <label class="ml-4 p-2" style="font-size: 10px;">{{$presupuesto['condicion']}}</label> 
        @endif
        @if($presupuesto['incluye'])
            <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
            <label class="ml-4 p-2 font-semibold" style="font-size: 10px;">Incluye: </label> 
            <label class="ml-4 p-2" style="font-size: 10px;">{{$presupuesto['incluye']}}</label> 
        @endif
        @if($presupuesto['excluye'])
            <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
            <label class="ml-4 p-2 font-semibold" style="font-size: 10px;">Excluye: </label> 
            <label class="ml-4 p-2" style="font-size: 10px;">{{$presupuesto['excluye']}}</label> 
        @endif
        @if($presupuesto['adicionales'] != '')
            <div style="border-top: 1px solid #ddd; margin-top: 10px;"></div>
            <label class="ml-4 p-2 font-semibold" style="font-size: 10px;">Adicionales: </label>
            <ul class="ml-4 list-none" style="font-size: 8px;"> <!-- Aplicamos el mismo tamaño de fuente -->
                @foreach(explode('*',$presupuesto['adicionales']) as $item)
                    @if(trim($item) != '') {{-- Evitar ítems vacíos --}}
                        <li class="font-semibold" style="font-size: 11px; font-weight: bold;">* {{ trim($item) }}</li> <!-- Negrita y tamaño -->
                    @endif
                @endforeach
            </ul>
        @endif
        </form>
    </body>

    
    
        
    
