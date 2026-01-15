<?php
/*
 * Hook per l'implementazione del sistema di gestione dei prospetti di laurea. Viene lanciato da uno snippet.
 */

use GestioneProspettiLaurea\view\PaginaAdmin;
use GestioneProspettiLaurea\view\PaginaUser;

define("SNIPPETS_BASE_PATH", __DIR__ . "/");
define("SNIPPETS_BASE_URL", site_url("gestione-prospetti-laurea/"));

require_once SNIPPETS_BASE_PATH . "vendor/autoload.php";

/*
 * Hook di bootstrap della pagina principale.
 */
function bootstrap() {
    if (is_front_page()) {
        // mostra pagina user
        PaginaUser::mostraPaginaUser();
        exit();
    } else if (is_page("gpl-admin")) {
        // accoda stile admin
        echo '<link rel="stylesheet" href="' . admin_url('css/wp-admin.css') . '" type="text/css" />';

        // mostra pagina admin
        PaginaAdmin::mostraPaginaAdmin();
        exit();
    } else {
        // tutte le altre pagine vanno a 404
        PaginaUser::mostra404();
    }
}
