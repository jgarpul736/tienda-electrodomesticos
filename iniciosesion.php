<?php
require_once 'UsuarioBBDD.php';
require_once 'Usuario.php';

session_start();

$usuarioBBDD = new UsuarioBBDD();

if(isset($_POST['iniciarSesion'])){
    if(isset($_POST['email']) && isset($_POST['password'])){
        $email = $_POST['email'];
        $pass = $_POST['password'];

        if($usuarioBBDD->validarUsuario($email, $pass)){
            $usuario = $usuarioBBDD->getUsuario($email);

            $_SESSION['usuario']['email'] = $usuario->email;
            $_SESSION['usuario']['nombre'] = $usuario->nombre;
            $_SESSION['usuario']['idusuario'] = $usuario->idusuario;
            $_SESSION['usuario']['rol'] = $usuario->rol;


            header('Location: tienda.php');
            exit;
        } else {
            $_SESSION['errorInicioSesion'] = "El email o la contraseña son incorrectos";
            header('Location: index.php');
            exit;
        }
    } else {
        $_SESSION['errorRegistro'] = "Faltan datos en el registro";
        header('Location: registro.php');
        exit;
    }

}
?>