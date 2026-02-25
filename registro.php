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
        min-height: 100vh;
        margin: 0;
        margin-top: 100px;
    }

    form {
        background: white;
        padding: 20px 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        width: 300px;
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
    }
</style>

<h3>Registro de usuarios</h3>

<form method="post" action="index.php">

    <label for="dni">DNI: </label>
    <input type="text" id="dni" name="dni" required/><br>

    <label for="apellidos">Apellidos: </label>
    <input type="text" id="apellidos" name="apellidos" required/><br>

    <label for="nombre">Nombre: </label>
    <input type="text" id="nombre" name="nombre" required/><br>

    <label for="email">Email: </label>
    <input type="email" id="email" name="email" required/><br>

    <label for="password">Password: </label>
    <input type="password" id="password" name="password" required/><br>

    <input type="submit" value="Registrar" name="registrar">
</form>

<?php

if (isset($_SESSION['errorRegistro'])) {
    echo "<p style='color:red'>" . $_SESSION['errorRegistro'] . "</p>";
    unset($_SESSION['errorRegistro']); // lo elimino para que no se repita
}

?>