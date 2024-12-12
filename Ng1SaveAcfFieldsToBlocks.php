<?php
class Ng1SaveAcfFieldsToBlocks {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('acf/update_field_group', array($this, 'save_field_group_to_block'), 10, 1);
    }

    public function save_field_group_to_block($field_group) {
        // Vérifie si le groupe de champs est lié à un bloc
        if (!$this->is_block_field_group($field_group)) {
            return $field_group;
        }

        // Récupère le nom du bloc depuis la location
        $block_name = $this->get_block_name($field_group);
        if (!$block_name) {
            return $field_group;
        }

        // Chemins possibles pour le bloc
        $theme_block_path = get_stylesheet_directory() . '/acf-blocks/' . $block_name;
        $mu_block_path = WPMU_PLUGIN_DIR . '/acf-blocks/' . $block_name;

        // Cherche le bloc dans les dossiers
        $block_path = null;
        if (file_exists($theme_block_path . '/block.json')) {
            $block_path = $theme_block_path;
        } elseif (file_exists($mu_block_path . '/block.json')) {
            $block_path = $mu_block_path;
        }

        // Si le bloc existe, sauvegarde les champs
        if ($block_path) {
            $this->save_fields_json($field_group, $block_path);
        }

        return $field_group;
    }

    private function is_block_field_group($field_group) {
        if (!isset($field_group['location']) || !is_array($field_group['location'])) {
            return false;
        }

        foreach ($field_group['location'] as $location_group) {
            foreach ($location_group as $location_rule) {
                if (isset($location_rule['param']) && 
                    $location_rule['param'] === 'block' && 
                    $location_rule['operator'] === '==' && 
                    strpos($location_rule['value'], 'ng1/') === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    private function get_block_name($field_group) {
        foreach ($field_group['location'] as $location_group) {
            foreach ($location_group as $location_rule) {
                if ($location_rule['param'] === 'block' && 
                    $location_rule['operator'] === '==' && 
                    strpos($location_rule['value'], 'ng1/') === 0) {
                    return str_replace('ng1/', '', $location_rule['value']);
                }
            }
        }
        return null;
    }

    private function save_fields_json($field_group, $block_path) {
        // Crée le dossier acf s'il n'existe pas
        $acf_dir = $block_path . '/acf';
        if (!file_exists($acf_dir)) {
            wp_mkdir_p($acf_dir);
        }

        // Prépare le fichier JSON
        $json_file = $acf_dir . '/fields.json';
        $json_data = wp_json_encode(array($field_group), JSON_PRETTY_PRINT);

        // Sauvegarde le fichier
        if ($json_data) {
            file_put_contents($json_file, $json_data);
        }
    }
}

// Initialise la classe
Ng1SaveAcfFieldsToBlocks::get_instance(); 