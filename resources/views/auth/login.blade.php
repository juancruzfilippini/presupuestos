<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #eaeaea;
            /* Fondo suave */
        }

        .login-form {
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

        .login-logo img {
            max-width: 150px;
            margin-bottom: 30px;
        }

        /* Estilo para el contenedor */
        .container {
            background-color: #eaeaea;
            padding: 50px;
            border-radius: 15px;
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="login-form card p-4">
            <div class="text-center mb-4 login-logo">
                <img src="https://github.com/juancruzfilippini/logo-presupuestos/blob/main/hu_logo.png?raw=true"
                    alt="Logo" class="img-fluid">
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" class="form-control" required autofocus>
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input id="password" type="password" name="password" class="form-control" required>
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-3 form-check">
                    <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
                    <label for="remember_me" class="form-check-label">Recordar</label>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-link">Crear una cuenta</a>
                    @endif
                    <button type="submit" class="btn btn-primary">Ingresar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>