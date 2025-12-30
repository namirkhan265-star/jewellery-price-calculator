<?php
/**
 * Diamond Rates Admin
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Diamond_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_post_jpc_add_diamond', array($this, 'handle_add_diamond'));
        add_action('admin_post_jpc_update_diamond', array($this, 'handle_update_diamond'));
        add_action('admin_post_jpc_delete_diamond', array($this, 'handle_delete_diamond'));
    }
    
    public static function render_page() {
        $diamond = JPC_Diamond::get_instance();
        $diamonds = $diamond->get_all();
        
        include JPC_PLUGIN_DIR . 'templates/admin/diamonds.php';
    }
    
    public function handle_add_diamond() {
        check_admin_referer('jpc_add_diamond');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'jewellery-price-calc'));
        }
        
        $carat = sanitize_text_field($_POST['diamond_carat']);
        $price = floatval($_POST['diamond_price']);
        
        $diamond = JPC_Diamond::get_instance();
        $result = $diamond->add($carat, $price);
        
        if ($result) {
            wp_redirect(add_query_arg(array('page' => 'jpc-diamonds', 'message' => 'added'), admin_url('admin.php')));
        } else {
            wp_redirect(add_query_arg(array('page' => 'jpc-diamonds', 'message' => 'error'), admin_url('admin.php')));
        }
        exit;
    }
    
    public function handle_update_diamond() {
        check_admin_referer('jpc_update_diamond');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'jewellery-price-calc'));
        }
        
        $id = intval($_POST['diamond_id']);
        $carat = sanitize_text_field($_POST['diamond_carat']);
        $price = floatval($_POST['diamond_price']);
        
        $diamond = JPC_Diamond::get_instance();
        $result = $diamond->update($id, $carat, $price);
        
        if ($result) {
            wp_redirect(add_query_arg(array('page' => 'jpc-diamonds', 'message' => 'updated'), admin_url('admin.php')));
        } else {
            wp_redirect(add_query_arg(array('page' => 'jpc-diamonds', 'message' => 'error'), admin_url('admin.php')));
        }
        exit;
    }
    
    public function handle_delete_diamond() {
        check_admin_referer('jpc_delete_diamond_' . $_GET['id']);
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'jewellery-price-calc'));
        }
        
        $id = intval($_GET['id']);
        
        $diamond = JPC_Diamond::get_instance();
        $result = $diamond->delete($id);
        
        if ($result) {
            wp_redirect(add_query_arg(array('page' => 'jpc-diamonds', 'message' => 'deleted'), admin_url('admin.php')));
        } else {
            wp_redirect(add_query_arg(array('page' => 'jpc-diamonds', 'message' => 'error'), admin_url('admin.php')));
        }
        exit;
    }
}
