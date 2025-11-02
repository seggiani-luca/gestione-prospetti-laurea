<?php
namespace GestioneProspettiLaurea;

if (!defined("ABSPATH")) {
    exit();
}

require_once plugin_dir_path(dirname(__FILE__)) . "vendor/autoload.php";

/*
 * Classe boundary usata per definire le pagine accessibili dall'utente. In particolare si occupa di redirezionare la
 * front page alla view definita dal plugin, e redirezionare tutte le altre pagine verso 404.
 */
class PaginaUser
{
    public static function init()
    {
        // redireziona alla pagina del plugin
        add_action("template_redirect", [__CLASS__, "modificaFrontPage"]);
    }

    public static function modificaFrontPage()
    {
        if (is_front_page()) {
            // mostra pagina plugin
            self::mostraFrontPage();
            exit();
        } else {
            // tutte le altre pagine vanno a 404
            self::mostra404();
        }
    }

    public static function mostraFrontPage()
    {
        include plugin_dir_path(dirname(__FILE__)) . "views/user.php";
    }

    public static function mostra404()
    {
        // imposta status 404
        global $wp_query;
        $wp_query->set_404();
        status_header(404);

        // mostra il 404 del tema corente
        get_template_part("404");
    }
}
