<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <style>
        .container {
            background-color: #eaeaea;
            padding: 50px;
            border-radius: 15px;
        }

        body {
            background-color: #eaeaea;
            /* Fondo suave */
        }

        .form-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            border-radius: 30px;
        }

        .btn-primary {
            background-color: #0066cc;
            border: none;
            border-radius: 30px;
            padding: 10px 20px;
        }

        .btn-primary:hover {
            background-color: #004c99;
        }

        .btn-link {
            color: #0066cc;
            text-decoration: none;
        }

        .btn-link:hover {
            text-decoration: underline;
        }

        .form-logo img {
            max-width: 150px;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="form-container card p-4">
            <!-- Logo similar al login -->
            <div class="text-center mb-4 form-logo">
                <img src="https://github.com/juancruzfilippini/logo-presupuestos/blob/main/hu_logo.png?raw=true"
                    alt="Logo" class="img-fluid">
            </div>



            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre y Apellido</label>
                    <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required
                        autofocus autocomplete="name">
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required
                        autocomplete="username">
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input id="password" type="password" name="password" class="form-control" required
                        autocomplete="new-password">
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control"
                        required autocomplete="new-password">
                    @error('password_confirmation')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('login') }}" class="btn btn-link">Ya tienes una cuenta?</a>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
</body>

</html>