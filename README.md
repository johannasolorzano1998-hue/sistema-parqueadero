# Sistema Parqueadero

Aplicacion PHP + MySQL reconstruida para abrir y ejecutar desde Visual Studio Code.

## Requisitos

- MySQL Server 8.0 activo. Puedes administrarlo con MySQL Workbench.
- La carpeta incluye PHP portable en `tools/php`, por lo que no necesitas agregar PHP al PATH para usar `start.bat`.

## Conexion MySQL

La conexion esta en `.env`:

```text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_parqueadero
DB_USERNAME=root
DB_PASSWORD=tu_clave
```

El archivo `database/mysql_schema.sql` se puede abrir y ejecutar en MySQL Workbench.

## Inicio rapido

Desde esta carpeta:

```powershell
.\start.bat
```

O directamente:

```powershell
.\tools\php\php.exe -S 127.0.0.1:8000 -t public
```

Luego abre:

```text
http://127.0.0.1:8000
```

## Usuarios demo

```text
Admin:    admin@example.com / Admin123!
Operador: operador@example.com / Operador123!
```

Despues de probar, cambia estas claves en una version real.

## Seguridad incluida

- Consultas preparadas con PDO para evitar inyeccion SQL.
- Validacion por lista blanca para placas: `ABC-123` o `ABC-1234`.
- Normalizacion de placa antes de guardar: mayusculas, sin espacios, guion automatico.
- Control de acceso por rol: solo `admin` ve la lista de usuarios.
- CSRF token en formularios POST.
- Sesiones regeneradas al iniciar sesion.

## Arquitectura MVC

```text
public/index.php                    Front controller y rutas
app/Controllers/AuthController.php  Login y logout
app/Controllers/ParqueaderoController.php Dashboard, entrada y salida
app/Models/User.php                 Modelo de usuarios
app/Models/Registro.php             Modelo de registros del parqueadero
app/Core/View.php                   Renderizado de vistas y layouts
views/layouts/app.php               Layout principal autenticado
views/layouts/auth.php              Layout de autenticacion
views/auth/login.php                Vista de login
views/parqueadero/dashboard.php     Vista del dashboard
src/Database.php                    Conexion MySQL, migracion y semillas
src/Security.php                    Autenticacion, CSRF y sanitizacion
assets/css/app.css                  Estilos fuente
public/assets/css/app.css           Estilos servidos por el navegador
database/mysql_schema.sql           Script para MySQL Workbench
```

Mas detalle en `docs/arquitectura-mvc.md`.

## OWASP ZAP

Cuando tengas ZAP o Docker instalado, puedes escanear:

```powershell
php -S 127.0.0.1:8000 -t public
zap-baseline.py -t http://127.0.0.1:8000 -r zap-report.html
```

Para explorar el dashboard protegido, configura autenticacion en ZAP con un usuario valido.
