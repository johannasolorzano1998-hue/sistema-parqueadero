# Conectar con MySQL Workbench

## Datos de conexion

Usa los mismos valores del archivo `.env`:

```text
Hostname: 127.0.0.1
Port: 3306
Username: root
Default Schema: sistema_parqueadero
```

La clave esta configurada localmente en `.env`.

## Pasos en Workbench

1. Abre MySQL Workbench.
2. Crea o abre la conexion local `MySQL80`.
3. Verifica que el usuario sea `root` y el puerto `3306`.
4. En el panel izquierdo, haz clic derecho sobre `SCHEMAS`.
5. Selecciona `Refresh All`.
6. Debe aparecer la base:

```text
sistema_parqueadero
```

Dentro deben aparecer estas tablas:

```text
users
registros
```

## Crear tablas manualmente

Si no aparecen las tablas:

1. Abre el archivo `database/mysql_schema.sql`.
2. Copia o ejecuta el script en Workbench.
3. Vuelve a refrescar `SCHEMAS`.

La aplicacion tambien crea las tablas automaticamente cuando arranca con PHP.
