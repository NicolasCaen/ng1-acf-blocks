<?php

class Ng1LoadBlockFunctions {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('acf/init', [$this, 'loadBlockFunctions'], 20);
    }
    
    public function loadBlockFunctions() {
        // Chemins des dossiers de blocs
        $theme_blocks_path = get_stylesheet_directory() . '/acf-blocks/';
        $mu_blocks_path = WPMU_PLUGIN_DIR . '/acf-blocks/';
        
        if (DEBUG_LOADING_ACF_BLOCKS) {
            error_log('Recherche des blocs dans : ' . $theme_blocks_path);
            error_log('Recherche des blocs dans : ' . $mu_blocks_path);
        }

        // Charge les fonctions depuis le thème
        if (is_dir($theme_blocks_path)) {
            $this->load_functions_from_directory($theme_blocks_path);
        }

        // Charge les fonctions depuis mu-plugins
        if (is_dir($mu_blocks_path)) {
            $this->load_functions_from_directory($mu_blocks_path);
        }
    }

    private function load_functions_from_directory($directory) {
        $block_directories = glob($directory . '*', GLOB_ONLYDIR);
        
        foreach ($block_directories as $block_path) {
            $functions_file = $block_path . '/functions.php';
            
            if (file_exists($functions_file)) {
                if (DEBUG_LOADING_ACF_BLOCKS) {
                    error_log('Loading block functions: ' . $functions_file);
                }
                include_once $functions_file;
            }
        }
    }
}

// Initialisation
Ng1LoadBlockFunctions::getInstance(); 