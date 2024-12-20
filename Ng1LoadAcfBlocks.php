<?php  
class Ng1LoadAcfBLocks {

private static $instance = null;

/**
 * Récupère l'instance unique de la classe en utilisant le modèle Singleton.
 *
 * @return Ng1LoadAcfBLocks
 */
public static function get_instance() {
    if (null === self::$instance) {
        self::$instance = new self();
    }
    return self::$instance;
}

/**
 * Constructeur privé pour s'assurer qu'une seule instance est créée.
 */
private function __construct() {
    // Actions et filtres WordPress
    add_action( 'init', array( $this, 'load_blocks' ), 5 );
    add_filter( 'acf/settings/load_json', array( $this, 'load_acf_field_group' ) );
    add_filter( 'block_categories_all', array( $this, 'block_categories' ), 10, 2 );
    add_action('wp_enqueue_scripts', array($this, 'register_block_script_from_folder'));
}

/**
 * Charge les blocs.
 */
public function load_blocks() {
    // Récupère le thème en cours
    $theme  = wp_get_theme();
    // Récupère la liste des blocs
    $blocks = $this->get_blocks();

    foreach($blocks as $block) {
        // Vérifie d'abord dans le thème
        $theme_block_json = get_stylesheet_directory() . '/acf-blocks/' . $block . '/block.json';
        // Vérifie ensuite dans mu-plugins
        $mu_block_json = WPMU_PLUGIN_DIR . '/acf-blocks/' . $block . '/block.json';

        // Enregistre le bloc s'il existe dans l'un des deux emplacements
        if (file_exists($theme_block_json)) {
            register_block_type($theme_block_json);
        } 
        if (file_exists($mu_block_json)) {
            register_block_type($mu_block_json);
        }
    }
}

/**
 * Enregistre les scripts JavaScript des sous-répertoires des blocs.
 */
public function register_block_script_from_folder() {
    // Vérifie si nous sommes sur une page qui contient des blocs
    if (!has_blocks()) {
        return;
    }

    global $post;
    $blocks = parse_blocks($post->post_content);
    $registered_blocks = [];

    // Chemins vers les répertoires des blocs
    $theme_block_directory = get_stylesheet_directory() . '/acf-blocks/';
    $mu_block_directory = WPMU_PLUGIN_DIR . '/acf-blocks/';

    foreach ($blocks as $block) {
        // Vérifie si 'blockName' est défini et non null
        if (!isset($block['blockName']) || $block['blockName'] === null) {
            continue;
        }

        // Extrait le nom du bloc (ex: 'ng1/sample' -> 'sample')
        $block_name = str_replace('ng1/', '', $block['blockName']);
        
        if (in_array($block_name, $registered_blocks)) {
            continue;
        }

        // Vérifie d'abord dans le thème
        $theme_js_file = $theme_block_directory . $block_name . '/assets/js/function.js';
        // Vérifie ensuite dans mu-plugins
        $mu_js_file = $mu_block_directory . $block_name . '/assets/js/function.js';

        if (file_exists($theme_js_file)) {
            $handle = 'ng1-' . sanitize_title($block_name);
            // Correction de l'URL pour le thème
            $src = get_stylesheet_directory_uri() . '/acf-blocks/' . $block_name . '/assets/js/function.js';
            wp_enqueue_script($handle, $src, array('jquery'), filemtime($theme_js_file), true);
            $registered_blocks[] = $block_name;
        } 
        elseif (file_exists($mu_js_file)) {
            $handle = 'ng1-' . sanitize_title($block_name);
            // Correction de l'URL pour mu-plugins
            $src = content_url('mu-plugins/acf-blocks/' . $block_name . '/assets/js/function.js');
            wp_enqueue_script($handle, $src, array('jquery'), filemtime($mu_js_file), true);
            $registered_blocks[] = $block_name;
        }
    }
}

/**
 * Charge les groupes de champs ACF pour les blocs.
 */
public function load_acf_field_group( $paths ) {
    $blocks = $this->get_blocks();
    foreach( $blocks as $block ) {
        $paths[] = get_stylesheet_directory() . '/acf-blocks/' . $block.'/acf';
    }
    return $paths;
}

/**
 * Récupère la liste des blocs.
 */
public function get_blocks() {
    $theme   = wp_get_theme();
    $blocks  = get_option('cwp_blocks');
    $version = get_option('cwp_blocks_version');

    // Vérifie si la liste des blocs est vide ou si la version du thème a changé
    if (empty($blocks) || version_compare($theme->get('Version'), $version) || (function_exists('wp_get_environment_type') && 'production' !== wp_get_environment_type())) {
        // Chemin vers le dossier des blocs dans le thème
        $blocks_folder_path = get_stylesheet_directory() . '/acf-blocks/';
        
        // Chemin vers le dossier des blocs dans mu-plugins
        $mu_blocks_folder_path = WPMU_PLUGIN_DIR . '/acf-blocks/';

        // Crée le dossier acf-blocks s'il n'existe pas dans le thème
        wp_mkdir_p($blocks_folder_path);

        // Récupère la liste des sous-répertoires dans le répertoire des blocs du thème
        $theme_blocks = is_dir($blocks_folder_path) ? scandir($blocks_folder_path) : [];
        
        // Récupère la liste des sous-répertoires dans le répertoire des blocs de mu-plugins
        $mu_blocks = is_dir($mu_blocks_folder_path) ? scandir($mu_blocks_folder_path) : [];

        // Combine les deux listes de blocs
        $blocks = array_merge($theme_blocks, $mu_blocks);

        // Supprime les éléments indésirables
        $blocks = array_values(array_diff($blocks, array('..', '.', '.DS_Store', '_base-block')));

        // Met à jour les options
        update_option('cwp_blocks', $blocks);
        update_option('cwp_blocks_version', $theme->get('Version'));
    }
    return $blocks;
}
/**
 * Catégories de blocs.
 */
public function block_categories($block_categories, $editor_context) {
    if (!empty($editor_context->post)) {
        array_unshift(
            $block_categories,
            array(
                'slug'  => 'pixelea',
                'title' => __('pixelea', 'pixelea'),
                'icon'  => 'games',
            )
        );
    }
    return $block_categories;
}
}

// Instancie la classe en utilisant le modèle Singleton
Ng1LoadAcfBLocks::get_instance();
