@php
    use App\Models\ObraSocial;
    use App\Models\Convenio;
    use App\Models\Prestacion;
@endphp

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<x-app-layout>
    <x-slot name="title">Especificar Anestesia</x-slot>

    <form method="POST" action="{{ route('presupuestos.updateAnestesia', ['id' => $presupuesto->id]) }}"
        class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg" enctype="multipart/form-data">
        @csrf

        <input type="hidden" id="presupuesto_id" name="presupuesto_id" value="{{$id}}">

        <h1 class="text-2xl font-bold mb-6">ESPECIFICAR ANESTESIA</h1>

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
                    value="{{$presupuesto->medico_tratante}}">
            </div>
        </div>
        <div class="mb-4">

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
                                        value="{{$anestesia->complejidad}}" class="border-none w-full">
                                </td>
                                <td class="border px-4 py-2">
                                    <input type="text" name="precio_anestesia{{ $loop->iteration }}"
                                        value="{{$anestesia->precio}}" class="border-none w-full"
                                        oninput="updateTotalPresupuesto()">
                                </td>
                                <td class="border px-4 py-2">
                                    <select type="text" name="anestesia_id{{ $loop->iteration }}" class="border-none w-full" style="min-width: 200px;">
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
            @endif
            <div style="border-top: 1px solid #000; padding-top: 10px; margin-top: 20px; margin-bottom: 20px"></div>


            <button type="submit" class="btn btn-primary">
                Guardar Anestesia
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