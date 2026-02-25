<?php
require_once "Usuario.php";
require_once "Producto.php";
require_once "ProductoBBDD.php";
require_once "CarritoBBDD.php";

session_start();

?>

    <style>
        body {
            background: #f3f4f6;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            margin: 0;
            padding-top: 40px;
            font-family: "Segoe UI", Arial, sans-serif;
        }


        h2, h3 {
            text-align: center;
            color: #333;
        }


        .tabla-productos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fafafa;
            border-radius: 8px;
            overflow: hidden;
        }


        .tabla-productos th,
        .tabla-productos td {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px;
            text-align: center;
            color: #333;
        }


        .tabla-productos th {
            background-color: #4f46e5;
            color: white;
            font-weight: bold;
        }


        .tabla-productos tr:last-child td {
            border-bottom: none;
        }


        form {
            margin-top: 15px;
            text-align: center;
        }


        input[type="submit"], button {
            background-color: #4f46e5;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: bold;
        }


        input[type="submit"]:hover,
        button:hover {
            background-color: #4338ca;
        }


        a {
            display: inline-block;
            margin-top: 10px;
            color: #4f46e5;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.2s;
        }


        a:hover {
            color: #3730a3;
            text-decoration: underline;
        }

        .mensaje-vacio {
            margin-top: 40px;
            padding: 25px 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
            color: #555;
            font-size: 18px;
            text-align: center;
            width: 80%;
            max-width: 500px;
        }
    </style>
<?php

if (isset($_SESSION['usuario'])) {

    // Abro la conexión
    $productoBBDD = new ProductoBBDD();

    $productosBD = $productoBBDD->getProductos();

    $idUsuario = $_SESSION['usuario']['idusuario'];

    if (!isset($idUsuario)) {
        echo "Error: usuario no identificado";
        exit;
    }


    $carritoBBDD = new CarritoBBDD();
    $conexion = $carritoBBDD->getConexion();

    $productosCarrito = array();

    if (isset($_POST['eliminarProducto'])) {
        $idProducto = $_POST['idproducto'];


        // Obtener la cantidad que tenía en el carrito
        $sqlCantidad = $conexion->query("SELECT cantidad FROM carrito WHERE idusuario = $idUsuario AND idproducto = $idProducto");

        $cantidadEnCarrito = 0;
        if ($fila = $sqlCantidad->fetch(PDO::FETCH_ASSOC)) {
            $cantidadEnCarrito = $fila['cantidad'];
        }


        // Borra la fila del carrito usando PDO
        $consultaEliminar = $conexion->prepare("DELETE FROM carrito WHERE idusuario = ? AND idproducto = ?");
        $consultaEliminar->execute([$idUsuario, $idProducto]);


        header("Location: carrito.php"); // recargar página
        exit;
    }


    $nombre = $_SESSION['usuario']['nombre'];
    echo "Bienvenido: " . $nombre . "<br>";

    ?>

    <!-- Esto es para cerrar sesión -->
    <form method="post" action="logout.php" onsubmit="return confirm('¿Estás seguro de que quieres cerrar sesión?')">
        <input type="submit" value="Cerrar Sesión">
    </form>

    <?php

    echo "<h2>Carrito de la compra</h2>";





    if (isset($_POST['anadidoACarrito'])) {
        if (isset($_POST['idproducto'], $_POST['cantidad'])) {
            $idProducto = $_POST['idproducto'];
            $cantidadAAgregar = $_POST['cantidad'];

            $conexion = $carritoBBDD->getConexion();
            // Obtener cantidad actual en carrito
            $resultado = $conexion->query("SELECT cantidad FROM carrito WHERE idusuario = $idUsuario AND idproducto = $idProducto");

            $cantidadEnCarrito = 0;
            if ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $cantidadEnCarrito = $fila['cantidad'];
            }

            // Stock total en la BBDD usando PDO
            $resultadoStock = $conexion->query("SELECT stock FROM productos WHERE idproducto = $idProducto");
            $filaStock = $resultadoStock->fetch(PDO::FETCH_ASSOC);
            $stockTotal = $filaStock['stock'];


            // Cantidad ya reservada en el carrito (del mismo usuario)
            $resultadoReservado = $conexion->query("SELECT SUM(cantidad) AS reservado FROM carrito WHERE idusuario = $idUsuario AND idproducto = $idProducto");
            // Obtiene la fila como array asociativo
            $fila = $resultadoReservado->fetch(PDO::FETCH_ASSOC);

            // Si hay resultado, toma el valor; si no, 0
            $reservado = $fila ? $fila['reservado'] : 0;

            $stockDisponible = $stockTotal - $reservado;

            // Verificar stock disponible
            if ($cantidadAAgregar <= $stockTotal) {

                if ($cantidadEnCarrito > 0) {
                    // Actualizar cantidad en carrito
                    $nuevaCantidad = $cantidadEnCarrito + $cantidadAAgregar;

                    $consultaActualizar = "UPDATE carrito SET cantidad = ? WHERE idusuario = ? AND idproducto = ?";
                    $consultaActualizar = $conexion->prepare($consultaActualizar);
                    $consultaActualizar->execute([$nuevaCantidad, $idUsuario, $idProducto]);

                } else { // Insertar producto nuevo en carrito
                    $carritoBBDD->insertarEnCarrito($idUsuario, $idProducto, $cantidadAAgregar);
                }

            } else {
                echo "<p style='color:red'>No se pueden añadir más unidades de las existentes en stock (disponibles: $stockTotal)</p>";
            }
        }
    }


    $productosCarrito = $carritoBBDD->getProductosEnCarrito($idUsuario);

    if (!$productosCarrito) {
        $productosCarrito = []; // fuerza a ser array
    }
    ?>
    <table class="tabla-productos">
        <tr>
            <td>Nombre</td>
            <td>Marca</td>
            <td>Modelo</td>
            <td>Precio</td>
            <td>Unidades</td>
        </tr>
        <?php
        foreach ($productosCarrito as $producto) { ?>

            <tr>
                <td><?php echo $producto['nombre'] ?></td>
                <td><?php echo $producto['marca'] ?></td>
                <td><?php echo $producto['modelo'] ?></td>
                <td><?php echo $producto['precio'] ?></td>
                <td><?php echo $producto['cantidad'] ?></td>
                <form method="post" action="carrito.php"
                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar este producto del carrito?')">
                    <td>
                        <input type="hidden" name="idproducto" value="<?php echo $producto['idproducto'] ?>">
                        <button type="submit" name="eliminarProducto">Eliminar Producto</button>
                    </td>
                </form>
            </tr>
        <?php } ?>
    </table>


    <form method="post" action="tienda.php">
        <button type="submit">Volver a la tienda</button>
    </form>
    <form method="post" action="carrito.php">
        <button type="submit" name="finalizarCompra">Finalizar Compra</button>
    </form>


    <?php

// Finalizar compra
    if (isset($_POST['finalizarCompra'])) {
        $conexion = $carritoBBDD->getConexion();
        $conexion->beginTransaction();
        $error = false;


        $totalCompra = 0;


        foreach ($productosCarrito as $producto) {
            $pId = $producto['idproducto'];
            $cantidad = $producto['cantidad'];


            // Obtener stock actual del producto
            $resultadoStock = $conexion->query("SELECT stock FROM productos WHERE idproducto = $pId");
            $filaStock = $resultadoStock->fetch(PDO::FETCH_ASSOC);
            $stock = $filaStock['stock'];


            if ($stock >= $cantidad) {
                // Actualizar stock usando PDO
                $stmtActualizar = $conexion->prepare(
                        "UPDATE productos SET stock = stock - :cantidad WHERE idproducto = :idproducto"
                );
                $stmtActualizar->execute([
                        ':cantidad'   => $cantidad,
                        ':idproducto' => $pId
                ]);

                $totalCompra += $cantidad * $producto['precio'];
            } else {
                $error = true;
                break;
            }

        }


        if ($error) {
            $conexion->rollback();
            echo "<p style='color:red'>Error: Stock insuficiente para completar la compra.</p>";
        } else {
            // Borrar carrito
            foreach ($productosCarrito as $producto) {
                $consultaEliminarCarrito = $conexion->prepare("DELETE FROM carrito WHERE idusuario = ? AND idproducto = ?");
                $consultaEliminarCarrito->execute([$idUsuario, $producto['idproducto']]);
            }


            $conexion->commit();


            // Guardar datos en sesión para factura
            $_SESSION['compra'] = $productosCarrito;
            $_SESSION['total_compra'] = $totalCompra;


            /** FACTURA **/
            $productosCarrito = [];
            $precioTotal = 0;

            if (isset($_SESSION['compra']) && !empty($_SESSION['compra'])) {
                $productosCarrito = $_SESSION['compra'];
            }

            if (isset($_SESSION['total_compra']) && !empty($_SESSION['total_compra'])) {
                $precioTotal = $_SESSION['total_compra'];
            } else {
                echo "<div class='mensaje-vacio'>No hay productos para mostrar en la factura.</div>";
                exit;
            }


            echo "== TOTAL DE LA COMPRA ==" . "<br>" .
                    "-----------------------------------------------------------------------" . "<br>";

            foreach ($productosCarrito as $producto) {
                echo $producto['nombre'] . " " . $producto['marca'] . " " . $producto['modelo'] . " -- " . $producto['cantidad'] . " x " . $producto['precio'] . "€ = " . ($producto['cantidad'] * $producto['precio']) . "€" . "<br>";
            };

            echo "-----------------------------------------------------------------------" . "<br>" .
                    "El total es: $precioTotal €";

            ?>
            <br>
            <a href="tienda.php">Regresar a la tienda</a> <?php
        }
    }
} else { // Por si se mete alguien directamente en carrito.php sin haber inciiado sesión
    echo "<h3 style='color: red;'>Donde vas? ve a registrarte o a iniciar sesión</h3>";
    ?>
    <form method="post" action="index.php">
        <input type="submit" value="Iniciar Sesión">
    </form>
    <form method="post" action="registro.php">
        <input type="submit" value="Registrarse">
    </form>
    <?php
}
?>