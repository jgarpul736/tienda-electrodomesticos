<?php

class CarritoBBDD{

    private $conexion;

    public function __construct($host = 'bbdd', $user = 'root', $pass = 'Ciclo2gs', $bd = 'tienda'){
        $this->conexion = new PDO("mysql:host=$host;dbname=$bd;charset=utf8", $user, $pass);
    }

    public function getConexion(){
        return $this->conexion;
    }

    public function insertarEnCarrito($idUsuario, $idProducto, $cantidad){

        $consultaInsertar = $this->conexion->prepare("INSERT INTO carrito (idusuario, idproducto, cantidad) VALUES(?,?,?)");

        $consultaInsertar->bindParam(1, $idUsuario);
        $consultaInsertar->bindParam(2, $idProducto);
        $consultaInsertar->bindParam(3, $cantidad);

        $resultado = $consultaInsertar->execute();

        if ($resultado) {
            echo "Producto insertado correctamente";
        } else {
            echo "Error al insertar el producto";
        }

    }


    public function getProductosEnCarrito($idUsuario) {
        $resultado = $this->conexion->prepare("SELECT c.*, p.nombre, p.marca, p.modelo, p.precio, p.stock
        FROM carrito c
        JOIN productos p ON c.idproducto = p.idproducto
        WHERE c.idusuario = ?");
        $resultado->execute([$idUsuario]);
        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }



}