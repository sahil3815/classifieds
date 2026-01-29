<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var Listing $listing
 */

use Rtcl\Models\Listing;
use RtclStore\Helpers\Functions as StoreFunctions;

$store = false;
if ( class_exists( 'RtclStore' ) ) {
	$store = StoreFunctions::get_user_store( $listing->get_owner_id() );
}
?>

<?php do_action( 'cl_classified_before_user_information' ); ?>

<div class="rtcl-listing-user-info">
	<div class="rtcl-listing-side-title">
		<h3><?php esc_html_e( 'Information', 'cl-classified' ); ?></h3>
	</div>
	<div class="rtcl-list-group">
		<?php do_action( 'rtcl_listing_seller_information', $listing ); ?>
	</div>
</div>