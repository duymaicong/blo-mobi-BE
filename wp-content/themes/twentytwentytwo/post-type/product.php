<?php
function my_custom_post_product()
{
    $labels = array(
        'name'               => _x('Sản phẩm', 'post type general name'),
        'singular_name'      => _x('Product', 'post type singular name'),
        'add_new'            => _x('Thêm mới', 'book'),
        'add_new_item'       => __('Thêm mới sản phẩm'),
        'edit_item'          => __('Sửa sản phẩm'),
        'new_item'           => __('Sản phẩm mới'),
        'all_items'          => __('Tất cả sản phẩm'),
        'view_item'          => __('Xem sản phẩm'),
        'search_items'       => __('Tìm kiếm sản phẩm'),
        'not_found'          => __('No products found'),
        'not_found_in_trash' => __('No products found in the Trash'),

        'archives'              => _x('Recipe archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'recipe'),
        'insert_into_item'      => _x('Insert into recipe', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'recipe'),
        'uploaded_to_this_item' => _x('Uploaded to this recipe', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'recipe'),
        'filter_items_list'     => _x('Filter recipes list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'recipe'),
        'items_list_navigation' => _x('Recipes list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'recipe'),
        'items_list'            => _x('Recipes list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'recipe'),
        'parent_item_colon'  => '',
        'menu_name'          => 'Sản phẩm'
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Holds our products and product specific data',
        'public'        => true,
        'menu_position' => 5,
        'menu_icon'     => 'dashicons-images-alt',
        'supports'      => array('title', 'editor', 'thumbnail', 'comments', 'author'),
        'rewrite'       => array('slug' => 'san-pham'),
        'has_archive'   => true,
        'taxonomies'  => array( 'category' ),
    );
    register_post_type('product', $args); // product set post type
}
add_action('init', 'my_custom_post_product');


function product_columns_head($cols)
{
    $cols = array_merge(
        array_slice($cols, 0, 2, true),
        array('title' => __('Tiêu đề', '')),
        array('avatar' => __('Avatar', '')),
        array('author' => __('Tác giả', '')),
        array_slice($cols, 2, null, true)
    );
    return $cols;
}


function product_columns_content($col_name, $post_ID)
{
    if ($col_name == 'title') {
        $value = CFS()->get('post_title', $post_ID);
        if ($value) {
            echo $value;
        }
    }
    if ($col_name == 'author') {
        $value = CFS()->get('author', $post_ID);
        if ($value) {
            echo $value;
        }
    }
    if ($col_name == 'avatar') {
        echo get_avatar(get_the_author_meta($post_ID), $size = '40', $default = '', $alt = '', $args = array('class' => 'wt-author-img'));
    }
}
add_filter('manage_product_posts_columns', 'product_columns_head');
add_action('manage_product_posts_custom_column', 'product_columns_content', 10, 2);


function property_gallery_add_metabox()
{
    add_meta_box(
        'post_custom_gallery',
        'Gallery',
        'property_gallery_metabox_callback',
        'product', // Change post type name
        'normal',
        'core'
    );
}
add_action('admin_init', 'property_gallery_add_metabox');

function property_gallery_metabox_callback()
{
    wp_nonce_field(basename(__FILE__), 'sample_nonce');
    global $post;
    $gallery_data = get_post_meta($post->ID,'gallery_data',false);


?>
<style>
.close {
    position: absolute;
    left: 70px;
    color: red;
    font-size: 15px;
}
</style>
<div id="img_box_container">
    <div class="row">
        <div class="col">
            <div class="fw-bolder">Ảnh</div>
            <div class="d-none">
                <?php
                    print_r($gallery_data); ?>
            </div>
        </div>
        <div class="col fw-bolder">Nội dung</div>
        <div class="col fw-bolder">Tiêu đề</div>
    </div>
    <?php if ($gallery_data) {
            $length = count($gallery_data);
            for ($i = 0; $i < $length; $i++) {
        ?>
    <div class="row mt-2">
        <div class="col">
            <div class="field field-anh cfs_file">
                <span class="file_url"><img height="75" width="75" src="<?php echo $gallery_data[$i]['image_url'] ?>"
                        onclick="open_media_uploader_image_this(this)"></span>
                <svg onclick="remove_img(this)" xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="close"
                    fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                    <path
                        d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z" />
                </svg>
                <input class="value_src d-none" type="text" name="image_url[]" class="text w-100" 
                    value="<?php echo $gallery_data[$i]['image_url'] ?>">
            </div>
        </div>
        <div class="col">
            <div class="field field-mo_ta cfs_text">
                <input type="text" name="description[]" class="text w-100"
                    value="<?php echo $gallery_data[$i]['description'] ?>">
            </div>
        </div>
        <div class="col">
            <div class="field field-tieu_de cfs_text">
                <input type="text" name="note[]" class="text w-100" value="<?php echo $gallery_data[$i]['note'] ?>">
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
        <input class="button add" type="button" value="+" onclick="open_media_uploader_image_plus();"
            title="Add image" />
    </div>
</div>



<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">



<?php

}

function property_gallery_styles_scripts()
{ ?>
<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript">
function open_media_uploader_image_this(obj) {
    media_uploader = wp.media({
        frame: "post",
        state: "insert",
        multiple: false
    });
    media_uploader.on("insert", function() {
        var json = media_uploader.state().get("selection").first().toJSON();
        var image_url = json.url;
        console.log(image_url);
        jQuery(obj).attr('src', image_url);
        jQuery(obj).parent().parent().children(".value_src").val(image_url);
    });
    media_uploader.open();
}

// add image
function open_media_uploader_image_plus() {
    media_uploader = wp.media({
        frame: "post",
        state: "insert",
        multiple: true
    });
    media_uploader.on("insert", function() {
        var length = media_uploader.state().get("selection").length;
        var images = media_uploader.state().get("selection").models

        for (var i = 0; i < length; i++) {
            var image_url = images[i].changed.url;
            var box = jQuery('#img_box_container').html();
            var html = '';
            html += '<div class="row mt-2">';
            html += '<div class="col">';
            html += '<div class="field field-anh cfs_file">';
            html += '<span class="file_url"><img height="75" width="75" src="' + images[i].changed.url +
                '" onclick="open_media_uploader_image_this(this)"></span>';
            html += ' <svg onclick="remove_img(this)" xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="close"' +
                ' fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">' +
                '<path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z" />' +
                '</svg>';
            html += '<input class="value_src d-none" type="text" name="image_url[]" value="' + images[i].changed
                .url + '">';
            html += '</div>';
            html += '</div>';
            html += '<div class="col">'
            html += '<div class="field field-mo_ta cfs_text">';
            html += '<input type="text" name="description[]" class="text w-100" value="">';
            html += '</div>';
            html += '</div>';
            html += '<div class="col">';
            html += '<div class="field field-tieu_de cfs_text">';
            html += '<input type="text" name="note[]" class="text w-100" value="">';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            jQuery('#img_box_container').append(html);
        }
    });
    media_uploader.open();
}

// delete image
function remove_img(row) {
    // jQuery(row).parent().parent().parent().remove();
    // console.log("xoa row");
    console.log(jQuery(row).parent().parent().parent().remove());

}
</script>


<?php
}

add_action('admin_head-post.php', 'property_gallery_styles_scripts');
add_action('admin_head-post-new.php', 'property_gallery_styles_scripts');


function property_gallery_save($post_id)
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
    if ('product' != $_POST['post_type']) // here you can set the post type name
        return;
        
    if ($_POST['image_url']) {
        delete_post_meta($post_id,'gallery_data');
        // Build array for saving post meta
        $gallery = array();
        for ($i = 0; $i < count($_POST['image_url']); $i++) {
            add_post_meta($post_id, 'gallery_data', ([
                'image_url' => $_POST['image_url'][$i],
                'description' => $_POST['description'][$i],
                'note' => $_POST['note'][$i]
            ]));
            // $gallery['image_url'][$i] = $_POST['image_url'][$i];
        }
    }
    // Nothing received, all fields are empty, delete option
    else {
        delete_post_meta($post_id, 'gallery_data');
    }
}

add_action('save_post', 'property_gallery_save');





















/**
 * Add custom taxonomies
 *
 * Additional custom taxonomies can be defined here
 * https://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function add_custom_taxonomies()
{
    // Add new "Locations" taxonomy to Posts
    register_taxonomy('location', 'dia_diem', array(
        // Hierarchical taxonomy (like categories)
        'hierarchical' => true,
        // This array of options controls the labels displayed in the WordPress Admin UI
        'labels' => array(
            'name' => _x('Khu vực', 'taxonomy general name'),
            'singular_name' => _x('Location', 'taxonomy singular name'),
            'search_items' =>  __('Search Locations'),
            'all_items' => __('All Locations'),
            'parent_item' => __('Parent khu vực'),
            'parent_item_colon' => __('Parent Location:'),
            'edit_item' => __('Edit Location'),
            'update_item' => __('Update Location'),
            'add_new_item' => __('Thêm khu vực'),
            'new_item_name' => __('New Location Name'),
            'menu_name' => __('Khu vực'),
        ),
        // Control the slugs used for this taxonomy
        'rewrite' => array(
            'slug' => 'khu-vuc', // This controls the base slug that will display before each term
            'with_front' => false, // Don't display the category base before "/locations/"
            'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
        ),
    ));
}
add_action('init', 'add_custom_taxonomies', 0);





?>