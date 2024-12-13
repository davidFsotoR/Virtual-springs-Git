<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../mains/style.css">
    <title>Recuperar contraseña </title>
    <link rel="icon" href="../mains/assets/imgs/img_3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <nav class="custom-nav navbar-expand-lg">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <ul class="custom-navbar">
                <li class="custom-navbar-item">
                    <a href="../mains/iniciarsesion.php" class="custom-navbar-link hover-effect">
                        <img class="custom-icon" src="../mains/Assets/imgs/img_10.png" width="23" height="23"> Cancelar
                    </a>
                </li>
            </ul>
            <div class="custom-logo">
                <img src="../mains/Assets/imgs/img_3.png">
            </div>
        </div>
    </nav>

    <form class="custom-box" action="../metods/procesar_recuperacion.php" method="POST">
        <h1>Recuperar Contraseña</h1>
        <h4>Escribe el correo asociado a tu cuenta:</h4>
        <input type="email" name="email" required>
        <input type="submit" value="Recuperar Contraseña">
    </form>

    <!-- Mostrar mensajes de error o éxito aquí -->
    <script>
        <?php
        // Verificamos si hay mensajes de error
        if (isset($_GET['error'])) {
            echo "Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{$_GET['error']}',
                confirmButtonText: 'Aceptar'
            });";
        }
        ?>
    </script>
</body>

</html>

