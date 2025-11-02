<?php
/*
 * Plugin name: Gestione prospetti laurea
 * Description: Implementa il sistema di gestione dei prospetti di laurea
 * Author: Luca Seggiani
 */

use GestioneProspettiLaurea\PaginaAdmin;
use GestioneProspettiLaurea\PaginaUser;

require_once plugin_dir_path(__FILE__) . "vendor/autoload.php";

if (is_admin()) {
    // mostra menu admin
    PaginaAdmin::init();
} else {
    // mostra main page user
    PaginaUser::init();
}
