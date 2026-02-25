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
            width: 100%;
            max-width: none;
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

        .contenedor-flex {
            display: flex;
            flex-wrap: wrap;   /* permite que los elementos salten de línea */
            justify-content: center;
            gap: 40px;         /* controla el espacio entre form y tabla */
        }

        .tabla-lateral {
            display: flex;
            align-items: flex-start;
            gap: 20px; /* separación bonita */
        }

        /* La tabla ocupa todo el espacio necesario */
        .tabla-lateral table {
            flex: 1;
        }

        /* Los forms de eliminar se muestran en columna */
        .tabla-lateral form {
            display: block;
            margin: 0;
        }

        /* Botón estilizado alineado a la izquierda */
        #botonEliminar {
            background-color: #dc2626;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            display: block;
            width: 140px;
            margin-bottom: 12px;
        }

        #botonEliminar:hover {
            background-color: #b91c1c;
        }



        /* --- Inputs estilizados --- */
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px 10px;
            border: 2px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus, input[type="number"]:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 5px rgba(79,70,229,0.4);
            outline: none;
        }

        /* --- Mensajes de estado --- */
        .mensaje {
            flex-basis: 100%;  /* ocupa todo el ancho */
            order: -1;         /* se coloca antes que el formulario y la tabla */
            text-align: center;
            font-weight: bold;
            font-size: 22px;
            margin-bottom: 20px;
        }


        .mensaje.exito {
            color: #16a34a;
        }

        .mensaje.error {
            color: #dc2626;
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

    ?>


    <h2>Eliminar Productos</h2>
    <div class="contenedor-flex">

        <?php


        // --- Conexión y carga de productos ---
        $productoBBDD = new ProductoBBDD();

        if(isset($_POST['eliminarProducto'])){
            $idproducto = $_POST['idproducto'];

            if ($productoBBDD->existeProductoPorId($idproducto)) {
                $productoBBDD->eliminarProductoPorId($idproducto);
                echo '<div class="mensaje exito">Producto eliminado correctamente.</div>';
            } else {
                echo '<div class="mensaje error">El producto no existe en la base de datos.</div>';
            }

            // Recargar la lista de productos después de eliminar
            $productosBD = $productoBBDD->getProductos();
        }

        // Inserta los productos base si no existen
        $productosBase = [
                new Producto("1", "Lavadora", "Bosch", "Ultra", 400, 48),
                new Producto("2", "Secadora", "Balay", "Master", 250, 23),
                new Producto("3", "Batidora", "Siemens", "TheBoss", 175, 34),
                new Producto("4", "Microondas", "Elica", "Electricy", 300, 35),
                new Producto("5", "Plancha", "Aeg", "Novemba", 350, 6)
        ];

        // Obtiene todos los productos desde la base de datos
        $productosBD = $productoBBDD->getProductos();
        ?>
        <div class="tabla-lateral">
            <table class="tabla-productos">
                <tr>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Precio (€)</th>
                    <th>Stock</th>
                </tr>

                <?php
                foreach ($productosBD as $producto):

                    // Calcular stock disponible
                    $reservado = $productoBBDD->cantidadReservada($_SESSION['usuario']['idusuario'], $producto['idproducto']);
                    $maxDisponible = max(0, $producto['stock'] - $reservado);
                ?>

                <tr>
                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($producto['marca']); ?></td>
                    <td><?php echo htmlspecialchars($producto['modelo']); ?></td>
                    <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                    <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                    <td>
                        <form method="post" action="eliminarProductos.php">
                            <input type="hidden" name="idproducto" value="<?php echo htmlspecialchars($producto['idproducto']); ?>">
                            <button id="botonEliminar" type="submit" name="eliminarProducto">Eliminar Producto</button>
                        </form>
                    </td>
                </tr>

                <?php endforeach; ?>
            </table>


        </div>
    </div>

    <a href="tienda.php" style="background-color: #4f46e5; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; margin-bottom: 10px">Volver a la tienda</a>

    <a href="actualizarProductos.php" style="background-color: #4f46e5; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;  margin-bottom: 10px">Actualizar Productos </a>

    <a href="insertarProducto.php" style="background-color: #4f46e5; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;  margin-bottom: 10px">Insertar Producto </a>

    <?php


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