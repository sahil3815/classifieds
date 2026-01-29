<?php
/**
 * Theme Demo Configuration File
 *
 * This file contains the configuration for the demo importer.
 *
 * @package RadiusTheme\CL_Classified_Core
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

return [
	// Basic theme information.
	'blog_slug'            => 'blog',
	'demo_url'             => 'https://www.radiustheme.net/public-demo/cl-classified/',
	'menus'                => [ 'primary' => 'Main Menu' ],

	// File paths.
	'demo_content_zip'     => 'demo-files/demo-content.zip',

	// Demo variants.
	'demo_variants'        => [
		'home-1' => [
			'name'    => 'Home 1',
			'preview' => plugins_url( 'screenshots/screenshot1.png', __DIR__ ),
			'url'     => '',
		],
	],

	// Additional settings.
	'settings_json'        => [
		'rtcl_filter_settings',
	],

	// WordPress repository plugins.
	'wp_plugins'           => [
		'classified-listing'          => 'Classified Listing – Classified ads & Business Directory Plugin',
		'classified-listing-toolkits' => 'Classified Listing Toolkits',
		'elementor'                   => 'Elementor Page Builder',
	],

	// Enable/disable import features.
	'features'             => [
		'rtcl_support'    => true,
		'elementor_fixes' => true,
	],

	// Elementor data fixes.
	'elementor_fixes'      => [
		'rtcl-listing-single-location' => [ 'location' ],
		'rtcl-listing-category-slider' => [ 'rtcl_cats' ],
	],

	// RTCL Pages to remove before import.
	'rtcl_pages_to_remove' => [
		'Listings',
		'Listing Form',
		'My Account',
		'Checkout',
	],

	// RTCL pages to create/update.
	'rtcl_pages'           => [
		'listings'     => 'All Ads',
		'listing_form' => 'Post an Ad',
		'myaccount'    => 'My Account',
		'checkout'     => 'Checkout',
	],

	// Pre-import options.
	'pre_import_options'   => [
		'elementor_experiment-e_font_icon_svg' => 'inactive',
		'elementor_experiment-container'       => 'inactive',
		'elementor_experiment-nested-elements' => 'inactive',
		'elementor_font_display'               => 'swap',
		'elementor_load_fa4_shim'              => 'yes',
	],

	// Post-import options.
	'post_import_options'  => [
		'elementor_allow_unfiltered_files' => true,
	],

	// Custom RTCL import files.
	'rtcl_custom_files'    => [
		'rtcl_options' => 'demo-files/rtcl-settings.json',
		'rtcl_forms'   => 'demo-files/rtclform.json',
	],
];
