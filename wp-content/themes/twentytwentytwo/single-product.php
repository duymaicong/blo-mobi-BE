<?php

defined('ABSPATH') || exit;
global $cfs;
global $wpdb;
global $current_user;

$id = get_the_ID();

// get meta
$metas = get_post_meta($post->ID, 'gallery_data', true);

// get page number
$page = (int) $_GET['pa'];
$page = $page >= 1 ? $page : 1;

// $x = $_GET['pa'];

// $url = home_url().'/san-pham/'.get_post()->post_name;





get_header();
echo json_encode($metas);
?>

<!-- <h1>day la content</h1>
<div>
    <p>current_user:<?php
                    //  echo $current_user->ID;
                    ?></p>
</div>
<div>
    <p>title:
        <?php
        // echo get_the_title();
        ?></p>
</div>
<div>
    <p>content:
        <?php
        //  echo get_the_content(); 
        ?></p>
</div>
<div>
    <p>id:
        <?php
        //  echo $id; 
        ?></p>
</div>
<div>
    <p>paged:
        <?php
        // echo $page; 
        ?></p>
</div>
<div>
    <?php
    // print_r($metas); 
    ?>
</div> -->
<div class="container-fluid">
    <?php echo $page; ?>
    <div id="prd">
        <?php for ($i = 3 * ($page - 1); $i < 3 * ($page); $i++) {
            if (isset($metas['image_url'][$i])) {

        ?>
                <div class="row justify-content-center mt-3">
                    <div class="col-4">
                        <div class="card w-100 border-0">
                            <img class="card-img-top" src="<?php echo $metas['image_url'][$i]; ?>" alt="<?php echo $metas['note'][$i]; ?>" title="<?php echo $metas['note'][$i]; ?>">
                            <div class="card-body d-flex justify-content-center">
                                <h5 class="card-title"><?php echo $metas['description'][$i]; ?></h5>
                            </div>
                        </div>
                    </div>
                </div>

        <?php
            }
        } ?>
    </div>
    <div class="d-flex justify-content-center mt-2">
        <nav aria-label="Page navigation example ">
            <?php
            $total = ceil(count($metas['image_url']) / 3); ?>
            <ul class="pagination">
                <li class="page-item">
                    <p class="page-link" aria-label="Previous" id="previous_page" data-target-id="<?php echo $id; ?>" data-target-total="<?php echo $total; ?>">
                        <span aria-hidden="true">&laquo;</span>
                    </p>
                </li>
                <?php
                for ($i = 0; $i < $total; $i++) {
                    $p = $i + 1;
                ?>
                    <li class="page-item <?php if (($i + 1 == $page)) {
                                                echo 'active';
                                            } ?>" id="<?php echo  $i + 1;?>">
                        <p class="page-link number" data-target-id="<?php echo $id; ?>" data-target-page="<?php echo $i + 1; ?>"><?php print_r($p); ?></p>
                    </li>
                <?php
                }
                ?>

                <li class="page-item">
                    <p class="page-link" aria-label="Next" id="next_paged" data-target-id="<?php echo $id; ?>" data-target-total="<?php echo $total; ?>">
                        <span aria-hidden="true">&raquo;</span>
                    </p>
                </li>
            </ul>
        </nav>
    </div>
</div>
<div class="nav-previous alignleft"><?php next_posts_link('Older posts'); ?></div>
<div class="nav-next alignright"><?php previous_posts_link('Newer posts'); ?></div>
<script>
    // function set_pagination(){
    //     console.log('ok');
    // }
    (function($) {

        $('body').on('click', '.pagination .number', function(e) {
            var page = parseInt($(this).attr('data-target-page'));
            const params = new URLSearchParams(window.location.search);
            var product_id = $(this).attr('data-target-id');
            var url = window.location.origin + window.location.pathname;
            url = url + '?pa=' + page;
            $(this).parent().siblings().removeClass('active');
            $(this).parent().addClass('active');
            history.replaceState({}, '', url);
            $.ajax({
                type: "post", //Phương thức truyền post hoặc get
                dataType: "json", //Dạng dữ liệu trả về xml, json, script, or html
                url: '<?php echo admin_url('admin-ajax.php'); ?>', //Đường dẫn chứa hàm xử lý dữ liệu. Mặc định của WP như vậy
                data: {
                    action: "pagination_number", //Tên action
                    product_id: product_id,
                },
                context: this,
                success: function(response) {
                    //Làm gì đó khi dữ liệu đã được xử lý
                    if (response.status) {
                        $('#prd').html('');
                        var image_url = response.data.image_url;
                        console.log(page);
                        var length = 3 * page;
                        console.log(Object.keys(image_url).length)
                        if (length > Object.keys(image_url).length) {
                            length = Object.keys(image_url).length;
                        }
                        image = Object.values(image_url);
                        console.log(image_url[0]);
                        for (let i = 3 * (page - 1); i < length; i++) {
                            var html = '';
                            html += '<div class="row justify-content-center mt-3">';
                            html += '<div class="col-4">';
                            html += '<div class="card w-100 border-0">';
                            html += '<img class="card-img-top" src="';
                            html += image_url[i];
                            html += '" alt="';
                            html += '" title="';
                            html += '">';
                            html += '<div class="card-body d-flex justify-content-center">';
                            html += '<h5 class="card-title">';
                            html += '</h5>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                            $('#prd').append(html);

                        }
                    } else {
                        alert('Đã có lỗi xảy ra');
                    }
                }
            })

        })

        $('body').on('click', '#next_paged', function(e) {
            const params = new URLSearchParams(window.location.search);
            var page = parseInt(params.get("pa"));
            var total = parseInt($(this).attr('data-target-total'));
            var htm = '<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden"></span></div></div>';
            $('#prd').html('');
            $('#prd').append(htm);
            $('.pagination .page-item').removeClass('active');
            if (isNaN(page)) {
                page = 1;
            }
            page = page + 1;
            var url = window.location.origin + window.location.pathname;

            if (page == (total + 1)) {
                page =1;
                url = url;
            } else {
                url = url + '?pa=' + page;
            }
            var product_id = $(this).attr('data-target-id');
            history.replaceState({}, '', url);
            var active ='#'+page;
            $(active).addClass('active');
            console.log($(active));
            $.ajax({
                type: "post", //Phương thức truyền post hoặc get
                dataType: "json", //Dạng dữ liệu trả về xml, json, script, or html
                url: '<?php echo admin_url('admin-ajax.php'); ?>', //Đường dẫn chứa hàm xử lý dữ liệu. Mặc định của WP như vậy
                data: {
                    action: "pagination_number", //Tên action
                    product_id: product_id,
                },
                context: this,
                success: function(response) {
                    //Làm gì đó khi dữ liệu đã được xử lý
                    if (response.status) {
                        $('#prd').html('');
                        var image_url = response.data.image_url;
                        console.log(page);
                        var length = 3 * page;
                        console.log(Object.keys(image_url).length)
                        if (length > Object.keys(image_url).length) {
                            length = Object.keys(image_url).length;
                        }
                        image = Object.values(image_url);
                        console.log('page',page);
                        console.log(image_url[0]);
                        for (let i = 3 * (page - 1); i < length; i++) {
                            var html = '';
                            html += '<div class="row justify-content-center mt-3">';
                            html += '<div class="col-4">';
                            html += '<div class="card w-100 border-0">';
                            html += '<img class="card-img-top" src="';
                            html += image_url[i];
                            html += '" alt="';
                            html += '" title="';
                            html += '">';
                            html += '<div class="card-body d-flex justify-content-center">';
                            html += '<h5 class="card-title">';
                            html += '</h5>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                            $('#prd').append(html);

                        }
                    } else {
                        alert('Đã có lỗi xảy ra');
                    }
                }
            })
        });
        $('body').on('click', '#previous_page', function(e) {
            const params = new URLSearchParams(window.location.search);
            var page = parseInt(params.get("pa"));
            var total = parseInt($(this).attr('data-target-total'));
            var htm = '<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden"></span></div></div>';
            $('#prd').html('');
            $('#prd').append(htm);
            $('.pagination .page-item').removeClass('active');
            if (isNaN(page)) {
                page = 1;
            }
            page = page - 1;
            var url = window.location.origin + window.location.pathname;

            if (page == 0) {
                page =total;
                url = url + '?pa=' + page;
            } else {
                url = url + '?pa=' + page;
            }
            var product_id = $(this).attr('data-target-id');
            history.replaceState({}, '', url);
            var active ='#'+page;
            $(active).addClass('active');
            console.log($(active));
            $.ajax({
                type: "post", //Phương thức truyền post hoặc get
                dataType: "json", //Dạng dữ liệu trả về xml, json, script, or html
                url: '<?php echo admin_url('admin-ajax.php'); ?>', //Đường dẫn chứa hàm xử lý dữ liệu. Mặc định của WP như vậy
                data: {
                    action: "pagination_number", //Tên action
                    product_id: product_id,
                },
                context: this,
                success: function(response) {
                    //Làm gì đó khi dữ liệu đã được xử lý
                    if (response.status) {
                        $('#prd').html('');
                        var image_url = response.data.image_url;
                        console.log(page);
                        var length = 3 * page;
                        console.log(Object.keys(image_url).length)
                        if (length > Object.keys(image_url).length) {
                            length = Object.keys(image_url).length;
                        }
                        image = Object.values(image_url);
                        console.log('page',page);
                        console.log(image_url[0]);
                        for (let i = 3 * (page - 1); i < length; i++) {
                            var html = '';
                            html += '<div class="row justify-content-center mt-3">';
                            html += '<div class="col-4">';
                            html += '<div class="card w-100 border-0">';
                            html += '<img class="card-img-top" src="';
                            html += image_url[i];
                            html += '" alt="';
                            html += '" title="';
                            html += '">';
                            html += '<div class="card-body d-flex justify-content-center">';
                            html += '<h5 class="card-title">';
                            html += '</h5>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                            $('#prd').append(html);

                        }
                    } else {
                        alert('Đã có lỗi xảy ra');
                    }
                }
            })
        });



    })(jQuery);
</script>

<?php
get_footer();
?>