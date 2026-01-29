<?php
/**
 * @author  RadiusTheme
 * @since   1.0.0
 * @version 1.1.0
 */

namespace RadiusTheme\ClassifiedLite;

class TGM_Config {

	public $base;
	public $path;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		$this->base = 'cl_classified';
		$this->path = Constants::$theme_plugins_dir;

		add_action( 'tgmpa_register', [ $this, 'register_required_plugins' ] );
	}

	/**
	 * Register the required plugins for this theme.
	 *
	 * @return void
	 */
	public function register_required_plugins() {
		$plugins = [
			// Bundled
			[
				'name'     => 'CL Classified Core',
				'slug'     => 'cl-classified-core',
				'source'   => 'https://radiustheme.net/bundle-plugins/cl-classified/cl-classified-core.zip',
				'required' => true,
				'version'  => '2.0.0',
			],
			[
				'name'     => 'RT Framework',
				'slug'     => 'rt-framework',
				'source'   => 'https://radiustheme.net/bundle-plugins/cl-classified/rt-framework.zip',
				'required' => true,
				'version'  => '3.0',
			],
			// Repository
			[
				'name'     => 'Classified Listing',
				'slug'     => 'classified-listing',
				'required' => true,
			],
			[
				'name'     => 'Classified Listing Toolkits',
				'slug'     => 'classified-listing-toolkits',
				'required' => true,
			],
			[
				'name'     => 'Elementor Page Builder',
				'slug'     => 'elementor',
				'required' => false,
			],
			[
				'name'     => 'Easy Demo Importer',
				'slug'     => 'easy-demo-importer',
				'required' => false,
			],
		];

		$config = [
			'id'           => $this->base,
			// Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => $this->path,
			// Default absolute path to bundled plugins.
			'menu'         => $this->base . '-install-plugins',
			// Menu slug.
			'has_notices'  => true,
			// Show admin notices or not.
			'dismissable'  => true,
			// If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',
			// If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,
			// Automatically activate plugins after installation or not.
			'message'      => '',
			// Message to output right before the plugins table.
		];

		tgmpa( $plugins, $config );
	}
}
