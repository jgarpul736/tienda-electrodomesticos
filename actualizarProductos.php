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
            justify-content: center;
            align-items: flex-start;
            gap: 40px; /* espacio entre formulario y tabla */
            flex-wrap: wrap;
        }

        /* Formulario lateral (izquierda de la tabla) */
        .formulario-lateral {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 280px;
            min-width: 260px;
        }

        /* Título dentro del formulario */
        .formulario-lateral h3 {
            text-align: center;
            color: #333;
            margin-bottom: 15px;
        }

        /* Campos del formulario */
        .formulario-lateral label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
            color: #444;
        }

        .formulario-lateral input[type="text"],
        .formulario-lateral input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .formulario-lateral input[type="text"]:focus,
        .formulario-lateral input[type="number"]:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 5px rgba(79, 70, 229, 0.4);
        }

        /* Botón de actualizar dentro del formulario */
        .formulario-lateral input[type="submit"] {
            background-color: #4f46e5;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
            width: 100%;
        }

        .formulario-lateral input[type="submit"]:hover {
            background-color: #4338ca;
        }

        /* Tabla de productos */
        .tabla-lateral {
            flex: 1; /* ocupa el resto del espacio */
            min-width: 500px;
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
        #botonActualizar {
            background-color: #b99a1c;
            b99a1c
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            display: block;
            width: 140px;
            margin-bottom: 12px;
        }

        #botonActualizar:hover {
            background-color: #efdd96;
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
            box-shadow: 0 0 5px rgba(79, 70, 229, 0.4);
            outline: none;
        }

        /* --- Mensajes de estado --- */
        .mensaje {
            flex-basis: 100%; /* ocupa todo el ancho */
            order: -1; /* se coloca antes que el formulario y la tabla */
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


    <h2>Actualizar Productos</h2>
    <div class="contenedor-flex">


        <?php


        // --- Conexión y carga de productos ---
        $productoBBDD = new ProductoBBDD();

        if (isset($_POST['ActualizarProducto']) && isset($_POST['idproducto']) &&
                isset($_POST['nombreInsertar']) && isset($_POST['marcaInsertar']) && isset($_POST['modeloInsertar']) &&
                isset($_POST['precioInsertar']) && isset($_POST['stockInsertar'])) {
            $idproducto = $_POST['idproducto'];
            $nombre = $_POST['nombreInsertar'];
            $marca = $_POST['marcaInsertar'];
            $modelo = $_POST['modeloInsertar'];
            $precio = $_POST['precioInsertar'];
            $stock = $_POST['stockInsertar'];

            if ($productoBBDD->existeProductoPorId($idproducto)) {

                $productoBBDD->actualizarProductos($idproducto, $nombre, $marca, $modelo, $precio, $stock);

                echo '<div class="mensaje exito">Producto actualizado correctamente.</div>';
            } else {
                echo '<div class="mensaje error">El producto no se puede actualizar</div>';
            }
            // Recargar la lista de productos después de actualizar
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
                            <form method="post" action="actualizarProductos.php">
                                <input type="hidden" name="nombre"
                                       value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <input type="hidden" name="marca"
                                       value="<?php echo htmlspecialchars($producto['marca']); ?>">
                                <input type="hidden" name="modelo"
                                       value="<?php echo htmlspecialchars($producto['modelo']); ?>">
                                <input type="hidden" name="precio"
                                       value="<?php echo htmlspecialchars($producto['precio']); ?>">
                                <input type="hidden" name="stock"
                                       value="<?php echo htmlspecialchars($producto['stock']); ?>">
                                <input type="hidden" name="idproducto"
                                       value="<?php echo htmlspecialchars($producto['idproducto']); ?>">
                                <button id="botonActualizar" type="submit" name="actualizarProducto">Actualizar Producto</button>
                            </form>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </table>


        </div>
    </div>

    <div class="contenedor-flex">
    <?php
    if (isset($_POST['actualizarProducto'])) {
        $idproducto = $_POST['idproducto'];
        $nombre = $_POST['nombre'];
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];
        ?>
        <div class="formulario-lateral">
            <form method="post" action="actualizarProductos.php">
                <input type="hidden" name="idproducto" value="<?php echo $idproducto ?>">

                <label for="nombre">Nombre: </label>
                <input type="text" id="name" name="nombreInsertar" value="<?php echo $nombre ?>"/><br><br>

                <label for="marca">Marca: </label>
                <input type="text" id="marca" name="marcaInsertar" value="<?php echo $marca ?>"/><br><br>

                <label for="modelo">Modelo: </label>
                <input type="text" id="modelo" name="modeloInsertar" value="<?php echo $modelo ?>"/><br><br>

                <label for="precio">Precio: </label>
                <input type="text" id="precio" name="precioInsertar" value="<?php echo $precio ?>"/><br><br>

                <label for="stock">Stock: </label>
                <input type="text" id="stock" name="stockInsertar" value="<?php echo $stock ?>"/><br><br>

                <input type="submit" value="Actualizar Producto" name="ActualizarProducto">
            </form>
        </div>
        </div>
        <?php
    }

    ?>

    <a href="tienda.php"
       style="background-color: #4f46e5; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; margin-bottom: 10px">Volver
        a la tienda</a>

    <a href="eliminarProductos.php"
       style="background-color: #4f46e5; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;  margin-bottom: 10px">Eliminar
        Productos </a>

    <a href="insertarProducto.php"
       style="background-color: #4f46e5; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;  margin-bottom: 10px">Insertar
        Producto </a>

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