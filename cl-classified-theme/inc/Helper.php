<?php
/**
 * @author  RadiusTheme
 * @since   1.0.0
 * @version 1.0.0
 */

namespace RadiusTheme\ClassifiedLite;

use RtclPro\Helpers\Fns;

class Helper {
	/**
	 * Check if sidebar is active
	 *
	 * @return bool
	 */
	public static function has_sidebar() {
		return ( self::has_full_width() ) ? false : true;
	}
	/**
	 * Check if full width layout is active
	 *
	 * @return bool
	 */
	public static function has_full_width() {
		$full_width_layout  = Options::$layout == 'full-width';
		$not_active_sidebar = ! is_active_sidebar( Options::$sidebar );

		return $full_width_layout || $not_active_sidebar;
	}

	/**
	 * Get layout class
	 *
	 * @return void
	 */
	public static function the_layout_class() {
		$fullwidth_col = ( Options::$options['blog_style'] == 'style1' && is_home() ) ? 'col-sm-10 offset-sm-1 col-12' : 'col-sm-12 col-12';

		$layout_class = self::has_sidebar() ? 'col-lg-8 col-sm-12 col-12' : $fullwidth_col;
		if ( Options::$layout == 'left-sidebar' ) {
			$layout_class .= ' order-lg-2';
		}

		$layout_class = apply_filters( 'cl_classified_layout_class', $layout_class );

		echo esc_attr( $layout_class );
	}

	/**
	 * Get sidebar class
	 *
	 * @return void
	 */
	public static function the_sidebar_class() {
		$sidebar_class = self::has_sidebar() ? 'col-lg-4 col-sm-12 sidebar-break-lg' : 'col-sm-12 col-12';

		$sidebar_class = apply_filters( 'cl_classified_sidebar_class', $sidebar_class );

		echo esc_attr( $sidebar_class );
	}
	/**
	 * Callback function for rendering individual comments.
	 *
	 * @param \WP_Comment $comment The comment object.
	 * @param array       $args    An array of arguments passed to the callback.
	 * @param int         $depth   Depth of the current comment in the thread.
	 *
	 * @return void
	 */
	public static function comments_callback( $comment, $args, $depth ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$args2 = get_defined_vars();
		self::get_template_part( 'template-parts/comments-callback', $args2 );
	}
	/**
	 * Get all sidebar list
	 *
	 * @return array
	 */
	public static function nav_menu_args() {
		$nav_menu_args = [
			'theme_location' => 'primary',
			'container'      => 'nav',
			'fallback_cb'    => false,
			'walker'         => new Menu_Walker(),
		];

		return $nav_menu_args;
	}
	/**
	 * Require a file from the child or parent theme directories.
	 *
	 * @param string      $filename Name of the file to include.
	 * @param string|bool $dir      Optional subdirectory inside the theme. Default false.
	 *
	 * @return void|false Returns false if the file does not exist.
	 */
	public static function requires( $filename, $dir = false ) {
		if ( $dir ) {
			$child_file = get_stylesheet_directory() . '/' . $dir . '/' . $filename;

			if ( file_exists( $child_file ) ) {
				$file = $child_file;
			} else {
				$file = get_template_directory() . '/' . $dir . '/' . $filename;
			}
		} else {
			$child_file = get_stylesheet_directory() . '/inc/' . $filename;

			if ( file_exists( $child_file ) ) {
				$file = $child_file;
			} else {
				$file = Constants::$theme_inc_dir . $filename;
			}
		}
		if ( file_exists( $file ) ) {
			require_once $file;
		} else {
			return false;
		}
	}
	/**
	 * Get the correct file URL from child or parent theme.
	 *
	 * @param string $path Relative path to the file, e.g., '/assets/js/script.js'.
	 *
	 * @return string URL of the file.
	 */
	public static function get_file( $path ) {
		$file = get_stylesheet_directory_uri() . $path;
		if ( ! file_exists( $file ) ) {
			$file = get_template_directory_uri() . $path;
		}

		return $file;
	}
	/**
	 * Get the URL of an image file from the theme assets.
	 *
	 * @param string $filename Name of the image file, e.g., 'logo.png'.
	 *
	 * @return string URL of the image file.
	 */
	public static function get_img( $filename ) {
		$path = '/assets/img/' . $filename;

		return self::get_file( $path );
	}
	/**
	 * Get the URL of an image file from the theme assets.
	 *
	 * @param string $filename Name of the image file, e.g., 'logo.png'.
	 *
	 * @return string URL of the image file.
	 */
	public static function get_css( $filename ) {
		$path = '/assets/css/' . $filename . '.css';

		return self::get_file( $path );
	}
	/**
	 * Get the correct CSS file URL, using an RTL version if applicable.
	 *
	 * @param string $filename Name of the CSS file without extension, e.g., 'style'.
	 *
	 * @return string URL of the CSS file.
	 */
	public static function get_maybe_rtl_css( $filename ) {
		if ( is_rtl() ) {
			$path = '/assets/css-rtl/' . $filename . '.css';

			return self::get_file( $path );
		} else {
			return self::get_css( $filename );
		}
	}
	/**
	 * Get the URL of an RTL CSS file from the theme assets.
	 *
	 * @param string $filename Name of the CSS file without extension, e.g., 'style'.
	 *
	 * @return string URL of the RTL CSS file.
	 */
	public static function get_rtl_css( $filename ) {
		$path = '/assets/css-rtl/' . $filename . '.css';

		return self::get_file( $path );
	}
	/**
	 * Get the URL of a JavaScript file from the theme assets.
	 *
	 * @param string $filename Name of the JS file without extension, e.g., 'script'.
	 *
	 * @return string URL of the JavaScript file.
	 */
	public static function get_js( $filename ) {
		$path = '/assets/js/' . $filename . '.js';

		return self::get_file( $path );
	}
	/**
	 * Load a template part from the child or parent theme.
	 *
	 * @param string $template Template path relative to the theme directory, without leading slash or extension.
	 * @param array  $args     Optional array of variables to extract into the template. Default empty array.
	 *
	 * @return bool True if the template was loaded, false if a file does not exist.
	 */
	public static function get_template_part( $template, $args = [] ) {
		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		$template = '/' . $template . '.php';

		if ( file_exists( get_stylesheet_directory() . $template ) ) {
			$file = get_stylesheet_directory() . $template;
		} else {
			$file = get_template_directory() . $template;
		}
		if ( file_exists( $file ) ) {
			require $file;
		} else {
			return false;
		}
	}

	/**
	 * Get all sidebar list
	 *
	 * @return array
	 */
	public static function custom_sidebar_fields() {
		$base                                   = 'cl_classified';
		$sidebar_fields                         = [];
		$sidebar_fields['sidebar']              = esc_html__( 'Sidebar', 'cl-classified' );
		$sidebar_fields['rtcl-archive-sidebar'] = esc_html__( 'Listing Archive Sidebar', 'cl-classified' );
		$sidebar_fields['rtcl-single-sidebar']  = esc_html__( 'Listing Single Sidebar', 'cl-classified' );
		$sidebars                               = get_option( "{$base}_custom_sidebars", [] );

		if ( $sidebars ) {
			foreach ( $sidebars as $sidebar ) {
				$sidebar_fields[ $sidebar['id'] ] = $sidebar['name'];
			}
		}

		return $sidebar_fields;
	}

	/**
	 * Get the available header layouts.
	 *
	 * Returns either a simple list of header names or detailed data for images,
	 * depending on the $return_type parameter.
	 *
	 * @param string $return_type Optional. If 'header', returns a detailed array with image URLs and names. Default ''.
	 *
	 * @return array Array of header layouts. Structure depends on $return_type.
	 */
	public static function get_header_list( $return_type = '' ) {
		if ( 'header' === $return_type ) {
			return [
				'1' => [
					'image' => trailingslashit( get_template_directory_uri() ) . 'assets/img/header-1.png',
					'name'  => esc_html__( 'Style 1', 'cl-classified' ),
				],
			];
		} else {
			return [
				'default' => esc_html__( 'Default', 'cl-classified' ),
				'1'       => esc_html__( 'Layout 1', 'cl-classified' ),
			];
		}
	}

	/**
	 * Get the available footer layouts.
	 *
	 * Returns either a detailed array with images and names if $return_type is 'footer',
	 * or a simple array of footer names otherwise. The result is filterable via 'cl_classified_footer_layout'.
	 *
	 * @param string $return_type Optional. If 'footer', returns a detailed array with images and names. Default ''.
	 *
	 * @return array Array of footer layouts. Structure depends on $return_type.
	 */
	public static function get_footer_list( $return_type = '' ) {
		if ( 'footer' === $return_type ) {
			$layout = [
				'1' => [
					'image' => trailingslashit( get_template_directory_uri() ) . 'assets/img/footer-1.png',
					'name'  => esc_html__( 'Layout 1', 'cl-classified' ),
				],
			];
		} else {
			$layout = [
				'default' => esc_html__( 'Default', 'cl-classified' ),
				'1'       => esc_html__( 'Layout 1', 'cl-classified' ),
			];
		}

		return apply_filters( 'cl_classified_footer_layout', $layout );
	}

	/**
	 * Get site search style
	 *
	 * @return array
	 */
	public static function get_search_form_style() {
		$style = [
			'standard' => esc_html__( 'Standard', 'cl-classified' ),
		];

		if ( class_exists( 'RtclPro' ) ) {
			$style = array_merge(
				$style,
				[
					'popup'      => esc_html__( 'Popup', 'cl-classified' ),
					'suggestion' => esc_html__( 'Auto Suggestion', 'cl-classified' ),
					'dependency' => esc_html__( 'Dependency Selection', 'cl-classified' ),
				]
			);
		}

		return $style;
	}
	/**
	 * Load or get a custom listing template part.
	 *
	 * @param string $template Name of the template file, without path or extension.
	 * @param bool   $echo     Whether to echo the template part (true) or return its path (false). Default true.
	 * @param array  $args     Optional array of arguments to pass to the template part. Default empty array.
	 * @param string $path     Optional subpath inside 'classified-listing'. Default 'custom/'.
	 *
	 * @return string|void Returns the template path if $echo is false; otherwise void.
	 */
	public static function get_custom_listing_template( $template, $echo = true, $args = [], $path = 'custom/' ) {
		$template = 'classified-listing/' . $path . $template;
		if ( $echo ) {
			self::get_template_part( $template, $args );
		} else {
			$template .= '.php';

			return $template;
		}
	}
	/**
	 * Load or get a custom store template part.
	 *
	 * @param string $template Name of the template file, without path or extension.
	 * @param bool   $echo     Whether to echo the template part (true) or return its path (false). Default true.
	 * @param array  $args     Optional array of arguments to pass to the template part. Default empty array.
	 *
	 * @return string|void Returns the template path if $echo is false; otherwise void.
	 */
	public static function get_custom_store_template( $template, $echo = true, $args = [] ) {
		$template = 'classified-listing/store/custom/' . $template;
		if ( $echo ) {
			self::get_template_part( $template, $args );
		} else {
			$template .= '.php';

			return $template;
		}
	}
	/**
	 * Get the correct CSS file URL, using an RTL version if applicable.
	 *
	 * @return bool
	 */
	public static function is_chat_enabled() {
		if ( Options::$options['header_chat_icon'] && class_exists( 'Rtcl' ) && class_exists( 'RtclPro' ) ) {
			if ( Fns::is_enable_chat() ) {
				return true;
			}
		}

		return false;
	}
	/**
	 * @return mixed|null
	 */
	public static function get_primary_color() {
		return apply_filters( 'rdtheme_primary_color', Options::$options['primary_color'] );
	}
	/**
	 * @return mixed|null
	 */
	public static function get_secondary_color() {
		return apply_filters( 'rdtheme_secondary_color', Options::$options['secondary_color'] );
	}
	/**
	 * @return mixed|null
	 */
	public static function get_top_bg_color() {
		return apply_filters( 'rdtheme_top_bg_color', Options::$options['top_listing_bg'] );
	}
	/**
	 * @return mixed|null
	 */
	public static function get_lite_primary_color() {
		return apply_filters( 'rdtheme_lite_primary_color', Options::$options['lite_primary_color'] );
	}
	/**
	 * @return mixed|null
	 */
	public static function get_body_color() {
		return apply_filters( 'rdtheme_body_color', Options::$options['body_color'] );
	}
	/**
	 * Check if the login button is enabled in the theme customizer.
	 *
	 * @return bool
	 */
	public static function is_header_btn_enabled() {
		$btn_flag = get_theme_mod( 'header_btn' );
		if ( empty( $btn_flag ) ) {
			return false;
		}

		return true;
	}
	/**
	 * Check if the login button is enabled in the theme customizer.
	 *
	 * @return bool
	 */
	public static function is_trheader_enable() {
		$tr_header = get_theme_mod( 'tr_header' );
		if ( empty( $tr_header ) ) {
			return false;
		}

		return true;
	}
	/**
	 * Show copywrite area
	 *
	 * @return bool
	 */
	public static function is_copyright_area_enabled() {
		$flag = get_theme_mod( 'copyright_area' );
		if ( empty( $flag ) ) {
			return false;
		}

		return true;
	}
	/**
	 * Check if the login button is enabled in the theme customizer.
	 *
	 * @return bool True if enabled, false otherwise.
	 */
	public static function is_login_btn_enabled() {
		$btn_flag = get_theme_mod( 'header_login_icon' );
		if ( empty( $btn_flag ) ) {
			return false;
		}

		return true;
	}
	/**
	 * Temporarily replace the global $wp_query with a new query.
	 *
	 * Stores the current $wp_query, replaces it with a new WP_Query object,
	 * and returns the original query for later restoration.
	 *
	 * @param \WP_Query $query New WP_Query object to set temporarily.
	 *
	 * @return \WP_Query The original WP_Query object before replacement.
	 */
	public static function wp_set_temp_query( $query ) {
		global $wp_query;
		$temp     = $wp_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$wp_query = $query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		return $temp;
	}
	/**
	 * Reset a temporary WP_Query instance.
	 *
	 * Restores the global $wp_query to a temporary query and resets post-data.
	 *
	 * @param \WP_Query $temp Temporary WP_Query object to restore.
	 *
	 * @return void
	 */
	public static function wp_reset_temp_query( $temp ) {
		global $wp_query;
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$wp_query = $temp;
		wp_reset_postdata();
	}
	/**
	 * Convert a hex color code to an RGB string.
	 *
	 * @param string $hex Hex color code (e.g., '#ff0000' or 'f00').
	 *
	 * @return string Comma-separated RGB values, e.g., '255, 0, 0'.
	 */
	public static function hex2rgb( $hex ) {
		$hex = str_replace( '#', '', $hex );
		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgb = "$r, $g, $b";

		return $rgb;
	}
	/**
	 * Get social links
	 *
	 * @return array[]
	 */
	public static function socials() {
		$rdtheme_socials = [
			'facebook'  => [
				'icon' => 'fab fa-facebook-square',
				'url'  => Options::$options['facebook'],
			],
			'twitter'   => [
				'icon' => 'fab fa-twitter',
				'url'  => Options::$options['twitter'],
			],
			'linkedin'  => [
				'icon' => 'fab fa-linkedin-in',
				'url'  => Options::$options['linkedin'],
			],
			'youtube'   => [
				'icon' => 'fab fa-youtube',
				'url'  => Options::$options['youtube'],
			],
			'pinterest' => [
				'icon' => 'fab fa-pinterest',
				'url'  => Options::$options['pinterest'],
			],
			'instagram' => [
				'icon' => 'fab fa-instagram',
				'url'  => Options::$options['instagram'],
			],
			'skype'     => [
				'icon' => 'fab fa-skype',
				'url'  => Options::$options['skype'],
			],
		];

		return array_filter( $rdtheme_socials, [ __CLASS__, 'filter_social' ] );
	}
	/**
	 * Filter social links to exclude empty URLs.
	 *
	 * @param array $args Argument array containing 'url'.
	 *
	 * @return bool True if the URL is not empty, false otherwise.
	 */
	public static function filter_social( $args ) {
		return ( $args['url'] != '' );
	}
}
