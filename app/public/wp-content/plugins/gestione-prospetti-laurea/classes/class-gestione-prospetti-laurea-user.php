<?php
if (!defined("ABSPATH"))
    exit;

class GestioneProspettiLaureaUser
{

    public function __construct()
    {
        add_action('template_redirect', [$this, "edit_front_page"]);
    }

    public function edit_front_page() {
        // only show user page
        if(is_front_page()) {
            $this->render_front_page();
            exit;
        } else {
            $this->render_404();
        }
    }

    public function render_front_page()
    {
        include plugin_dir_path(dirname(__FILE__)) . "views/user_page.php";
    }

    public function render_404() {
        // set status
        global $wp_query;
        $wp_query->set_404();
        status_header(404);

        // show theme 404
        get_template_part("404");
    }

}
