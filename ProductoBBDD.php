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

    // Validar que es número
    private function esNumeroValido($valor) {
        return is_numeric($valor) && $valor > 0;
    }

    // Obtener mensaje de error personalizado
    public function obtenerErrorPrecioStock($precio, $stock) {
        if (!$this->esNumeroValido($precio)) {
            return "❌ El precio debe ser un número válido (mayor que 0). Ej: 99.99 o 100";
        }
        if (!$this->esNumeroValido($stock)) {
            return "❌ El stock debe ser un número entero válido (mayor que 0). Ej: 10 o 50";
        }
        return null; // Sin errores
    }

    public function insertarProducto($producto) {
        // VALIDACIÓN PERSONALIZADA
        $errorValidacion = $this->obtenerErrorPrecioStock($producto->precio, $producto->stock);
        if ($errorValidacion) {
            return [
                'exito' => false,
                'mensaje' => $errorValidacion
            ];
        }

        $consultaInsertar = $this->conexion->prepare(
            "INSERT INTO productos (nombre, marca, modelo, precio, stock) VALUES (?, ?, ?, ?, ?)"
        );

        $nombre = $producto->nombre;
        $marca = $producto->marca;
        $modelo = $producto->modelo;
        $precio = (float)$producto->precio;
        $stock = (int)$producto->stock;

        // BindParam con los 5 parámetros correctos
        $consultaInsertar->bindParam(1, $nombre);
        $consultaInsertar->bindParam(2, $marca);
        $consultaInsertar->bindParam(3, $modelo);
        $consultaInsertar->bindParam(4, $precio);
        $consultaInsertar->bindParam(5, $stock);

        try {
            $resultado = $consultaInsertar->execute();

            if ($resultado) {
                return [
                    'exito' => true,
                    'mensaje' => "✅ Producto insertado correctamente."
                ];
            } else {
                return [
                    'exito' => false,
                    'mensaje' => "❌ Error al insertar el producto."
                ];
            }
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => "❌ Error en la base de datos: " . $e->getMessage()
            ];
        }
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
        // VALIDACIÓN PERSONALIZADA
        $errorValidacion = $this->obtenerErrorPrecioStock($precio, $stock);
        if ($errorValidacion) {
            return [
                'exito' => false,
                'mensaje' => $errorValidacion
            ];
        }

        $consultaActualizar = $this->conexion->prepare("UPDATE productos SET nombre = ?, marca = ?, modelo = ?, precio = ?, stock = ? WHERE idproducto = ?");

        // Convertir a tipos correctos
        $precio = (float)$precio;
        $stock = (int)$stock;
        $idproducto = (int)$idproducto;

        $consultaActualizar->bindParam(1, $nombre);
        $consultaActualizar->bindParam(2, $marca);
        $consultaActualizar->bindParam(3, $modelo);
        $consultaActualizar->bindParam(4, $precio);
        $consultaActualizar->bindParam(5, $stock);
        $consultaActualizar->bindParam(6, $idproducto);

        try {
            $resultado = $consultaActualizar->execute();

            if ($resultado) {
                return [
                    'exito' => true,
                    'mensaje' => "✅ Producto actualizado correctamente."
                ];
            } else {
                return [
                    'exito' => false,
                    'mensaje' => "❌ Error al actualizar el producto."
                ];
            }
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => "❌ Error en la base de datos: " . $e->getMessage()
            ];
        }
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