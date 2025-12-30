<?php
/**
 * Metal Rates Admin
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Metal_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_post_jpc_add_metal', array($this, 'handle_add_metal'));
        add_action('admin_post_jpc_update_metal', array($this, 'handle_update_metal'));
        add_action('admin_post_jpc_delete_metal', array($this, 'handle_delete_metal'));
    }
    
    public static function render_page() {
        $metal = JPC_Metal::get_instance();
        $metals = $metal->get_all();
        
        include JPC_PLUGIN_DIR . 'templates/admin-metals.php';
    }
    
    public function handle_add_metal() {
        check_admin_referer('jpc_add_metal');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'jewellery-price-calc'));
        }
        
        $name = sanitize_text_field($_POST['metal_name']);
        $price = floatval($_POST['metal_price']);
        
        $metal = JPC_Metal::get_instance();
        $result = $metal->add($name, $price);
        
        if ($result) {
            wp_redirect(add_query_arg(array('page' => 'jpc-metals', 'message' => 'added'), admin_url('admin.php')));
        } else {
            wp_redirect(add_query_arg(array('page' => 'jpc-metals', 'message' => 'error'), admin_url('admin.php')));
        }
        exit;
    }
    
    public function handle_update_metal() {
        check_admin_referer('jpc_update_metal');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'jewellery-price-calc'));
        }
        
        $id = intval($_POST['metal_id']);
        $name = sanitize_text_field($_POST['metal_name']);
        $price = floatval($_POST['metal_price']);
        
        $metal = JPC_Metal::get_instance();
        $result = $metal->update($id, $name, $price);
        
        if ($result) {
            wp_redirect(add_query_arg(array('page' => 'jpc-metals', 'message' => 'updated'), admin_url('admin.php')));
        } else {
            wp_redirect(add_query_arg(array('page' => 'jpc-metals', 'message' => 'error'), admin_url('admin.php')));
        }
        exit;
    }
    
    public function handle_delete_metal() {
        check_admin_referer('jpc_delete_metal_' . $_GET['id']);
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'jewellery-price-calc'));
        }
        
        $id = intval($_GET['id']);
        
        $metal = JPC_Metal::get_instance();
        $result = $metal->delete($id);
        
        if ($result) {
            wp_redirect(add_query_arg(array('page' => 'jpc-metals', 'message' => 'deleted'), admin_url('admin.php')));
        } else {
            wp_redirect(add_query_arg(array('page' => 'jpc-metals', 'message' => 'error'), admin_url('admin.php')));
        }
        exit;
    }
}
