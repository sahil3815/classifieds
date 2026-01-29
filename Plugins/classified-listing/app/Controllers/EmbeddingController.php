<?php

namespace Rtcl\Controllers;

use Rtcl\Helpers\Functions;
use Rtcl\Models\EmbeddingModel;
use Rtcl\Services\EmbeddingService;

class EmbeddingController {

	public function __construct() {
		if ( ! Functions::is_semantic_search_enabled() ) {
			return;
		}
		add_action( 'init', [ EmbeddingModel::class, 'create_table' ] );
		add_action( 'save_post_rtcl_listing', [ $this, 'handle_listing_save' ], 10, 2 );
		add_action( 'rtcl_listing_form_after_save_or_update', [ $this, 'handle_listing_frontend_save' ], 99 );
		// cron process to manage embeddings for existing listings
		add_action( 'rtcl_embedding_cron_run', [ $this, 'process_batch' ] );
		add_action( 'admin_notices', [ $this, 'show_notice' ] );
		add_action( 'init', [ $this, 'start_cron' ] );
	}

	/**
	 * Show admin notice during processing
	 */
	public function show_notice() {
		if ( ! get_option( 'rtcl_embedding_in_progress' ) ) {
			return;
		}

		$progress = get_option( 'rtcl_embedding_progress', [ 'processed' => 0, 'total' => 0 ] );
		$total    = max( 1, intval( $progress['total'] ) );
		$done     = intval( $progress['processed'] );
		$percent  = min( 100, round( ( $done / $total ) * 100 ) );
		?>
		<div class="notice notice-warning">
			<p><strong>Classified Listing</strong> - 🔄 AI Search data training in progress... <?php
				echo esc_html( "{$done}/{$total} processed ({$percent}%)" ) ?></p>
		</div>
		<?php
	}

	/**
	 * @return void
	 */
	public function start_cron() {
		if ( isset( $_GET['action'] ) && sanitize_text_field( $_GET['action'] ) === 'rtcl_start_embedding_process' ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Access denied.', 'classified-listing' ) );
			}

			if ( ! Functions::verify_nonce() ) {
				wp_die( __( 'Invalid request.', 'classified-listing' ) );
			}

			if ( ! wp_next_scheduled( 'rtcl_embedding_cron_run' ) ) {
				wp_schedule_single_event( time() + 2, 'rtcl_embedding_cron_run' );
			}

			update_option( 'rtcl_embedding_in_progress', true );
			update_option( 'rtcl_embedding_progress', [ 'processed' => 0, 'total' => Functions::need_listings_embedding() ] );
		}
	}

	/**
	 * @return void
	 */
	public function process_batch() {
		$listings = get_posts( [
			'post_type'      => 'rtcl_listing',
			'post_status'    => 'publish',
			'posts_per_page' => 25,
			'fields'         => 'ids',
			'meta_query'     => [
				[
					'key'     => '_has_embedding',
					'compare' => 'NOT EXISTS',
				],
			],
		] );

		if ( empty( $listings ) ) {
			delete_option( 'rtcl_embedding_in_progress' );
			delete_option( 'rtcl_embedding_progress' );
			update_option( 'rtcl_embedding_process_completed', time() );

			return; // all done
		}

		$service = new EmbeddingService();

		foreach ( $listings as $id ) {
			$title   = get_the_title( $id );
			$content = get_post_field( 'post_content', $id );
			$service->generate_and_store( $id, $title, $content );
			update_post_meta( $id, '_has_embedding', 1 );
		}

		// Update progress
		$progress              = get_option( 'rtcl_embedding_progress', [ 'processed' => 0, 'total' => 0 ] );
		$progress['processed'] += count( $listings );
		update_option( 'rtcl_embedding_progress', $progress );

		// Schedule the next batch immediately
		wp_schedule_single_event( time() + 2, 'rtcl_embedding_cron_run' );
	}

	/**
	 * @param $listing
	 *
	 * @return void
	 */
	public function handle_listing_frontend_save( $listing ): void {
		if ( ! $listing ) {
			return;
		}

		if ( $listing->get_status() !== 'publish' ) {
			return;
		}

		$title   = $listing->get_the_title();
		$content = $listing->get_the_content();

		$service = new EmbeddingService();
		$service->generate_and_store( $listing->get_id(), $title, $content );
	}

	/**
	 * Generate embeddings automatically when listing is saved
	 */
	public function handle_listing_save( $post_id, $post ) {
		if ( $post->post_status !== 'publish' ) {
			return;
		}

		$title   = $post->post_title;
		$content = $post->post_content;

		$service = new EmbeddingService();
		$service->generate_and_store( $post_id, $title, $content );
	}

	/**
	 * Handle REST API search request
	 */
	public function search_listings( $request ) {
		$params = $request->get_json_params();
		$query  = sanitize_text_field( $params['query'] ?? '' );

		$service = new EmbeddingService();
		$results = $service->search( $query );

		return rest_ensure_response( [ 'results' => $results ] );
	}
}