<x-app-layout>
    <x-slot name="title">Clientes</x-slot>

    <div class="container mt-4">
        <h1>Clientes</h1>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary mb-3">Crear Cliente</a>
        <form method="GET" action="{{ route('clientes.index') }}" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por nombres o apellidos">
                <button class="btn btn-primary" type="submit">Buscar</button>
                <!-- Botón de borrado -->
                @if($search)
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">×</a>
                @endif
            </div>
        </form>
        <!-- Mensaje de éxito -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Contador de registros -->
        <p>Total de registros: {{ $clientes->total() }}</p>

        <table class="table">
            <thead>
                <tr>
                    <!--th>ID</th-->
                    <th>#</th> <!-- Contador -->
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Empresa</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clientes as $cliente)
                    <tr>
                        <!--td>{{ $cliente->id }}</td-->
                        <td>{{ $loop->iteration + $clientes->perPage() * ($clientes->currentPage() - 1) }}</td> <!-- Contador -->
                        <td>{{ $cliente->nombres }}</td>
                        <td>{{ $cliente->apellidos }}</td>
                        <td>{{ $cliente->empresa }}</td>
                        <td>{{ $cliente->mail }}</td>
                        <td>{{ $cliente->telefono }}</td>
                        <td>
                            <!-- Botón de Editar -->
                            <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-warning btn-sm">Editar</a>

                            <!-- Formulario de Eliminar -->
                            <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este cliente?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Enlaces de paginación -->
        {{ $clientes->appends(['search' => $search])->links() }}
    </div>
</x-app-layout>