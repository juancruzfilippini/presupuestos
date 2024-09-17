@php
    use App\Models\ObraSocial;
    use App\Models\Convenio;
    use App\Models\Prestacion;
@endphp

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


<x-app-layout>
    <x-slot name="title">Farmacia</x-slot>

    <form method="POST" action="{{ route('presupuestos.updateFarmacia', $presupuesto->id) }}"
        class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg" enctype="multipart/form-data">
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

            <table class="table-auto w-full mb-4">
                <thead>
                    <tr>
                        <th class="codigo border px-4 py-2 text-center">CÓDIGO</th>
                        <th class="fixed-width border px-4 py-2 text-center"> {{$presupuesto->especialidad }}</th>
                        <th class="border px-4 py-2 text-center">MÓDULO TOTAL (A)</th>
                    </tr>

                </thead>
                <tbody>

                    @foreach($prestaciones as $prestacion)
                        <input type="hidden" id="prestacion_id" name="prestacion_id_{{ $loop->iteration }}"
                            value="{{$prestacion->id}}">

                        <tr>
                            <td class="border px-4 py-2 text-center">
                                <input class="w-full text-center" name="codigo_{{ $loop->iteration }}"
                                    value="{{ $prestacion->codigo_prestacion }}" />
                            </td>
                            @if(is_null($prestacion->nombre_prestacion))
                                <td class="border px-4 py-2 text-center">
                                    <input class="w-full text-center" name="prestacion_{{ $loop->iteration }}"
                                        value="{{ Prestacion::getPrestacionById($prestacion->prestacion_salutte_id) }}" />
                                </td>
                            @else
                                <td class="border px-4 py-2 text-center">
                                    <input class="w-full text-center" name="prestacion_{{ $loop->iteration }}"
                                        value="{{ $prestacion->nombre_prestacion }}" />
                                </td>
                            @endif
                            <td class="border px-4 py-2 text-center">
                                <input class="w-full text-center" name="modulo_total_{{ $loop->iteration }}"
                                    value="{{ $prestacion->modulo_total }}" oninput="updateTotalPresupuesto()" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


            @if (count($anestesias) > 0)
                <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>
                @foreach($anestesias as $anestesia)
                    <input type="hidden" name="precio_anestesia{{ $loop->iteration }}" value="{{$anestesia->precio}}"
                        class="border-none w-full" oninput="updateTotalPresupuesto()" disabled>
                @endforeach
                </tbody>
                <label id="adicional_anestesia" style="display: none; color: red;">*20% de recargo por anestesia*</label>
                <label for="total_anestesia" class="font-semibold">TOTAL ANESTESIA: $</label>
                <input type="float" id="total_anestesia" name="total_anestesia"
                    class="border rounded p-2 w-2 ml-1 text-center" value="" disabled>
            @endif






            <div class="mb-6">
                <br>
                <label for="total_presupuesto" class="font-semibold">TOTAL PRESUPUESTO: $</label>
                <input type="float" id="total_presupuesto" name="total_presupuesto"
                    class="border rounded p-2 w-2 ml-1 text-center" value="{{$presupuesto->total_presupuesto}}"
                    readonly>
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

    let edad;
    updateEdad({{ $presupuesto->edad }});
    console.log({{$presupuesto->edad}});
    updateTotalPresupuesto();

    function updateEdad($edad) {
        edad = $edad;
        console.log(edad);
    }


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


    function confirmDelete(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }



    $('#search-input').keypress(function (e) {
        if (e.which === 13) { // 13 es el código de la tecla Enter
            e.preventDefault(); // Evitar el comportamiento predeterminado de la tecla Enter
            $('#search-button').click(); // Llamar a la función de búsqueda
        }
    });

    function calcularEdad(fechaNacimiento) {
        const hoy = new Date();
        const nacimiento = new Date(fechaNacimiento);
        let edad = hoy.getFullYear() - nacimiento.getFullYear();
        const mes = hoy.getMonth() - nacimiento.getMonth();

        // Ajustar si la fecha de nacimiento aún no ha pasado este año
        if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
            edad--;
        }

        return edad;
    }

    $('#search-button').click(function (e) {
        e.preventDefault();
        var searchTerm = $('#search-input').val().trim();
        if (!searchTerm) {
            $('#results').html('<p>Por favor, ingrese un término de búsqueda.</p>');
            return;
        }

        $.ajax({
            url: '{{ route('presupuestos.searchPatient') }}',
            method: 'GET',
            data: { search: searchTerm },
            success: function (data) {
                var resultHtml = '';
                if (data.length > 0) {
                    data.forEach(function (patient) {

                        const fechaNacimiento = patient.fecha_nacimiento; // Asumiendo que esto está en formato 'YYYY-MM-DD'
                        edad = calcularEdad(fechaNacimiento);

                        resultHtml += '<div>';
                        resultHtml += '<p><strong>HC Electrónica:</strong> ' + patient.id + '</p>';
                        resultHtml += '<p><strong>Nombre:</strong> ' + patient.nombres + ' ' + patient.apellidos + '</p>';
                        resultHtml += '<p><strong>DNI:</strong> ' + patient.documento + '</p>';
                        resultHtml += '<p><strong>Fecha de Nacimiento:</strong> ' + patient.fecha_nacimiento + '</p>';
                        resultHtml += '<p><strong>Edad:</strong> ' + edad + ' años</p>';
                        resultHtml += '<button type="button" class="btn btn-primary select-button" data-edad="' + edad + '" data-id="' + patient.id + '" data-name="' + patient.nombres + ' ' + patient.apellidos + '">Seleccionar</button>';
                        resultHtml += '</div><hr>';
                    });
                } else {
                    resultHtml = '<p>No se encontraron resultados.</p>';
                }
                $('#results').html(resultHtml);
            },
            error: function (xhr, status, error) {
                var errorMessage = 'Error al buscar datos.';
                if (xhr.status === 404) {
                    errorMessage = 'La ruta no fue encontrada.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Error interno del servidor.';
                }
                $('#results').html('<p>' + errorMessage + '</p>');
            }
        });
    });

    $('#results').on('click', '.select-button', function (e) {
        e.preventDefault();
        var selectedName = $(this).data('name');
        var selectedId = $(this).data('id');
        var selectedEdad = $(this).data('edad');
        $('#selected-person').val(selectedName);
        $('#paciente_salutte_id').val(selectedId);
        $('#edad').val(selectedEdad);
        updateTotalPresupuesto();
    });


    document.getElementById('toggleCondicion').addEventListener('change', function () {
        var textareaCondicion = document.getElementById('condicion');
        textareaCondicion.style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('toggleIncluye').addEventListener('change', function () {
        var textareaIncluye = document.getElementById('incluye');
        textareaIncluye.style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('toggleExcluye').addEventListener('change', function () {
        var textareaExcluye = document.getElementById('excluye');
        textareaExcluye.style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('toggleAdicionales').addEventListener('change', function () {
        var textareaAdicionales = document.getElementById('adicionales');
        textareaAdicionales.style.display = this.checked ? 'block' : 'none';
    });

    document.addEventListener('DOMContentLoaded', function () {
        var toggleCondicion = document.getElementById('toggleCondicion');
        var condicionContainer = document.getElementById('condicionContainer');
        var condicionTextarea = document.getElementById('condicion');

        function updateTextareaVisibility() {
            if (toggleCondicion.checked) {
                condicionContainer.style.display = 'block';
                if (!condicionTextarea.value.trim()) {
                    condicionTextarea.value = 'EFECTIVO/TRANSFERENCIA BANCARIA/TARJETA DE DÉBITO/TARJETA DE CRÉDITO';
                }
            } else {
                condicionContainer.style.display = 'none';
                condicionTextarea.value = '';
            }
        }

        toggleCondicion.addEventListener('change', updateTextareaVisibility);
        updateTextareaVisibility(); // Llama a la función al cargar la página para manejar el estado inicial
    });

    document.addEventListener('DOMContentLoaded', function () {
        var toggleIncluye = document.getElementById('toggleIncluye');
        var incluyeContainer = document.getElementById('incluyeContainer');
        var incluyeTextarea = document.getElementById('incluye');

        function updateTextareaVisibility() {
            if (toggleIncluye.checked) {
                incluyeContainer.style.display = 'block';
                if (!incluyeTextarea.value.trim()) {
                    incluyeTextarea.value = '( A ) Módulo: Módulo: Incluye Gastos Hospitalarios: Honorarios Médicos sin anestesistas (A.M.A.). PENSION: Raciones de corresponder, habitación compartida, Enfermería, Antibióticos de primera y segunda generación. Sueros. Descartables Básicos: Jeringas, gasas, algodón, tela adhesiva, vendas, guantes, agujas.\n' +
                        '( B ) Oxígeno: En la actualidad el oxígeno se factura por separado del Módulo y equivale al monto consignado por el período de utilización por hasta cuatro horas de quirófano, de corresponder.';
                }

            } else {
                incluyeContainer.style.display = 'none';
                incluyeTextarea.value = '';
            }
        }

        toggleIncluye.addEventListener('change', updateTextareaVisibility);
        updateTextareaVisibility(); // Llama a la función al cargar la página para manejar el estado inicial
    });

    document.addEventListener('DOMContentLoaded', function () {
        var toggleExcluye = document.getElementById('toggleExcluye');
        var excluyeContainer = document.getElementById('excluyeContainer');
        var excluyeTextarea = document.getElementById('excluye');

        function updateTextareaVisibility() {
            if (toggleExcluye.checked) {
                excluyeContainer.style.display = 'block';
                if (!excluyeTextarea.value.trim()) {
                    excluyeTextarea.value = 'Antibióticos de tercera y cuarta generación, Rx., Ecografías y demás insumos que se encuentran en la ficha de consumo que se facturarán a valores kairos.';
                }

            } else {
                excluyeContainer.style.display = 'none';
                excluyeTextarea.value = '';
            }
        }

        toggleExcluye.addEventListener('change', updateTextareaVisibility);
        updateTextareaVisibility(); // Llama a la función al cargar la página para manejar el estado inicial
    });

    document.addEventListener('DOMContentLoaded', function () {
        var toggleAdicionales = document.getElementById('toggleAdicionales');
        var adicionalesContainer = document.getElementById('adicionalesContainer');
        var adicionalesTextarea = document.getElementById('adicionales');

        function updateTextareaVisibility() {
            if (toggleAdicionales.checked) {
                adicionalesContainer.style.display = 'block';
                if (!adicionalesTextarea.value.trim()) {
                    adicionalesTextarea.value = '*Las prestaciones / insumos / medicación no contempladas en el presente presupuesto, que sean requeridas durante la intervención o recuperación se adicionarán al valor del mismo. \n' +
                        '*  Al momento de realizar su ingreso, se le solicitará   firmar un pagaré.  El mismo le será devuelto a los 30 días de recibir el alta médica. \n' +
                        '*Este presupuesto tiene una validez de 15 días desde la fecha de emisión, con excepción de honorarios de AMA  que serán los vigentes al momento de la cirugía.';
                }

            } else {
                adicionalesContainer.style.display = 'none';
                adicionalesTextarea.value = '';
            }
        }

        toggleAdicionales.addEventListener('change', updateTextareaVisibility);
        updateTextareaVisibility(); // Llama a la función al cargar la página para manejar el estado inicial
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