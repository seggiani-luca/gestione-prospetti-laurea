<?php
namespace GestioneProspettiLaurea;

if (!defined("ABSPATH")) {
    exit();
}

require_once plugin_dir_path(dirname(__FILE__)) . "vendor/autoload.php";

/*
 * Classe boundary usata per definire le pagine accessibili dall'amministratore. In particolare si occupa di
 * redirezionare la pagina di configurazione principale a quella del plugin, e nascondere alcune pagine di
 * configurazione inutili al plugin.
 */
class PaginaAdmin
{
    public static function init()
    {
        // mostra solo pagine relative al plugin
        add_action("admin_menu", [__CLASS__, "modificaMenuAdmin"]);

        // redireziona alla pagina di configurazione del plugin
        add_action("admin_init", [__CLASS__, "redirezionaDashboard"]);
    }

    public static function modificaMenuAdmin()
    {
        // aggiungi pagina di configurazione del plugin
        add_menu_page(
            "Gestione prospetti laurea",
            "Gestione prospetti laurea",
            "manage_options",
            "gestione-prospetti-laurea",
            [__CLASS__, "mostraPaginaAdmin"],
            "dashicons-admin-generic",
        );

        // ripulisci alcune pagine inutili
        remove_menu_page("index.php");
        remove_menu_page("edit.php");
        remove_menu_page("upload.php");
        remove_menu_page("edit.php?post_type=page");
        remove_menu_page("edit-comments.php");
        remove_menu_page("tools.php");

        // ripulisci alcune pagine di configurazione inutili
        remove_submenu_page("options-general.php", "options-writing.php");
        remove_submenu_page("options-general.php", "options-reading.php");
        remove_submenu_page("options-general.php", "options-discussion.php");
        remove_submenu_page("options-general.php", "options-media.php");
        remove_submenu_page("options-general.php", "options-permalink.php");
        remove_submenu_page("options-general.php", "options-privacy.php");
    }

    public static function redirezionaDashboard()
    {
        // se si sta cercando di accedere all'index dell'amministratore (dashboard), redireziona
        global $pagenow;
        if ($pagenow === "index.php") {
            wp_redirect("admin.php?page=gestione-prospetti-laurea");
            exit();
        }
    }

    public static function mostraPaginaAdmin()
    {
        include plugin_dir_path(dirname(__FILE__)) . "/views/admin.php";
    }
}
