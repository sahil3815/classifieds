<?php
/**
 * Content wrappers
 *
 * @package     ClassifiedListing/Templates
 * @version     1.4.0
 */

use Rtcl\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$template               = Functions::get_theme_slug_for_templates();
$sidebar_position       = Functions::get_option_item( 'rtcl_single_listing_settings', 'detail_page_sidebar_position', 'right' );
$content_position_class = '';

if ( Functions::is_listings() || ( 'left' === $sidebar_position && Functions::is_listing() ) || Functions::is_listing_taxonomy() ) {
	$content_position_class = 'rtcl-order-2';
}
?>
<div id="primary" class="main-content <?php echo esc_attr( $content_position_class ); ?>">
