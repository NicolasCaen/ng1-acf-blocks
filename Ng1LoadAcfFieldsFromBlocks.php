<?php
/**
 * Classe pour charger les champs ACF depuis les dossiers des blocs
 * 
 * @package NG1_ACF_Blocks
 * @since 1.0.0
 */

class Ng1LoadAcfFieldsFromBlocks {
    private static $instance = null;

    private function debug_log($message) {
        if (defined('DEBUG_LOADING_ACF_BLOCKS') && DEBUG_LOADING_ACF_BLOCKS) {
            error_log('Debug ACF Blocks - ' . $message);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('acf/include_fields', [$this, 'load_acf_fields_from_blocks']);
        add_action('init', [$this, 'load_acf_fields_from_blocks'], 5);
    }

    /**
     * Charge les champs ACF depuis les dossiers des blocs
     */
    public function load_acf_fields_from_blocks() {
        // Chemins des dossiers de blocs
        $theme_blocks_path = get_stylesheet_directory() . '/acf-blocks/';
        $mu_blocks_path = WPMU_PLUGIN_DIR . '/acf-blocks/';

        $this->debug_log('Theme path: ' . $theme_blocks_path);
        $this->debug_log('MU path: ' . $mu_blocks_path);

        // Charge les champs depuis le thème
        if (is_dir($theme_blocks_path)) {
            $this->load_fields_from_directory($theme_blocks_path);
        }

        // Charge les champs depuis mu-plugins
        if (is_dir($mu_blocks_path)) {
            $this->load_fields_from_directory($mu_blocks_path);
        }
    }


    private function load_fields_from_directory($directory) {
        $block_directories = glob($directory . '*', GLOB_ONLYDIR);
        $this->debug_log('Scanning directory: ' . $directory);
        $this->debug_log('Found directories: ' . print_r($block_directories, true));
    
        foreach ($block_directories as $block_dir) {
            $this->debug_log('Processing directory: ' . $block_dir);
            
            $acf_json_file = $block_dir . '/acf/fields.json';
            $block_json_file = $block_dir . '/block.json';
    
            $this->debug_log('Looking for:');
            $this->debug_log('- ACF JSON file: ' . $acf_json_file);
            $this->debug_log('- Block JSON file: ' . $block_json_file);
            $this->debug_log('- ACF JSON exists: ' . (file_exists($acf_json_file) ? 'YES' : 'NO'));
            $this->debug_log('- Block JSON exists: ' . (file_exists($block_json_file) ? 'YES' : 'NO'));
    
            if (file_exists($acf_json_file) && file_exists($block_json_file)) {
                $json_content = file_get_contents($acf_json_file);
                $block_content = file_get_contents($block_json_file);
                
                $this->debug_log('Content of fields.json: ' . $json_content);
                
                $field_group = json_decode($json_content, true);
                $block_info = json_decode($block_content, true);
    
                if ($field_group && $block_info) {
                    $this->debug_log('Successfully parsed JSON files');
                    $this->debug_log('Block name: ' . $block_info['name']);
                    
                    // On prend le premier groupe de champs si c'est un tableau
                    if (is_array($field_group) && isset($field_group[0])) {
                        $field_group = $field_group[0];
                    }

                    // Mise à jour de la location pour le bloc
                    $field_group['location'] = [
                        [
                            [
                                'param' => 'block',
                                'operator' => '==',
                                'value' => $block_info['name'],
                            ]
                        ]
                    ];
    
                    $this->debug_log('Registering field group with key: ' . $field_group['key']);   
                    // Enregistre le groupe de champs
                    acf_add_local_field_group($field_group);

                    $this->debug_log('Field group registered successfully');
                } else {
                    $this->debug_log('Failed to parse JSON files');
                    if (!$field_group) $this->debug_log('Invalid fields.json structure');
                    if (!$block_info) $this->debug_log('Invalid block.json structure');
                }
            } else {
                $this->debug_log('Required files not found');
            }
        }
    }
}

// Initialisation unique
Ng1LoadAcfFieldsFromBlocks::getInstance(); 