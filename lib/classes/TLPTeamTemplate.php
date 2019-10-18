<?php

if ( ! class_exists( 'TLPTeamTemplate' ) ):

	/**
	 *
	 */
	class TLPTeamTemplate {

		function __construct() {
			add_filter( 'template_include', array( $this, 'template_loader' ) );
		}

		public static function template_loader( $template ) {
			$find = array();
			$file = null;
			global $TLPteam;
			if ( is_single() && get_post_type() == $TLPteam->post_type ) {

				$file   = 'single-team.php';
				$find[] = $file;
				$find[] = $TLPteam->templatesPath . $file;

			}

			if ( $file ) {

				$template = locate_template( array_unique( $find ) );
				if ( ! $template ) {
					$template = $TLPteam->templatesPath . $file;
				}
			}

			return $template;
		}

	}

endif;
