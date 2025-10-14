<?php
/*
 * Plugin name: Gestione prospetti laurea
 * Description: Implementa il sistema di gestione dei prospetti di laurea.
 * Author: Luca Seggiani
 */

if (is_admin()) {
    // show admin menu
    require_once plugin_dir_path(__FILE__) . "classes/class-gestione-prospetti-laurea-admin.php";
    new GestioneProspettiLaureaAdmin();
} else {
    // show user page + bypass others
    require_once plugin_dir_path(__FILE__) . "classes/class-gestione-prospetti-laurea-user.php";
    new GestioneProspettiLaureaUser();
}
