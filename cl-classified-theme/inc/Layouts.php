<?php
/**
 * @author  RadiusTheme
 * @since   1.0.0
 * @version 1.0.0
 */

namespace RadiusTheme\ClassifiedLite;

use Rtcl\Helpers\Functions;

class Layouts {

	protected static $instance = null;

	public $base;
	public $type;
	public $meta_value;
	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		$this->base = 'cl_classified';

		add_action( 'template_redirect', [ $this, 'layout_settings' ] );
	}
	/**
	 * Get the instance of the class
	 *
	 * @return self|null
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	/**
	 * Set layout settings
	 *
	 * @return void
	 */
	public function layout_settings() {
		$is_listing         = false;
		$is_listing_archive = false;

		if ( class_exists( 'Rtcl' ) ) {
			$is_listing_archive = Functions::is_listings() || Functions::is_listing_taxonomy();
		}

		if ( $is_listing_archive ) {
			$is_listing = true;
		}
		// Single Pages
		if ( ( is_single() || is_page() ) && ! $is_listing ) {
			$post_type        = get_post_type();
			$post_id          = get_the_id();
			$this->meta_value = get_post_meta( $post_id, "{$this->base}_layout_settings", true );

			switch ( $post_type ) {
				case 'post':
					$this->type = 'single_post';
					break;
				case 'rtcl_listing':
					$this->type = 'listing_single';

					Options::$options[ $this->type . '_layout' ]  = 'right-sidebar';
					Options::$options[ $this->type . '_sidebar' ] = '';
					break;
				default:
					$this->type = 'page';
			}

			Options::$layout            = $this->meta_layout_option( 'layout' );
			Options::$sidebar           = $this->meta_layout_option( 'sidebar' );
			Options::$padding_top       = $this->meta_layout_option( 'padding_top' );
			Options::$padding_bottom    = $this->meta_layout_option( 'padding_bottom' );
			Options::$has_top_bar       = $this->meta_layout_global_option( 'top_bar', true );
			Options::$header_width      = $this->meta_layout_global_option( 'header_width' );
			Options::$header_style      = $this->meta_layout_global_option( 'header_style' );
			Options::$menu_alignment    = $this->meta_layout_global_option( 'menu_alignment' );
			Options::$footer_style      = $this->meta_layout_global_option( 'footer_style' );
			Options::$has_tr_header     = $this->meta_layout_global_option( 'tr_header', true );
			Options::$has_breadcrumb    = $this->meta_layout_global_option( 'breadcrumb', true );
			Options::$has_banner_search = $this->meta_layout_global_option( 'banner_search', true );

		} elseif ( is_home() || is_archive() || is_search() || is_404() || $is_listing ) {
			if ( is_404() ) {
				$this->type                                   = 'error';
				Options::$options[ $this->type . '_layout' ]  = 'full-width';
				Options::$options[ $this->type . '_sidebar' ] = '';
			} elseif ( $is_listing_archive ) {
				$this->type = 'listing_archive';
			} else {
				$this->type = 'blog';
			}

			Options::$layout            = $this->layout_option( 'layout' );
			Options::$sidebar           = $this->layout_option( 'sidebar' );
			Options::$padding_top       = $this->layout_option( 'padding_top' );
			Options::$padding_bottom    = $this->layout_option( 'padding_bottom' );
			Options::$has_breadcrumb    = $this->layout_global_option( 'breadcrumb', true );
			Options::$has_banner_search = $this->layout_global_option( 'banner_search', true );
			Options::$has_top_bar       = $this->layout_global_option( 'top_bar', true );
			Options::$header_width      = $this->layout_global_option( 'header_width' );
			Options::$menu_alignment    = $this->layout_global_option( 'menu_alignment' );
			Options::$header_style      = $this->layout_global_option( 'header_style' );
			Options::$footer_style      = $this->layout_global_option( 'footer_style' );
			Options::$has_tr_header     = $this->layout_global_option( 'tr_header', true );
		}
	}

	/**
	 * Get a meta, type-specific, or global option value for a given key.
	 *
	 * Checks meta first, then type-specific layout option, then global option.
	 * Optionally converts the result to a boolean.
	 *
	 * @param string $key     Option or meta key suffix.
	 * @param bool   $is_bool Whether to return a boolean value. Default false.
	 *
	 * @return mixed|string|bool Option value or boolean if $is_bool is true.
	 */
	private function meta_layout_global_option( $key, $is_bool = false ) {
		$layout_key = $this->type . '_' . $key;

		$meta      = ! empty( $this->meta_value[ $key ] ) ? $this->meta_value[ $key ] : 'default';
		$op_layout = Options::$options[ $layout_key ] ? Options::$options[ $layout_key ] : 'default';
		$op_global = Options::$options[ $key ];

		if ( $meta != 'default' ) {
			$result = $meta;
		} elseif ( $op_layout != 'default' ) {
			$result = $op_layout;
		} else {
			$result = $op_global;
		}
		if ( $is_bool ) {
			$result = ( $result === 1 || $result === 'on' ) ? true : false;
		}

		return $result;
	}

	/**
	 * Get a meta or global option value for a given key.
	 *
	 * Checks meta-value first, then type-specific option, then global option.
	 * Optionally converts the result to a boolean.
	 *
	 * @param string $key     Option- or meta-key.
	 * @param bool   $is_bool Whether to return a boolean value. Default false.
	 *
	 * @return mixed|string|bool Option value or boolean if $is_bool is true.
	 */
	private function meta_global_option( $key, $is_bool = false ) {
		$meta      = ! empty( $this->meta_value[ $key ] ) ? $this->meta_value[ $key ] : 'default';
		$op_layout = Options::$options[ $key ] ? Options::$options[ $key ] : 'default';
		$op_global = Options::$options[ $key ];

		if ( $meta != 'default' ) {
			$result = $meta;
		} elseif ( $op_layout != 'default' ) {
			$result = $op_layout;
		} else {
			$result = $op_global;
		}
		if ( $is_bool ) {
			$result = ( $result === 1 || $result === 'on' ) ? true : false;
		}

		return $result;
	}

	/**
	 * Get a meta-layout option value for a given key.
	 *
	 * Returns the meta-value if set, otherwise falls back to the type-specific option.
	 *
	 * @param string $key Option or meta key suffix.
	 *
	 * @return mixed Option value or meta value.
	 */
	private function meta_layout_option( $key ) {
		$layout_key = $this->type . '_' . $key;

		$meta      = ! empty( $this->meta_value[ $key ] ) ? $this->meta_value[ $key ] : 'default';
		$op_layout = Options::$options[ $layout_key ];

		if ( $meta != 'default' ) {
			$result = $meta;
		} else {
			$result = $op_layout;
		}

		return $result;
	}

	/**
	 * Get a layout option value, falling back to a global option if not set.
	 *
	 * Optionally converts the result to a boolean.
	 *
	 * @param string $key     Option key suffix.
	 * @param bool   $is_bool Whether to return a boolean value. Default false.
	 *
	 * @return mixed|string|bool Option value or boolean if $is_bool is true.
	 */
	private function layout_global_option( $key, $is_bool = false ) {
		$layout_key = $this->type . '_' . $key;

		$op_layout = Options::$options[ $layout_key ] ? Options::$options[ $layout_key ] : 'default';
		$op_global = Options::$options[ $key ];

		if ( $op_layout != 'default' ) {
			$result = $op_layout;
		} else {
			$result = $op_global;
		}
		if ( $is_bool ) {
			$result = ( $result === 1 || $result === 'on' ) ? true : false;
		}

		return $result;
	}

	/**
	 * Get a layout option value for the current type.
	 *
	 * @param string $key Option key suffix.
	 *
	 * @return mixed|null Returns the option value if set, null otherwise.
	 */
	private function layout_option( $key ) {
		$layout_key = $this->type . '_' . $key;
		$op_layout  = Options::$options[ $layout_key ];

		return $op_layout;
	}
	/**
	 * Get a background image URL for a given key.
	 *
	 * Checks post meta first (if $is_single), then type-specific option, then global option,
	 * and falls back to a default image.
	 *
	 * @param string $key       Option or meta key suffix.
	 * @param bool   $is_single Whether to use meta value for a single item. Default true.
	 *
	 * @return string URL of the background image.
	 */
	private function bgimg_option( $key, $is_single = true ) {
		$layout_key = $this->type . '_' . $key;

		if ( $is_single ) {
			$meta = ! empty( $this->meta_value[ $key ] ) ? $this->meta_value[ $key ] : '';
		} else {
			$meta = '';
		}

		$op_layout = Options::$options[ $layout_key ];
		$op_global = Options::$options[ $key ];

		if ( $meta ) {
			$src = wp_get_attachment_image_src( $meta, 'full', true );
			$img = $src[0];
		} elseif ( ! empty( $op_layout['url'] ) ) {
			$img = $op_layout['url'];
		} elseif ( ! empty( $op_global['url'] ) ) {
			$img = $op_global['url'];
		} else {
			$img = Helper::get_img( 'banner.jpg' );
		}

		return $img;
	}
}
