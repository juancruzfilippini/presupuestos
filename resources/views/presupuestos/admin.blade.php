@php
use App\Models\ObraSocial;
use App\Models\Convenio;
use App\Models\Prestacion;
@endphp

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<x-app-layout>
    <x-slot name="title">Administrar</x-slot>

    <form method="POST" action="{{ route('presupuestos.convenio') }}"
        class="w-full mx-auto p-6 bg-white shadow-md rounded-lg" enctype="multipart/form-data">

        @csrf <!-- El token CSRF dentro del formulario -->

        <h1 class="text-2xl font-bold mb-6">CONVENIO</h1>

        <div class="mb-4">
            <label for="email" style="margin-bottom: 10px; margin-right: 10px;">MODIFICAR CONVENIO: </label>
            <select id="convenio" name="convenio_id" class="border w-auto h-10" style="min-width: 200px;">
                @foreach ($convenios as $convenio)
                <option value="{{ $convenio['id'] }}">{{ $convenio['nombre'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Campo oculto para enviar el nombre del convenio -->
        <input type="hidden" id="nombre_convenio" name="nombre_convenio" value="">

        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px; margin-bottom: 20px"></div>

        <h1 class="text-2xl font-bold mb-6">USUARIOS</h1>

        <table class="w-auto table table-borderless">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Rol</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $usuario)
                <tr>
                    <td class="px-4 py-2 border-b-2 border-gray-300 text-left">{{ $usuario->name }} - {{$usuario->email}}</td>
                    <td class="px-4 py-2 border-b-2 border-gray-300 text-left">
                        <!-- Input oculto para enviar el id del usuario -->
                        <input type="hidden" name="usuarios_ids[]" value="{{ $usuario->id }}">

                        <!-- Select con el rol del usuario -->
                        <select name="roles[]" class="form-select">
                            @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}" {{ $usuario->rol_id == $rol->id ? 'selected' : '' }}>
                                {{ $rol->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>


        <button type="submit" class="btn btn-primary">
            Guardar cambios
        </button>
    </form>

</x-app-layout>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Cuando el select de convenio cambie, actualizamos el campo hidden con el nombre del convenio seleccionado
    $(document).ready(function() {
        // Ejecutar cuando la página se carga
        var convenioNombre = $("#convenio option:selected").text();
        $('#nombre_convenio').val(convenioNombre); // Actualizar el valor del campo hidden

        // Ejecutar cuando el select cambia
        $('#convenio').on('change', function() {
            convenioNombre = $("#convenio option:selected").text();
            $('#nombre_convenio').val(convenioNombre); // Actualizar el valor del campo hidden
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
</style>