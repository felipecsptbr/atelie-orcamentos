@echo off
cls
echo ===============================================
echo   CONFIGURADOR ATELIE ORCAMENTOS - XAMPP
echo ===============================================
echo.

echo [1/3] Verificando XAMPP...
if not exist "C:\xampp\htdocs" (
    echo ERRO: XAMPP nao encontrado!
    echo Instale o XAMPP primeiro: https://www.apachefriends.org/
    pause
    exit /b 1
)
echo OK: XAMPP encontrado!

echo.
echo [2/3] Copiando arquivos para XAMPP...
set DESTINO=C:\xampp\htdocs\atelie-orcamentos

if exist "%DESTINO%" (
    echo Pasta ja existe. Removendo...
    rmdir /s /q "%DESTINO%"
)

mkdir "%DESTINO%"
copy /y "*.php" "%DESTINO%\"
copy /y "*.sql" "%DESTINO%\"

echo OK: Arquivos copiados!

echo.
echo [3/3] Verificando servicos XAMPP...
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I /N "httpd.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo OK: Apache esta rodando!
) else (
    echo AVISO: Apache nao esta rodando
    echo Abra o XAMPP Control Panel e inicie Apache e MySQL
)

tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo OK: MySQL esta rodando!
) else (
    echo AVISO: MySQL nao esta rodando
    echo Abra o XAMPP Control Panel e inicie Apache e MySQL
)

echo.
echo ===============================================
echo   INSTALACAO CONCLUIDA!
echo ===============================================
echo.
echo Proximos passos:
echo 1. Abra o XAMPP Control Panel
echo 2. Inicie Apache e MySQL (se ainda nao estao rodando)
echo 3. Acesse: http://localhost/atelie-orcamentos/instalar.php
echo 4. Siga as instrucoes na tela
echo.
echo ===============================================

set /p escolha=Deseja abrir o navegador agora? (S/N): 
if /i "%escolha%"=="S" (
    start http://localhost/atelie-orcamentos/instalar.php
)

pause