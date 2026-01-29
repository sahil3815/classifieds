<?php
/**
 * @author  RadiusTheme
 * @since   1.0.0
 * @version 1.1.0
 */

if ( ! isset( $content_width ) ) {
	$content_width = 1240;
}

add_action( 'init', 'cl_classified_load_textdomain', 20 );
add_action( 'after_setup_theme', 'cl_classified_theme_load' );
/**
 * Load theme textdomain
 *
 * @return void
 */
function cl_classified_load_textdomain() {
	load_theme_textdomain( 'cl-classified', get_template_directory() . '/languages' );
}

/**
 * Load theme
 *
 * @return void
 */
function cl_classified_theme_load() {
	do_action( 'cl_classified_theme_init' );
}

define( 'CL_CLASSIFIED_VERSION', '2.0.1' );

require_once 'lib/class-tgm-plugin-activation.php';
require_once 'inc/init.php';
require_once 'inc/Customizer/sanitization.php';
