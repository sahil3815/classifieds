<?php
/**
 * @author  RadiusTheme
 * @since   1.0.0
 * @version 1.1.0
 */

namespace RadiusTheme\ClassifiedLite;

class Scripts {

	public $version;
	protected static $instance = null;

	/**
	 * Constructor.
	 *
	 * Registers and enqueues frontend and Gutenberg scripts/styles.
	 *
	 * Hooks:
	 * - wp_enqueue_scripts: register_scripts (priority 12)
	 * - wp_enqueue_scripts: enqueue_scripts (priority 15)
	 * - enqueue_block_editor_assets: register_gutenberg_scripts
	 * - enqueue_block_editor_assets: enqueue_gutenberg_scripts (priority 20)
	 *
	 * @return void
	 * @version 1.1.0
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->version = Constants::$theme_version;
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ], 12 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 15 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'register_gutenberg_scripts' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_gutenberg_scripts' ], 20 );
	}

	/**
	 * @return self|null
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get Google fonts URL.
	 *
	 * @return string
	 */
	public function fonts_url() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
		$fonts_url = '';
		$subsets   = '';
		$bodyFont  = 'Lato';
		$bodyFW    = '300,400,500,600,700';

		$menuFont  = 'Nunito';
		$menuFontW = '300,400,500,600,700';

		$hFont  = 'Nunito';
		$hFontW = '300,400,500,600,700';
		$h1Font = '';
		$h2Font = '';
		$h3Font = '';
		$h4Font = '';
		$h5Font = '';
		$h6Font = '';

		// Body Font
		$body_font = json_decode( Options::$options['typo_body'], true );

		if ( $body_font['font'] == 'Inherit' ) {
			$bodyFont = 'Lato';
		} else {
			$bodyFont = $body_font['font'];
		}
		$bodyFontW = $body_font['regularweight'];

		// Menu Font
		$menu_font = json_decode( Options::$options['typo_menu'], true );
		if ( $menu_font['font'] == 'Inherit' ) {
			$menuFont = 'Nunito';
		} else {
			$menuFont = $menu_font['font'];
		}
		$menuFontW = $menu_font['regularweight'];

		// Heading Font
		$h_font = json_decode( Options::$options['typo_heading'], true );
		if ( $h_font['font'] == 'Inherit' ) {
			$hFont = 'Nunito';
		} else {
			$hFont = $h_font['font'];
		}
		$hFontW  = $h_font['regularweight'];
		$h1_font = json_decode( Options::$options['typo_h1'], true );
		$h2_font = json_decode( Options::$options['typo_h2'], true );
		$h3_font = json_decode( Options::$options['typo_h3'], true );
		$h4_font = json_decode( Options::$options['typo_h4'], true );
		$h5_font = json_decode( Options::$options['typo_h5'], true );
		$h6_font = json_decode( Options::$options['typo_h6'], true );

		if ( 'off' !== _x( 'on', 'Google font: on or off', 'cl-classified' ) ) {
			if ( ! empty( $h1_font['font'] ) ) {
				if ( $h1_font['font'] == 'Inherit' ) {
					$h1Font  = $hFont;
					$h1FontW = $hFontW;
				} else {
					$h1Font  = $h2_font['font'];
					$h1FontW = $h1_font['regularweight'];
				}
			}
			if ( ! empty( $h2_font['font'] ) ) {
				if ( $h2_font['font'] == 'Inherit' ) {
					$h2Font  = $hFont;
					$h2FontW = $hFontW;
				} else {
					$h2Font  = $h2_font['font'];
					$h2FontW = $h2_font['regularweight'];
				}
			}
			if ( ! empty( $h3_font['font'] ) ) {
				if ( $h3_font['font'] == 'Inherit' ) {
					$h3Font  = $hFont;
					$h3FontW = $hFontW;
				} else {
					$h3Font  = $h3_font['font'];
					$h3FontW = $h3_font['regularweight'];
				}
			}
			if ( ! empty( $h4_font['font'] ) ) {
				if ( $h4_font['font'] == 'Inherit' ) {
					$h4Font  = $hFont;
					$h4FontW = $hFontW;
				} else {
					$h4Font  = $h4_font['font'];
					$h4FontW = $h4_font['regularweight'];
				}
			}
			if ( ! empty( $h5_font['font'] ) ) {
				if ( $h5_font['font'] == 'Inherit' ) {
					$h5Font  = $hFont;
					$h5FontW = $hFontW;
				} else {
					$h5Font  = $h5_font['font'];
					$h5FontW = $h5_font['regularweight'];
				}
			}
			if ( ! empty( $h6_font['font'] ) ) {
				if ( $h6_font['font'] == 'Inherit' ) {
					$h6Font  = $hFont;
					$h6FontW = $hFontW;
				} else {
					$h6Font  = $h6_font['font'];
					$h6FontW = $h6_font['regularweight'];
				}
			}

			$check_families = [];
			$font_families  = [];

			// Body Font
			if ( 'off' !== $bodyFont ) {
				$font_families[] = $bodyFont . ':300,400,500,600,700';
			}
			$check_families[] = $bodyFont;

			// Menu Font
			if ( 'off' !== $menuFont ) {
				if ( ! in_array( $menuFont, $check_families, true ) ) {
					$font_families[]  = $menuFont . ':300,400,500,600,700';
					$check_families[] = $menuFont;
				}
			}

			// Heading Font
			if ( 'off' !== $hFont ) {
				if ( ! in_array( $hFont, $check_families, true ) ) {
					$font_families[]  = $hFont . ':300,400,500,600,700';
					$check_families[] = $hFont;
				}
			}

			// Heading 1 Font
			if ( ! empty( $h1_font['font'] ) ) {
				if ( 'off' !== $h1Font ) {
					if ( ! in_array( $h1Font, $check_families, true ) ) {
						$font_families[]  = $h1Font . ':' . $h1FontW;
						$check_families[] = $h1Font;
					}
				}
			}
			// Heading 2 Font
			if ( ! empty( $h2_font['font'] ) ) {
				if ( 'off' !== $h2Font ) {
					if ( ! in_array( $h2Font, $check_families, true ) ) {
						$font_families[]  = $h2Font . ':' . $h2FontW;
						$check_families[] = $h2Font;
					}
				}
			}
			// Heading 3 Font
			if ( ! empty( $h3_font['font'] ) ) {
				if ( 'off' !== $h3Font ) {
					if ( ! in_array( $h3Font, $check_families, true ) ) {
						$font_families[]  = $h3Font . ':' . $h3FontW;
						$check_families[] = $h3Font;
					}
				}
			}
			// Heading 4 Font
			if ( ! empty( $h4_font['font'] ) ) {
				if ( 'off' !== $h4Font ) {
					if ( ! in_array( $h4Font, $check_families, true ) ) {
						$font_families[]  = $h4Font . ':' . $h4FontW;
						$check_families[] = $h4Font;
					}
				}
			}

			// Heading 5 Font
			if ( ! empty( $h5_font['font'] ) ) {
				if ( 'off' !== $h5Font ) {
					if ( ! in_array( $h5Font, $check_families, true ) ) {
						$font_families[]  = $h5Font . ':' . $h5FontW;
						$check_families[] = $h5Font;
					}
				}
			}
			// Heading 6 Font
			if ( ! empty( $h6_font['font'] ) ) {
				if ( 'off' !== $h6Font ) {
					if ( ! in_array( $h6Font, $check_families, true ) ) {
						$font_families[]  = $h6Font . ':' . $h6FontW;
						$check_families[] = $h6Font;
					}
				}
			}
			$final_fonts = array_unique( $font_families );
			$query_args  = [
				'family'  => rawurlencode( implode( '|', $final_fonts ) ),
				'subset'  => rawurlencode( $subsets ),
				'display' => rawurlencode( 'fallback' ),
			];

			$fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
		}

		return esc_url_raw( $fonts_url );
	}

	/**
	 * Register all the scripts
	 *
	 * @return void
	 */
	public function register_scripts() {
		/* Deregister */
		wp_deregister_style( 'font-awesome' );
		// Google fonts
		wp_register_style( 'cl-classified-gfonts', $this->fonts_url(), [], $this->version );
		// Style
		wp_register_style( 'font-awesome', Helper::get_file( '/assets/font-awesome/css/font-awesome-all.min.css' ), [], $this->version );
		wp_register_style( 'bootstrap', Helper::get_maybe_rtl_css( 'bootstrap.min' ), [], $this->version );
		wp_register_style( 'cl-classified-main', Helper::get_maybe_rtl_css( 'main' ), [], $this->version );
		if ( is_rtl() ) {
			wp_register_style( 'cl-classified-rtl', Helper::get_css( 'rtl' ), [], $this->version );
		}

		// Script
		wp_register_script( 'bootstrap', Helper::get_js( 'bootstrap.bundle.min' ), [ 'jquery' ], $this->version, true );
		wp_register_script( 'cl-classified-main', Helper::get_js( 'main' ), [ 'jquery' ], $this->version, true );
	}

	/**
	 * Enqueue frontend CSS and JS scripts for the theme.
	 *
	 * Loads Google Fonts, Bootstrap, Font Awesome, main styles/scripts,
	 * RTL styles if needed, dynamic inline styles, conditional scripts,
	 * and localized JS variables.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		/*------CSS--------*/
		wp_enqueue_style( 'cl-classified-gfonts' );
		wp_enqueue_style( 'bootstrap' );
		wp_enqueue_style( 'font-awesome' );
		wp_enqueue_style( 'cl-classified-main' );
		if ( is_rtl() ) {
			wp_enqueue_style( 'cl-classified-rtl' );
		}
		$this->dynamic_style();
		/*-------JS--------*/
		$this->conditional_scripts(); // Conditional Scripts
		wp_enqueue_script( 'bootstrap' );

		wp_enqueue_script( 'cl-classified-main' );
		$this->localized_scripts(); // Localization
	}

	/**
	 * Register Gutenberg-related styles for the editor.
	 *
	 * Registers Google Fonts and Gutenberg styles with optional RTL support.
	 *
	 * @return void
	 */
	public function register_gutenberg_scripts() {
		wp_register_style( 'cl-classified-gfonts', $this->fonts_url(), [], $this->version );
		wp_register_style( 'cl-classified-gutenberg', Helper::get_maybe_rtl_css( 'gutenberg' ), [], $this->version );
	}

	/**
	 * Enqueue and apply dynamic styles for Gutenberg editor blocks.
	 *
	 * Loads Google Fonts, Gutenberg styles, and applies custom inline CSS
	 * with a wrapper for editor blocks.
	 *
	 * @return void
	 */
	public function enqueue_gutenberg_scripts() {
		wp_enqueue_style( 'cl-classified-gfonts' );
		wp_enqueue_style( 'cl-classified-gutenberg' );
		ob_start();
		Helper::requires( 'common.php', 'dynamic-styles' );
		$dynamic_css = ob_get_clean();
		$css         = $this->add_wrapper_to_css( $dynamic_css, '.wp-block.editor-block-list__block' );
		$css         = str_replace( 'gtnbg_root', '', $css );
		wp_add_inline_style( 'cl-classified-gutenberg', $css );
	}

	/**
	 * Enqueue conditional scripts based on page context.
	 *
	 * Specifically, enqueues the WordPress comment-reply script
	 * if comments are open and threaded comments are enabled.
	 *
	 * @return void
	 */
	private function conditional_scripts() {
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Localize theme-related data for JavaScript.
	 *
	 * Provides AJAX URL, theme URLs, sticky menu settings, and allows filters.
	 *
	 * @return void
	 */
	private function localized_scripts() {
		global $post;

		$localize_data = [
			'ajaxUrl'              => admin_url( 'admin-ajax.php' ),
			'appendHtml'           => '',
			'themeUrl'             => get_stylesheet_directory_uri(),
			'hasStickyMenu'        => Options::$options['sticky_header'] ? 1 : 0,
			'rtStickyHeaderOffset' => Options::$options['sticky_header'] ? 130 : 10,
		];

		$localize_data = apply_filters( 'cl_classified_localized_data', $localize_data );

		wp_localize_script( 'cl-classified-main', 'CLClassified', $localize_data );
	}

	/**
	 * Generate and enqueue dynamic CSS for the frontend.
	 *
	 * Combines template styles, additional dynamic styles, and minifies them.
	 *
	 * @return void
	 */
	private function dynamic_style() {
		$dynamic_css = $this->template_style();
		ob_start();
		Helper::requires( 'frontend.php', 'dynamic-styles' );
		$dynamic_css .= ob_get_clean();
		$dynamic_css  = $this->optimized_css( $dynamic_css );

		wp_register_style( 'cl-classified-dynamic', false, [], $this->version );
		wp_enqueue_style( 'cl-classified-dynamic' );
		wp_add_inline_style( 'cl-classified-dynamic', $dynamic_css );
	}

	/**
	 * Generate dynamic inline CSS for the theme template.
	 *
	 * Includes top/bottom padding and banner background image.
	 *
	 * @return string Inline CSS string.
	 */
	private function template_style() {
		$style = '';

		if ( Options::$padding_top != '' ) {
			$style .= 'body .content-area {padding-top:' . Options::$padding_top . '!important;}';
			$style .= 'body .rtcl-wrapper {padding-top:' . Options::$padding_top . '!important;}';
		}
		if ( Options::$padding_bottom != '' ) {
			$style .= 'body .content-area {padding-bottom:' . Options::$padding_bottom . '!important}';
			$style .= 'body .rtcl-wrapper {padding-bottom:' . Options::$padding_bottom . '!important}';
		}

		$bgimg = ! empty( wp_get_attachment_image_url( Options::$options['banner_image'], 'full' ) ) ? wp_get_attachment_image_url(
			Options::$options['banner_image'],
			'full',
		) : '';

		if ( ! empty( $bgimg ) ) {
			$style .= '.banner-search {background-image:url(' . $bgimg . ');}';
		}

		return $style;
	}

	/**
	 * Minify CSS by removing comments and unnecessary whitespace.
	 *
	 * @param  string $css  The original CSS string.
	 *
	 * @return string The optimized/minified CSS string.
	 */
	private function optimized_css( $css ) {
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		$css = str_replace( [ "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ], ' ', $css );

		return $css;
	}

	/**
	 * Add a CSS wrapper/parent selector to all rules in a CSS string.
	 *
	 * Example:
	 *   $css  = "h1 { color: red; } p { font-size: 14px; }"
	 *   $base = ".my-wrapper"
	 *   Result: ".my-wrapper h1 { color: red; } .my-wrapper p { font-size: 14px; }"
	 *
	 * @param  string $css  The original CSS string.
	 * @param  string $base  The wrapper/parent selector to prepend.
	 *
	 * @return string The modified CSS with wrapper added.
	 */
	private function add_wrapper_to_css( $css, $base ) {
		$parts = explode( '}', $css );
		foreach ( $parts as &$part ) {
			if ( empty( $part ) ) {
				continue;
			}

			$firstPart = substr( $part, 0, strpos( $part, '{' ) + 1 );
			$lastPart  = substr( $part, strpos( $part, '{' ) + 2 );
			$subParts  = explode( ',', $firstPart );
			foreach ( $subParts as &$subPart ) {
				$subPart = str_replace( "\n", '', $subPart );
				$subPart = $base . ' ' . trim( $subPart );
			}

			$part = implode( ', ', $subParts ) . $lastPart;
		}

		$resultCSS = implode( "}\n", $parts );

		return $resultCSS;
	}
}
