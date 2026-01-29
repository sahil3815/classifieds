<?php
/**
 * Demo Importer RTCL Handler
 *
 * This file contains RTCL-specific functionality.
 */

namespace RadiusTheme\CL_Classified_Core\Handlers;

use RadiusTheme\CL_Classified_Core\Utils;
use Rtcl\Models\Form\Form;
use SigmaDevs\EasyDemoImporter\Common\Functions\Helpers;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Demo Importer RTCL Handler Class.
 */
class RtclHandler {
	/**
	 * Theme configuration.
	 *
	 * @var array
	 */
	private $config = [];

	/**
	 * Utilities instance.
	 *
	 * @var Utils
	 */
	private $utils;

	/**
	 * Class Constructor.
	 *
	 * @param array $config Theme configuration.
	 * @param Utils $utils  Utils instance.
	 */
	public function __construct( array $config, Utils $utils ) {
		$this->config = $config;
		$this->utils  = $utils;
	}

	/**
	 * Prepare RTCL environment for import.
	 *
	 * @return void
	 */
	public function prepare_environment(): void {
		$this->update_rtcl_options();
		$this->remove_default_pages();
	}

	/**
	 * Rebuild RTCL environment after import.
	 *
	 * @return void
	 */
	public function rebuild_environment(): void {
		$this->update_rtcl_options();
		$this->clear_forms();
		$this->import_forms();
		$this->update_pages();
		$this->set_listing_types();
		$this->processFormBuilderData();
	}

	/**
	 * Remove default pages before import.
	 *
	 * @return void
	 */
	private function remove_default_pages(): void {
		if ( empty( $this->config['rtcl_pages_to_remove'] ) ) {
			return;
		}

		foreach ( $this->config['rtcl_pages_to_remove'] as $page_title ) {
			$page = Helpers::getPageByTitle( $page_title );

			if ( $page ) {
				wp_delete_post( $page->ID, true );
			}
		}
	}

	/**
	 * Clear RTCL forms table.
	 *
	 * @return void
	 */
	private function clear_forms(): void {
		global $wpdb;
		$table = $wpdb->prefix . 'rtcl_forms';

		$wpdb->query( "TRUNCATE TABLE $table" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Update RTCL membership settings.
	 *
	 * @return void
	 */
	private function update_rtcl_options(): void {
		$file_path = $this->utils->get_demo_content_path() . $this->config['rtcl_custom_files']['rtcl_options'];
		$data      = $this->utils->load_json_file( $file_path );

		if ( is_array( $data ) && ! empty( $data['settings'] ) ) {
			$admin_email = get_option( 'admin_email' );

			foreach ( $data['settings'] as $setting ) {
				$key   = $setting['key'];
				$value = $setting['value'];

				// Handle nested array for rtcl_email_settings.
				if ( 'rtcl_email_settings' === $key && is_array( $value ) ) {
					foreach ( $value as $sub_key => $sub_value ) {
						if ( in_array( $sub_key, [ 'from_email', 'admin_notice_emails' ], true ) ) {
							$value[ $sub_key ] = is_array( $sub_value ) ? [ $admin_email ] : $admin_email;
						}
					}
				}

				update_option( $key, $value );
			}
		}

		update_option( 'rtcl_importing_demo', 'yes' );
	}

	/**
	 * Import RTCL forms from JSON file.
	 *
	 * @return void
	 */
	private function import_forms(): void {
		if ( ! class_exists( '\Rtcl\Models\Form\Form' ) ) {
			return;
		}

		$file_path = $this->utils->get_demo_content_path() . $this->config['rtcl_custom_files']['rtcl_forms'];
		$forms     = $this->utils->load_json_file( $file_path );

		if ( $forms && is_array( $forms ) ) {
			foreach ( $forms as $form_item ) {
				Form::query()->insert( $form_item );
			}
		}
	}

	/**
	 * Update RTCL page options.
	 *
	 * @return void
	 */
	private function update_pages(): void {
		$pages = [];

		foreach ( $this->config['rtcl_pages'] as $key => $title ) {
			$page = Helpers::getPageByTitle( $title );

			if ( $page ) {
				$pages[ $key ] = $page->ID;
			}
		}

		if ( empty( $pages ) ) {
			return;
		}

		$settings         = array_merge( [ 'permalink' => 'listing' ], $pages );
		$defaults         = get_option( 'rtcl_advanced_settings', [] );
		$updated_settings = wp_parse_args( $settings, $defaults );

		update_option( 'rtcl_advanced_settings', $updated_settings );
	}

	/**
	 * Set RTCL listing types.
	 *
	 * @return void
	 */
	private function set_listing_types(): void {
		if ( empty( $this->config['rtcl_listing_types'] ) ) {
			return;
		}

		update_option( 'rtcl_listing_types', $this->config['rtcl_listing_types'] );
	}

	/**
	 * Process form builder data to replace taxonomy term IDs
	 *
	 * @return void
	 */
	private function processFormBuilderData() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'rtcl_forms';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) !== $table_name ) {
			return;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$forms = $wpdb->get_results( "SELECT * FROM {$table_name}", ARRAY_A );

		foreach ( $forms as $form ) {
			$fields_updated   = false;
			$sections_updated = false;

			if ( ! empty( $form['fields'] ) ) {
				$form_data = $this->parseJsonOrSerialized( $form['fields'] );

				if ( is_array( $form_data ) ) {
					$fields_updated = $this->processFieldConditions( $form_data );
				}
			}

			$sections_data = null;
			if ( ! empty( $form['sections'] ) ) {
				$sections_data = $this->parseJsonOrSerialized( $form['sections'] );

				if ( is_array( $sections_data ) && ! empty( $form['fields'] ) ) {
					$fields_for_reference = $this->parseJsonOrSerialized( $form['fields'] );
					if ( is_array( $fields_for_reference ) ) {
						$sections_updated = $this->processSectionConditions( $sections_data, $fields_for_reference );
					}
				}
			}

			$update_data   = [];
			$update_format = [];

			if ( $fields_updated && ! empty( $form['fields'] ) ) {
				$update_data['fields'] = $this->encodeInOriginalFormat( $form_data, $form['fields'] );
				$update_format[]       = '%s';
			}

			if ( $sections_updated && ! empty( $form['sections'] ) ) {
				$update_data['sections'] = $this->encodeInOriginalFormat( $sections_data, $form['sections'] );
				$update_format[]         = '%s';
			}

			if ( ! empty( $update_data ) ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update(
					$table_name,
					$update_data,
					[ 'id' => $form['id'] ],
					$update_format,
					[ '%d' ]
				);
			}
		}
	}

	/**
	 * Parse JSON or serialized data
	 *
	 * @param string $data The data to parse.
	 *
	 * @return array|null
	 */
	private function parseJsonOrSerialized( $data ) {
		if ( empty( $data ) ) {
			return null;
		}

		$parsed = maybe_unserialize( $data );

		if ( ! is_array( $parsed ) ) {
			$parsed = json_decode( $data, true );
		}

		return is_array( $parsed ) ? $parsed : null;
	}

	/**
	 * Process field conditions for taxonomy ID replacement
	 *
	 * @param array $form_data Form field data.
	 *
	 * @return bool
	 */
	private function processFieldConditions( &$form_data ) {
		$updated = false;

		foreach ( $form_data as &$field_config ) {
			if ( ! isset( $field_config['logics']['conditions'] ) || ! is_array( $field_config['logics']['conditions'] ) ) {
				continue;
			}

			foreach ( $field_config['logics']['conditions'] as &$condition ) {
				if ( ! isset( $condition['fieldId'] ) || ! isset( $condition['value'] ) ) {
					continue;
				}

				$field_id = $condition['fieldId'];

				if ( $this->isCategoryField( $form_data, $field_id ) ) {
					$old_id = $condition['value'];
					$new_id = $this->getNewTaxonomyId( $old_id );

					if ( $new_id && $new_id !== $old_id ) {
						$condition['value'] = $new_id;
						$updated            = true;
					}
				}
			}
		}

		return $updated;
	}

	/**
	 * Process section conditions for taxonomy ID replacement
	 *
	 * @param array $sections_data Section data.
	 * @param array $fields_data Field data for reference.
	 *
	 * @return bool
	 */
	private function processSectionConditions( &$sections_data, $fields_data ) {
		$updated = false;

		foreach ( $sections_data as &$section ) {
			if ( ! isset( $section['logics']['conditions'] ) || ! is_array( $section['logics']['conditions'] ) ) {
				continue;
			}

			foreach ( $section['logics']['conditions'] as &$condition ) {
				if ( ! isset( $condition['fieldId'] ) || ! isset( $condition['value'] ) ) {
					continue;
				}

				$field_id = $condition['fieldId'];

				if ( $this->isCategoryField( $fields_data, $field_id ) ) {
					$old_id = $condition['value'];
					$new_id = $this->getNewTaxonomyId( $old_id );

					if ( $new_id && $new_id !== $old_id ) {
						$condition['value'] = $new_id;
						$updated            = true;
					}
				}
			}
		}

		return $updated;
	}

	/**
	 * Get new taxonomy ID
	 *
	 * @param mixed $old_id The old taxonomy ID.
	 *
	 * @return int|null
	 */
	private function getNewTaxonomyId( $old_id ) {
		if ( function_exists( 'sd_edi' ) && method_exists( sd_edi(), 'getNewID' ) ) {
			return sd_edi()->getNewID( $old_id );
		}
		return null;
	}

	/**
	 * Encode data in the same format as the original
	 *
	 * @param array  $data The data to encode.
	 * @param string $original_data The original data string.
	 *
	 * @return string
	 */
	private function encodeInOriginalFormat( $data, $original_data ) {
		if ( ! $this->is_serialized_data( $original_data ) ) {
			$test_json = json_decode( $original_data, true );
			if ( json_last_error() === JSON_ERROR_NONE && is_array( $test_json ) ) {
				return wp_json_encode( $data );
			}
		}

		return maybe_serialize( $data );
	}

	/**
	 * Check if a field is a category field
	 *
	 * @param array  $form_data All form field data.
	 * @param string $field_id The field ID to check.
	 *
	 * @return bool
	 * @since 1.1.6
	 */
	private function isCategoryField( $form_data, $field_id ) {
		if ( ! isset( $form_data[ $field_id ] ) ) {
			return false;
		}

		$field = $form_data[ $field_id ];

		return isset( $field['name'] ) && 'category' === $field['name'] &&
			isset( $field['element'] ) && 'category' === $field['element'];
	}

	/**
	 * Check if a string is serialized data
	 *
	 * @param string $data The string to check.
	 *
	 * @return bool
	 */
	private function is_serialized_data( $data ) {
		if ( ! is_string( $data ) ) {
			return false;
		}

		$data = trim( $data );
		if ( 'N;' === $data ) {
			return true;
		}

		if ( strlen( $data ) < 4 ) {
			return false;
		}

		if ( ':' !== $data[1] ) {
			return false;
		}

		$lastc = substr( $data, -1 );
		if ( ';' !== $lastc && '}' !== $lastc ) {
			return false;
		}

		$token = $data[0];
		switch ( $token ) {
			case 's':
				if ( '"' !== substr( $data, -2, 1 ) ) {
					return false;
				}
				// fall through.
			case 'a':
			case 'O':
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b':
			case 'i':
			case 'd':
				return (bool) preg_match( "/^{$token}:[0-9.E+-]+;$/", $data );
		}

		return false;
	}
}
