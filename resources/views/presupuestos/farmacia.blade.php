@php
    use App\Models\ObraSocial;
    use App\Models\Convenio;
    use App\Models\Prestacion;
@endphp

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />


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
                        <a href="{{ Storage::url($archivo->file_path) }}" class="text-blue-500 hover:underline" download>
                            Descargar {{ basename($archivo->file_path) }}
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
                        <th class="fixed-width border px-4 py-2 text-center"> {{$presupuesto->especialidad }}</th>
                        <th class="border px-4 py-2 text-center">MÓDULO TOTAL (A)</th>
                    </tr>
                </thead>
                <tbody id="prestacionesBody">
                    @foreach($prestaciones as $prestacion)
                        <input type="hidden" id="prestacion_id" name="prestacion_id_{{ $loop->iteration }}"
                            value="{{$prestacion->id}}">
                        <tr class="original-prestacion">
                            <td class="border px-4 py-2 text-center">
                                <input class="w-full text-center bg-gray-100 text-gray-500" name="codigo_{{ $loop->iteration }}"
                                    value="{{ $prestacion->codigo_prestacion }}" />
                            </td>
                            <td class="border px-4 py-2 text-center">
                                <input class="w-full text-center bg-gray-100 text-gray-500" name="prestacion_{{ $loop->iteration }}"
                                    value="{{ $prestacion->nombre_prestacion ?? Prestacion::getPrestacionById($prestacion->prestacion_salutte_id) }}" />
                            </td>
                            <td class="border px-4 py-2 text-center">
                                <input class="w-full text-center bg-gray-100 text-gray-500 moduloTotal" name="modulo_total_{{ $loop->iteration }}"
                                    value="{{ $prestacion->modulo_total }}" oninput="updateTotalPresupuesto()" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="button" id="addPrestacionBtn" class="btn btn-success text-white rounded-full">
                <i class="fas fa-plus"></i> Prestación
            </button>
            <button type="button" id="removePrestacionBtn" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Eliminar Última
                Prestación</button>


            @if (count($anestesias) > 0)
                <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>
                @foreach($anestesias as $anestesia)
                    <input type="hidden" name="precio_anestesia{{ $loop->iteration }}" value="{{$anestesia->precio}}"
                        class="border-none w-auto" oninput="updateTotalPresupuesto()" disabled>
                @endforeach
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let prestacionCount = {{ count($prestaciones) }};
    let addedPrestaciones = 0;
    let edad;
    updateTotalPresupuesto();

    function updateTotalPresupuesto() {
        let totalPresupuesto = 0;
        let totalAnestesia = 0;

        // Sumar todos los valores de los campos de presupuesto
        $('input[name^="modulo_total_"]').each(function () {
            let value = parseFloat($(this).val()) || 0;
            totalPresupuesto += value;
        });

        // Sumar los precios de anestesia
        $('input[name^="precio_anestesia"]').each(function () {
            let value = parseFloat($(this).val()) || 0;
            totalAnestesia += value;
        });
        if (edad < 3 || edad > 65) {
            totalAnestesia = totalAnestesia * 1.2;

            // Mostrar el label oculto
            document.getElementById('adicional_anestesia').style.display = 'block';
        } else {
            // Ocultar el label si no se cumple la condición
            document.getElementById('adicional_anestesia').style.display = 'none';
        }

        // Sumar total de presupuesto y anestesia
        let totalGeneral = totalPresupuesto + totalAnestesia;

        // Actualizar el campo total_presupuesto con la suma total
        $('#total_presupuesto').val(totalGeneral.toFixed(2));

        // Actualizar el campo total_anestesia con el total calculado para anestesias
        $('#total_anestesia').val(totalAnestesia.toFixed(2));
    }

    $(document).ready(function () {

        $('#addPrestacionBtn').on('click', function () {
            prestacionCount++; // Incrementar el contador de prestaciones
            addedPrestaciones++; // Contar las prestaciones agregadas

            // Crear nueva fila con inputs
            let newRow = `
            <tr class="added-prestacion">
                <td class="border px-4 py-2 text-center">
                    <input class="w-full text-center" name="codigo_${prestacionCount}" />
                </td>
                <td class="border px-4 py-2 text-center">
                    <input class="w-full text-center" name="prestacion_${prestacionCount}" />
                </td>
                <td class="border px-4 py-2 text-center">
                    <input class="w-full text-center moduloTotal" name="modulo_total_${prestacionCount}" oninput="updateTotalPresupuesto()" />
                </td>
            </tr>
        `;

            // Añadir la nueva fila al cuerpo de la tabla
            $('#prestacionesBody').append(newRow);
        });

        $('#removePrestacionBtn').on('click', function () {
            if (addedPrestaciones > 0) { // Verificar si hay filas añadidas que se puedan eliminar
                $('#prestacionesBody').find('tr.added-prestacion').last().remove(); // Eliminar la última fila agregada
                addedPrestaciones--; // Reducir el contador de prestaciones añadidas
                updateTotalPresupuesto(); // Actualizar el total del presupuesto
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'No hay prestaciones agregadas que se puedan eliminar.'
                });
            }
        });


        /*$('#add-row111').click(function () {

            var rowCount = $('#table-auto tbody tr').length + 1;
            console.log(rowCount);
            var newRow = `<tr data-row="${rowCount}">
                    <td class="border px-4 py-2 text-center">
                        <button type="button" class="bg-red-500 text-white px-2 py-1 rounded remove-row">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                    <td class="border px-4 py-2">
                        <input type="text" name="codigo_${rowCount}" class="border w-full text-center">
                    </td>
                    <td class="border px-4 py-2">
                        <input name="prestacion_${rowCount}" class="fixed-width border"></input>
                    </td>
                    <td class="border px-4 py-2 text-right">
                        <input type="number" name="modulo_total_${rowCount}" class="border w-full text-right">
                    </td>
                    </tr>`;

            // Agregar la nueva fila a la tabla
            $('#table-auto tbody').append(newRow);

            // Inicializar select2 para el nuevo select


            // Actualizar el total después de agregar una fila
            updateTotalPresupuesto();

            // Agregar el evento de cambio para los nuevos inputs
            $('#table-auto').on('input', 'input[name^="modulo_total_"]', updateTotalPresupuesto);
        });

        // Agregar el evento de cambio para los inputs existentes
        $('#presupuesto-table').on('input', 'input[name^="modulo_total_"]', updateTotalPresupuesto);

        // Manejador para eliminar filas
        $('#presupuesto-table').on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
            updateTotalPresupuesto(); // Actualizar el total después de eliminar una fila
        });*/
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
</style>