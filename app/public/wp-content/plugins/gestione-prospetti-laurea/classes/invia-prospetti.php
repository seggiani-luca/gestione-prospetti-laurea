<?php
if (!defined("ABSPATH"))
    exit;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once plugin_dir_path(dirname(__FILE__)) . "lib/PHPMailer/src/PHPMailer.php";
require_once plugin_dir_path(dirname(__FILE__)) . "lib/PHPMailer/src/Exception.php";
require_once plugin_dir_path(dirname(__FILE__)) . "lib/PHPMailer/src/SMTP.php";

class InviaProspetti 
{
    public static function inviaProspetto()
    {
        // TODO: implementa con PHPMailer
        $mail = new PHPMailer();
    }

    public static function inviaProspetti($corso_di_laurea, $data, $matricole)
    {        
        echo("<p>inviaProspetti() chiamata</p>");
        die();
        
        // TODO: implementa chiamando inviaProspetto()
    }
}