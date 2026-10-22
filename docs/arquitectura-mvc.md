# Arquitectura Modelo Vista Controlador

La aplicacion fue refactorizada a una arquitectura MVC simple en PHP puro.

## Modelo

Los modelos encapsulan el acceso a datos y las consultas SQL preparadas.

```text
app/Models/User.php
app/Models/Registro.php
```

Responsabilidades:

- `User`: buscar usuarios por correo y listar usuarios publicos para administradores.
- `Registro`: listar vehiculos activos, registrar entradas, registrar salidas, calcular recaudacion y actividad reciente.

## Vista

Las vistas contienen HTML/PHP de presentacion. No hacen consultas SQL ni validaciones de negocio.

```text
views/layouts/auth.php
views/layouts/app.php
views/auth/login.php
views/parqueadero/dashboard.php
```

Responsabilidades:

- `layouts/auth.php`: estructura visual del login.
- `layouts/app.php`: sidebar, topbar y contenedor principal.
- `auth/login.php`: formulario de acceso.
- `parqueadero/dashboard.php`: dashboard, formularios, tablas y actividad reciente.

## Controlador

Los controladores reciben la peticion, validan flujo y coordinan modelos y vistas.

```text
app/Controllers/AuthController.php
app/Controllers/ParqueaderoController.php
```

Responsabilidades:

- `AuthController`: mostrar login, autenticar usuario y cerrar sesion.
- `ParqueaderoController`: mostrar dashboard, registrar entrada y registrar salida.

## Front Controller

```text
public/index.php
```

Es el unico punto de entrada web. Define rutas simples y delega a controladores:

```text
GET  /login
POST /login
POST /logout
GET  /
POST /entrada
POST /salida/{id}
```

## Flujo MVC

```text
Navegador
  -> public/index.php
  -> Controller
  -> Model
  -> View
  -> Navegador
```

## Seguridad mantenida

- Consultas preparadas con PDO.
- Validacion de placa por lista blanca.
- CSRF en formularios POST.
- Control de acceso por rol `admin` y `operador`.
- Sesion regenerada al iniciar sesion.
