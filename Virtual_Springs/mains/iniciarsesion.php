<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Virtual Strings</title>
    <link rel="icon" href="assets/imgs/img_3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        /* Asegurar que los placeholders sean visibles */
        input::placeholder {
            color: #6c757d !important;
            opacity: 1 !important;
        }

        .error-message {
            color: red;
            font-size: 0.9em;
            display: none;
        }

        .recover-password {
            color: ccc;
            cursor: pointer;
        }

        .recover-password:hover {
            text-decoration: underline;
        }
        .img_ojo {
            cursor: pointer;
            position: absolute;
            right: 50px;
            top: 50%;
            transform: translateY(-50%);
        }
        @media (max-width: 940px) {
            .img_ojo {
                left: 290px;
                top: 50%;
            }
        }
    </style>
</head>

<body>
    <nav class="custom-nav navbar-expand-lg">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <ul class="custom-navbar">
                <li class="custom-navbar-item">
                    <a href="../subs/indexUNLOG.html" class="custom-navbar-link hover-effect">
                        <img class="custom-icon" src="Assets/imgs/img_10.png" width="23" height="23"> Volver al inicio
                    </a>
                </li>
            </ul>
            <div class="custom-logo">
                <img src="Assets/imgs/img_3.png">
            </div>
        </div>
    </nav>

    <form action="../metods/iniciarsesion.php" method="POST" class="custom-box" id="formulario">
        <h1>Accede a tu cuenta</h1>

        <!-- Campo Placa -->
        <div class="mb-3">
            <input type="text" id="placa" name="placa" required placeholder="Placa" class="form-control">
        </div>

        <!-- Campo Contraseña -->
        <div class="mb-3 position-relative">
            <input type="password" id="contrasenia" name="contrasenia" placeholder="Contraseña" class="form-control" minlength="6">
            <span class="error-message" id="passwordError">La contraseña debe tener al menos 6 caracteres, una mayúscula y un número.</span>
            <span class="toggle-password img_ojo" onclick="togglePasswordVisibility()">
                <img id="toggleIcon" src="Assets/imgs/img_18-.png" width="20">
            </span>
        </div>

        <input type="submit" value="Iniciar Sesión" class="btn btn-primary">
        <input type="submit" value="¿No tienes cuenta? ¡Regístrate!" class="btn btn-primary"
            onclick="event.preventDefault(); window.location.href='Registro.html';">
        <p class="recover-password" onclick="window.location.href='../subs/recuperar_contrasena.html';">¿Olvidaste tu contraseña?</p>
    </form>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('contrasenia');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.src = 'Assets/imgs/img_17.png'; // Cambia a un ícono de "ojo cerrado"
            } else {
                passwordInput.type = 'password';
                toggleIcon.src = 'Assets/imgs/img_18-.png'; // Cambia de nuevo al ícono de "ojo abierto"
            }
        }
    </script>
</body>

</html>