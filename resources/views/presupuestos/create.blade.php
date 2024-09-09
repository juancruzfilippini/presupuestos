<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

</head>

<x-app-layout>

    <x-slot name="title">Crear Presupuesto</x-slot>



    <form method="POST" action="{{ route('presupuestos.store') }}"
        class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg" enctype="multipart/form-data">
        @csrf

        <h1 class="text-2xl font-bold mb-6">CREAR PRESUPUESTO</h1>

        <label for="fecha" class="font-semibold">PEDIDO MÉDICO:</label>
        <p></p>

        <label for="file_1">Subir archivo (PDF o Imagen)</label>
        <input type="file" name="file[]" id="file_1" class="form-control" accept=".pdf, .jpg, .jpeg, .png">
        <p></p>
        <button type="button" class="btn btn-primary btn-sm" id="addFile"><i class="fa fa-plus"></i> Agregar otro
            archivo</button>
        <p></p>
        <div id="fileInputs"></div>
        <p></p>


        <div class="flex justify-between mb-4">
            <div>
                <label for="fecha" class="font-semibold">FECHA:</label>
                <input type="date" id="fecha" name="fecha" class="border rounded p-2 w-full" value="{{ $today }}"
                    required readonly>
            </div>
            <div>
                <label for="medico_solicitante" class="font-semibold">MEDICO SOLICITANTE:</label>
                <input type="text" id="medico_solicitante" name="medico_solicitante" class="border rounded p-2 w-full"
                    value="">
            </div>
            <div>
                <label for="medico_tratante" class="font-semibold">MEDICO TRATANTE:</label>
                <input type="text" id="medico_tratante" name="medico_tratante" class="border rounded p-2 w-full"
                    value="">
            </div>
        </div>


        <div class="mb-4">
            <h2 class="text-lg font-semibold mb-2">DATOS DEL PACIENTE</h2>

            <div class="form-groupp">
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
            <input type="text" id="selected-person" name="paciente" class="form-control form-groupp"
                placeholder="Nombre del paciente seleccionado" readonly>
            <div class="form-row">
                <div class="form-groupp">
                    <label for="telefono" style="margin-top: 5px;">TELEFONO: </label>
                    <input type="text" id="telefono" name="telefono" class="form-control"
                        placeholder="Número telefónico del paciente">
                </div>
                <div class="form-groupp">
                    <label for="email" style="margin-top: 5px;">EMAIL: </label>
                    <input type="text" id="email" name="email" class="form-control" placeholder="Email del paciente">
                </div>
                <div class="form-groupp">
                    <label for="email" style="margin-top: 5px;">NUMERO DE AFILIADO: </label>
                    <input type="text" id="nro_afiliado" name="nro_afiliado" class="form-control"
                        placeholder="Numero de afiliado en obra social">
                </div>
            </div>

            <input type="hidden" id="paciente_salutte_id" name="paciente_salutte_id" value="">
            <p> </p>
            <p> </p>
            <div class="flex items-center">
                <label for="convenida" class="font-semibold mr-2">Convenida </label>
                <label class="switch">
                    <input type="checkbox" id="convenida" name="convenida" checked>
                    <span class="slider round"></span>
                </label>
                <div>
                    <label for="" class="font-semibold ml-3">Obra Social:</label>
                    <input type="text" id="input_obrasocial" name="input_obrasocial" class="border rounded p-2"
                        value="">
                </div>
            </div>
            <p></p>
            <div class="form-group row">
                <div class="col-md-6">
                    <label for="obra_social">Obra Social:</label>
                    <select class="form-control" name="obra_social" id="obra_social">
                        <option value="">Seleccione Obra Social</option>
                        @foreach($obrasSociales as $obraSocial)
                            <option value="{{ $obraSocial['id'] }}" data-id="{{ $obraSocial['nombre'] }}">
                                {{ $obraSocial['nombre'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="convenio">Convenio:</label>
                    <select class="form-control" name="convenio" id="convenio">
                        <option value="">Seleccione Convenio</option>
                    </select>
                </div>
            </div>



            <p></p>
        </div>

        <input type="text" id="detalle" name="detalle" class="form-control"
            placeholder="Asunto: Prestaciones quirurgicas">

        <p></p>

        <div class="mb-6" id="no-convenida-table">

            <p></p>
            <table class="table-auto w-full mb-4">
                <thead>
                    <tr>
                        <th class="border px-4 py-2"></th>
                        <th class="codigo border px-4 py-2 text-center">CÓDIGO</th>
                        <th class="fixed-width border px-4 py-2 text-center">
                            <input id="input_especialidad" name="input_especialidad" class="w-full text-center"
                                placeholder="Ingrese Especialidad" />
                        </th>

                        <th class="border px-4 py-2 text-center">MÓDULO TOTAL (A)</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <button type="button" id="add-row1" class="btn btn-success text-white p-2 rounded-full mb-4 ml-1"> <i
                    class="fas fa-plus"></i> Prestación </button>
        </div>

        <div class="mb-6">
            <table class="table-auto w-full mb-4" id="presupuesto-table">
                <thead>
                    <tr>
                        <th class="border px-4 py-2"></th>
                        <th class="codigo border px-4 py-2 text-center">CÓDIGO</th>
                        <th class="fixed-width border px-4 py-2 text-center">
                            <input id="especialidad" name="especialidad" class="w-full text-center"
                                placeholder="Ingrese Especialidad" />
                        </th>

                        <th class="border px-4 py-2 text-center">MÓDULO TOTAL (A)</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

            <button type="button" id="add-row" class="btn btn-success text-white p-2 rounded-full mb-4 ml-1"> <i
                    class="fas fa-plus"></i> Prestación </button>
        </div>

        <input type="hidden" id="hidden_anestesia_id" name="anestesia_id" value="0">
        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px;"></div>


        <div class="mb-4">
            <h2 class="text-lg font-semibold mb-2">OPCIONES</h2>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center mb-4">
                    <label for="anestesia" class="font-semibold mr-2">Anestesia</label>
                    <label class="switch">
                        <input type="checkbox" id="anestesia" name="anestesia">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>



        <!-- Tabla que debería mostrarse solo cuando el switch está activado -->
        <div class="mb-4" style="display:none;">
            <div style="margin-left: 30%">
                <button type="button" id="addRow"
                    class="bg-green-500 hover:bg-green-700 text-black font-bold py-2 px-4 rounded mb-4">
                    Agregar anestesia
                </button>
                <button type="button" id="removeRow"
                    class="bg-red-500 hover:bg-red-700 text-black font-bold py-2 px-4 rounded mb-4">
                    Eliminar ultima
                </button>
            </div>
            <table class="table-auto w-2 mb-4" id="anestesia-table" style="margin-left: 30%">
                <thead>
                    <th class="border px-4 py-2 text-center">Complejidad</th>
                    <th class="border px-4 py-2 text-center">Precio</th>
                    <th class="border px-4 py-2 text-center">Tipo</th>
                </thead>
                <tbody id="anestesia-body">

                </tbody>
            </table>

            <label for="total_anestesia" class="font-semibold">TOTAL ANESTESIA: $</label>
            <input type="number" id="total_anestesia" name="total_anestesia"
                class="border rounded p-2 w-2 ml-1 text-center" value="">
        </div>

        <div class="mb-6">
            <label for="total_presupuesto" class="font-semibold">TOTAL PRESUPUESTO: $</label>
            <input type="number" id="total_presupuesto" name="total_presupuesto"
                class="border rounded p-2 w-2 ml-1 text-center" value="">
        </div>



        <div class="mb-6">
            <div class="items-center ">
                <div class="flex items-center">
                    <label for="condicion" class="font-semibold mr-2">CONDICIÓN DE PAGO:</label>
                    <label class="switch">
                        <input type="checkbox" id="toggleCondicion" name="toggleCondicion" checked>
                        <span class="slider round"></span>

                    </label>
                </div>
            </div>
            <p></p>
            <textarea id="condicion" name="condicion"
                class="border rounded p-2 w-full">EFECTIVO/TRANSFERENCIA BANCARIA/TARJETA DE DÉBITO/TARJETA DE CRÉDITO</textarea>
        </div>

        <div class="mb-6">
            <div class="items-center ">
                <div class="flex items-center">
                    <label for="incluye" class="font-semibold mr-2">INCLUYE: </label>
                    <label class="switch">
                        <input type="checkbox" id="toggleIncluye" name="toggleIncluye" checked>
                        <span class="slider round"></span>

                    </label>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <textarea id="incluye" name="incluye" class="border rounded p-2 w-full">
( A ) Módulo: Módulo: Incluye Gastos Hospitalarios: Honorarios Médicos sin anestesistas (A.M.A.). PENSION: Raciones de corresponder, habitación compartida, Enfermería, Antibióticos de primera y segunda generación. Sueros. Descartables Básicos: Jeringas, gasas, algodón, tela adhesiva, vendas, guantes, agujas
( B ) Oxígeno: En la actualidad el oxígeno se factura por separado del Módulo y equivale al monto consignado por el período de utilización por hasta cuatro horas de quirófano, de corresponder.</textarea>
        </div>

        <div class="mb-6">
            <div class="items-center ">
                <div class="flex items-center">
                    <label for="excluye" class="font-semibold mr-2">EXCLUYE:</label>
                    <label class="switch">
                        <input type="checkbox" id="toggleExcluye" name="toggleExcluye" checked>
                        <span class="slider round"></span>

                    </label>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <textarea id="excluye" name="excluye"
                class="border rounded p-2 w-full">Antibióticos de tercera y cuarta generación, Rx., Ecografías y demás insumos que se encuentran en la ficha de consumo que se facturarán a valores kairos.</textarea>
        </div>

        <div class="mb-6">
            <div class="items-center ">
                <div class="flex items-center">
                    <label for="adicionales" class="font-semibold mr-2">ADICIONALES:</label>
                    <label class="switch">
                        <input type="checkbox" id="toggleAdicionales" name="toggleAdicionales" checked>
                        <span class="slider round"></span>

                    </label>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <textarea id="adicionales" name="adicionales"
                class="border rounded p-2 w-full">*Las prestaciones / insumos / medicación no contempladas en el presente presupuesto, que sean requeridas durante la intervención o recuperación se adicionarán al valor del mismo. 
*  Al momento de realizar su ingreso, se le solicitará   firmar un pagaré.  El mismo le será devuelto a los 30 días de recibir el alta médica.
*Este presupuesto tiene una validez de 15 días desde la fecha de emisión, con excepción de honorarios de AMA  que serán los vigentes al momento de la cirugía.</textarea>
        </div>

        <div class="mb-6">
            <button type="submit" class="btn btn-primary">Crear Presupuesto</button>
        </div>

    </form>




    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            function updateTotalPresupuesto() {
                let totalPresupuesto = 0;
                let totalAnestesia = 0; // Variable para el total de anestesia

                // Sumar todos los valores de los campos de presupuesto (por ejemplo, modulo_total_)
                $('input[name^="modulo_total_"]').each(function () {
                    let value = parseFloat($(this).val()) || 0;
                    totalPresupuesto += value;
                });

                // Sumar los precios de anestesia
                $('input[name="precio_anestesia[]"]').each(function () {
                    let value = parseFloat($(this).val()) || 0;
                    totalAnestesia += value;
                });

                // Sumar total de presupuesto y anestesia
                let totalGeneral = totalPresupuesto + totalAnestesia;

                // Actualizar el campo total_presupuesto con la suma total
                $('#total_presupuesto').val(totalGeneral.toFixed(2));

                // Actualizar el campo total_anestesia con el total calculado para anestesias
                $('#total_anestesia').val(totalAnestesia.toFixed(2));
            }



            document.getElementById('addRow').addEventListener('click', function () {
                var tableBody = document.getElementById('anestesia-body');
                var newRow = document.createElement('tr');

                newRow.innerHTML = `
            <td class="border px-4 py-2">
                <input type="text" name="complejidad[]" class="border-none w-full">
            </td>
            <td class="border px-4 py-2">
                <input type="number" name="precio_anestesia[]" class="border-none w-full">
            </td>
            <td>
                <select name="anestesia_id[]" class="border rounded" style="width: 250px; margin-right: 20px;">
                    <option value="1">Local</option>
                    <option value="2">Periférica</option>
                    <option value="3">Central</option>
                    <option value="4">Total</option>
                </select>
            </td>
        `;

                tableBody.appendChild(newRow);

                // Asignar el evento oninput dinámicamente
                newRow.querySelector('input[name="precio_anestesia[]"]').addEventListener('input', updateTotalPresupuesto);
            });

            document.getElementById('removeRow').addEventListener('click', function () {
                var tableBody = document.getElementById('anestesia-body');
                if (tableBody.rows.length > 1) {
                    tableBody.deleteRow(-1);
                } else {
                    alert('No puedes eliminar todas las filas.');
                }
            });

            $(document).ready(function () {

                // Inicializar select2 para selects existentes al cargar la página
                initializeSelect2();

                // Función para inicializar select2 en selects dinámicos
                function initializeSelect2() {
                    $('#obra_social, #convenio, .prestacion-select').select2({
                        placeholder: 'Seleccione',
                        allowClear: true
                    });
                }




                // Agregar fila y configurar select2
                $('#add-row').click(function () {
                    var rowCount = $('#presupuesto-table tbody tr').length + 1;
                    var newRow = `<tr data-row="${rowCount}">
                <td class="border px-4 py-2 text-center">
                    <button type="button" class="bg-red-500 text-white px-2 py-1 rounded remove-row">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
                <td class="border px-4 py-2">
                    <input type="text" name="codigo_${rowCount}" class="border-none w-full text-center">
                </td>
                <td class="border px-4 py-2">
                    <select name="prestacion_${rowCount}" class="fixed-width border-none prestacion-select">
                        <option value="">Seleccione prestación</option>
                    </select>
                </td>
                <td class="border px-4 py-2 text-right">
                    <input type="number" name="modulo_total_${rowCount}" class="border-none w-full text-right">
                </td>
                </tr>`;

                    // Agregar la nueva fila a la tabla
                    $('#presupuesto-table tbody').append(newRow);

                    // Inicializar select2 para el nuevo select
                    initializeSelect2();

                    // Cargar prestaciones en el nuevo select
                    loadPrestaciones($('#convenio').val(), $('#presupuesto-table tbody tr:last-child').find('.prestacion-select'));

                    // Actualizar el total después de agregar una fila
                    updateTotalPresupuesto();

                    // Agregar el evento de cambio para los nuevos inputs
                    $('#presupuesto-table').on('input', 'input[name^="modulo_total_"]', updateTotalPresupuesto);
                });

                $('#add-row1').click(function () {
                    var rowCount = $('#no-convenida-table tbody tr').length + 1;
                    var newRow = `<tr data-row="${rowCount}">
                <td class="border px-4 py-2 text-center">
                    <button type="button" class="bg-red-500 text-white px-2 py-1 rounded remove-row">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
                <td class="border px-4 py-2">
                    <input type="text" name="codigo_${rowCount}" class="border-none w-full text-center">
                </td>
                <td class="border px-4 py-2">
                    <input name="prestacion_${rowCount}" class="fixed-width border-none"></input>
                </td>
                <td class="border px-4 py-2 text-right">
                    <input type="number" name="modulo_total_${rowCount}" class="border-none w-full text-right">
                </td>
                </tr>`;

                    // Agregar la nueva fila a la tabla
                    $('#no-convenida-table tbody').append(newRow);

                    // Inicializar select2 para el nuevo select
                    initializeSelect2();


                    // Actualizar el total después de agregar una fila
                    updateTotalPresupuesto();

                    // Agregar el evento de cambio para los nuevos inputs
                    $('#no-convenida-table').on('input', 'input[name^="modulo_total_"]', updateTotalPresupuesto);
                });

                // Agregar el evento de cambio para los inputs existentes
                $('#presupuesto-table').on('input', 'input[name^="modulo_total_"]', updateTotalPresupuesto);

                // Manejador para eliminar filas
                $('#presupuesto-table').on('click', '.remove-row', function () {
                    $(this).closest('tr').remove();
                    updateTotalPresupuesto(); // Actualizar el total después de eliminar una fila
                });

                // Eliminar fila de la tabla de presupuesto
                $(document).on('click', '.remove-row', function () {
                    $(this).closest('tr').remove();
                });

                // Manejar el cambio de estado del switch de anestesia
                $(document).ready(function () {
                    const $switchElement = $('#anestesia');
                    const $selectElement = $('#anestesia-select');
                    const $tableElement = $('#anestesia-table').closest('div');
                    const $hiddenInput = $('#hidden_anestesia_id');

                    if ($switchElement.is(':checked')) {
                        $selectElement.show();
                        $tableElement.show();
                        $hiddenInput.val($selectElement.find('select').val());
                    } else {
                        $selectElement.hide();
                        $tableElement.hide();
                        $hiddenInput.val('0');
                    }

                    $switchElement.change(function () {
                        if ($(this).is(':checked')) {
                            $selectElement.show();
                            $tableElement.show();
                            $hiddenInput.val($selectElement.find('select').val());
                        } else {
                            $selectElement.hide();
                            $tableElement.hide();
                            $hiddenInput.val('0');
                        }
                    });

                    $selectElement.find('select').change(function () {
                        $hiddenInput.val($(this).val());
                    });
                });

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

                // Buscar paciente por DNI
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
                                    const edad = calcularEdad(fechaNacimiento);

                                    resultHtml += '<div>';
                                    resultHtml += '<p><strong>HC Electrónica:</strong> ' + patient.id + '</p>';
                                    resultHtml += '<p><strong>Nombre:</strong> ' + patient.nombres + ' ' + patient.apellidos + '</p>';
                                    resultHtml += '<p><strong>DNI:</strong> ' + patient.documento + '</p>';
                                    resultHtml += '<p><strong>Fecha de Nacimiento:</strong> ' + patient.fecha_nacimiento + '</p>';
                                    resultHtml += '<p><strong>Edad:</strong> ' + edad + '</p>';
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

                // Seleccionar paciente desde la lista de resultados
                $('#results').on('click', '.select-button', function (e) {
                    e.preventDefault();
                    var selectedName = $(this).data('name');
                    var selectedId = $(this).data('id');
                    $('#selected-person').val(selectedName);
                    $('#paciente_salutte_id').val(selectedId);
                });

                // Manejar el cambio en el select de obra social
                $('#obra_social').on('change', function () {
                    let obraSocial = $(this).find('option:selected').data('id');

                    if (obraSocial) {
                        $.ajax({
                            url: '{{ route('getConvenios') }}',
                            type: 'GET',
                            data: { obra_social: obraSocial },
                            success: function (data) {
                                let convenioSelect = $('#convenio');
                                convenioSelect.empty();
                                convenioSelect.append('<option value="">Seleccione Convenio</option>');

                                if (data.length > 0) {
                                    data.forEach(function (item) {
                                        convenioSelect.append(`<option value="${item.id}">${item.nombre}</option>`);
                                    });
                                } else {
                                    convenioSelect.append('<option value="">No hay convenios disponibles</option>');
                                }
                            }
                        });
                    } else {
                        $('#convenio').empty().append('<option value="">Seleccione Convenio</option>');
                    }
                });

                // Capturar cambio en el select de convenio
                $('#convenio').on('change', function () {
                    var convenioId = $(this).val();

                    if (convenioId) {
                        loadPrestaciones(convenioId, $('.prestacion-select'));
                    } else {
                        $('.prestacion-select').empty().append('<option value="">Seleccione Prestación</option>');
                    }
                });

                // Función para cargar las prestaciones en un select
                function loadPrestaciones(convenioId, selectElement) {
                    console.log(convenioId);
                    $.ajax({
                        url: '/getPrestaciones/' + convenioId,
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

                // Escuchar el cambio en el select de prestaciones
                $(document).on('change', '.prestacion-select', function () {
                    var selectedOption = $(this).find('option:selected');
                    var codigo = selectedOption.data('codigo');
                    $(this).closest('tr').find('input[name^="codigo"]').val(codigo);
                });


                $(document).on('change', '.prestacion-select', function () {
                    let selectedOption = $(this).find('option:selected');
                    let codigoPrestacion = selectedOption.data('codigo');
                    let rowCount = $(this).closest('tr').data('row'); // Obtenemos el rowCount correcto
                    $('#codigo_' + rowCount).val(codigoPrestacion);
                    let prestacionId = selectedOption.val();

                    let convenioId = $('#convenio').val();

                    console.log(convenioId, codigoPrestacion);

                    $.ajax({
                        url: '/obtenerPrecio/' + convenioId + '/' + codigoPrestacion,
                        method: 'GET',
                        success: function (response) {
                            console.log(response);
                            let precio = parseFloat(response[0].PRECIO);
                            // Truncar el precio eliminando la parte decimal
                            let precioTruncado = Math.floor(precio); // También puedes usar parseInt(precio)
                            console.log(precioTruncado);
                            console.log("rowCount:", rowCount);

                            $('input[name="modulo_total_' + rowCount + '"]').val(precioTruncado);
                            updateTotalPresupuesto();
                        },
                        error: function (xhr, status, error) {
                            console.error('Error en la consulta AJAX:', error);
                        }
                    });
                });

                // Manejar el cambio de estado del switch de convenida/no convenida
                // Manejar el cambio de estado del switch de convenida/no convenida
                $(document).ready(function () {
                    const $switchConvenida = $('#convenida');
                    const $presupuestoTable = $('#presupuesto-table').closest('div');
                    const $noconvenidaTable = $('#no-convenida-table').closest('div');
                    const $inputObrasocial = $('#input_obrasocial').closest('div');
                    const $obraSocialInput = $('#obra_social').closest('div');
                    const $convenioInput = $('#convenio').closest('div');

                    // Verificar el estado inicial del switch y mostrar/ocultar la tabla y los inputs
                    if ($switchConvenida.is(':checked')) {
                        $noconvenidaTable.hide();
                        $inputObrasocial.hide();
                        $presupuestoTable.show();
                        $obraSocialInput.show();
                        $convenioInput.show();
                    } else {
                        $noconvenidaTable.show();
                        $inputObrasocial.show();
                        $presupuestoTable.hide();
                        $obraSocialInput.hide();
                        $convenioInput.hide();
                    }

                    // Manejar el cambio de estado del switch
                    $switchConvenida.change(function () {
                        if ($(this).is(':checked')) {
                            $noconvenidaTable.hide();
                            $inputObrasocial.hide();
                            $presupuestoTable.show();
                            $obraSocialInput.show();
                            $convenioInput.show();
                        } else {
                            $noconvenidaTable.show();
                            $inputObrasocial.show();
                            $presupuestoTable.hide();
                            $obraSocialInput.hide();
                            $convenioInput.hide();
                        }
                    });
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


                let fileCount = 1;

                $('#addFile').on('click', function () {
                    fileCount++;

                    // Oculta el botón de eliminar del archivo anterior
                    $('.removeFile').hide();

                    const newFileInput = `
        <div id="fileContainer_${fileCount}">
            <label for="file_${fileCount}">Subir archivo (PDF o Imagen) ${fileCount}</label>
            <input type="file" name="file[]" id="file_${fileCount}" class="form-control" accept=".pdf, .jpg, .jpeg, .png">
            <p></p>
            <button type="button" class="removeFile btn btn-sm btn-danger" data-id="${fileCount}"><i class="fa fa-remove"></i> Eliminar</button>
            <p></p>
        </div>
    `;
                    $('#fileInputs').append(newFileInput);
                });

                $(document).on('click', '.removeFile', function () {
                    const id = $(this).data('id');

                    if (id === fileCount) {
                        $('#fileContainer_' + id).remove();
                        fileCount--;

                        // Muestra el botón de eliminar en el nuevo último archivo
                        $('#fileContainer_' + fileCount + ' .removeFile').show();
                    }
                });








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

        .codigo {
            min-width: 40px !important;
            max-width: 50px !important;

        }

        .fixed-width {
            width: 550px;
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

</x-app-layout>