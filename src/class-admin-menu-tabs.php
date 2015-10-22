<?php

namespace Frozzare\Admin_Menu_Tabs;

class Admin_Menu_Tabs {

    /**
     * The user meta key that Admin Menu Tabs use.
     *
     * @var string
     */
    private $user_meta_key = '_active_admin_menu_tab';

    /**
     * The instance of the loader class.
     *
     * @var \Frozzare\Admin_Menu_Tabs\Admin_Menu_Tabs
     */
    private static $instance;

    /**
     * The loader instance.
     *
     * @return \Frozzare\Admin_Menu_Tabs\Admin_Menu_Tabs
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * The constructor.
     */
    private function __construct() {
        $this->setup_actions();
    }

    /**
     * Render admin menu tabs.
     */
    public function adminmenu() {
        $user    = wp_get_current_user();
        $user_id = $user->ID;
        $tab     = get_user_meta( $user_id, $this->user_meta_key, true );
        $tab     = empty( $tab ) ? 'edit' : $tab;
        $update  = wp_get_update_data();
        $admin   = current_user_can( 'administrator' );
        $tab     = $admin ? $tab : 'edit';

        /**
         * Change which separator that will be the break
         * between edit and admin mode.
         *
         * @var int
         */
        $separator = (int) apply_filters( 'admin_menu_tabs_separator', 1 );
        ?>
        <ul id="adminmenutabs" data-separator="<?php echo $separator; ?>" class="admin-menu-tabs-<?php echo $admin ? 'show' : 'hide'; ?>">
            <li>
                <a href="#" class="admin-menu-tab admin-menu-tab-edit <?php echo $tab === 'edit' ? 'active' : ''; ?>"><?php _e( 'Edit', 'admin-menu-tabs' ); ?></a>
            </li>
            <li>
                <a href="#" class="admin-menu-tab admin-menu-tab-admin <?php echo $tab === 'admin' ? 'active' : ''; ?>"><?php _e( 'Admin', 'admin-menu-tabs' ); ?>
                    <?php if ( $update['counts']['total'] && current_user_can( 'update_core' ) ): ?>
                    <span class="update-plugins count-<?php echo $update['counts']['total']; ?>">
                        <span class="plugin-count"><?php echo $update['counts']['total']; ?></span>
                    </span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
        <?php
    }

    /**
     * Remove update-core.php from dashboard menu.
     */
    public function admin_init() {
        remove_submenu_page( 'index.php', 'update-core.php' );
    }

    /**
     * Enqueue script into admin footer.
     */
    public function admin_enqueue_scripts() {
        if ( ! current_user_can( 'administrator' ) ) {
            return;
        }

        wp_enqueue_script(
            'admin-menu-tabs-main',
            $this->get_plugin_url() . 'js/main.min.js',
            [],
            '',
            true
        );

        ?>
        <script type="text/javascript">
            window.adminMenuTabs = {
                url: '<?php echo admin_url(); ?>'
            };
        </script>
        <?php
    }

    /**
     * Add style to admin head.
     */
    public function admin_head() {
        if ( ! current_user_can( 'administrator' ) ) {
            return;
        }

        wp_enqueue_style(
            'admin-menu-tabs-main',
            $this->get_plugin_url() . '/css/style.min.css',
            false,
            null
        );

        ?>
        <style type="text/css">
            @media only screen and (min-width: 782px) {
                #adminmenu {
                    margin-top: 30px;
                }
            }

            @media only screen and (max-width: 782px) {
                .auto-fold #adminmenu {
                    margin-top: 40px;
                }
            }
        </style>
        <?php
    }

    /**
     * Move update core menu after options menu.
     */
    public function admin_menu() {
        global $menu;

        $update   = wp_get_update_data();
        $name     = __( 'Updates', 'admin-menu-tabs' );
        $position = 81;

        if ( $update['counts']['total'] ) {
            $name .= sprintf( ' <span class="update-plugins count-%s"><span class="plugin-count">%s</span></span>', $update['counts']['total'], $update['counts']['total'] );
        }

        while ( isset( $menu[ $position ] ) ) {
            $position++;
        }

        add_menu_page( $name, $name, 'update_core', 'update-core.php', '', '', $position );
    }

    /**
     * Change admin menu tab.
     */
    public function change_admin_menu_tab() {
        $tab     = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 'edit';
        $user    = wp_get_current_user();
        $user_id = $user->ID;
        update_user_meta( $user_id, $this->user_meta_key, $tab );
        wp_send_json_success();
    }

    /**
     * Get plugin url.
     *
     * @return string
     */
    private function get_plugin_url() {
        $plugin_url = plugin_dir_url( __FILE__ );

        if ( is_ssl() ) {
            $plugin_url = str_replace( 'http://', 'https://', $plugin_url );
        }

        return str_replace( 'src/', 'dist/', $plugin_url );
    }

    /**
     * Setup actions.
     */
    private function setup_actions() {
        add_action( 'adminmenu', [$this, 'adminmenu'] );
        add_action( 'admin_init', [$this, 'admin_init'] );
        add_action( 'admin_enqueue_scripts', [$this, 'admin_enqueue_scripts'] );
        add_action( 'admin_head', [$this, 'admin_head'] );
        add_action( 'admin_menu', [$this, 'admin_menu'] );
        add_action( 'wp_ajax_change_admin_menu_tab', [$this, 'change_admin_menu_tab'] );
    }
}

/**
 * Load Admin Menu Tabs plugin.
 */
add_filter( 'plugins_loaded', function () {
    return Admin_Menu_Tabs::instance();
} );
