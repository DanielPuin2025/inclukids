@echo off
REM ============================================================
REM  IncluKids - aplica las migraciones pendientes
REM ------------------------------------------------------------
REM  Recorre database\migrations\*.sql y aplica las que falten,
REM  llevando registro en la tabla `migraciones`. Seguro de
REM  correr varias veces.
REM
REM  Uso:  scripts\migrar.bat           (aplica lo pendiente)
REM        scripts\migrar.bat --list    (solo muestra el estado)
REM
REM  Ajusta PHP_BIN si tu instalacion usa otra ruta.
REM ============================================================

set "PHP_BIN=C:\xampp\php\php.exe"

if not exist "%PHP_BIN%" (
    echo No se encontro PHP en %PHP_BIN%. Ajusta PHP_BIN en este .bat.
    exit /b 1
)

"%PHP_BIN%" "%~dp0migrar.php" %*
