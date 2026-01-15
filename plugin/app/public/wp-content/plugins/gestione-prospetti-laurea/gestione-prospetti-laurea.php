<?php
/*
 * Plugin name: Gestione prospetti laurea
 * Description: Implementa il sistema di gestione dei prospetti di laurea
 * Author: Luca Seggiani
 */

use GestioneProspettiLaurea\view\PaginaAdmin;
use GestioneProspettiLaurea\view\PaginaUser;

define("PLUGIN_BASE_PATH", plugin_dir_path(__FILE__));
define("PLUGIN_BASE_URL", plugin_dir_url(__FILE__));

require_once PLUGIN_BASE_PATH . "vendor/autoload.php";

if (is_admin()) {
    // mostra menu admin
    PaginaAdmin::init();
} else {
    // mostra main page user
    PaginaUser::init();
}
