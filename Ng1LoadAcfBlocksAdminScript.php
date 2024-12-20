class AdminScriptLoader {
    private $plugin_path;
    private $plugin_url;
    private $script_path;
    private $script_url;

    public function __construct() {
        $this->plugin_path = plugin_dir_path(dirname(__FILE__));
        $this->plugin_url = plugin_dir_url(dirname(__FILE__));
        $this->script_path = $this->plugin_path . 'assets/js/admin.js';
        $this->script_url = $this->plugin_url . 'assets/js/admin.js';

        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_script']);
    }

    public function enqueue_admin_script() {
        if (!is_admin()) {
            return;
        }

        if (file_exists($this->script_path)) {
            wp_enqueue_script(
                'my-admin-script',
                $this->script_url,
                ['jquery'],
                filemtime($this->script_path),
                true
            );
        }
    }
}

// Initialisation
new AdminScriptLoader();
