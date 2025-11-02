<?php
namespace GestioneProspettiLaurea;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

if (!defined("ABSPATH")) {
    exit();
}

require_once plugin_dir_path(dirname(__FILE__)) . "vendor/autoload.php";

class InviaProspetti
{
    public static function inviaProspetto()
    {
        // TODO: implementa con PHPMailer
        $mail = new PHPMailer();
    }

    public static function inviaProspetti($corso_di_laurea, $data, $matricole)
    {
        // TODO: implementa chiamando inviaProspetto()
    }
}
