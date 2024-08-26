<head>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
@php
    use App\Models\ObraSocial;
@endphp


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
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th class="servicio" style="width: 5%;">Paciente</th>
                        <th class="" style="width: 5%;">Fecha</th>
                        <th class="" style="width: 5%;">Estado</th>
                        <th style="width: 5%;">Especialidad</th>
                        <th class="estado" style="width: 5%;">Total Presupuesto</th>
                        <th class="paciente" style="width: 10%;">Obra Social</th>
                        <th class="" style="width: 2%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($presupuestos as $presupuesto)
                        <tr>
                            <td style="width: 5%;">{{ $presupuesto->id }}</td>
                            <td class="">{{ $presupuesto->paciente }}</td>
                            <td class="">{{ \Carbon\Carbon::parse($presupuesto->fecha)->format('d/m/Y') }}</td>
                            <td class="">{{ $presupuesto->estado }}</td>
                            <td>{{ $presupuesto->especialidad }}</td>
                            <td class="">${{ number_format($presupuesto->total_presupuesto, 0, ',', '.') }}</td>
                            <td class="" style=""> {{ ObraSocial::getObraSocialById($presupuesto->obra_social) }}</td>
                            <td style="width: 2%;" >
                                <a href="{{ route('presupuestos.edit', $presupuesto->id) }}"
                                    class="btn btn-warning btn-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                </a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>


<style>
    /* Ajustes generales para la tabla */
    .table-responsive {
        overflow-x: auto;
        /* Permite desplazamiento horizontal si es necesario */
        width: 100%;
        /* Asegura que la tabla ocupe todo el ancho disponible */
        margin-bottom: 1rem;
        /* Espaciado por debajo de la tabla */
    }

    .table {
        width: 100%;
        /* Asegura que la tabla ocupe todo el ancho del contenedor */
        border-collapse: collapse;
        /* Mejora la apariencia de los bordes */
    }

    .table th,
    .table td {
        white-space: nowrap;
        /* Evita el ajuste de texto en las celdas */
        vertical-align: middle;
        /* Alinea verticalmente el contenido en las celdas */
        padding: 0.75rem;
        /* Espaciado interno de las celdas */
    }

    .table thead th {
        background-color: #f8f9fa;
        /* Color de fondo para el encabezado de la tabla */
        border-bottom: 2px solid #dee2e6;
        /* Borde inferior para el encabezado de la tabla */
    }

    /* Estilo para el texto del número de registros */
    .total-registros {
        margin-bottom: 1rem;
        /* Espaciado por debajo del texto */
    }

    /* Estilo para los textos dentro de la tabla */
    .diagnostico,
    .obrasocial,
    .paciente,
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