<?php
session_start(); // Asegúrate de iniciar la sesión al inicio del archivo
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa = $_POST['placa'];
    $contrasenia = $_POST['contrasenia'];

    // Validar entrada
    if (empty($placa) || empty($contrasenia)) {
        header("Location: ../mains/iniciarsesion.php?error=Debes%20llenar%20todos%20los%20campos.");
        exit();
    } else {
        // Consultar si la placa existe y obtener la contraseña y rol
        $sql = "SELECT Contrasenia, Rol FROM vehiculos WHERE Placa = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $placa);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            header("Location: ../mains/iniciarsesion.php?error=Placa%20no%20registrada.");
            exit();
        } else {
            $row = $result->fetch_assoc();
            $hash = $row['Contrasenia'];
            $rol = $row['Rol']; // Obtener el rol

            // Verificar la contraseña
            if (password_verify($contrasenia, $hash)) {
                // Aquí se inicia la sesión guardando la placa y el rol
                $_SESSION['placa'] = $placa; // Guardar la placa en la sesión
                $_SESSION['rol'] = $rol; // Guardar el rol en la sesión

                // Redirigir según el rol
                if ($rol === 'Operador') {
                    header("Location: ../operators/index_operador.php"); // Redirigir al index de operadores
                    exit();
                } elseif ($rol === 'Administrador') {
                    header("Location: ../admin/index_admin.php"); // Redirigir al index de administrador
                    exit();
                } else {
                    header("Location: ../mains/index.php"); // Redirigir al index de usuarios
                    exit();
                }
            } else {
                header("Location: ../mains/iniciarsesion.php?error=Contraseña%20incorrecta.");
                exit();
            }
        }
    }
}
?>



