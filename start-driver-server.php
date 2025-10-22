#!/usr/bin/env php
<?php

// Servidor dedicado para panel conductor en puerto 8001
echo "Iniciando servidor del panel conductor en puerto 8001...\n";
echo "Panel Conductor: http://127.0.0.1:8001/conductor\n";
echo "Panel Admin: http://127.0.0.1:8000/admin\n";
echo "Presiona Ctrl+C para detener\n\n";

// Cambiar al directorio del proyecto
chdir(__DIR__);

// Configurar variables de entorno específicas para conductor
putenv('FILAMENT_PANEL_MODE=driver');
putenv('SESSION_COOKIE=driver_session');

// Ejecutar servidor en puerto 8001
passthru('php -S 127.0.0.1:8001 -t public public/index.php');