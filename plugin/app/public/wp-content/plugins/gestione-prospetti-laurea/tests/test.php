<?php

use GestioneProspettiLaurea\tests\TestCreaProspetti;

define("PLUGIN_BASE_PATH", dirname(dirname(__FILE__)) . "/");
define("ABSPATH", "");

require_once PLUGIN_BASE_PATH . "vendor/autoload.php";

try {
    $crea_prospetti = new TestCreaProspetti();
    $crea_prospetti->testCreaProspetti();
} catch (\Exception $e) {
    echo "Eccezione durante test: " . $e->getMessage();
}

echo "\n";
