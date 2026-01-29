<?php

if ( class_exists( 'WP_Customize_Control' ) ) {
	if ( ! function_exists( 'rttheme_url_sanitization' ) ) {
		/**
		 * Sanitize a URL or a comma-separated list of URLs.
		 *
		 * Escapes URLs using esc_url_raw() and preserves comma separation if multiple URLs.
		 *
		 * @param  string|array $input  Input string or array of URLs.
		 *
		 * @return string Sanitized URL(s), comma-separated if multiple.
		 */
		function rttheme_url_sanitization( $input ) {
			if ( strpos( $input, ',' ) !== false ) {
				$input = explode( ',', $input );
			}
			if ( is_array( $input ) ) {
				foreach ( $input as $key => $value ) {
					$input[ $key ] = esc_url_raw( $value );
				}
				$input = implode( ',', $input );
			} else {
				$input = esc_url_raw( $input );
			}

			return $input;
		}
	}

	if ( ! function_exists( 'rttheme_switch_sanitization' ) ) {
		/**
		 * Sanitize a switch (boolean) value.
		 *
		 * Converts boolean true/false to integer 1/0.
		 *
		 * @param  bool|mixed $input  Switch value.
		 *
		 * @return int Sanitized integer value (1 or 0).
		 */
		function rttheme_switch_sanitization( $input ) {
			if ( true === $input ) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	if ( ! function_exists( 'rttheme_radio_sanitization' ) ) {
		/**
		 * Sanitize a radio or select input.
		 *
		 * Ensures the input is one of the allowed choices for the given setting.
		 * Returns the default value if input is invalid.
		 *
		 * @param  string|int $input  The input value to sanitize.
		 * @param  object     $setting  The WP_Customize_Setting object.
		 *
		 * @return string|int Sanitized value (valid choice or default).
		 */
		function rttheme_radio_sanitization( $input, $setting ) {
			// get the list of possible radio box or select options
			$choices = $setting->manager->get_control( $setting->id )->choices;

			if ( array_key_exists( $input, $choices ) ) {
				return $input;
			} else {
				return $setting->default;
			}
		}
	}

	if ( ! function_exists( 'rttheme_sanitize_integer' ) ) {
		/**
		 * Sanitize an input as an integer.
		 *
		 * Converts the input value to an integer.
		 *
		 * @param  mixed $input  Input value to sanitize.
		 *
		 * @return int Sanitized integer value.
		 */
		function rttheme_sanitize_integer( $input ) {
			return (int) $input;
		}
	}

	if ( ! function_exists( 'rttheme_text_sanitization' ) ) {
		/**
		 * Sanitize a text input or comma-separated list of texts.
		 *
		 * Splits input by commas, sanitizes each item, and returns
		 * the sanitized string (re-joined by commas) or a single sanitized value.
		 *
		 * @param  string $input  Input text or comma-separated values.
		 *
		 * @return string Sanitized string.
		 */
		function rttheme_text_sanitization( $input ) {
			if ( strpos( $input, ',' ) !== false ) {
				$input = explode( ',', $input );
			}
			if ( is_array( $input ) ) {
				foreach ( $input as $key => $value ) {
					$input[ $key ] = sanitize_text_field( $value );
				}
				$input = implode( ',', $input );
			} else {
				$input = sanitize_text_field( $input );
			}

			return $input;
		}
	}

	if ( ! function_exists( 'rttheme_google_font_sanitization' ) ) {
		/**
		 * Sanitize a Google Font setting.
		 *
		 * Decodes JSON input, applies sanitize_text_field() to each value,
		 * and re-encodes the array as JSON. Handles both arrays and single values.
		 *
		 * @param  string $input  JSON-encoded string representing Google Font settings.
		 *
		 * @return string Sanitized JSON-encoded string.
		 */
		function rttheme_google_font_sanitization( $input ) {
			$val = json_decode( $input, true );
			if ( is_array( $val ) ) {
				foreach ( $val as $key => $value ) {
					$val[ $key ] = sanitize_text_field( $value );
				}
				$input = wp_json_encode( $val );
			} else {
				$input = wp_json_encode( sanitize_text_field( $val ) );
			}

			return $input;
		}
	}

	if ( ! function_exists( 'rttheme_array_sanitization' ) ) {
		/**
		 * Sanitize an array of text values.
		 *
		 * Applies sanitize_text_field() to each element of the array.
		 * Returns an empty string if input is not an array.
		 *
		 * @param  mixed $input  Input array to sanitize.
		 *
		 * @return array|string Sanitized array, or empty string if input is invalid.
		 */
		function rttheme_array_sanitization( $input ) {
			if ( is_array( $input ) ) {
				foreach ( $input as $key => $value ) {
					$input[ $key ] = sanitize_text_field( $value );
				}
			} else {
				$input = '';
			}

			return $input;
		}
	}

	if ( ! function_exists( 'rttheme_in_range' ) ) {
		/**
		 * Clamp a number within a specified range.
		 *
		 * Ensures the input value is not lower than the minimum or higher than the maximum.
		 *
		 * @param  float|int $input  The input number to clamp.
		 * @param  float|int $min  Minimum allowed value.
		 * @param  float|int $max  Maximum allowed value.
		 *
		 * @return float|int Number clamped within the range.
		 */
		function rttheme_in_range( $input, $min, $max ) {
			if ( $input < $min ) {
				$input = $min;
			}
			if ( $input > $max ) {
				$input = $max;
			}

			return $input;
		}
	}

	if ( ! function_exists( 'rttheme_date_time_sanitization' ) ) {
		/**
		 * Sanitize a date or date-time input.
		 *
		 * Ensures the input matches the expected format. Falls back to the
		 * setting's default value if invalid.
		 *
		 * @param  string $input  Date or date-time string.
		 * @param  object $setting  WP_Customize_Setting object.
		 *
		 * @return string Sanitized date or date-time string.
		 */
		function rttheme_date_time_sanitization( $input, $setting ) {
			$datetimeformat = 'Y-m-d';
			if ( $setting->manager->get_control( $setting->id )->include_time ) {
				$datetimeformat = 'Y-m-d H:i:s';
			}
			$date = DateTime::createFromFormat( $datetimeformat, $input );
			if ( $date === false ) {
				$date = DateTime::createFromFormat( $datetimeformat, $setting->default );
			}

			return $date->format( $datetimeformat );
		}
	}

	if ( ! function_exists( 'rttheme_range_sanitization' ) ) {
		/**
		 * Sanitize a numeric range input.
		 *
		 * Ensures the input value respects the min, max, and step attributes
		 * of the Customizer control. Uses rttheme_in_range() for final clamping.
		 *
		 * @param  float|int $input  Input value to sanitize.
		 * @param  object    $setting  WP_Customize_Setting object.
		 *
		 * @return float|int Sanitized value within the allowed range.
		 */
		function rttheme_range_sanitization( $input, $setting ) {
			$attrs = $setting->manager->get_control( $setting->id )->input_attrs;

			$min  = ( isset( $attrs['min'] ) ? $attrs['min'] : $input );
			$max  = ( isset( $attrs['max'] ) ? $attrs['max'] : $input );
			$step = ( isset( $attrs['step'] ) ? $attrs['step'] : 1 );

			$number = floor( $input / $attrs['step'] ) * $attrs['step'];

			return rttheme_in_range( $number, $min, $max );
		}
	}

	if ( ! function_exists( 'rttheme_sanitize_file' ) ) {
		/**
		 * Sanitize an uploaded file.
		 *
		 * Ensures the file is one of the allowed image types (JPEG, GIF, PNG).
		 * Returns the default value if the file type is invalid.
		 *
		 * @param  string $file  File URL or path.
		 * @param  object $setting  WP_Customize_Setting object.
		 *
		 * @return string Sanitized file URL/path or default.
		 */
		function rttheme_sanitize_file( $file, $setting ) {
			$mimes = [
				'jpg|jpeg|jpe' => 'image/jpeg',
				'gif'          => 'image/gif',
				'png'          => 'image/png',
			];

			// check file type from file name
			$file_ext = wp_check_filetype( $file, $mimes );

			// if file has a valid mime type return it, otherwise return default
			return ( $file_ext['ext'] ? $file : $setting->default );
		}
	}
}
