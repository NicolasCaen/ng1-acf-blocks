<?php
/**
 * Plugin Name: NG1 ACF Blocks Manager
 * Plugin URI: https://github.com/votre-repo/ng1-acf-blocks
 * Description: Gestionnaire de blocs ACF qui automatise le chargement et la synchronisation des blocs Gutenberg avec ACF Pro. Inclut un système de synchronisation des champs ACF et un chargement automatique des assets.
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Nicolas GEHIN
 * Author URI: https://votre-site.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ng1-acf-blocks
 * Domain Path: /languages
 * 
 * @package NG1_ACF_Blocks
 * @author Nicolas GEHIN
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Définition de la constante de debug (lié à WP_DEBUG par défaut)
if (!defined('DEBUG_LOADING_ACF_BLOCKS')) {
    define('DEBUG_LOADING_ACF_BLOCKS', WP_DEBUG);
}


// Vérification de la présence d'ACF PRO
add_action('admin_init', function() {
    if (!class_exists('ACF')) {
        add_action('admin_notices', function() {
            echo '<div class="error"><p>' . 
                 __('NG1 ACF Blocks Manager requires Advanced Custom Fields PRO to be installed and activated.', 'ng1-acf-blocks') . 
                 '</p></div>';
        });
        return;
    }
});


include_once 'Ng1LoadAcfBlocks.php';
include_once 'Ng1LoadAcfFieldsFromBlocks.php';
include_once 'Ng1SaveAcfFieldsToBlocks.php';
include_once 'Ng1LoadBlockFunctions.php';