<?php
// Check Product Meta Fields - Find correct diamond field names
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) die('Access denied');

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check Meta</title>
    <style>
        body{font-family:monospace;padding:20px;background:#f5f5f5;font-size:12px}
        .container{background:white;padding:20px;border-radius:5px;max-width:1400px;margin:0 auto}
        h1{color:#2271b1;font-family:Arial}
        table{width:100%;border-collapse:collapse;margin:20px 0}
        th,td{padding:8px;border:1px solid #ddd;text-align:left}
        th{background:#2271b1;color:white}
        tr:nth-child(even){background:#f9f9f9}
        .highlight{background:#fff3cd!important;font-weight:bold}
        .info{background:#d1ecf1;border:2px solid #17a2b8;color:#0c5460;padding:15px;border-radius:5px;margin:20px 0;font-family:Arial}
        input{padding:8px;font-size:14px;width:100px}
        button{padding:8px 20px;background:#2271b1;color:white;border:none;border-radius:3px;cursor:pointer;font-size:14px}
    </style>
</head>
<body>
    <div class="container">
        <h1>Check Product Meta</h1>
        <div class="info">
            <form method="get">
                <label><strong>Product ID:</strong></label>
                <input type="number" name="id" value="<?php echo $product_id;?>" placeholder="ID">
                <button type="submit">Check</button>
            </form>
        </div>
        <?php
        if($product_id>0){
            $product=get_post($product_id);
            if($product && $product->post_type==='product'){
                echo '<h2>'.esc_html($product->post_title).' (ID: '.$product_id.')</h2>';
                global $wpdb;
                $meta=$wpdb->get_results($wpdb->prepare("SELECT meta_key,meta_value FROM {$wpdb->postmeta} WHERE post_id=%d ORDER BY meta_key",$product_id));
                echo '<table><thead><tr><th>Meta Key</th><th>Value</th></tr></thead><tbody>';
                foreach($meta as $m){
                    $is_diamond=(strpos($m->meta_key,'diamond')!==false);
                    $class=$is_diamond?'highlight':'';
                    $val=$m->meta_value;
                    if(is_serialized($val)){
                        $val='<pre>'.print_r(maybe_unserialize($val),true).'</pre>';
                    }else{
                        $val=esc_html($val);
                    }
                    echo '<tr class="'.$class.'"><td><strong>'.esc_html($m->meta_key).'</strong></td><td>'.$val.'</td></tr>';
                }
                echo '</tbody></table>';
                echo '<div class="info"><h3>Look for Diamond Fields</h3><p>Yellow rows contain "diamond". Find fields with values: 0.50 (carat), 5 (quantity), 3000 (price)</p></div>';
            }else{
                echo '<div class="info" style="background:#f8d7da;border-color:#dc3545;color:#721c24"><p>Product not found!</p></div>';
            }
        }else{
            echo '<div class="info"><p>Enter product ID above (use Test Product 2)</p></div>';
        }
        ?>
    </div>
</body>
</html>
