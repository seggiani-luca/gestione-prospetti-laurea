<?php
/*
 * Plugin name: Gestione prospetti laurea
 * Description: Implementa il sistema di gestione dei prospetti di laurea.
 * Author: Luca Seggiani
 */

if (is_admin()) 
{
    // mostra menu admin
    require_once plugin_dir_path(__FILE__) . "classes/pagina-admin.php";
    PaginaAdmin::init();
}
else
{
    // mostra main page user
    require_once plugin_dir_path(__FILE__) . "classes/pagina-user.php";
    PaginaUser::init();
}
