<?php
require_once "Usuario.php";
require_once "UsuarioBBDD.php";

session_start();
?>
    <style>
        h3 {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 20px;
            font-size: 1.5em;
            color: #333;
        }

        body {
            background: #f3f4f6;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            margin-top: -100px;
        }

        form {
            background: white;
            padding: 30px 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            position: relative;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #4f46e5;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #4338ca;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #4f46e5;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .registro {
            display: block;
            width: 94%;
            background-color: #4f46e5;
            color: white !important;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }

        .registro:hover {
            background-color: #4338ca;
        }

        .blanco {
            display: none;
        }

    </style>

    <h3>Iniciar Sesión</h3>

    <form method="post" action="iniciosesion.php">

        <label for="email">Email: </label>
        <input type="text" id="email" name="email" required/><br>

        <label for="password">Password: </label>
        <input type="password" id="password" name="password" required/><br>

        <input type="submit" value="Iniciar Sesión" name="iniciarSesion">
        <a href="registro.php" class="registro"> Registrarse<input type="submit" value="" name="registrarse" class="blanco"></form>

    <a href="#">¿Has olvidado tu contraseña?</a>
    </form>

<?php

if (isset($_SESSION['errorInicioSesion'])) {
    echo "<b><h3 style='color:red'>" . $_SESSION['errorInicioSesion'] . "</h3></b>";
    unset($_SESSION['errorInicioSesion']); // lo elimino para que no se repita
}


if(isset($_POST['registrar'])){

    $dni = $_POST['dni'];
    $ape = $_POST['apellidos'];
    $nom = $_POST['nombre'];
    $email = $_POST['email'];

    $pass = $_POST['password'];
    $hash = password_hash($pass, PASSWORD_DEFAULT); // Crear y mostrar contraseña cifrada
    $rol = isset($_POST['rol']) ? $_POST['rol'] : 1;

    $usuario = new Usuario($dni, $ape, $nom, $email, $hash, $rol);

    $usuarioBBDD = new UsuarioBBDD();
    $usuarioBBDD->insertarUsuario($usuario);


}


?>