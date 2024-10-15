@php
use App\Models\ObraSocial;
use App\Models\Convenio;
use App\Models\Prestacion;
@endphp

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



<x-app-layout>
    <x-slot name="title">Farmacia</x-slot>

    <form method="POST" action="{{ route('presupuestos.updateFarmacia', $presupuesto->id) }}"
        class="w-full mx-auto p-6 bg-white shadow-md rounded-lg" enctype="multipart/form-data">
        @csrf

        <input type="hidden" id="presupuesto_id" name="presupuesto_id" value="{{$id}}">

        <h1 class="text-2xl font-bold mb-6">FARMACIA</h1>

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

        <div class="flex justify-between mb-4">
            <div>
                <label for="fecha" class="font-semibold">FECHA:</label>
                <input type="date" id="fecha" name="fecha" class="border rounded p-2 w-full"
                    value="{{ $presupuesto->fecha }}" required readonly>
            </div>
            <div>
                <label for="medico_tratante" class="font-semibold">MEDICO TRATANTE:</label>
                <input type="text" id="medico_tratante" name="medico_tratante" class="border rounded p-2 w-full"
                    value="{{$presupuesto->medico_tratante}}" disabled>
            </div>
        </div>
        <div class="mb-4">

            <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>




            <p></p>
            <p></p>
            <h2 class="text-lg font-semibold mb-2">PRESTACIONES</h2>
            <p></p>

            <table class="table-auto w-full mb-4" id="prestacionesTable">
                <thead>
                    <tr>
                        <th class="codigo border px-4 py-2 text-center">CÓDIGO</th>
                        <th class="fixed-width border px-4 py-2 text-center"> DETALLE</th>
                        <th class="border px-4 py-2 text-center">MÓDULO TOTAL (A)</th>
                    </tr>
                </thead>
                <tbody id="prestacionesBody">
                    @foreach($prestaciones as $prestacion)
                    <input type="hidden" id="prestacion_id" name="prestacion_id_{{ $loop->iteration }}"
                        value="{{$prestacion->id}}">
                    <tr class="original-prestacion">
                        <td class="border px-4 py-2 text-center">
                            <input class="w-full border h-10 text-center bg-gray-100 text-gray-500"
                                name="codigo_{{ $loop->iteration }}" value="{{ $prestacion->codigo_prestacion }}"
                                readonly />
                        </td>
                        <td class="border px-4 py-2 text-center" style="max-width: 500px">
                            <input class="w-full border h-10 text-center bg-gray-100 text-gray-500"
                                name="prestacion_{{ $loop->iteration }}"
                                value="{{ $prestacion->nombre_prestacion ?? Prestacion::getPrestacionById($prestacion->prestacion_salutte_id) }}"
                                Readonly />
                        </td>
                        <td class="border px-4 py-2 text-center">
                            <input class="w-full border h-10 text-center bg-gray-100 text-gray-500 moduloTotal"
                                name="modulo_total_{{ $loop->iteration }}" value="{{ $prestacion->modulo_total }}"
                                oninput="updateTotalPresupuesto()" readonly />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="button" id="addPrestacionBtn" class="btn btn-success text-white rounded-full">
                <i class="fas fa-plus"></i> Prestación
            </button>
            <button type="button" id="removePrestacionBtn" class="btn btn-danger"><i class="fa-solid fa-trash"></i>
                Eliminar Última
                Prestación</button>


            @if (count($anestesias) > 0)
            <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>
            <h2 class="text-lg font-semibold mb-2">ANESTESIA</h2>
            <table class="table-auto w-auto mb-4" id="anestesia-table" style="margin-left: 30%">
                <thead>
                    <th class="border px-4 py-2 text-center">Complejidad</th>
                    <th class="border px-4 py-2 text-center">Precio</th>
                    <th class="border px-4 py-2 text-center">Tipo</th>
                </thead>
                <tbody id="anestesia-body">
                    @foreach($anestesias as $anestesia)
                    <input type="hidden" name="anestesia{{$loop->iteration}}" value="{{$anestesia->id}}">
                    <tr>
                        <td class="border px-4 py-2">
                            <input type="text" name="complejidad{{ $loop->iteration }}"
                                value="{{$anestesia->complejidad}}" class="border h-10 text-center w-full bg-gray-100 text-gray-500" readonly>
                        </td>
                        <td class="border px-4 py-2">
                            <input type="text" name="precio_anestesia{{ $loop->iteration }}"
                                value="{{$anestesia->precio}}" class="border h-10 text-center w-full bg-gray-100 text-gray-500"
                                oninput="updateTotalPresupuesto()" readonly>
                        </td>
                        <td class="border px-4 py-2">
                            <select type="text" name="anestesia_id{{ $loop->iteration }}"
                                class="border text-center w-full h-10 bg-gray-100 text-gray-500" style="min-width: 200px;" disabled>
                                <option value="0" {{ $anestesia->anestesia_id == 0 ? 'selected' : '' }}>Sin especificar
                                </option>
                                <option value="1" {{ $anestesia->anestesia_id == 1 ? 'selected' : '' }}>Local</option>
                                <option value="2" {{ $anestesia->anestesia_id == 2 ? 'selected' : '' }}>Periférica
                                </option>
                                <option value="3" {{ $anestesia->anestesia_id == 3 ? 'selected' : '' }}>Central</option>
                                <option value="4" {{ $anestesia->anestesia_id == 4 ? 'selected' : '' }}>Total</option>
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            </tbody>
            <label id="adicional_anestesia" style="display: none; color: red;">*20% de recargo por anestesia*</label>
            <label for="total_anestesia" class="font-semibold">TOTAL ANESTESIA: $</label>
            <input type="float" id="total_anestesia" name="total_anestesia"
                class="border rounded p-2 w-auto ml-1 text-center" value="" disabled>
            @endif






            <div class="mb-6 mt-2">
                <label for="total_presupuesto" class="font-semibold">TOTAL PRESUPUESTO: $</label>
                <input type="text" id="total_presupuesto" name="total_presupuesto"
                    class="border rounded p-2 w-auto ml-1 text-center bg-gray-100 text-black-500"
                    value="{{$presupuesto->total_presupuesto}}" readonly>
            </div>


            <button type="submit" class="btn btn-primary">
                Guardar Presupuesto
            </button>

    </form>

    <form class="mt-2" method="GET" action="{{ route('presupuestos.index') }}">
        @csrf

        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-arrow-left"></i>
            Volver
        </button>
    </form>

</x-app-layout>


<script>
    let convenioId = {!! json_encode($presupuesto->convenio) !!};

    let prestacionCount = {{ count($prestaciones) }};

    let addedPrestaciones = 0;
    let edad;

    updateTotalPresupuesto();

    function updateTotalPresupuesto() {
        let totalPresupuesto = 0;
        let totalAnestesia = 0;

        // Sumar todos los valores de los campos de presupuesto
        $('input[name^="modulo_total_"]').each(function() {
            let value = parseFloat($(this).val()) || 0;
            totalPresupuesto += value;
        });

        // Sumar los precios de anestesia

        $('input[name^="precio_anestesia"]').each(function() {
            let value = parseFloat($(this).val()) || 0;
            console.log(value);
            totalAnestesia += value;
        });


        if (edad < 3 || edad > 65) {
            totalAnestesia = totalAnestesia * 1.2;

            // Mostrar el label oculto

            document.getElementById('adicional_anestesia').style.display = 'block';
        }

        // Sumar total de presupuesto y anestesia
        let totalGeneral = totalPresupuesto + totalAnestesia;

        // Actualizar el campo total_presupuesto con la suma total
        $('#total_presupuesto').val(totalGeneral.toFixed(2));

        // Actualizar el campo total_anestesia con el total calculado para anestesias
        $('#total_anestesia').val(totalAnestesia.toFixed(2));
    }

    $(document).ready(function() {

        $('#addPrestacionBtn').on('click', function() {
            prestacionCount++; // Incrementar el contador de prestaciones
            addedPrestaciones++; // Contar las prestaciones agregadas
            console.log(prestacionCount);

            // Crear nueva fila con inputs
            let newRow = `
            <tr data-row="${prestacionCount}" class="added-prestacion">
                <td class="border px-4 py-2 text-center">
                    <input class="w-full border h-10 text-center" name="codigo_${prestacionCount}" />
                </td>
                <td class="border px-4 py-2" style="max-width: 500px">
                    <select name="prestacion_${prestacionCount}" class="border border h-10 w-full text-center prestacion-select"></select>
                </td>
                <td class="border px-4 py-2 text-center">
                    <input class="w-full border h-10 text-center moduloTotal" name="modulo_total_${prestacionCount}" oninput="updateTotalPresupuesto()" />
                </td>
            </tr>
        `;

            // Añadir la nueva fila al cuerpo de la tabla
            $('#prestacionesBody').append(newRow);

            var selectElement = $(`select[name="prestacion_${prestacionCount}"]`);
            initializeSelect2(selectElement);

            // Supongo que tienes un select para el convenio
            console.log(convenioId);
            loadPrestaciones(convenioId, selectElement);
        });

        $('#removePrestacionBtn').on('click', function() {
            if (addedPrestaciones > 0) { // Verificar si hay filas añadidas que se puedan eliminar
                $('#prestacionesBody').find('tr.added-prestacion').last().remove(); // Eliminar la última fila agregada
                addedPrestaciones--; // Reducir el contador de prestaciones añadidas
                prestacionCount--;
                updateTotalPresupuesto(); // Actualizar el total del presupuesto
            console.log(addedPrestaciones);

            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'No hay prestaciones agregadas que se puedan eliminar.'
                });
            }
        });

        function initializeSelect2(selectElement) {

            selectElement.select2({
                tags: true, // Esto permite que el usuario edite las opciones.
                placeholder: 'Seleccione una prestación',
                allowClear: true,
                width: '100%', // Para ajustar el ancho
                language: {
                    noResults: function() {
                        return "Seleccione un convenio.";
                    }
                }
            });
        }

        function loadPrestaciones(convenioId, selectElement) {
            console.log(convenioId + ' aaaaaaaa');
            $.ajax({
                url: '{{ url("/getPrestaciones") }}/' + convenioId,
                type: 'GET',
                success: function(data) {
                    selectElement.empty();
                    selectElement.append('<option value="">Seleccione Prestación</option>');
                    $.each(data, function(key, value) {
                        selectElement.append('<option value="' + value.prestacionid + '" data-codigo="' + value.prestacioncodigo + '" data-nombre="' + value.prestacionnombre + '">' + value.prestacionnombre + '</option>');
                    });
                }
            });
        }

        $(document).on('change', '.prestacion-select', function() {
            var selectedOption = $(this).find('option:selected');
            var codigo = selectedOption.data('codigo');
            $(this).closest('tr').find('input[name^="codigo"]').val(codigo);
        });
    });

    $(document).on('change', '.prestacion-select', function() {
        let selectedOption = $(this).find('option:selected');
        let codigoPrestacion = selectedOption.data('codigo');
        let prestacionCount = $(this).closest('tr').data('row'); // Obtenemos el rowCount correcto
        $('#codigo_' + prestacionCount).val(codigoPrestacion);
        let prestacionId = selectedOption.val();

        
        console.log('prestacionCount ', prestacionCount);

        console.log(convenioId, codigoPrestacion);

        $.ajax({
            url: '{{ url("/obtenerPrecio") }}/' + convenioId + '/' + codigoPrestacion,
            method: 'GET',
            success: function(response) {
                console.log('obtenerprecioooo');
                let precio = parseFloat(response[0].PRECIO);
                // Truncar el precio eliminando la parte decimal
                let precioTruncado = Math.floor(precio); // También puedes usar parseInt(precio)
                console.log(precioTruncado);
                console.log("rowCount:", prestacionCount);

                $('input[name="modulo_total_' + prestacionCount + '"]').val(precioTruncado);
                updateTotalPresupuesto();
            },
            error: function(xhr, status, error) {
                console.error('Error en la consulta AJAX:', error);

            }
        });
    });
</script>

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #4caf50;
    }

    input:checked+.slider:before {
        transform: translateX(26px);
    }

    .form-row {
        display: flex;
        align-items: center;
        /* Alinea verticalmente en el centro */
        gap: 20px;
        /* Espacio entre los elementos */
    }

    .form-groupp {
        display: flex;
        flex-direction: column;
        /* Alinea el label y el input en columna */
        flex: 1;
        /* Permite que ambos elementos ocupen espacio igual */
    }

    .form-groupp label {
        margin-bottom: 5px;
        /* Espacio entre el label y el input */
    }

    .select2-container .select2-selection--single {
        border: 1px solid #d1d5db;
        /* Aplica el mismo borde que los inputs */
        height: 38px;
        /* Altura similar a los inputs */
        padding: 0.375rem 0.75rem;
        /* Espaciado interno similar */
        border-radius: 0.25rem;
        /* Iguala el borde redondeado */
        box-sizing: border-box;
        /* Asegura que los estilos sean consistentes */
    }

    /* Asegura que el select no tenga un icono de dropdown desalineado */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        /* Ajusta la altura del icono del dropdown */
    }

    /* Elimina el fondo azul al enfocar */
    .select2-container--default .select2-selection--single:focus {
        outline: none;
        box-shadow: none;
        border-color: #d1d5db;
    }

    /* Asegura que el texto esté alineado verticalmente */
    .select2-selection__rendered {
        line-height: 38px;
        /* Igualar la altura */
    }
</style>