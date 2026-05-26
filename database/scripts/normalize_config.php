<?php

// Normalizar variables sincronizables a mayúsculas
// Ejecutar: php artisan tinker < database/scripts/normalize_config.php

use App\Models\ConfiguracionVariable;

$vars = ConfiguracionVariable::whereNotNull('tabla_sincronizacion')->get();

foreach ($vars as $var) {
    $data = json_decode($var->valor, true) ?? [];
    $normalized = [];
    foreach ($data as $k => $v) {
        $kNorm = mb_strtoupper(str_replace(' ', '_', trim($k)));
        $normalized[$kNorm] = mb_strtoupper(trim($v));
    }
    $var->valor = json_encode($normalized, JSON_UNESCAPED_UNICODE);
    $var->save();
    echo $var->clave . ': ' . $var->valor . PHP_EOL;
}

echo "Done.\n";
