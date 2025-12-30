/**
 * Update diamond with price history logging
 */
public static function update($id, $data) {
    global $wpdb;
    $table = $wpdb->prefix . 'jpc_diamonds';
    
    // First check if diamond exists and get old price
    $old_diamond = self::get_by_id($id);
    
    if (!$old_diamond) {
        error_log('JPC: Diamond ID ' . $id . ' not found');
        return false;
    }
    
    $old_price = floatval($old_diamond->price_per_carat);
    
    $update_data = array(
        'type' => sanitize_text_field($data['type']),
        'carat' => floatval($data['carat']),
        'certification' => sanitize_text_field($data['certification']),
        'price_per_carat' => floatval($data['price_per_carat']),
        'display_name' => sanitize_text_field($data['display_name']),
    );
    
    error_log('JPC: Updating diamond ID ' . $id . ' with data: ' . print_r($update_data, true));
    
    $result = $wpdb->update($table, $update_data, array('id' => $id));
    
    // $result can be 0 (no rows changed), false (error), or number of rows affected
    if ($result === false) {
        error_log('JPC: Failed to update diamond. Error: ' . $wpdb->last_error);
        return false;
    }
    
    // Log price change if price was updated
    $new_price = floatval($data['price_per_carat']);
    if ($old_price != $new_price) {
        // Use JPC_Metals::log_price_change which now supports diamonds
        JPC_Metals::log_price_change($id, $old_price, $new_price, 'diamond');
        error_log('JPC: Logged diamond price change from ' . $old_price . ' to ' . $new_price);
    }
    
    error_log('JPC: Diamond updated successfully. Rows affected: ' . $result);
    return true; // Return true even if 0 rows changed (data was same)
}
