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

        .formulario-lateral {
            background: white;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            flex: 0 0 340px;
        }

        .tabla-lateral {
            flex: 1;
            min-width: 550px;
            overflow-x: auto;
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


<h2>¿Qué producto quieres añadir?</h2>
<div class="contenedor-flex">

    <div class="formulario-lateral">
    <form method="post" action="insertarProducto.php">

        <label for="nombre">Nombre: </label>
        <input type="text" id="name" name="nameInsertar" required/><br><br>

        <label for="marca">Marca: </label>
        <input type="text" id="marca" name="marcaInsertar" required/><br><br>

        <label for="modelo">Modelo: </label>
        <input type="text" id="modelo" name="modeloInsertar" required/><br><br>

        <label for="precio">Precio: </label>
        <input type="text" id="precio" name="precioInsertar" required/><br><br>

        <label for="stock">Stock: </label>
        <input type="text" id="stock" name="stockInsertar" required/><br><br>

        <input type="submit" value="Insertar Producto" name="InsertarProducto">
    </form>
    </div>
<?php
    if(isset($_POST["InsertarProducto"])){
        $nombreInsertar = $_POST['nameInsertar'];
        $marcaInsertar = $_POST['marcaInsertar'];
        $modeloInsertar = $_POST['modeloInsertar'];
        $precioInsertar = $_POST['precioInsertar'];
        $stockInsertar = $_POST['stockInsertar'];

        $producto = new Producto(null, $nombreInsertar, $marcaInsertar, $modeloInsertar, $precioInsertar, $stockInsertar);
        $productoBBDD = new ProductoBBDD();

        // Insertar solo este producto
        if (!$productoBBDD->existeProducto($producto->nombre, $producto->marca, $producto->modelo)) {
            $productoBBDD->insertarProducto($producto);
            echo '<div class="mensaje exito">Producto insertado correctamente.</div>';
        } else {
            echo '<div class="mensaje error">El producto ya existe en la base de datos.</div>';
        }

    }


    // --- Conexión y carga de productos ---
    $productoBBDD = new ProductoBBDD();

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

            ?>


            <form method="post" action="carrito.php">
                <tr>
                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($producto['marca']); ?></td>
                    <td><?php echo htmlspecialchars($producto['modelo']); ?></td>
                    <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                    <td><?php echo htmlspecialchars($producto['stock']); ?></td>

                    <input type="hidden" name="idproducto"
                           value="<?php echo htmlspecialchars($producto['idproducto']); ?>">
                </tr>
            </form>
        <?php endforeach; ?>
    </table>


</div>
</div>

    <a href="tienda.php" style="background-color: #4f46e5; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; margin-bottom: 10px">Volver a la tienda</a>
    <a href="actualizarProductos.php" style="background-color: #4f46e5; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;  margin-bottom: 10px">Actualizar Productos</a>
    <a href="eliminarProductos.php" style="background-color: #4f46e5; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none"">Eliminar Productos</a>
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