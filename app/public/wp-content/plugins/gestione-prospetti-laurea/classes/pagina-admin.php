<?php
if (!defined("ABSPATH"))
    exit;

class PaginaAdmin
{

    public static function init()
    {
        // mostra solo pagine relative al plugin
        add_action("admin_menu", ["PaginaAdmin", "modificaMenuAdmin"]);

        // redireziona alla pagina di configurazione del plugin
        add_action("admin_init", ["PaginaAdmin", "redirezionaDashboard"]);
    }

    public static function modificaMenuAdmin()
    {
        // aggiungi pagina di configurazione del plugin
        add_menu_page(
            "Gestione prospetti laurea",
            "Gestione prospetti laurea",
            "manage_options",
            "gestione-prospetti-laurea",
            ["PaginaAdmin", "mostraPaginaAdmin"],
            "dashicons-admin-generic"
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
        global $pagenow;
        if ($pagenow === "index.php") 
        {
            wp_redirect("admin.php?page=gestione-prospetti-laurea");
            exit;
        }
    }

    public static function mostraPaginaAdmin()
    {
        include plugin_dir_path(dirname(__FILE__)) . "/views/admin.php";
    }

}