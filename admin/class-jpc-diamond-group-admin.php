<?php
/**
 * Diamond Groups Admin
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Diamond_Group_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_post_jpc_add_diamond_group', array($this, 'handle_add_group'));
        add_action('admin_post_jpc_update_diamond_group', array($this, 'handle_update_group'));
        add_action('admin_post_jpc_delete_diamond_group', array($this, 'handle_delete_group'));
    }
    
    public static function render_page() {
        $group = JPC_Diamond_Group::get_instance();
        $groups = $group->get_all();
        
        include JPC_PLUGIN_DIR . 'templates/admin-diamond-groups.php';
    }
    
    public function handle_add_group() {
        check_admin_referer('jpc_add_diamond_group');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'jewellery-price-calc'));
        }
        
        $name = sanitize_text_field($_POST['group_name']);
        $price = floatval($_POST['group_price']);
        
        $group = JPC_Diamond_Group::get_instance();
        $result = $group->add($name, $price);
        
        if ($result) {
            wp_redirect(add_query_arg(array('page' => 'jpc-diamond-groups', 'message' => 'added'), admin_url('admin.php')));
        } else {
            wp_redirect(add_query_arg(array('page' => 'jpc-diamond-groups', 'message' => 'error'), admin_url('admin.php')));
        }
        exit;
    }
    
    public function handle_update_group() {
        check_admin_referer('jpc_update_diamond_group');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'jewellery-price-calc'));
        }
        
        $id = intval($_POST['group_id']);
        $name = sanitize_text_field($_POST['group_name']);
        $price = floatval($_POST['group_price']);
        
        $group = JPC_Diamond_Group::get_instance();
        $result = $group->update($id, $name, $price);
        
        if ($result) {
            wp_redirect(add_query_arg(array('page' => 'jpc-diamond-groups', 'message' => 'updated'), admin_url('admin.php')));
        } else {
            wp_redirect(add_query_arg(array('page' => 'jpc-diamond-groups', 'message' => 'error'), admin_url('admin.php')));
        }
        exit;
    }
    
    public function handle_delete_group() {
        check_admin_referer('jpc_delete_diamond_group_' . $_GET['id']);
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'jewellery-price-calc'));
        }
        
        $id = intval($_GET['id']);
        
        $group = JPC_Diamond_Group::get_instance();
        $result = $group->delete($id);
        
        if ($result) {
            wp_redirect(add_query_arg(array('page' => 'jpc-diamond-groups', 'message' => 'deleted'), admin_url('admin.php')));
        } else {
            wp_redirect(add_query_arg(array('page' => 'jpc-diamond-groups', 'message' => 'error'), admin_url('admin.php')));
        }
        exit;
    }
}
