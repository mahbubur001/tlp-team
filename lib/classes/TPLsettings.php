<?php

/**
 *
 */
class TPLsettings {

	function __construct() {
		add_action( 'init', array( $this, 'tlp_pluginInit' ) );
		add_action( 'wp_ajax_tlpTeamSettings', array( $this, 'tlpTeamSettings' ) );
		add_action( 'admin_menu', array( $this, 'tlp_menu_register' ) );
		add_filter( 'plugin_action_links_' . TLP_TEAM_PLUGIN_ACTIVE_FILE_NAME, array( $this, 'tlp_team_marketing' ) );
		add_action( 'admin_notices', array( $this, 'deprecated_admin_notice' ) );
	}

	function tlp_team_marketing( $links ) {
		$links[] = '<a target="_blank" href="' . esc_url( 'http://demo.radiustheme.com/wordpress/plugins/tlp-team/' ) . '">Demo</a>';
		$links[] = '<a target="_blank" href="' . esc_url( 'https://radiustheme.com/how-to-setup-configure-tlp-team-free-version-for-wordpress/' ) . '">Documentation</a>';
		$links[] = '<a target="_blank" style="color: #39b54a;font-weight: 700;" href="' . esc_url( 'https://www.radiustheme.com/downloads/tlp-team-pro-for-wordpress/' ) . '">Get Pro</a>';

		return $links;
	}

	function deprecated_admin_notice() {
		$screen = get_current_screen();
		if ( isset( $screen->post_type ) && ( $screen->post_type == TLPTeam()->post_type || $screen->post_type == TLPTeam()->getScPostType() ) ) {
			$class   = 'notice notice-error is-dismissible';
			$message = sprintf( __( 'Our old ShortCode generator is now deprecated, This will be removed end of the year 2019. You should use our latest <a href="%s">ShortCode Generator.</a>', 'tlp-team' ), admin_url( 'edit.php?post_type=team-sc' ) );
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		}
	}

	/**
	 *  Ajax response for settings update
	 */
	function tlpTeamSettings() {
		global $TLPteam;

		$error = true;
		if ( $TLPteam->verifyNonce() ) {
			unset( $_REQUEST['action'] );
			unset( $_REQUEST['tlp_nonce'] );
			unset( $_REQUEST['_wp_http_referer'] );
			update_option( $TLPteam->options['settings'], $_REQUEST );
			flush_rewrite_rules();
			$response = array(
				'error' => $error,
				'msg'   => __( 'Settings successfully updated', TLP_TEAM_SLUG )
			);
		} else {
			$response = array(
				'error' => true,
				'msg'   => __( 'Security Error!!', TLP_TEAM_SLUG )
			);
		}
		wp_send_json( $response );
	}

	/**
	 *  Text domain + image size register
	 */
	function tlp_pluginInit() {
		$this->load_plugin_textdomain();
		global $TLPteam;
		$settings = get_option( $TLPteam->options['settings'] );
		$width    = ! empty( $settings['feature_img']['width'] ) ? absint( $settings['feature_img']['width'] ) : 400;
		$height   = ! empty( $settings['feature_img']['height'] ) ? absint( $settings['feature_img']['height'] ) : 400;
		add_image_size( $TLPteam->options['feature_img_size'], $width, $height, true );
	}


	/**
	 *  TLP menu addition
	 */
	function tlp_menu_register() {
		$sc   = add_submenu_page( 'edit.php?post_type=team', __( 'Shortcode generator', TLP_TEAM_SLUG ), __( 'ShortCode (Deprecated)', TLP_TEAM_SLUG ), 'administrator', 'tlp_team_sc', array(
			$this,
			'tlp_team_sc'
		) );
		$page = add_submenu_page( 'edit.php?post_type=team', __( 'TLP Team Settings', TLP_TEAM_SLUG ), __( 'Settings', TLP_TEAM_SLUG ), 'administrator', 'tlp_team_settings', array(
			$this,
			'tlp_team_settings'
		) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'tlp_style' ) );
		add_action( 'admin_print_scripts-' . $page, array( $this, 'tlp_script' ) );
		add_action( 'admin_print_styles-' . $sc, array( $this, 'tlp_style' ) );
		add_action( 'admin_print_scripts-' . $sc, array( $this, 'tlp_script' ) );

	}

	/**
	 *  TLP Style addition
	 */
	function tlp_style() {
		global $TLPteam;
		wp_enqueue_style( 'tpl_css_settings', $TLPteam->assetsUrl . 'css/settings.css', '', TLP_TEAM_VERSION );
	}

	/**
	 *  Tlp script addition
	 */
	function tlp_script() {
		global $TLPteam;
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'tpl_js_settings', $TLPteam->assetsUrl . 'js/settings.js', array(
			'jquery',
			'wp-color-picker'
		), TLP_TEAM_VERSION, true );
		$nonce = wp_create_nonce( $TLPteam->nonceText() );
		wp_localize_script( 'tpl_js_settings', 'tpl_var', array( 'tlp_nonce' => $nonce ) );
	}

	function tlp_team_settings() {
		global $TLPteam;
		$TLPteam->render_view( 'settings' );
	}

	function tlp_team_sc() {
		global $TLPteam;
		$TLPteam->render_view( 'sc' );
	}

	/**
	 * Load the plugin text domain for translation.
	 * @since 0.1.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( TLP_TEAM_SLUG, false, TLP_TEAM_LANGUAGE_PATH );
	}

}
