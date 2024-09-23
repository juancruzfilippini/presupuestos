<head>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
@php
    use App\Models\ObraSocial;
    use App\Models\Estado;
@endphp

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap 4 y 5 (Elige una versión, usualmente no se usan ambas juntas) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>


<!-- jQuery UI -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<!-- ChartJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>

<!-- Sparkline -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sparklines/0.4.1/sparkline.js"></script>

<!-- JQVMap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jquery.vmap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/maps/jquery.vmap.usa.js"></script>

<!-- jQuery Knob Chart -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-knob/1.2.13/jquery.knob.min.js"></script>

<!-- Daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/daterangepicker/3.1.0/daterangepicker.js"></script>

<!-- Tempusdominus Bootstrap 4 -->
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js">
    </script>

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
    <x-slot name="title">Presupuestos</x-slot>
    @section('title', 'Presupuestos')
    <div class="mt-4">
        <h1>Presupuestos</h1>

        <form method="GET" action="{{ route('presupuestos.index') }}" class="mb-4">
            <!-- Aquí puedes agregar los campos de búsqueda si es necesario -->
        </form>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <p class="total-registros">Total de registros: {{ $presupuestos->count() }}</p>

        <div class="table-responsive">
            <table class="table table-bordered" id="tabla_presupuestos">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th class="paciente" style="">Paciente</th>
                        <th class="servicio" style="">Medico Tratante</th>
                        <th class="" style="width: 5%;">Fecha</th>
                        <th class="" style="width: 5%;">Estado</th>
                        <th style="width: 5%;">Detalle</th>
                        <th class="estado" style="width: 5%;">Total Presupuesto</th>
                        <th class="" style="width: 10%;">Obra Social</th>
                        <th class="" style="width: 2%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($presupuestos as $presupuesto)
                        <tr>
                            <td style="width: 5%;">{{ $presupuesto->id }}</td>
                            <td class="">{{ $presupuesto->paciente }}</td>
                            <td class="">{{ $presupuesto->medico_tratante}}</td>
                            <td class="">{{ \Carbon\Carbon::parse($presupuesto->fecha)->format('d/m/Y') }}</td>
                            <td class="">{{ Estado::find($presupuesto->estado)->nombre ?? "Estado no asignado" }}</td>
                            <td>{{ $presupuesto->detalle }}</td>
                            <td class="">${{ number_format($presupuesto->total_presupuesto, 0, ',', '.') }}</td>
                            <td class="" style="">
                                @if (is_numeric($presupuesto->obra_social))
                                    {{ ObraSocial::getObraSocialById($presupuesto->obra_social) }}
                                @else
                                    {{ $presupuesto->obra_social }}
                                @endif
                            </td>

                            <td>
                                @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 2)
                                    @if($presupuesto->estado != 4)
                                        <a href="{{ route('presupuestos.edit', $presupuesto->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    @endif
                                @endif
                                @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 2 || Auth::user()->rol_id == 4)
                                    <a href="{{ route('presupuestos.firmar', $presupuesto->id) }}"
                                        class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-check"></i>
                                    </a>
                                @endif
                                @if(Auth::user()->rol_id == 3 && ($presupuesto->estado == 8 || $presupuesto->estado == 7))
                                    <a href="{{ route('presupuestos.farmacia', $presupuesto->id) }}"
                                        class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-prescription-bottle-medical"></i>
                                    </a>
                                @endif
                                @if(Auth::user()->rol_id == 5 && ($presupuesto->estado == 5 || $presupuesto->estado == 7))
                                    <a href="{{ route('presupuestos.anestesia', $presupuesto->id) }}"
                                        class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-prescription-bottle-medical"></i>
                                    </a>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>


<style>
    .form-group .input-group {
        max-width: 165px;
        /* Ajusta el ancho máximo del input */
    }

    .form-group input {
        border: 1px solid gray;
        border-top-left-radius: 5px;
        /* Bordes redondeados superior izquierdo */
        border-bottom-left-radius: 5px;
        /* Bordes redondeados inferior izquierdo */
    }

    .input-group-append {
        border-top-right-radius: 5px;
        /* Bordes redondeados superior derecho */
        border-bottom-right-radius: 5px;
        /* Bordes redondeados inferior derecho */
    }

    /* Estilo para el texto del número de registros */
    .total-registros {
        margin-bottom: 1rem;
        /* Espaciado por debajo del texto */
    }

    /* Estilo para los textos dentro de la tabla */
    .paciente {
        max-width: 23ch;
        /* Ajusta el ancho máximo de las celdas según sea necesario */
        overflow: hidden;
        /* Oculta el texto desbordado */
        text-overflow: ellipsis;
        /* Añade puntos suspensivos para el texto desbordado */
        white-space: nowrap;
        /* Evita el ajuste de texto en las celdas */
    }

    .diagnostico,
    .obrasocial,
    .estado,
    .servicio {
        max-width: 23ch;
        /* Ajusta el ancho máximo de las celdas según sea necesario */
        overflow: hidden;
        /* Oculta el texto desbordado */
        text-overflow: ellipsis;
        /* Añade puntos suspensivos para el texto desbordado */
        white-space: nowrap;
        /* Evita el ajuste de texto en las celdas */
    }

    /* Botones */
    .btn .fas {
        margin-right: 0;
    }

    /* Margen superior */
    .mt-4 {
        margin-top: 1.5rem;
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