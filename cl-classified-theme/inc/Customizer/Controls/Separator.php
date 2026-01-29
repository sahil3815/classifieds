<?php

namespace RadiusTheme\ClassifiedLite\Customizer\Controls;

use WP_Customize_Control;

if ( class_exists( 'WP_Customize_Control' ) ) {
	class Separator extends WP_Customize_Control {

		public $type = 'separator';
		/**
		 * @return void
		 */
		public function render_content() {
			?>
			<p>
			<hr/>
			</p>
			<?php
		}
	}
}