<?php
function my_custom_post_order()
{
    $labels = array(
        'name'               => _x('Đơn hàng', 'post type general name'),
        'singular_name'      => _x('Order', 'post type singular name'),
        'add_new'            => _x('Thêm mới', 'book'),
        'add_new_item'       => __('Thêm mới đơn hàng'),
        'edit_item'          => __('Sửa đơn hàng'),
        'new_item'           => __('đơn hàng mới'),
        'all_items'          => __('Tất cả đơn hàng'),
        'view_item'          => __('Xem đơn hàng'),
        'search_items'       => __('Tìm kiếm đơn hàng'),
        'not_found'          => __('No order found'),
        'not_found_in_trash' => __('No orders found in the Trash'),

        'archives'              => _x('Recipe archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'recipe'),
        'insert_into_item'      => _x('Insert into recipe', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'recipe'),
        'uploaded_to_this_item' => _x('Uploaded to this recipe', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'recipe'),
        'filter_items_list'     => _x('Filter recipes list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'recipe'),
        'items_list_navigation' => _x('Recipes list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'recipe'),
        'items_list'            => _x('Recipes list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'recipe'),
        'parent_item_colon'  => '',
        'menu_name'          => 'Đơn hàng'
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Holds our products and product specific data',
        'public'        => true,
        'menu_position' => 5,
        'menu_icon'     => 'dashicons-images-alt',
        'supports'      => array('title', 'editor', 'thumbnail'),
        'rewrite'       => array('slug' => 'don-hang'),
        'has_archive'   => true,
    );
    register_post_type('order', $args); // product set post type
}
add_action('init', 'my_custom_post_order');


function order_columns_head($cols)
{
    $cols = array_merge(
        array_slice($cols, 0, 2, true),
        array('title' => __('Tiêu đề', '')),
        array('total_price' => __('Tổng giá', '')),
        array_slice($cols, 2, null, true)
    );
    return $cols;
}


function order_columns_content($col_name, $post_ID)
{
    if ($col_name == 'title') {
        $value = CFS()->get('post_title', $post_ID);
        if ($value) {
            echo $value;
        }
    }
    if ($col_name == 'total_price') {
        // $value = CFS()->get('total_price', $post_ID);
        $value = get_post_meta($post_ID, 'total_price', true);
        if ($value) {
            echo number_format($value) . ' đ';
        }
    }
   
}
add_filter('manage_order_posts_columns', 'order_columns_head');
add_action('manage_order_posts_custom_column', 'order_columns_content', 10, 2);





function property_list_order_add_metabox()
{
    add_meta_box(
        'post_custom_order_product',
        'Giao dịch',
        'property_order_product_metabox_callback',
        'order', // Change post type name
        'normal',
        'core'
    );
    add_meta_box(
        'post_custom_customer',
        'Khách hàng',
        'property_customer_metabox_callback',
        'order', // Change post type name
        'normal',
        'core'
    );
    add_meta_box(
        'post_custom_seller',
        'Người bán',
        'property_seller_metabox_callback',
        'order', // Change post type name
        'normal',
        'core'
    );
}
add_action('admin_init', 'property_list_order_add_metabox');

function property_order_product_metabox_callback()
{
    wp_nonce_field(basename(__FILE__), 'sample_nonce');
    global $post;
    $order_product      = get_post_meta($post->ID, 'order_product', false);
    $total_price        = get_post_meta($post->ID, 'total_price', true);
    $status_order       = get_post_meta($post->ID, 'status_order', true);

?>
    <style>
        .close {
            position: absolute;
            left: -2px;
            color: red;
            font-size: 15px;
        }
    </style>
    <div id="box_container">
        <div class="row">
            <div class="col">
                <div class="fw-bolder">Sản phẩm</div>
                <div class="d-none">
                    <?php
                    print_r($order_product); ?>
                </div>
            </div>
            <div class="col fw-bolder">Đường dẫn đến sản phẩm</div>
            <div class="col fw-bolder">Số Lượng</div>
            <div class="col fw-bolder">Giá</div>
            <div class="col fw-bolder">Thành tiền</div>
        </div>
        <?php if ($order_product) {
            $length = count($order_product);
            for ($i = 0; $i < $length; $i++) {
        ?>
                <div class="row mt-2">
                    <div class="col">
                        <div class="field ">
                            <a href="<?php echo $order_product[$i]['product_link'] ?>"><?php echo $order_product[$i]['product_name'] ?></a>
                            <input class="d-none" type="text" name="product_name[]" class="text w-100" value="<?php echo $order_product[$i]['product_name'] ?>">
                            <svg onclick="remove_product(this)" xmlns="http://www.w3.org/2000/svg" color="red" width="16" height="16" class="close" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z" />
                            </svg>
                            <input class="d-none" type="text" name="url_thumnail[]" class="text w-100" value="<?php echo $order_product[$i]['url_thumnail'] ?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="field ">
                            <a href="<?php echo $order_product[$i]['product_link'] ?>"><?php echo $order_product[$i]['product_link'] ?></a>
                            <input class="d-none" type="text" name="product_link[]" class="text" value="<?php echo $order_product[$i]['product_link'] ?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="field ">
                            <p><?php echo $order_product[$i]['product_number'] ?></p>
                            <input class="d-none" type="text" name="product_number[]" class="text w-100" value="<?php echo $order_product[$i]['product_number'] ?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="field ">
                            <p><?php echo number_format($order_product[$i]['product_price'], 0, '', ',') . ' đ'; ?></p>
                            <input class="d-none" type="text" name="product_price[]" class="text w-100" value="<?php echo $order_product[$i]['product_price'] ?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="field  check_price">
                            <p><?php echo number_format($order_product[$i]['product_total_price'], 0, '', ',') . ' đ'; ?></p>
                            <input class="d-none" type="text" name="product_total_price[]" class="text w-100 total_price" value="<?php echo $order_product[$i]['product_total_price'] ?>">
                        </div>
                    </div>
                </div>

            <?php
            } ?>

        <?php
        } ?>

    </div>
    <div class="row mt-3">
        <div class="col">
            <div class="fw-bolder">Tổng số tiền giao dịch</div>
        </div>
        <div class="col"><p id="total_price"><?php 
        if($total_price)
        echo number_format($total_price, 0, '', ',') . ' đ';  ?></p></div>
        <div class="d-none"><input id="result_price" value="<?php echo $total_price ?>"></input></div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <div class="fw-bolder">Trạng thái xét duyệt</div>
        </div>
        <div class="col"><input type="checkbox" value="" name="status_order" <?php if ($status_order) {
                                                                                echo 'checked';
                                                                            } ?>></div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <input class="button add" type="button" value="+" onclick="add_order_product();" title="Thêm giao dịch" />
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">



<?php

}

function property_customer_metabox_callback()
{
    wp_nonce_field(basename(__FILE__), 'sample_nonce');
    global $post;
    $customer_id = (int)get_post_meta($post->ID, 'customer_id', true);
    $args = [
        'include' => [$customer_id], // Get users of these IDs.
    ];

    $link = get_users($args);
    $link = get_edit_user_link($customer_id);
    $get_address = get_post_meta($post->ID, 'get_address', true);

    $paid = get_post_meta($post->ID, 'paid', true);
    $received = get_post_meta($post->ID, 'received', true);
?>
    <div id="box_container m-2">
        <div class="row m-2">
            <div class="d-none"><?php print_r($link); ?></div>
            <div class="d-none"><?php print_r($paid); ?></div>
            <div class="col">
                <div class="fw-bolder">Mã số khách hàng</div>
            </div>
            <div class="col"><input name="customer_id" value="<?php echo $customer_id ?>"></input></div>
        </div>
        <div class="row m-2">
            <div class="col">
                <div class="fw-bolder">Đường dẫn đến địa chỉ khách hàng</div>
            </div>
            <div class="col"><a href="<?php print_r($link); ?>"><?php print_r($link); ?></a></div>
        </div>
        <div class="row m-2">
            <div class="col">
                <div class="fw-bolder">Địa chỉ giao hàng</div>
            </div>
            <div class="col"><input name="get_address" value="<?php echo $get_address ?>"></input></div>
        </div>
        <div class="row m-2">
            <div class="col">
                <div class="fw-bolder">Đã thanh toán</div>
            </div>
            <div class="col"><input type="checkbox" value="" name="check_paid" <?php if ($paid) {
                                                                                    echo 'checked';
                                                                                }  ?>></div>
        </div>
        <div class="row m-2">
            <div class="col">
                <div class="fw-bolder">Đã nhận hàng</div>
            </div>
            <div class="col"><input type="checkbox" value="" name="received" <?php if ($received) {
                                                                                    echo 'checked';
                                                                                } ?>></div>
        </div>
    </div>

<?php
}

function property_seller_metabox_callback()
{
    wp_nonce_field(basename(__FILE__), 'sample_nonce');
    global $post;
    $seller_id = (int)get_post_meta($post->ID, 'seller_id', true);
    $args = [
        'include' => [$seller_id], // Get users of these IDs.
    ];

    $link = get_users($args);
    $link = get_edit_user_link($seller_id);

    $shipped = get_post_meta($post->ID, 'shipped', true);
    

?>
    <div id="box_container m-2">
        <div class="row m-2">
            <div class="d-none"><?php print_r($link); ?></div>
            <div class="d-none"><?php print_r($shipped); ?></div>
            <div class="col">
                <div class="fw-bolder">Mã số người bán</div>
            </div>
            <div class="col"><input name="seller_id" value="<?php echo $seller_id ?>"></input></div>
        </div>
        <div class="row m-2">
            <div class="col">
                <div class="fw-bolder">Đường dẫn đến địa chỉ người bán</div>
            </div>
            <div class="col"><a href="<?php print_r($link); ?>"><?php print_r($link); ?></a></div>
        </div>
        <div class="row m-2">
            <div class="col">
                <div class="fw-bolder">Đã chuyển hàng</div>
            </div>
            <div class="col"><input type="checkbox" value="" name="shipped" <?php if ($shipped) {
                                                                                echo 'checked';
                                                                            }  ?>></div>
        </div>
    </div>
<?php

}


function property_order_product_styles_scripts()
{ ?>

    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript">
        function formatCash(n, currency) {
            return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + ' ' + currency;
        }
        // add_order_product

        function add_order_product() {
            console.log("add_order_product");
            var box = jQuery('#box_container').html();
            var html = '';
            html += '<div class="row mt-2">';
            html += '<div class="col">';
            html += '<div class="field field-mo_ta cfs_text">';
            html += '<input type="text" name="product_name[]" class="text w-100" value="">';
            html += '</div>';
            html += '</div>';
            html += '<div class="col">'
            html += '<div class="field field-mo_ta cfs_text">';
            html += '<input type="text" name="product_link[]" class="text w-100" value="">';
            html += '</div>';
            html += '</div>';
            html += '<div class="col">';
            html += '<div class="field ">';
            html += '<input type="text" name="product_number[]" class="text w-100 number_product" value="" onkeyup="format_number_product(this)">';
            html += '</div>';
            html += '</div>';
            html += '<div class="col">';
            html += '<div class="field ">';
            html += '<input type="text" name="product_price[]" class="text w-100 test price" value="" onkeyup="format_cash_price(this)">';
            html += '</div>';
            html += '</div>';
            html += '<div class="col">';
            html += '<div class="field  check_price">';
            html += '<p class="total_price_p"></p>'
            html += '<input type="text" name="product_total_price[]" class="text w-100 total_price d-none" value="" onkeyup="format_cash(this)">';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            jQuery('#box_container').append(html);

        }

        function format_cash(e) {
            var n = jQuery(e).val().replace(/\D/g, '');
            jQuery(e).val(formatCash(n, ''));
            console.log(jQuery(e).parent().parent().siblings().find('.price').val());
            console.log(jQuery(e).parent().parent().siblings().find('.number_product').val());
        }

        function format_cash_price(e) {
            var n = jQuery(e).val().replace(/\D/g, '');
            jQuery(e).val(formatCash(n, ''));
            console.log(jQuery(e).parent().parent().siblings().find('.number_product').val());
            var number_product = jQuery(e).parent().parent().siblings().find('.number_product').val();
            number_product = parseInt(number_product.replace(/\D/g, ''));
            n = parseInt(n);
            jQuery(e).parent().parent().siblings().find('.total_price').val(formatCash(n * number_product, ''));
            jQuery(e).parent().parent().siblings().find('.total_price_p').text(formatCash(n * number_product, ' đ'));


            // update total price
            if (number_product) {
                var x = jQuery(".check_price :input");
                var total = 0;
                for (let i = 0; i < x.length; i++) {
                    console.log(x[i]);
                    if (x[i].defaultValue) {
                        total += parseInt(x[i].defaultValue);
                    } else {
                        total += parseInt((x[i].value).replace(/\D/g, ''));
                    }

                }
                console.log(total);
                jQuery("#total_price").text(formatCash(total, ' vnđ'));
                jQuery("#result_price").val(total);
            }
        }

        function format_number_product(e) {
            var n = jQuery(e).val().replace(/\D/g, '');
            jQuery(e).val(n);
            console.log(jQuery(e).parent().parent().siblings().find('.price').val().replace(/\D/g, ''));
            var price = jQuery(e).parent().parent().siblings().find('.price').val();
            price = parseInt(price.replace(/\D/g, ''));
            n = parseInt(n);
            jQuery(e).parent().parent().siblings().find('.total_price').val(formatCash(n * price, ''));
            jQuery(e).parent().parent().siblings().find('.total_price_p').text(formatCash(n * price, ' đ'));


            // update total price
            if (price) {
                var x = jQuery(".check_price :input");
                var total = 0;
                for (let i = 0; i < x.length; i++) {
                    console.log(x[i]);
                    if (x[i].defaultValue) {
                        total += parseInt(x[i].defaultValue);
                    } else {
                        total += parseInt((x[i].value).replace(/\D/g, ''));
                    }

                }
                console.log(total);
                jQuery("#total_price").text(formatCash(total, ' vnđ'));
                jQuery("#result_price").val(total);
            }
        }

        function remove_product(e) {

            console.log(jQuery(e).parent().parent().parent().remove());
            console.log("day la delete");
            var x = jQuery(".check_price :input");
            var total = 0;
            for (let i = 0; i < x.length; i++) {
                console.log(x[i]);
                if (x[i].defaultValue) {
                    total += parseInt(x[i].defaultValue);
                } else {
                    total += parseInt((x[i].value).replace(/\D/g, ''));
                }

            }
            console.log(total);
            jQuery("#total_price").text(formatCash(total, ' vnđ'));
            jQuery("#result_price").val(total);
            console.log(jQuery("#result_price").val());

        }
    </script>


<?php
}

add_action('admin_head-post.php', 'property_order_product_styles_scripts');
add_action('admin_head-post-new.php', 'property_order_product_styles_scripts');


function property_order_product_save($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    $is_autosave = wp_is_post_autosave($post_id);
    $is_revision = wp_is_post_revision($post_id);
    $is_valid_nonce = (isset($_POST['sample_nonce']) && wp_verify_nonce($_POST['sample_nonce'], basename(__FILE__))) ? 'true' : 'false';

    if ($is_autosave || $is_revision || !$is_valid_nonce) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Correct post type
    if ('order' != $_POST['post_type']) // here you can set the post type name
        return;

    if ($_POST['product_name']) {
        delete_post_meta($post_id, 'order_product');
        // Build array for saving post meta

        $gallery = array();
        $total_price = 0;
        for ($i = 0; $i < count($_POST['product_name']); $i++) {
            if (!empty($_POST['product_name'][$i])) {
                $total_price += (int) str_replace(",", "", $_POST['product_total_price'][$i]);
                add_post_meta($post_id, 'order_product', ([
                    'product_name' => $_POST['product_name'][$i],
                    'product_link' => $_POST['product_link'][$i],
                    'url_thumnail' =>  $_POST['url_thumnail'][$i],
                    'product_number' => $_POST['product_number'][$i],
                    'product_price' => str_replace(",", "", $_POST['product_price'][$i]),
                    'product_total_price' => str_replace(",", "", $_POST['product_total_price'][$i])
                ]));
            }
            // $gallery['image_url'][$i] = $_POST['image_url'][$i];
        }
        if (isset($_POST['status_order'])) {
            $check = true;
        } else {
            $check = false;
        }
        update_post_meta($post_id, 'status_order', $check);
        update_post_meta($post_id, 'total_price', $total_price);
    }
    // Nothing received, all fields are empty, delete option
    else {
        delete_post_meta($post_id, 'gallery_data');
    }
    // update customer
    update_post_meta($post_id, 'customer_id', $_POST['customer_id']);
    update_post_meta($post_id, 'get_address', $_POST['get_address']);
    if (isset($_POST['check_paid'])) {
        $check = true;
    } else {
        $check = false;
    }
    update_post_meta($post_id, 'paid', $check);

    if (isset($_POST['received'])) {
        $check = true;
    } else {
        $check = false;
    }
    update_post_meta($post_id, 'received', $check);


    // nguoi ban
    update_post_meta($post_id, 'seller_id', $_POST['seller_id']);

    if (isset($_POST['shipped'])) {
        $check = true;
    } else {
        $check = false;
    }
    update_post_meta($post_id, 'shipped',  $check);
    // wp_update_post( array( 'ID' => $post_id, 'post_status' => 'public' ) );
}

add_action('save_post', 'property_order_product_save');

// 










?>