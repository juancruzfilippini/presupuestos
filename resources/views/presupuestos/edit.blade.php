@php
    use App\Models\ObraSocial;
    use App\Models\Convenio;
    use App\Models\Prestacion;
@endphp

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<x-app-layout>
    <x-slot name="title">Editar Presupuesto</x-slot>

    <form method="POST" action="{{ route('presupuestos.update', $presupuesto->id) }}"
        class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg" enctype="multipart/form-data">
        @csrf

        <input type="hidden" id="presupuesto_id" name="presupuesto_id" value="{{$id}}">

        <h1 class="text-2xl font-bold mb-6">EDITAR PRESUPUESTO</h1>

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
                <label for="medico_solicitante" class="font-semibold">MEDICO SOLICITANTE:</label>
                <input type="text" id="medico_solicitante" name="medico_solicitante" class="border rounded p-2 w-full"
                    value="{{$presupuesto->medico_solicitante}}">
            </div>
            <div>
                <label for="medico_tratante" class="font-semibold">MEDICO TRATANTE:</label>
                <input type="text" id="medico_tratante" name="medico_tratante" class="border rounded p-2 w-full"
                    value="{{$presupuesto->medico_tratante}}">
            </div>
        </div>
        <div class="mb-4">
            <h2 class="text-lg font-semibold mb-2">CAMBIAR PACIENTE</h2>

            <div class="form-group">
                <label for="search-input">DNI: </label>
                <div class="input-group">
                    <input type="text" id="search-input" name="documento" class="form-control"
                        placeholder="Ingrese DNI del paciente">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary" id="search-button">
                            Buscar
                        </button>
                    </div>
                </div>
            </div>
            <p></p>

            <div id="results"></div>
            <p></p>
            <input type="text" id="selected-person" name="paciente" class="form-control"
                value="{{$presupuesto->paciente}}" readonly>
            <input type="hidden" id="paciente_salutte_id" name="paciente_salutte_id" value="">
            <p> </p>
            <p> </p>

            <div id="results"></div>


            <div class="d-flex justify-content-between align-items-center">
                <label for="search-input" class="p-2 font-semibold">Obra Social:
                    @if(is_numeric($presupuesto->obra_social))
                        <input class="form-control" id="input_obrasocial" name="input_obrasocial" type="text"
                            value="{{ ObraSocial::getObraSocialById($presupuesto->obra_social) }}" readonly>
                    @else
                        <input class="form-control" id="input_obrasocial" name="input_obrasocial" type="text"
                            value="{{ $presupuesto->obra_social }}">
                    @endif
                </label>
                <input type="hidden" id="obra_social" name="obra_social" value="">

                @if(is_numeric($presupuesto->convenio))
                    <label for="search-input" class="p-2 font-semibold">Convenio:
                        <input class="form-control" id="convenio" name="convenio" type="text" style="width: 400px;"
                            value="{{ Convenio::getConvenioById($presupuesto->convenio) }}" readonly>
                @else
                    @if (!is_null($presupuesto->convenio))
                        <input class="form-control" id="convenio" name="convenio" type="text" style="width: 400px;"
                            value="{{ $presupuesto->convenio }}" readonly>
                    @else
                        <input type="hidden" id="convenio" name="convenio" value="">
                    @endif
                @endif
                </label>
            </div>

            <p></p>
            <p></p>
            <h2 class="text-lg font-semibold mb-2">PRESTACIONES</h2>

            <input type="text" id="detalle" name="detalle" class="form-control" value="{{ $presupuesto->detalle }}"
                placeholder="{{ empty($presupuesto->detalle) ? 'Asunto: Prestaciones quirúrgicas' : '' }}">


            <p></p>

            <table class="table-auto w-full mb-4">
                <thead>
                    <tr>
                        <th class="codigo border px-4 py-2 text-center">CÓDIGO</th>
                        <th class="fixed-width border px-4 py-2 text-center">
                            <input id="input_especialidad" name="input_especialidad" class="w-full text-center"
                                value="{{ $presupuesto->especialidad }}" />
                        </th>
                        <th class="border px-4 py-2 text-center">MÓDULO TOTAL (A)</th>
                    </tr>
                    <input type="hidden" id="especialidad" name="especialidad" value="">

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



            <div id="anestesia-select" class="">
                <label for="anestesia_id" class="font-semibold">Especificar Anestesia:</label>
                <select id="anestesia_id" name="anestesia_id" class="border rounded" style="width: 250px;">
                    <option value="0" {{ $presupuesto->anestesia_id == 0 ? 'selected' : '' }}>Sin anestesia</option>
                    <option value="1" {{ $presupuesto->anestesia_id == 1 ? 'selected' : '' }}>Local</option>
                    <option value="2" {{ $presupuesto->anestesia_id == 2 ? 'selected' : '' }}>Periférica</option>
                    <option value="3" {{ $presupuesto->anestesia_id == 3 ? 'selected' : '' }}>Central</option>
                    <option value="4" {{ $presupuesto->anestesia_id == 4 ? 'selected' : '' }}>Total</option>
                </select>
            </div>

            <div class="mb-4" style="">
                <table class="table-auto w-2 mb-4" id="anestesia-table" style="margin-left: 30%">
                    <thead>
                        <th class="border px-4 py-2 text-center">Complejidad/Tipo paciente</th>
                        <th class="border px-4 py-2 text-center">Precio</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border px-4 py-2">
                                <input type="text" name="complejidad" id="complejidad" class="border-none w-full"
                                    value="{{$presupuesto->complejidad}}">
                            </td>
                            <td class="border px-4 py-2">
                                <input type="number" name="precio_anestesia" id="precio_anestesia"
                                    class="border-none w-full" value="{{$presupuesto->precio_anestesia}}"
                                    oninput="updateTotalPresupuesto()">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="flex: 1; text-align: right;">
                <label for="detalle_anestesia" class="font-semibold">Detalle:</label>
                <input type="text" id="detalle_anestesia" name="detalle_anestesia" style="width: 400px;" value="{{$presupuesto->detalle_anestesia}}">
            </div>
            </div>


            <div class="mb-6">
                <label for="total_presupuesto" class="font-semibold">TOTAL PRESUPUESTO: $</label>
                <input type="number" id="total_presupuesto" name="total_presupuesto"
                    class="border rounded p-2 w-2 ml-1 text-center" value="{{$presupuesto->total_presupuesto}}"
                    readonly>
            </div>

            <div class="mb-6">
                <div class="items-center">
                    <div class="flex items-center">
                        <label for="condicion" class="font-semibold mr-2">CONDICIÓN DE PAGO:</label>
                        <label class="switch">
                            <input type="checkbox" id="toggleCondicion" name="toggleCondicion" {{ empty($presupuesto->condicion) ? '' : 'checked' }}>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <p></p>

                <div id="condicionContainer" class="mb-6" style="display: none;">
                    <textarea id="condicion" name="condicion" class="border rounded p-2 w-full">
@if (!is_null($presupuesto->condicion)){{$presupuesto->condicion}}@endif 
</textarea>
                </div>

                <div class="mb-6">
                    <div class="items-center">
                        <div class="flex items-center">
                            <label for="incluye" class="font-semibold mr-2">INCLUYE:</label>
                            <label class="switch">
                                <input type="checkbox" id="toggleIncluye" name="toggleIncluye" {{ empty($presupuesto->incluye) ? '' : 'checked' }}>
                                <span class="slider round"></span>
                                <p></p>
                            </label>
                        </div>
                    </div>
                    <p></p>
                    <div id="incluyeContainer" class="mb-6" style="display: none;">
                        <textarea id="incluye" name="incluye" class="border rounded p-2 w-full">
@if (!is_null($presupuesto->incluye)){{$presupuesto->incluye}}@endif
</textarea>
                    </div>
                    <div class="mb-6">
                        <div class="items-center">
                            <div class="flex items-center">
                                <label for="excluye" class="font-semibold mr-2">EXCLUYE:</label>
                                <label class="switch">
                                    <input type="checkbox" id="toggleExcluye" name="toggleExcluye" {{ empty($presupuesto->excluye) ? '' : 'checked' }}>
                                    <span class="slider round"></span>

                                </label>
                            </div>
                        </div>
                        <p></p>
                        <div id="excluyeContainer" class="mb-6" style="display: none;">
                            <p></p>
                            <textarea id="excluye" name="excluye" class="border rounded p-2 w-full">
@if (!is_null($presupuesto->excluye)){{$presupuesto->excluye}}@endif
</textarea>
                        </div>

                        <div class="mb-6">
                            <div class="items-center">
                                <div class="flex items-center">
                                    <label for="adicionales" class="font-semibold mr-2">Adicionales:</label>
                                    <label class="switch">
                                        <input type="checkbox" id="toggleAdicionales" name="toggleAdicionales" {{ empty($presupuesto->adicionales) ? '' : 'checked' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <p></p>
                            <div id="adicionalesContainer" class="mb-6" style="display: none;">
                                <textarea id="adicionales" name="adicionales" class="border rounded p-2 w-full">
@if (!is_null($presupuesto->adicionales)){{$presupuesto->adicionales}}@endif 
</textarea>
                            </div>


                            <div class="mb-6">
                                <button type="submit" class="btn btn-primary">Guardar Presupuesto</button>

                            </div>

    </form>


    <form method="POST" action="{{ route('presupuestos.destroy', $presupuesto->id) }}"
        id="delete-form-{{ $presupuesto->id }}">
        @csrf
        @method('DELETE')
        <button style="margin-top: 50px" type="button" class="btn btn-danger"
            onclick="confirmDelete({{ $presupuesto->id }})">
            Eliminar Presupuesto
        </button>
    </form>


</x-app-layout>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

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

    function updateTotalPresupuesto() {
        let total = 0;

        // Sumar todos los valores de los campos que comienzan con "modulo_total_"
        $('input[name^="modulo_total_"]').each(function () {
            let value = parseFloat($(this).val()) || 0;
            total += value;
        });

        // Obtener el valor del campo precio_anestesia y sumarlo al total
        let precioAnestesia = parseFloat($('#precio_anestesia').val()) || 0;
        total += precioAnestesia;

        // Actualizar el campo total_presupuesto con el total calculado
        $('#total_presupuesto').val(total.toFixed(2));
    }

    $('#search-input').keypress(function(e) {
                if (e.which === 13) { // 13 es el código de la tecla Enter
                    e.preventDefault(); // Evitar el comportamiento predeterminado de la tecla Enter
                    $('#search-button').click(); // Llamar a la función de búsqueda
                }
            });

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
                        resultHtml += '<div>';
                        resultHtml += '<p><strong>HC Electrónica:</strong> ' + patient.id + '</p>';
                        resultHtml += '<p><strong>Nombre:</strong> ' + patient.nombres + ' ' + patient.apellidos + '</p>';
                        resultHtml += '<p><strong>DNI:</strong> ' + patient.documento + '</p>';
                        resultHtml += '<p><strong>Fecha de Nacimiento:</strong> ' + patient.fecha_nacimiento + '</p>';
                        resultHtml += '<button type="button" class="btn btn-primary select-button" data-id="' + patient.id + '" data-name="' + patient.nombres + ' ' + patient.apellidos + '">Seleccionar</button>';
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
        $('#selected-person').val(selectedName);
        $('#paciente_salutte_id').val(selectedId);
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
</style>