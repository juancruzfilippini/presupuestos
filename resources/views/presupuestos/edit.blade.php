@php
    use App\Models\ObraSocial;
    use App\Models\Convenio;
    use App\Models\Prestacion;
@endphp

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

<title>Sistema de Presupuestos</title>
<x-app-layout>
    <x-slot name="title">Editar Presupuesto</x-slot>

    <form method="POST" action="{{ route('presupuestos.update', $presupuesto->id) }}"
        class="w-full mx-auto p-6 bg-white shadow-md rounded-lg" enctype="multipart/form-data">
        @csrf

        <input type="hidden" id="presupuesto_id" name="presupuesto_id" value="{{$id}}">
        <input type="hidden" id="procesoFarmacia" name="procesoFarmacia" value="{{$procesoFarmacia}}">
        <input type="hidden" id="firmaDireccion" name="firmaDireccion" value="{{$firmaDireccion}}">

        <h1 class="text-2xl font-bold mb-6">EDITAR PRESUPUESTO</h1>

        <label for="fecha" class="font-semibold">PEDIDO MÉDICO:</label>
        <p></p>

        @if ($archivos->count() > 0)
            <div class="mb-4">
                <label for="download_file" class="font-semibold">Archivo adjunto:</label>
                @foreach ($archivos as $archivo)
                    <li>
                        <a href="{{ asset('storage/' . $archivo->file_path) }}" target="_blank"
                            class="text-blue-500 hover:underline">
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
                <label for="medico_solicitante" class="font-semibold">MEDICO SOLICITANTE:</label>
                <input type="text" id="medico_solicitante" name="medico_solicitante" class="border rounded p-2 w-full"
                    value="{{$presupuesto->medico_solicitante}}">
            </div>
            <div>
                <label for="medico_tratante" class="font-semibold">MEDICO TRATANTE:</label>
                <select name="medico_tratante" id="medico_tratante" class="border rounded p-2 w-full" required>
                    <option value="">Seleccione un profesional</option>
                    @foreach ($profesionales as $profesional)
                    <option value="{{$profesional->nombre}}" <?= $presupuesto->medico_tratante === "$profesional->nombre" ? 'selected' : '' ?>>{{$profesional->nombre}}</option>
                    @endforeach
                </select>

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
                value="{{$presupuesto->paciente}}" readonly oninput="">
            <input type="hidden" id="paciente_salutte_id" name="paciente_salutte_id" value="">
            <input type="hidden" id="edad" name="edad" value="">
            <p> </p>
            <p> </p>
            <div class="form-row">
                <div class="form-groupp">
                    <label for="telefono" style="margin-top: 5px;">TELEFONO: </label>
                    <input type="text" id="telefono" name="telefono" class="form-control"
                        value="{{$presupuesto->telefono}}">
                </div>
                <div class="form-groupp">
                    <label for="email" style="margin-top: 5px;">EMAIL: </label>
                    <input type="text" id="email" name="email" class="form-control" value="{{$presupuesto->email}}">
                </div>
                <div class="form-groupp">
                    <label for="email" style="margin-top: 5px;">NUMERO DE AFILIADO: </label>
                    <input type="text" id="nro_afiliado" name="nro_afiliado" class="form-control"
                        value="{{$presupuesto->nro_afiliado}}">
                </div>
            </div>
            <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>


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
                        <th class="px-4 py-2 text-center" style="width: 1px; white-space: nowrap"></th>
                        <th class="codigo border px-4 py-2 text-center">CÓDIGO</th>
                        <th class="fixed-width border px-4 py-2 text-center">DETALLE</th>
                        <th class="border px-4 py-2 text-center">MÓDULO TOTAL (A)</th>
                    </tr>
                    <input type="hidden" id="especialidad" name="especialidad" value="">
                </thead>
                <tbody id="prestacionesTableBody">
                    @foreach($prestaciones as $prestacion)
                        <input class="h-10 border" type="hidden" id="prestacion_id"
                            name="prestacion_id_{{ $loop->iteration }}" value="{{ $prestacion->id }}">
                        <input class="h-10 border" type="hidden" id="prestacion_salutte_id"
                            name="prestacion_salutte_id_{{ $loop->iteration }}"
                            value="{{ $prestacion->prestacion_salutte_id }}">

                        <tr data-row="{{ $loop->iteration }}" class="prestacion-row">
                            <td class="px-4 py-2 text-center">
                                <button style="width: 1px; white-space: nowrap" type="button" class="delete-prestacion-btn"
                                    data-id="{{ $prestacion->id }}"><i class="fa fa-trash"></i></button>
                            </td>
                            <td class="border px-4 py-2 text-center">
                                <input class="w-full text-center border h-10" name="codigo_{{ $loop->iteration }}"
                                    value="{{ $prestacion->codigo_prestacion }}" readonly />
                            </td>
                            <td class="border px-4 py-2 text-center">
                                <input name="prestacion_{{ $loop->iteration }}" value="{{ $prestacion->nombre_prestacion }}"
                                    class="w-full text-center border h-10" data-row="{{ $loop->iteration }}">
                                <!-- Las opciones de prestaciones se cargarán con AJAX -->

                                <input type="hidden" name="cantidad_{{ $loop->iteration }}"
                                    value="{{ $prestacion->cantidad }}">
                            </td>
                            <td class="border px-4 py-2 text-center">
                                <div style="display: flex;">
                                    <select name="cantidad_{{ $loop->iteration }}"
                                        class="border w-full text-center h-10 cantidad-select"
                                        style="max-width: 80px; margin-right: 10px;">
                                        @for ($i = 1; $i <= 20; $i++)
                                            <option value="{{ $i }}" {{ $i == $prestacion->cantidad ? 'selected' : '' }}>{{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    <input class="w-full text-center border h-10" name="modulo_total_{{ $loop->iteration }}"
                                        value="{{ $prestacion->modulo_total }}"
                                        oninput="this.value = this.value.replace(',', '.'); updateTotalPresupuesto();" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Botón para agregar nueva prestación -->
            <div class="text-left mt-4">
                <button type="button" id="addPrestacionBtn" class="px-2 py-2 bg-green-700 text-white rounded"><i
                        class="fa fa-plus mr-2"></i>Agregar
                    Prestación</button>
            </div>



            @if (count($anestesias) > 0)
                <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>
                <h2 class="text-lg font-semibold mb-2">ANESTESIA</h2>



                <table class="table-auto w-2 mb-4" id="anestesia-table" style="margin-left: 30%">
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
                                        value="{{$anestesia->complejidad}}" class="border w-auto h-10 text-center">
                                </td>
                                <td class="border px-4 py-2">
                                    <input type="text" name="precio_anestesia{{ $loop->iteration }}"
                                        value="{{$anestesia->precio}}" class="border w-auto h-10 text-center"
                                        oninput="this.value = this.value.replace(',', '.'); updateTotalPresupuesto();">
                                </td>
                                <td class="border px-4 py-2">
                                    <select type="text" name="anestesia_id{{ $loop->iteration }}"
                                        class="border w-auto h-10 text-center" style="min-width: 200px;">
                                        <option value="0" {{ $anestesia->anestesia_id == 0 ? 'selected' : '' }}>Sin especificar
                                        </option>
                                        <option value="1" {{ $anestesia->anestesia_id == 1 ? 'selected' : '' }}>Anestesia Local
                                        </option>
                                        <option value="2" {{ $anestesia->anestesia_id == 2 ? 'selected' : '' }}>Anestesia Regional
                                        </option>
                                        <option value="3" {{ $anestesia->anestesia_id == 3 ? 'selected' : '' }}>Sedación
                                            Superficial</option>
                                        <option value="4" {{ $anestesia->anestesia_id == 4 ? 'selected' : '' }}>Anestesia General
                                        </option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <label id="adicional_anestesia" style="display: none; color: red;">*20% de recargo por anestesia*</label>
                <label for="total_anestesia" class="font-semibold">TOTAL ANESTESIA: $</label>
                <input type="float" id="total_anestesia" name="total_anestesia"
                    class="border rounded p-2 w-auto ml-1 text-center" value="">

            @endif






            <div class="mb-6">
                <br>
                <label for="total_presupuesto" class="font-semibold">TOTAL PRESUPUESTO: $</label>
                <input type="float" id="total_presupuesto" name="total_presupuesto"
                    class="border rounded p-2 w-auto ml-1 text-center" value="{{$presupuesto->total_presupuesto}}"
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


    


</x-app-layout>


<script>

    let edad;
    updateEdad({{ $presupuesto->edad }});
    updateTotalPresupuesto();

    function updateEdad($edad) {
        edad = $edad;
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

        if (edad <= 3 || edad >= 65) {
            totalAnestesia = totalAnestesia * 1.2;

            // Mostrar el label oculto si existe
            let labelAnestesia = document.getElementById('adicional_anestesia');
            if (labelAnestesia) {
                labelAnestesia.style.display = 'block';
            }
        } else {
            // Ocultar el label si no se cumple la condición
            let labelAnestesia = document.getElementById('adicional_anestesia');
            if (labelAnestesia) {
                labelAnestesia.style.display = 'none';
            }
        }



        // Sumar total de presupuesto y anestesia
        let totalGeneral = totalPresupuesto + totalAnestesia;

        // Actualizar el campo total_presupuesto con la suma total
        $('#total_presupuesto').val(totalGeneral.toFixed(2));

        // Actualizar el campo total_anestesia con el total calculado para anestesias
        $('#total_anestesia').val(totalAnestesia.toFixed(2));
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

    // Función para escuchar cambios en el select de cantidad
    function updateModuloTotal(element, initialTotal) {
        const cantidadSelect = element;
        const cantidad = parseInt(cantidadSelect.value);

        // Tomamos el valor unitario actual basado en el data-attribute recalibrado en recalibrateUnitPrice.
        const valorUnitario = parseFloat(cantidadSelect.getAttribute('data-valor-unitario'));

        // Calculamos el nuevo módulo total.
        const nuevoModuloTotal = valorUnitario * cantidad;

        // Obtenemos el input del módulo total y actualizamos su valor.
        const moduloTotalInput = cantidadSelect.closest('td').querySelector('input[name^="modulo_total_"]');
        moduloTotalInput.value = nuevoModuloTotal.toFixed(2); // Formateamos a 2 decimales.

        // Llamamos a la función para actualizar el total del presupuesto.
        updateTotalPresupuesto();
    }

    function recalibrateUnitPrice(moduloTotalInput) {
        const cantidadSelect = moduloTotalInput.closest('td').querySelector('.cantidad-select');
        const cantidad = parseInt(cantidadSelect.value);
        const nuevoModuloTotal = parseFloat(moduloTotalInput.value);

        // Si el nuevo módulo total está vacío o no es un número, salimos de la función.
        if (isNaN(nuevoModuloTotal) || moduloTotalInput.value.trim() === '') {
            moduloTotalInput.value = ''; // Limpia el campo en lugar de mostrar 'NaN'.
            return;
        }

        // Calculamos el nuevo valor unitario.
        const nuevoValorUnitario = nuevoModuloTotal / cantidad;

        // Asignamos el nuevo valor unitario al data-attribute para que se use en updateModuloTotal.
        cantidadSelect.setAttribute('data-valor-unitario', nuevoValorUnitario.toFixed(2));

        // Actualizamos el total del presupuesto.
        updateTotalPresupuesto();
    }

    // Inicializar escucha en cada select de cantidad.
    document.querySelectorAll('.cantidad-select').forEach(select => {
        const moduloTotalInput = select.closest('td').querySelector('input[name^="modulo_total_"]');
        const initialTotal = parseFloat(moduloTotalInput.value);
        const cantidadInicial = parseInt(select.value);

        console.log('cantidad select. modulototal: ' + moduloTotalInput + 'initialTotal: ' + initialTotal + 'cantidadInicial: ' + cantidadInicial);

        // Calculamos el valor unitario inicial y lo guardamos como data-attribute.
        const valorUnitarioInicial = initialTotal / cantidadInicial;
        select.setAttribute('data-valor-unitario', valorUnitarioInicial.toFixed(2));

        // Evento de cambio para recalcular el módulo total al cambiar la cantidad.
        select.addEventListener('change', function () {
            updateModuloTotal(this, parseFloat(moduloTotalInput.value));
        });

        // Evento blur para recalibrar el valor unitario al cambiar el módulo total manualmente.
        moduloTotalInput.addEventListener('blur', function () {
            recalibrateUnitPrice(this);
        });
    });

    function initCantidadSelect() {

        document.querySelectorAll('.cantidad-select').forEach(select => {
            const moduloTotalInput = select.closest('td').querySelector('input[name^="modulo_total_"]');
            const initialTotal = parseFloat(moduloTotalInput.value);
            const cantidadInicial = parseInt(select.value);

            console.log('cantidad select. modulototal: ' + moduloTotalInput + 'initialTotal: ' + initialTotal + 'cantidadInicial: ' + cantidadInicial);

            // Calculamos el valor unitario inicial y lo guardamos como data-attribute.
            const valorUnitarioInicial = initialTotal / cantidadInicial;
            select.setAttribute('data-valor-unitario', valorUnitarioInicial.toFixed(2));

            // Evento de cambio para recalcular el módulo total al cambiar la cantidad.
            select.addEventListener('change', function () {
                updateModuloTotal(this, parseFloat(moduloTotalInput.value));
            });

            // Evento blur para recalibrar el valor unitario al cambiar el módulo total manualmente.
            moduloTotalInput.addEventListener('blur', function () {
                recalibrateUnitPrice(this);
            });
        });
    }
    let prestacionCount = {{ count($prestaciones) }}; // El contador de prestaciones inicial, basado en las prestaciones existentes.
    let convenioId = {{$presupuesto->convenio}}; // Asegúrate de que esta variable contenga el ID del convenio.

    $('#addPrestacionBtn').on('click', function () {
        prestacionCount++; // Incrementar el contador de prestaciones


        // Crear nueva fila con inputs y un select para la prestación
        let newRow = `
        <tr data-row="${prestacionCount}" class="prestacion-row">
            <td class="px-4 py-2 text-center">
                
            </td>
            <td class="border px-4 py-2 text-center">
                <input class="w-full text-center border h-10" name="codigo_${prestacionCount}"/>
            </td>
            <td class="border px-4 py-2 text-center">
                <select name="prestacion_${prestacionCount}" class="w-full text-center border h-10 prestacion-select" data-row="${prestacionCount}"></select>
                <input type="hidden" name="cantidad_${prestacionCount}" value="1">
            </td>
            <td class="border px-4 py-2 text-center">
                <div style="display: flex;">
                    <select name="cantidad_${prestacionCount}" class="border w-full text-center h-10 cantidad-select" style="max-width: 80px; margin-right: 10px;">
                        ${Array.from({ length: 20 }, (_, i) => `<option value="${i + 1}">${i + 1}</option>`).join('')}
                    </select>
                    <input class="w-full text-center border h-10" name="modulo_total_${prestacionCount}" oninput="this.value = this.value.replace(',', '.'); updateTotalPresupuesto();" />
                </div>
            </td>
            
        </tr>
    `;
        // Añadir la nueva fila al cuerpo de la tabla
        $('#prestacionesTableBody').append(newRow);

        // Inicializar select2 en el nuevo select de la fila
        let selectElement = $(`select[name="prestacion_${prestacionCount}"]`);
        initializeSelect2(selectElement);

        // Cargar prestaciones en el nuevo select
        console.log(convenioId);

        loadPrestaciones(convenioId, selectElement);

        // Actualizar automáticamente el campo de código cuando se selecciona una prestación
        selectElement.on('change', function () {
            let selectedOption = $(this).find('option:selected');
            let codigo = selectedOption.data('codigo');
            let rowId = $(this).data('row');
            $(`input[name="codigo_${rowId}"]`).val(codigo); // Actualizar el campo de código
        });

    });

    // Función para cargar prestaciones desde el backend
    function loadPrestaciones(convenioId, selectElement) {
        $.ajax({
            url: '{{ url("/getPrestaciones") }}/' + convenioId,
            type: 'GET',
            success: function (data) {
                selectElement.empty();
                selectElement.append('<option value="">Seleccione Prestación</option>');
                $.each(data, function (key, value) {
                    selectElement.append('<option value="' + value.prestacionid + '" data-codigo="' + value.prestacioncodigo + '" data-nombre="' + value.prestacionnombre + '">' + value.prestacionnombre + '</option>');
                });
            }
        });
    }

    $(document).on('change', '.prestacion-select', function () {
        let selectedOption = $(this).find('option:selected');
        let codigoPrestacion = selectedOption.data('codigo');
        let rowCount = $(this).closest('tr').data('row'); // Obtenemos el rowCount correcto
        $('#codigo_' + rowCount).val(codigoPrestacion);
        let prestacionId = selectedOption.val();

        let convenioId = {{$presupuesto->convenio}}

            console.log(convenioId, codigoPrestacion, 'aaaaaaaaaaasdddddddd');

        $.ajax({
            url: '{{ url("/obtenerPrecio") }}/' + convenioId + '/' + codigoPrestacion,
            method: 'GET',
            success: function (response) {
                console.log('obtenerprecioooo');
                let precio = parseFloat(response[0].PRECIO);
                let precioConDecimales = precio.toFixed(2); // Mantiene dos decimales
                console.log(precioConDecimales);
                console.log("rowCount:", rowCount);

                $('input[name="modulo_total_' + rowCount + '"]').val(precioConDecimales);
                updateTotalPresupuesto();
            },
            error: function (xhr, status, error) {
                console.error('Error en la consulta AJAX:', error);
            }
        });
        setTimeout(function () {
            initCantidadSelect();
        }, 1000);

    });

    $(document).on('click', '.delete-prestacion-btn', function () {
        let prestacionEliminada = $(this).data('id');
        const rowElement = $(this).closest('tr'); // Guardar la fila para eliminarla tras la respuesta

        console.log(prestacionEliminada);
        // Confirmación antes de eliminar
        if (confirm('¿Estás seguro de que quieres eliminar esta prestación?')) {
            $.ajax({
                url: '{{ route("deletePrestacion", "") }}/' + prestacionEliminada, // Genera la URL correctamente
                type: 'POST',
                data: {
                    _method: 'DELETE',  // Indica a Laravel que se trata de un DELETE
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    alert('Prestación eliminada correctamente');
                    rowElement.remove(); // Remover la fila de la vista
                    updateTotalPresupuesto();
                    location.reload(); // Refrescar la página después de eliminar
                },
                error: function (xhr) {
                    alert('Error al eliminar la prestación');
                }
            });

        }
        prestacionEliminada = '';
        console.log(prestacionEliminada);
    });


    // Función para inicializar select2 en un select dado
    function initializeSelect2(selectElement) {
        selectElement.select2({
            tags: true, // Esto permite que el usuario edite las opciones.
            placeholder: 'Seleccione una prestación',
            allowClear: true,
            width: '100%', // Para ajustar el ancho
            language: {
                noResults: function () {
                    return "Seleccione un convenio.";
                }
            }
        });
    }


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

    /* Limitar el ancho de la columna de la prestación */
    .prestacion-select {
        width: 100%;
        max-width: 350px;
        /* Puedes ajustar este valor */
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
</style>