<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions as RtclFunctions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
get_header( 'store' );

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
?>

<?php while ( have_posts() ) : ?>
	<?php the_post(); ?>

	<?php RtclFunctions::get_template_part( 'content', 'single-store' ); ?>

<?php endwhile; // end of the loop. ?>

<?php
do_action( 'rtcl_after_main_content' );

do_action( 'rtcl_store_sidebar' );

do_action( 'rtcl_after_content_wrapper' );
?>

<?php
get_footer( 'store' );
