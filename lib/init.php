<?php

class TLPteam
{
    public $options;
    private $sc_post_type;
    public $post_type;
    public $assetsUrl;

    protected static $_instance;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    function __construct() {

        $this->options = array(
            'settings'          => 'tlp_team_settings',
            'version'           => TLP_TEAM_VERSION,
            'feature_img_size'  => 'team-thumb',
            'installed_version' => 'tlp_team_installed_version'
        );

        $this->post_type = 'team';
        $this->sc_post_type = 'team-sc';
        $settings = get_option($this->options['settings']);
        $this->post_type_slug = isset($settings['slug']) ? ($settings['slug'] ? sanitize_title_with_dashes($settings['slug']) : 'team') : 'team';
        $this->incPath = dirname(__FILE__);
        $this->functionsPath = $this->incPath . '/functions/';
        $this->classesPath = $this->incPath . '/classes/';
        $this->modelsPath = $this->incPath . '/models/';
        $this->widgetsPath = $this->incPath . '/widgets/';
        $this->viewsPath = $this->incPath . '/views/';
	    $this->templatesPath  = $this->incPath . '/templates/';

        $this->assetsUrl = TLP_TEAM_PLUGIN_URL . '/assets/';
        $this->TLPLoadModel($this->modelsPath);
        $this->TLPLoadClass($this->classesPath);

        $this->defaultSettings = array(
            'primary_color'    => '#0367bf',
            'feature_img'      => array(
                'width'  => 400,
                'height' => 400
            ),
            'slug'             => 'team',
            'link_detail_page' => 'yes',
            'custom_css'       => null
        );


        register_activation_hook(TLP_TEAM_PLUGIN_ACTIVE_FILE_NAME, array($this, 'activate'));
        register_deactivation_hook(TLP_TEAM_PLUGIN_ACTIVE_FILE_NAME, array($this, 'deactivate'));

    }

    public function activate() {
        flush_rewrite_rules();
        $this->insertDefaultData();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    function TLPLoadModel($dir) {
        if (!file_exists($dir)) {
            return;
        }
        foreach (scandir($dir) as $item) {
            if (preg_match("/.php$/i", $item)) {
                require_once($dir . $item);
            }
        }
    }

    function TLPLoadClass($dir) {
        if (!file_exists($dir)) {
            return;
        }

        $classes = array();

        foreach (scandir($dir) as $item) {
            if (preg_match("/.php$/i", $item)) {
                require_once($dir . $item);
                $className = str_replace(".php", "", $item);
                $classes[] = new $className;
            }
        }

        if ($classes) {
            foreach ($classes as $class) {
                $this->objects[] = $class;
            }
        }
    }

    function loadWidget($dir) {
        if (!file_exists($dir)) {
            return;
        }
        foreach (scandir($dir) as $item) {
            if (preg_match("/.php$/i", $item)) {
                require_once($dir . $item);
                $class = str_replace(".php", "", $item);

                if (method_exists($class, 'register_widget')) {
                    $caller = new $class;
                    $caller->register_widget();
                } else {
                    register_widget($class);
                }
            }
        }
    }


	/**
	 * @param $viewName
	 * @param array $args
	 * @param bool $return
	 * @return string|void
	 */
	function render_view($viewName, $args = array(), $return = false ) {
		$path     = str_replace( ".", "/", $viewName );
		$viewPath = $this->viewsPath . $path . '.php';
		if ( ! file_exists( $viewPath ) ) {
			return;
		}
		if ( $args ) {
			extract( $args );
		}
		if ( $return ) {
			ob_start();
			include $viewPath;

			return ob_get_clean();
		}
		include $viewPath;
	}


	/**
	 * @param $viewName
	 * @param array $args
	 * @param bool $return
	 *
	 * @return string|void
	 */
	function render( $viewName, $args = array(), $return = false ) {

		$path = str_replace( ".", "/", $viewName );
		if ( $args ) {
			extract( $args );
		}
		$template = array(
			"tlp-team/{$path}.php"
		);

		if ( ! $template_file = locate_template( $template ) ) {
			$template_file = $this->templatesPath . $viewName . '.php';
		}
		if ( ! file_exists( $template_file ) ) {
			return;
		}
		if ( $return ) {
			ob_start();
			include $template_file;

			return ob_get_clean();
		} else {
			include $template_file;
		}
	}


    /**
     * Dynamicaly call any  method from models class
     * by pluginFramework instance
     */
    function __call($name, $args) {
        if (!is_array($this->objects)) {
            return;
        }
        foreach ($this->objects as $object) {
            if (method_exists($object, $name)) {
                $count = count($args);
                if ($count == 0) {
                    return $object->$name();
                } elseif ($count == 1) {
                    return $object->$name($args[0]);
                } elseif ($count == 2) {
                    return $object->$name($args[0], $args[1]);
                } elseif ($count == 3) {
                    return $object->$name($args[0], $args[1], $args[2]);
                } elseif ($count == 4) {
                    return $object->$name($args[0], $args[1], $args[2], $args[3]);
                } elseif ($count == 5) {
                    return $object->$name($args[0], $args[1], $args[2], $args[3], $args[4]);
                } elseif ($count == 6) {
                    return $object->$name($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
                }
            }
        }
    }

    private function insertDefaultData() {
        global $TLPteam;
        update_option($TLPteam->options['installed_version'], $TLPteam->options['version']);
        if (!get_option($TLPteam->options['settings'])) {
            update_option($TLPteam->options['settings'], $TLPteam->defaultSettings);
        }
    }

    /**
     * @return string
     */
    public function getScPostType() {
        return $this->sc_post_type;
    }

}

/**
 * @return TLPteam
 */
function TLPTeam() {
    global $TLPteam;
    $TLPteam = TLPteam::instance();
    return $TLPteam;
}

TLPTeam();
