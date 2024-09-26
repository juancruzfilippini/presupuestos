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

        <h1 class="text-2xl font-bold mb-6">ADMINISTRAR</h1>

        <div class="mb-4">
            <label for="email" style="margin-bottom: 10px; margin-right: 10px;">MODIFICAR CONVENIO: </label>
            <select id="convenio" name="convenio_id" class="border w-auto" style="min-width: 200px;">
                <option value="">Seleccione un convenio</option>
                @foreach ($convenios as $convenio)
                    <option value="{{ $convenio['id'] }}">{{ $convenio['nombre'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Campo oculto para enviar el nombre del convenio -->
        <input type="hidden" id="nombre_convenio" name="nombre_convenio" value="">

        <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px; margin-bottom: 20px"></div>

        <button type="submit" class="btn btn-primary">
            Guardar cambios
        </button>
    </form>

</x-app-layout>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

    // Cuando el select de convenio cambie, actualizamos el campo hidden con el nombre del convenio seleccionado
    $('#convenio').on('change', function() {
        var convenioNombre = $("#convenio option:selected").text();
        $('#nombre_convenio').val(convenioNombre); // Actualizar el valor del campo hidden
    });
    

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