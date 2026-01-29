<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions;
use Rtcl\Services\FormBuilder\FBHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
get_header( 'listing' );

do_action( 'rtcl_before_content_wrapper' );
?>
<?php
/**
 * Hook: rtcl_before_main_content.
 *
 * @hooked rtcl_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked rtcl_breadcrumb - 20
 */
do_action( 'rtcl_before_main_content' );

global $listing;
$enableBuilder = FBHelper::isEnableSingleBuilder( $listing );

while ( have_posts() ) :
	the_post();

	if ( $enableBuilder ) {
		$form = $listing->getForm();
		Functions::get_template( 'single-layout/builder', [ 'form' => $form ] );
	} else {
		Functions::get_template_part( 'content', 'single-rtcl_listing' );
	}
endwhile;
?>

<?php
/**
 * Hook: rtcl_after_main_content.
 *
 * @hooked rtcl_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'rtcl_after_main_content' );
?>

<?php

do_action( 'rtcl_after_content_wrapper' );

get_footer( 'listing' );