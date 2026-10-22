# Abrir en Visual Studio Code

## 1. Abrir carpeta

En VS Code:

```text
File > Open Folder
```

Selecciona:

```text
C:\Users\Fredy\Downloads\practicas\sistema-parqueadero
```

## 2. PHP portable incluido

Este proyecto trae PHP portable en:

```text
tools\php\php.exe
```

## 3. Iniciar el sistema

Antes verifica que MySQL este activo y que `.env` tenga tus credenciales correctas:

```text
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_parqueadero
DB_USERNAME=root
DB_PASSWORD=tu_clave
```

Puedes abrir `database/mysql_schema.sql` en MySQL Workbench para crear las tablas manualmente. La aplicacion tambien intenta crearlas automaticamente al arrancar.

En la terminal de VS Code:

```powershell
.\tools\php\php.exe -S 127.0.0.1:8000 -t public
```

O ejecuta:

```powershell
.\start.bat
```

Luego abre:

```text
http://127.0.0.1:8000
```

## 4. Usuarios demo

```text
Admin:    admin@example.com / Admin123!
Operador: operador@example.com / Operador123!
```
