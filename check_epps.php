<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$epps = \App\Models\Epp::orderBy('nombre')->pluck('nombre')->toArray();
echo "=== EPPs EN LA BASE DE DATOS ===\n";
foreach ($epps as $i => $epp) {
    echo ($i+1) . ". $epp\n";
}
echo "\nTotal: " . count($epps) . " EPPs\n";
?>
