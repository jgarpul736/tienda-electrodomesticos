<?php

class ProductoBBDD{
    private $conexion;

    public function __construct($host = 'bbdd', $user = 'root', $pass = 'Ciclo2gs', $bd = 'tienda'){
        $this->conexion = new PDO("mysql:host=$host;dbname=$bd;charset=utf8", $user, $pass);
    }

    public function getConexion(){
        return $this->conexion;
    }

    public function cantidadReservada($idusuario, $idproducto) {
        $stmt = $this->conexion->prepare("
            SELECT SUM(cantidad) AS reservado 
            FROM carrito 
            WHERE idusuario = :idusuario AND idproducto = :idproducto
        ");
        $stmt->execute([
            ':idusuario' => $idusuario,
            ':idproducto' => $idproducto
        ]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? (int)$fila['reservado'] : 0;
    }

    public function insertarProducto($producto) {
        $consultaInsertar = $this->conexion->prepare(
            "INSERT INTO productos (nombre, marca, modelo, precio, stock) VALUES (?, ?, ?, ?, ?)"
        );

        $nombre = $producto->nombre;
        $marca = $producto->marca;
        $modelo = $producto->modelo;
        $precio = $producto->precio;
        $stock = $producto->stock;

        // BindParam con los 5 parámetros correctos
        $consultaInsertar->bindParam(1, $nombre);
        $consultaInsertar->bindParam(2, $marca);
        $consultaInsertar->bindParam(3, $modelo);
        $consultaInsertar->bindParam(4, $precio);
        $consultaInsertar->bindParam(5, $stock);

        $consultaInsertar->execute(); // Devuelve un booleano

    }

    public function getProductos(){
        $resultado = $this->conexion->query("SELECT * FROM productos");

        $productosArray = [];

        // Recorre las filas de la tabla
        while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $productosArray[] = $fila;
        }

        return $productosArray;
    }

    public function existeProductoPorId($idproducto) {
        $consultaExisteID = $this->conexion->prepare("SELECT COUNT(*) AS total FROM productos WHERE idproducto = :id");
        $consultaExisteID->execute([':id' => $idproducto]);
        $fila = $consultaExisteID->fetch(PDO::FETCH_ASSOC);
        return $fila && $fila['total'] > 0;
    }

    public function eliminarProductoPorId($idproducto) {
        $consultaEliminarProducto = $this->conexion->prepare("DELETE FROM productos WHERE idproducto = :id");
        $consultaEliminarProducto->execute([':id' => $idproducto]);
    }

    public function actualizarProductos($idproducto, $nombre, $marca, $modelo, $precio, $stock) {
        $consultaActualizar = $this->conexion->prepare("UPDATE productos SET nombre = ?, marca = ?, modelo = ?, precio = ?, stock = ? WHERE idproducto = ?");

        $consultaActualizar->bindParam(1, $nombre);
        $consultaActualizar->bindParam(2, $marca);
        $consultaActualizar->bindParam(3, $modelo);
        $consultaActualizar->bindParam(4, $precio);
        $consultaActualizar->bindParam(5, $stock);
        $consultaActualizar->bindParam(6, $idproducto);

        $consultaActualizar->execute();
    }

    public function existeProducto($nombre, $marca, $modelo){
        $stmt = $this->conexion->prepare("
        SELECT COUNT(*) AS total
        FROM productos
        WHERE nombre = :nombre AND marca = :marca AND modelo = :modelo
    ");
        $stmt->execute([
            ':nombre' => $nombre,
            ':marca'  => $marca,
            ':modelo' => $modelo
        ]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila && $fila['total'] > 0;
    }



}