<?php
class UsuarioBBDD{
    private $conexion;

    public function __construct($host = 'bbdd', $user = 'root', $pass = 'Ciclo2gs', $bd = 'tienda'){
        $this->conexion = new PDO("mysql:host=$host;dbname=$bd;charset=utf8", $user, $pass);
    }



    public function insertarUsuario($usuario) {

        if ($this->getUsuario($usuario->email)) {
            echo "El usuario con este email ya existe";
            return false;
        }

        $consultaInsertar = $this->conexion->prepare("INSERT INTO usuarios (dni, apellidos, nombre, email, password, rol) VALUES (:dni, :apellidos, :nombre, :email, :password, :rol)");

        $dni = $usuario->dni;
        $apellidos = $usuario->apellidos;
        $nombre = $usuario->nombre;
        $email = $usuario->email;
        $password = $usuario->password;
        $rol = $usuario->rol;

        $consultaInsertar->bindParam(':dni', $dni, PDO::PARAM_STR);
        $consultaInsertar->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
        $consultaInsertar->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $consultaInsertar->bindParam(':email', $email, PDO::PARAM_STR);
        $consultaInsertar->bindParam(':password', $password, PDO::PARAM_STR);
        $consultaInsertar->bindParam(':rol', $rol, PDO::PARAM_STR);


        $resultado = $consultaInsertar->execute(); // -> Devuelve un booleano

        if ($resultado) {
            echo "Usuario insertado correctamente";
        } else {
            echo "Error al insertar el usuario";
        }
    }


    public function getUsuario($email) {
        // Ejecutamos la consulta con PDO
        $resultado = $this->conexion->query("SELECT * FROM usuarios WHERE email = " . $this->conexion->quote($email));

        // Recorremos los resultados (aunque solo debería haber uno)
        while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
            // Creamos un objeto Usuario con los datos de la fila
            $u = new Usuario($fila['dni'], $fila['apellidos'], $fila['nombre'], $fila['email'], $fila['password'], $fila['rol']);
            $u->idusuario = $fila['idusuario'];
            return $u; // Devolvemos el primer usuario encontrado
        }

        // Si no se encontró ningún usuario
        return null;
    }


    public function validarUsuario($email, $passwordTextoPlano){
        $usuario = $this->getUsuario($email);

        if($usuario != null){
            return password_verify($passwordTextoPlano, $usuario->password);
        } else {
            return false;
        }

    }
}