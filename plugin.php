<?php

/**
 * Plugin Name: Admin Menu Tabs
 * Description: Add edit and admin tabs to admin menu and move updates after option menu.
 * Author: Fredrik Forsmo
 * Author URI: http://forsmo.me
 * Version: 1.0.1
 * Plugin URI: https://github.com/frozzare/wp-admin-menu-tabs
 * Textdomain: admin-menu-tabs
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Load Admin Menu Tabs.
require_once __DIR__ . '/src/class-admin-menu-tabs.php';
