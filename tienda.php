<?php
require_once "Usuario.php";
require_once "Producto.php";
require_once "ProductoBBDD.php";

session_start();

?>

    <style>
        body {
            background: #f3f4f6;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 2em;
        }

        .tabla-productos {
            width: 90%;
            max-width: 900px;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .tabla-productos th, .tabla-productos td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        .tabla-productos th {
            background-color: #4f46e5;
            color: white;
            font-weight: bold;
        }

        .tabla-productos tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        button, input[type="submit"] {
            background-color: #4f46e5;
            color: white;
            padding: 10px 15px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }

        button:hover, input[type="submit"]:hover {
            background-color: #4338ca;
        }

        input[type="number"] {
            width: 60px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
        }
    </style>

<?php
if (isset($_SESSION['usuario'])) {

    echo "<p>Bienvenido: " . htmlspecialchars($_SESSION['usuario']['nombre']) . "</p>";
    ?>
    <form method="post" action="logout.php" onsubmit="return confirm('¿Estás seguro de que quieres cerrar sesión?')">
        <input type="submit" value="Cerrar Sesión">
    </form>
    <?php

    // --- Conexión y carga de productos ---
    $productoBBDD = new ProductoBBDD();

    // Obtiene todos los productos desde la base de datos
    $productosBD = $productoBBDD->getProductos();
    ?>

    <h2>Tienda de Electrodomésticos</h2>

    <table class="tabla-productos">
        <tr>
            <th>Nombre</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Precio (€)</th>
            <th>Stock</th>
            <th>Acción</th>
        </tr>

        <?php
        foreach ($productosBD as $producto):

            // Calcular stock disponible
            $reservado = $productoBBDD->cantidadReservada($_SESSION['usuario']['idusuario'], $producto['idproducto']);
            $maxDisponible = max(0, $producto['stock'] - $reservado);
            ?>


            <form method="post" action="carrito.php">
                <tr>
                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($producto['marca']); ?></td>
                    <td><?php echo htmlspecialchars($producto['modelo']); ?></td>
                    <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                    <td>
                        <?php if ($maxDisponible > 0) { ?>
                            <input type="number" name="cantidad" value="1" min="1" max="<?php echo $maxDisponible; ?>">
                        <?php } else { ?>
                            <span style="color:red;">Sin stock</span>
                        <?php }; ?>
                    </td>

                    <input type="hidden" name="idproducto"
                           value="<?php echo htmlspecialchars($producto['idproducto']); ?>">

                    <td>
                        <?php if ($maxDisponible > 0) { ?>
                            <button type="submit" name="anadidoACarrito">Añadir al carrito</button>
                        <?php } else { ?>
                            <button type="button" disabled style="background-color: gray;">No disponible</button>
                        <?php }; ?>
                    </td>
                </tr>
            </form>
        <?php endforeach; ?>
    </table>

    <form action="carrito.php" method="post">
        <button type="submit">Ver Carrito</button>
    </form>

    <?php
    if(isset($_SESSION['usuario']['rol']) && $_SESSION['usuario']['rol'] == 0){
        ?>

        <form action="insertarProducto.php" method="post">
            <button type="submit" name="insertarProducto">Insertar Producto</button>
        </form>

        <form action="actualizarProductos.php" method="post">
            <button type="submit" name="actualizarProductos">Actualizar Productos</button>
        </form>

        <form action="eliminarProductos.php" method="post">
            <button type="submit" name="eliminarProductos">Eliminar Productos</button>
        </form>
        <?php
    }

    if (isset($_POST['anadidoACarrito'])) {
        $id = $_POST['idproducto'];
        foreach ($productosBD as $producto) {
            if ($producto['idproducto'] == $id) {
                echo "<h4 style='color:green;'>Se ha añadido " . htmlspecialchars($producto['nombre']) . " " . htmlspecialchars($producto['marca']) . " al carrito</h4>";
            }
        }
    }

} else {
    echo "<h3 style='color: red;'>¿Dónde vas? Ve a registrarte o iniciar sesión</h3>";
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