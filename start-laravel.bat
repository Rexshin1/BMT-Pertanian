@echo off
setlocal

set "PHP_EXE=D:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe"

if not exist "%PHP_EXE%" (
    echo PHP 8.1.10 tidak ditemukan: %PHP_EXE%
    echo Periksa lokasi PHP Laragon dan sesuaikan PHP_EXE di file ini.
    pause
    exit /b 1
)

cd /d "%~dp0"
echo Local: http://127.0.0.1:8108
echo Mobile: http://IP-LAPTOP:8108 (laptop and phone must use the same Wi-Fi)
"%PHP_EXE%" artisan serve --host=0.0.0.0 --port=8108 --no-reload

if errorlevel 1 (
    echo.
    echo Laravel berhenti dengan error. Baca pesan di atas sebelum mencoba lagi.
    pause
)
