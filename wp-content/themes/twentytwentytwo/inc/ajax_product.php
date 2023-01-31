<?php 
add_action( 'wp_ajax_pagination_number', 'pagination_number_init' );
add_action( 'wp_ajax_nopriv_pagination_number', 'pagination_number_init' );
function pagination_number_init() {
    global $wpdb, $nxt_r;
 
    //do bên js để dạng json nên giá trị trả về dùng phải encode
    $product_id = (isset($_POST['product_id']))?(int)esc_attr($_POST['product_id']) : '';
    $data = get_post_meta($product_id, 'gallery_data', true);


    $response['status'] = 200;
    $response['data']   = $data;
    echo json_encode($response);
 
    die();//bắt buộc phải có khi kết thúc
}




?>