<?php
/*
Plugin Name: NBP Kurs Walut
Description: NPB Kurs walut
Version:     1.0.0
Author:      Paweł Rudnicki
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: nbp-kurs-walut
*/

require_once(dirname(__FILE__) . '/class.nbp-widget.php');

if ( ! function_exists('nbp_load_widget') ) {
	function nbp_load_widget() {
		register_widget( 'NBP_Widget' );
	}
}

add_action( 'widgets_init', 'nbp_load_widget' );
