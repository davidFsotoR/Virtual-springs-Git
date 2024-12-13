<?php
// Asegúrate de que los campos sean accesibles
if (!isset($_POST['placa'])) {
    die("Error: No se proporcionó la placa.");
}

if (!isset($_POST['contrasenia'])) {
    die("Error: No se proporcionó la contraseña.");
}

$placa = htmlspecialchars($_POST['placa']); // Sanitizar la placa
$contrasenia = htmlspecialchars($_POST['contrasenia']); // Sanitizar la contraseña

// Aquí puedes hacer lo que necesites con los datos, como almacenarlos en la base de datos
echo "Placa: " . $placa;
echo "Contraseña: " . $contrasenia;

// Aquí puedes proceder con las validaciones necesarias y la inserción en la base de datos.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../mains/style.css">
    <title>Virtual Springs</title>
    <link rel="icon" href="../mains/Assets/imgs/img_3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <style>
        /* Estilo para los mensajes de error */
        .error-message {
            color: red;
            font-size: 0.9em;
            display: none;
        }

        /* Asegurar que los placeholders sean visibles */
        input::placeholder {
            color: #6c757d;
            opacity: 1;
        }
        .custom-box h2 {
    color: white;
    font-weight: 100;
}
    </style>
</head>

<body>
    <nav class="custom-nav navbar-expand-lg">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <ul class="custom-navbar">
                <li class="custom-navbar-item">
                    <a href="../mains/Registro.html" class="custom-navbar-link hover-effect">
                        <img class="custom-icon" src="../mains/Assets/imgs/img_10.png" width="23" height="23"> Cancelar
                    </a>
                </li>
            </ul>
            <div class="custom-logo">
                <img src="../mains/Assets/imgs/img_3.png">
            </div>
        </div>
    </nav>
    <form action="../metods/guardar_datos.php" method="POST" class="custom-box" id="registrationForm">
        <h2>Bríndenos información extra para la placa "<?php echo $placa; ?>"</h2>
        <input type="hidden" name="placa" value="<?php echo $placa; ?>">
        <input type="hidden" name="contrasenia" value="<?php echo $contrasenia; ?>">

        <!-- Campo para el nombre del propietario -->
        <input type="text" id="propietario" name="propietario" required placeholder="Ingrese su nombre...">
        <span class="error-message" id="propError" style="display:none; color:red;">No se admiten números en este campo.</span>

        <!-- Campo para el número telefónico -->
        <input type="text" id="numero_propietario" name="numero_propietario" required placeholder="Ingrese su número telefónico...">
        <span class="error-message" id="phoneError" style="display:none; color:red;">El número debe tener máximo 10 dígitos y no contener letras ni símbolos.</span>

        <!-- Campo para el correo electrónico -->
        <input type="text" id="correo_propietario" name="correo_propietario" required placeholder="Ingrese su correo electrónico...">
        <span class="error-message" id="emailError" style="display:none; color:red;">Por favor, ingrese un correo válido.</span>

        <!-- Combobox para seleccionar el tipo de vehículo -->
        <select id="tipo_vehiculo" name="tipo_vehiculo" required>
            <option value="" disabled selected>Seleccione el tipo de vehículo...</option>
            <option value="Carro">Carro</option>
            <option value="Camion">Camión</option>
            <option value="Moto">Moto</option>
        </select>
        <input type="submit" value="Registrarse">
    </form>
    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            let isValid = true;

            // Validación de correo electrónico
            const email = document.getElementById('correo_propietario').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const emailError = document.getElementById('emailError');
            if (!emailRegex.test(email)) {
                emailError.style.display = 'block';
                isValid = false;
            } else {
                emailError.style.display = 'none';
            }

            // Validación del nombre del propietario (sin números)
            const propietario = document.getElementById('propietario').value;
            const propError = document.getElementById('propError');
            const nameRegex = /^[^\d]*$/; // No permite números
            if (!nameRegex.test(propietario)) {
                propError.style.display = 'block';
                isValid = false;
            } else {
                propError.style.display = 'none';
            }

            // Validación del número telefónico (máximo 10 dígitos, solo números)
            const numero = document.getElementById('numero_propietario').value;
            const phoneRegex = /^\d{1,10}$/; // Solo números, máximo 10 dígitos
            const phoneError = document.getElementById('phoneError');
            if (!phoneRegex.test(numero)) {
                phoneError.style.display = 'block';
                isValid = false;
            } else {
                phoneError.style.display = 'none';
            }

            // Prevenir el envío si hay errores
            if (!isValid) {
                event.preventDefault(); // Bloquea el envío del formulario
            }
        });
    </script>
</body>

</html>