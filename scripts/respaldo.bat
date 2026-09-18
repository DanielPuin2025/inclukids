@echo off
REM ============================================================
REM  IncluKids - respaldo de la base de datos (Windows / XAMPP)
REM ------------------------------------------------------------
REM  Genera  backups\inclukids_AAAA-MM-DD_HHMM.sql  y conserva
REM  solo los ULTIMOS 14. Pensado para el Programador de tareas
REM  de Windows (una vez al dia).
REM
REM  Ajusta MYSQL_BIN, DB, DBUSER y DBPASS si tu instalacion
REM  usa otros valores.
REM ============================================================

setlocal enabledelayedexpansion

set "MYSQL_BIN=C:\xampp\mysql\bin"
set "DB=inclukids"
set "DBUSER=root"
set "DBPASS="

REM Carpeta de este script -> raiz del proyecto -> backups\
set "PROY=%~dp0.."
set "DEST=%PROY%\backups"
if not exist "%DEST%" mkdir "%DEST%"

REM Fecha/hora AAAA-MM-DD_HHMM (independiente de la config regional)
for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyy-MM-dd_HHmm"') do set "STAMP=%%i"
set "ARCHIVO=%DEST%\inclukids_!STAMP!.sql"

if "%DBPASS%"=="" (
    "%MYSQL_BIN%\mysqldump.exe" -u %DBUSER% --single-transaction --routines --events "%DB%" > "%ARCHIVO%"
) else (
    "%MYSQL_BIN%\mysqldump.exe" -u %DBUSER% -p%DBPASS% --single-transaction --routines --events "%DB%" > "%ARCHIVO%"
)

if errorlevel 1 (
    echo [%date% %time%] ERROR al respaldar >> "%DEST%\respaldo.log"
    del "%ARCHIVO%" 2>nul
    exit /b 1
)

echo [%date% %time%] OK %ARCHIVO% >> "%DEST%\respaldo.log"

REM Conservar solo los 14 mas recientes
for /f "skip=14 delims=" %%f in ('dir /b /o-d "%DEST%\inclukids_*.sql"') do del "%DEST%\%%f"

endlocal
