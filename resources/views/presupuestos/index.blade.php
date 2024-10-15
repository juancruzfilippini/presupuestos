<head>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
@php
    use App\Models\ObraSocial;
    use App\Models\Estado;
    use App\Models\Presupuestos_aprobados;
@endphp

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap 4 y 5 (Elige una versión, usualmente no se usan ambas juntas) -->
<!-- jQuery UI -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<!-- Sparkline -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sparklines/0.4.1/sparkline.js"></script>

<!-- jQuery Knob Chart -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-knob/1.2.13/jquery.knob.min.js"></script>

<script src="https://cdn.tailwindcss.com"></script>

<!-- Tempusdominus Bootstrap 4 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>

<!-- Summernote -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>

<!-- overlayScrollbars -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.13.1/js/jquery.overlayScrollbars.min.js">
</script>

<!-- AdminLTE -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/demo.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/pages/dashboard.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<!-- List.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.min.js" crossorigin="anonymous"
    referrerpolicy="no-referrer"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>

<!-- JSZip (Necesario para la exportación a Excel) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>

<x-app-layout>
    <x-slot name="title"> Presupuestos</x-slot>
    @section('title', 'Presupuestos')
    <div class="mt-4">
        <h1 class="ml-2">Presupuestos</h1>

        <form method="GET" action="{{ route('presupuestos.index') }}" class="mb-4">
            <input type="hidden" name="page" value="{{ request()->input('page') }}">

            <!-- Primera fila de filtros -->
            <div class="input-group mb-3">
                <input type="number" name="search_nro_presupuesto"
                    value="{{ request()->input('search_nro_presupuesto') }}" class="form-control"
                    placeholder="Buscar N° Presupuesto">

                @if (request()->input('search_nro_presupuesto'))
                    <a href="{{ route('presupuestos.index') }}" class="btn btn-secondary">×</a>
                @endif

                <input type="text" name="search_paciente" value="{{ request()->input('search_paciente') }}"
                    class="form-control" placeholder="Buscar Paciente">

                @if (request()->input('search_paciente'))
                    <a href="{{ route('presupuestos.index') }}" class="btn btn-secondary">×</a>
                @endif

                <input type="text" name="search_medico_tratante"
                    value="{{ request()->input('search_medico_tratante') }}" class="form-control"
                    placeholder="Buscar Medico">

                @if (request()->input('search_medico_tratante'))
                    <a href="{{ route('presupuestos.index') }}" class="btn btn-secondary">×</a>
                @endif

                <input type="text" name="search_estado" value="{{ request()->input('search_estado') }}"
                    class="form-control" placeholder="Buscar Estado">

                @if (request()->input('search_estado'))
                    <a href="{{ route('presupuestos.index') }}" class="btn btn-secondary">×</a>
                @endif

                <input type="text" name="search_detalle" value="{{ request()->input('search_detalle') }}"
                    class="form-control" placeholder="Buscar Detalle">

                @if (request()->input('search_detalle'))
                    <a href="{{ route('presupuestos.index') }}" class="btn btn-secondary">×</a>
                @endif

                <input type="text" name="search_total_presupuesto"
                    value="{{ request()->input('search_total_presupuesto') }}" class="form-control"
                    placeholder="Buscar por Monto">

                @if (request()->input('search_total_presupuesto'))
                    <a href="{{ route('presupuestos.index') }}" class="btn btn-secondary">×</a>
                @endif

                <input type="text" name="search_obra_social" value="{{ request()->input('search_obra_social') }}"
                    class="form-control" placeholder="Buscar Obra Social">

                @if (request()->input('search_obra_social'))
                    <a href="{{ route('presupuestos.index') }}" class="btn btn-secondary">×</a>
                @endif
            </div>

            <!-- Segunda fila de filtros -->
            <div class="input-group mb-3">
                <!-- Filtro "Desde" -->
                <div class="input-group-prepend">
                    <span class="input-group-text">Desde</span>
                </div>
                <input type="date" name="search_desde" value="{{ request()->input('search_desde') }}"
                    class="form-control col-2" style="max-width: 200px" placeholder="Buscar por fecha de inicio">

                @if (request()->input('search_desde'))
                    <a href="{{ route('presupuestos.index') }}" class="btn btn-secondary">×</a>
                @endif

                <!-- Filtro "Hasta" -->
                <div style="margin-left: 25px" class="input-group-prepend">
                    <span class="input-group-text">Hasta</span>
                </div>
                <input type="date" name="search_hasta" value="{{ request()->input('search_hasta') }}"
                    class="form-control col-2" style="max-width: 200px" placeholder="Buscar por fecha de fin">

                @if (request()->input('search_hasta'))
                    <a href="{{ route('presupuestos.index') }}" class="btn btn-secondary">×</a>
                @endif

                <!-- Botón de Buscar -->
                <button style="margin-left: 5px;" class="btn btn-primary rounded" type="submit">Buscar</button>

                <!-- Separador flexible que empuja el botón de Exportar hacia la derecha -->
                <div class="ml-auto"></div>

                <!-- Botón de Exportar -->
                
            </div>

        </form>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <p class="total-registros">Total de presupuestos: {{ $presupuestos->count() }}</p>

        <div class="table-responsive" style="overflow: hidden !important;">
    <table class="table table-bordered" id="tabla_presupuestos" style="max-width: 100%;">
        <thead class="table-dark">
            <tr>
                <th style="width: 5%;">N°</th>
                <th class="paciente">Paciente</th>
                <th class="paciente">HC</th>
                <th class="servicio">Medico Tratante</th>
                <th style="width: 10%;">Fecha</th>
                <th style="width: 10%;">Estado</th>
                <th>Detalle</th>
                <th style="width: 12%;">Total Presupuesto</th>
                <th>Obra Social</th>
                <th style="width: 15%;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($presupuestos as $presupuesto)
                <tr>
                    <td>{{ $presupuesto->id }}</td>
                    <td class="paciente">{{ $presupuesto->paciente }}</td>
                    <td class="paciente">{{ $presupuesto->paciente_salutte_id }}</td>
                    <td class="servicio">{{ $presupuesto->medico_tratante }}</td>
                    <td>{{ \Carbon\Carbon::parse($presupuesto->fecha)->format('d/m/Y') }}</td>
                    <td class="estado">
                        <span class="badge {{ $presupuesto->estado == 9 ? 'bg-success' : 'bg-secondary' }}">
                            {{ Estado::find($presupuesto->estado)->nombre ?? "Estado no asignado" }}
                        </span>
                    </td>
                    <td class="detalle">{{ $presupuesto->detalle }}</td>
                    <td class="text-end">
                        @if($presupuesto->estado == 9)
                            ${{ number_format($presupuesto->total_presupuesto, 0, ',', '.') }}<br>
                            <small>Aprobado por: ${{ number_format(Presupuestos_aprobados::getAprobadoById($presupuesto->id), 2, ',', '.') }}</small>
                        @else
                            ${{ number_format($presupuesto->total_presupuesto, 0, ',', '.') }}
                        @endif
                    </td>
                    <td>
                        @if (is_numeric($presupuesto->obra_social))
                            {{ ObraSocial::getObraSocialById($presupuesto->obra_social) }}
                        @else
                            {{ $presupuesto->obra_social }}
                        @endif
                    </td>
                    <td class="text-center">
                        @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 2 || Auth::user()->rol_id == 4)
                            <a href="{{ route('presupuestos.firmar', $presupuesto->id) }}" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-check"></i>
                            </a>
                        @endif
                        @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 2)
                            @if($presupuesto->estado != 4 && $presupuesto->estado != 3 && $presupuesto->estado != 9)
                                <a href="{{ route('presupuestos.edit', $presupuesto->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            @endif
                        @endif
                        @if(Auth::user()->rol_id == 3 && ($presupuesto->estado == 8 || $presupuesto->estado == 7))
                            <a href="{{ route('presupuestos.farmacia', $presupuesto->id) }}" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-prescription-bottle-medical"></i>
                            </a>
                        @endif
                        @if(Auth::user()->rol_id == 5 && ($presupuesto->estado == 5 || $presupuesto->estado == 7))
                            <a href="{{ route('presupuestos.anestesia', $presupuesto->id) }}" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-prescription-bottle-medical"></i>
                            </a>
                        @endif
                        
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

        {{ $presupuestos->appends([
    'search_nro_presupuesto' => request()->input('search_nro_presupuesto'),
    'search_paciente' => request()->input('search_paciente'),
    'search_medico_tratante' => request()->input('search_medico_tratante'),
    'search_estado' => request()->input('search_estado'),
    'search_detalle' => request()->input('search_detalle'),
    'search_desde' => request()->input('search_desde'),
    'search_hasta' => request()->input('search_hasta'),
    'search_obra_social' => request()->input('search_obra_social'),
    'search_total_presupuesto' => request()->input('search_total_presupuesto'),
    'page' => request()->input('page')
])->links() }}

    </div>
</x-app-layout>

<style>
    .form-group .input-group {
        max-width: 165px;
    }

    .form-group input {
        border: 1px solid gray;
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }

    .input-group-append {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    .total-registros {
        margin-bottom: 1rem;
    }

    .paciente {
        max-width: 23ch;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .diagnostico,
    .obrasocial,
    .estado,
    .servicio {
        max-width: 23ch;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .btn .fas {
        margin-right: 0;
    }

    .mt-4 {
        margin-top: 1.5rem;
    }

    body {
        overflow-x: hidden;
    }

    
    .paciente, .servicio, .detalle {
        max-width: 20ch;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .table-responsive {
        margin: 20px 0;
    }
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
    }
    .table td.text-end {
        text-align: right;
    }
    .badge {
        padding: 0.5em 0.75em;
    }

</style>
<script>
    $(document).ready(function () {
        $("#tabla_presupuestos").DataTable({
            "order": [
                [0, 'desc']
            ],
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            "pageLength": 20, // Establecer paginación por 20
            "searching": false, // Quitar la barra de búsqueda
            "language": {
                "decimal": "",
                "emptyTable": "No hay datos disponibles en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
                "infoFiltered": "(filtrado de _MAX_ entradas totales)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": " _MENU_ ",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "zeroRecords": "No se encontraron registros coincidentes",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": ": activar para ordenar la columna ascendente",
                    "sortDescending": ": activar para ordenar la columna descendente"
                }
            }
        });
    })
</script>