<?php
if (!defined("ABSPATH"))
    exit;

class GestioneProspettiLaureaAdmin
{

    public function __construct()
    {
        // show only plugin relative pages
        add_action("admin_menu", [$this, "edit_admin_menu"]);

        // redirect straight to plugin settings
        add_action("admin_init", [$this, "redirect_dashboard"]);
    }

    public function edit_admin_menu()
    {
        // add plugin configuration page
        add_menu_page(
            "Gestione prospetti laurea",
            "Gestione prospetti laurea",
            "manage_options",
            "gestione-prospetti-laurea",
            [$this, "render_settings_page"],
            "dashicons-admin-generic"
        );

        // clean up some other pages
        remove_menu_page("index.php");
        remove_menu_page("edit.php");
        remove_menu_page("upload.php");
        remove_menu_page("edit.php?post_type=page");
        remove_menu_page("edit-comments.php");
        remove_menu_page("tools.php");

        remove_submenu_page("options-general.php", "options-writing.php");
        remove_submenu_page("options-general.php", "options-reading.php");
        remove_submenu_page("options-general.php", "options-discussion.php");
        remove_submenu_page("options-general.php", "options-media.php");
        remove_submenu_page("options-general.php", "options-permalink.php");
        remove_submenu_page("options-general.php", "options-privacy.php");
    }

    public function redirect_dashboard() {
        global $pagenow;
        if ($pagenow === "index.php") {
            wp_redirect("admin.php?page=gestione-prospetti-laurea");
            exit;
        }
    }

    public function render_settings_page()
    {
        include plugin_dir_path(dirname(__FILE__)) . "/views/settings_page.php";
    }

}