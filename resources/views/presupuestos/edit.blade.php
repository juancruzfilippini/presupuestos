<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

</head>

<x-app-layout>

    <x-slot name="title">Editar Presupuesto</x-slot>



    <form method="POST" action="{{ route('presupuestos.store') }}"
        class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg" enctype="multipart/form-data">
        @csrf

        <h1 class="text-2xl font-bold mb-6">EDITAR PRESUPUESTO</h1>

        <label for="fecha" class="font-semibold">PEDIDO MÉDICO:</label>
        <p></p>

        @if ($presupuesto->file_path)
            <div class="mb-4">
                <label for="download_file" class="font-semibold">Archivo adjunto:</label>
                <a href="{{ Storage::url($presupuesto->file_path) }}" class="text-blue-500 hover:underline" download>
                    Descargar archivo
                </a>
            </div>
        @endif

    </form>
</x-app-layout>