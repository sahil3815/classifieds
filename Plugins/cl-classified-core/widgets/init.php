<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace RadiusTheme\CL_Classified_Core;

class Custom_Widgets_Init {

	public $widgets;
	protected static $instance = null;

	public function __construct() {

		// Widgets -- filename=>classname /@dev
		$this->widgets = [
			'about' => 'About_Widget',
		];

		add_action( 'widgets_init', [ $this, 'custom_widgets' ] );
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function custom_widgets() {
		if ( ! class_exists( 'RT_Widget_Fields' ) ) {
			return;
		}

		foreach ( $this->widgets as $filename => $classname ) {

			$template_name = '/widgets/' . $filename . '.php';

			if ( file_exists( get_stylesheet_directory() . $template_name ) ) {
				$file = get_stylesheet_directory() . $template_name;
			} elseif ( file_exists( get_template_directory() . $template_name ) ) {
				$file = get_template_directory() . $template_name;
			} else {
				$file = __DIR__ . '/' . $filename . '.php';
			}

			require_once $file;

			$class = __NAMESPACE__ . '\\' . $classname;
			register_widget( $class );
		}
	}
}

Custom_Widgets_Init::instance();
