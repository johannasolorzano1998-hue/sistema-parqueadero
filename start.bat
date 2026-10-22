@echo off
cd /d "%~dp0"
echo Sistema Parqueadero disponible en http://127.0.0.1:8000
if exist "%~dp0tools\php\php.exe" (
    "%~dp0tools\php\php.exe" -S 127.0.0.1:8000 -t public
) else (
    php -S 127.0.0.1:8000 -t public
)
