<?php
namespace GestioneProspettiLaurea;

if (!defined("ABSPATH")) {
    exit();
}

require_once plugin_dir_path(dirname(__FILE__)) . "vendor/autoload.php";

class DisegnaProspetti
{
    public static function disegnaProspetti()
    {
        // TODO: implementa con FPDF
        $pdf = new FPDF();
    }
}
