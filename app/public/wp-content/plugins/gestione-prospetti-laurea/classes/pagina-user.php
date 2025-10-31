<?php
if (!defined("ABSPATH"))
    exit;

class PaginaUser
{

    public static function init()
    {
        // redireziona alla pagina del plugin
        add_action('template_redirect', ["PaginaUser", "modificaFrontPage"]);
    }

    public static function modificaFrontPage() {
        if(is_front_page()) 
        {
            // mostra pagina plugin
            self::mostraFrontPage();
            exit;
        } 
        else 
        {
            // tutte le altre pagine vanno a 404
            self::mostra404();
        }
    }

    public static function mostraFrontPage()
    {
        include plugin_dir_path(dirname(__FILE__)) . "views/user.php";
    }

    public static function mostra404() {
        // imposta status 404
        global $wp_query;
        $wp_query->set_404();
        status_header(404);

        // mostra il 404 del tema corente
        get_template_part("404");
    }

}
