# Tienda de Electrodomésticos - Aplicación Web PHP

Aplicación web de una tienda de electrodomésticos con sistema de usuarios, catálogo de productos y carrito de compras. Desarrollada en PHP con base de datos MySQL y ejecutada en contenedores Docker.

## 🎯 Funcionalidades

- **Autenticación de usuarios**: Registro e inicio de sesión con contraseñas hasheadas
- **Catálogo de productos**: Visualización de electrodomésticos disponibles
- **Carrito de compras**: Añadir, ver y eliminar productos del carrito
- **Gestión de productos** (Admin): 
  - Insertar nuevos productos
  - Actualizar información de productos
  - Eliminar productos
- **Control de stock**: Sistema de reserva de productos en el carrito
- **Roles de usuario**: Administrador (rol 0) y usuario normal (rol 1)

## 📋 Requisitos previos

- Docker Desktop instalado
- Docker Compose v3 o superior
- Terminal/Consola

## 🚀 Instalación y ejecución

### 1. Clonar el repositorio

```bash
git clone https://github.com/[tu-usuario]/tienda-electrodomesticos.git
cd tienda-electrodomesticos
```

### 2. Levantarlos contenedores

```bash
docker compose up -d
```

Esto creará:
- **Contenedor web**: Servidor Apache + PHP 8.2 (puerto 8888)
- **Contenedor bbdd**: MariaDB (puerto 3307)

### 3. Acceder a la aplicación

Abre tu navegador y ve a:
```
http://localhost:8888
```

## 👤 Credenciales de prueba

### Administrador
- **Email**: jesus@gmail.com
- **Contraseña**: 1234

### Usuario normal
- **Email**: antonio@gmail.com
- **Contraseña**: 1234

## 📁 Estructura del proyecto

```
├── index.php                  # Página de login
├── registro.php               # Formulario de registro
├── iniciosesion.php          # Procesamiento de inicio de sesión
├── tienda.php                # Catálogo de productos
├── carrito.php               # Gestión del carrito de compras
├── logout.php                # Cerrar sesión
│
├── insertarProducto.php      # Insertar nuevo producto (Admin)
├── actualizarProductos.php   # Actualizar producto (Admin)
├── eliminarProductos.php     # Eliminar producto (Admin)
│
├── Usuario.php               # Clase Usuario
├── UsuarioBBDD.php          # Gestión BD de usuarios
├── Producto.php              # Clase Producto
├── ProductoBBDD.php         # Gestión BD de productos
├── CarritoBBDD.php          # Gestión BD del carrito
│
├── Dockerfile                # Configuración imagen Docker PHP
├── docker-compose.yml        # Orquestación de contenedores
├── bbdd.sql                  # Dump inicial de base de datos
├── xdebug.ini               # Configuración de Xdebug
└── README.md                 # Este archivo
```

## 🗄️ Base de datos

**Tablas principales:**

### usuarios
- `idusuario` (INT, PK, Auto-increment)
- `dni` (VARCHAR 9)
- `apellidos` (VARCHAR 50)
- `nombre` (VARCHAR 50)
- `email` (VARCHAR 70, UNIQUE)
- `password` (VARCHAR 100 - hasheada con bcrypt)
- `rol` (INT) - 0: Admin, 1: Usuario normal

### productos
- `idproducto` (INT, PK, Auto-increment)
- `nombre` (VARCHAR 50)
- `marca` (VARCHAR 50)
- `modelo` (VARCHAR 50)
- `precio` (FLOAT)
- `stock` (INT)

### carrito
- `idusuario` (INT, FK)
- `idproducto` (INT, FK)
- `cantidad` (INT)
- **PK**: (idusuario, idproducto)

## 🔧 Configuración técnica

### Stack tecnológico
- **Backend**: PHP 8.2
- **Servidor web**: Apache 2.4
- **Base de datos**: MariaDB 10.4
- **Gestor de base de datos**: PDO
- **Depuración**: Xdebug 3

### Variables de conexión (Docker)

Los archivos PHP usan estas configuraciones por defecto:
```php
Host: bbdd        // Nombre del servicio Docker
Usuario: root
Contraseña: Ciclo2gs
Base de datos: tienda
Charset: utf8
```

## 🛑 Detener los contenedores

```bash
docker compose down
```

Para detener sin borrar volúmenes:
```bash
docker compose stop
```

Para reanudar:
```bash
docker compose start
```

## 🔄 Reiniciar contenedores

Si realizas cambios en los archivos PHP:
```bash
docker compose restart web
```

## 🐛 Depuración con Xdebug

La configuración de Xdebug está lista para PhpStorm:
- **Puerto**: 9003
- **IDE Key**: PHPSTORM
- **Host**: host.docker.internal

En PhpStorm: `Run > Edit Configurations > PHP Remote Debug`

## 📊 Ver logs

```bash
# Logs del contenedor web
docker logs web

# Logs del contenedor de base de datos
docker logs bd

# Ver en tiempo real
docker logs -f web
```

## 🚨 Solucionar problemas

### Error: "No such file or directory"
Verifica que todos los archivos `.php` estén en el directorio raíz del proyecto.

### Error de conexión a BD
Asegúrate de que el contenedor `bbdd` está corriendo:
```bash
docker compose ps
```

### Puerto 8888 ya en uso
Cambia el puerto en `docker-compose.yml`:
```yaml
ports:
  - "8889:80"  # Cambiar a otro puerto disponible
```

## 📝 Notas de desarrollo

- Las contraseñas se almacenan hasheadas con `password_hash()` (bcrypt)
- El control de sesiones está habilitado en todos los formularios
- Se utiliza PDO preparado para prevenir inyecciones SQL
- El stock se reserva en el carrito hasta finalizar la compra

## 👨‍💼 Autor

Jesús García Pulido

## 📄 Licencia

Este proyecto es de uso educativo.
