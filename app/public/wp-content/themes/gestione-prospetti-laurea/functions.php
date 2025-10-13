<?php
// enqueues style.css on the front.
if (!function_exists("gestione_prospetti_laurea_enqueue_styles")) :
	/**
	 * enqueues style.css on the front.
	 */
	function gestione_prospetti_laurea_enqueue_styles() {
		wp_enqueue_style(
			"gestione-prospetti-laurea-style",
			get_parent_theme_file_uri("style.css"),
			array(),
			wp_get_theme()->get("Version")
		);
	}
endif;
add_action("wp_enqueue_scripts", "gestione_prospetti_laurea_enqueue_styles");