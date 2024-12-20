<?php

class Ng1LoadBlockAdminScripts {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'loadBlockAdminScripts']);
    }
    
    public function loadBlockAdminScripts() {
        if (!is_admin()) {
            return;
        }

        // Chemins des dossiers de blocs
        $theme_blocks_path = get_stylesheet_directory() . '/acf-blocks/';
        $mu_blocks_path = WPMU_PLUGIN_DIR . '/acf-blocks/';
        
            if (defined('DEBUG_LOADING_ACF_BLOCKS') && DEBUG_LOADING_ACF_BLOCKS) {
            error_log('Recherche des scripts admin dans : ' . $theme_blocks_path);
            error_log('Recherche des scripts admin dans : ' . $mu_blocks_path);
        }

        // Charge les scripts depuis le thème
        if (is_dir($theme_blocks_path)) {
            $this->load_scripts_from_directory($theme_blocks_path);
        }

        // Charge les scripts depuis mu-plugins
        if (is_dir($mu_blocks_path)) {
            $this->load_scripts_from_directory($mu_blocks_path);
        }
    }

    private function load_scripts_from_directory($directory) {
        $block_directories = glob($directory . '*', GLOB_ONLYDIR);
        
        foreach ($block_directories as $block_path) {
            // Vérifie les deux types de fichiers possibles
            $admin_script = $block_path . '/assets/js/admin.js';
            $admin_edit_script = $block_path . '/assets/js/admin-edit.js';
            
            // Pour admin.js (scripts admin classiques)
            if (file_exists($admin_script)) {
                $this->enqueue_admin_script($block_path, $admin_script, 'admin', ['jquery']);
            }
            
            // Pour admin-edit.js (scripts Gutenberg)
            if (file_exists($admin_edit_script)) {
                $this->enqueue_admin_script($block_path, $admin_edit_script, 'admin-edit', ['jquery', 'wp-edit-post']);
            }
        }
    }

    private function enqueue_admin_script($block_path, $script_path, $script_type, $dependencies) {
        $block_name = basename($block_path);
        
            if (defined('DEBUG_LOADING_ACF_BLOCKS') && DEBUG_LOADING_ACF_BLOCKS) {
            error_log('Loading ' . $script_type . ' script: ' . $script_path);
        }
        
        // Convertit le chemin système en URL
        $script_url = str_replace(
            [get_stylesheet_directory(), WPMU_PLUGIN_DIR],
            [get_stylesheet_directory_uri(), WPMU_PLUGIN_URL],
            $block_path
        ) . '/assets/js/' . $script_type . '.js';

        wp_enqueue_script(
            'acf-block-' . $script_type . '-' . $block_name,
            $script_url,
            $dependencies,
            filemtime($script_path),
            true
        );
    }
}

// Initialisation
Ng1LoadBlockAdminScripts::getInstance(); 