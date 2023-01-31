<?php

use Tmeister\Firebase\JWT\JWT;
use Tmeister\Firebase\JWT\Key;


//Dynamic Route at /product/[id]  82

add_action('rest_api_init', function () {
	register_rest_route('my/v1', '/product-image', [
		'methods' => 'get',
		'callback' => 'get_product_image',
		'permission_callback' => '__return_true',
	]);
});
// Get image product
//http://localhost/wp_project/wp-json/my/v1/product-image
// id=82 index=1
function get_product_image($request)
{
	$params = wp_parse_args($request->get_params(), [
		'index' => '',
		'id' => ''
	]);
	$index = $params['index'];
	$count = 3 * $index;
	$product = get_post_meta($params['id'], 'gallery_data', true);
	// $product->thumbnail = get_the_post_thumbnail_url( $product->ID );
	$lenght = 0;
	if (!empty($product['image_url'])) {
		$lenght = count($product['image_url']);
		if ($count > $lenght) {
			$count = $lenght;
		}
		for ($i = 3 * ($index - 1); $i < $count; $i++) {
			$image_url[] = $product['image_url'][$i];
		}
	} else {
		$image_url = [];
	}
	if (!empty($product['description'])) {
		$lenght = count($product['description']);
		if ($count > $lenght) {
			$count = $lenght;
		}
		for ($i = 3 * ($index - 1); $i < $count; $i++) {
			$description[] = $product['description'][$i];
		}
	} else {
		$description = [];
	}
	if (!empty($product['note'])) {
		$lenght = count($product['note']);
		if ($count > $lenght) {
			$count = $lenght;
		}
		for ($i = 3 * ($index - 1); $i < $count; $i++) {
			$note[] = $product['note'][$i];
		}
	} else {
		$note = [];
	}
	if (!empty($note) || !empty($description) || !empty($image_url)) {
		$response = [
			'message' => 'lấy dữ liệu thành công',
			'data' => [
				'status' => 200,
				'image_url' => $image_url,
				'description' => $description,
				'note' => $note
			],
		];
	} else {
		$response = [
			'message' => 'không có dữ liệu',
			'data' => [
				'status' => 204,
				'image_url' => $image_url,
				'description' => $description,
				'note' => $note
			],
		];
	}

	return $response;
}


//Dynamic Route at /product/[id]  82

add_action('rest_api_init', function () {
	register_rest_route('my/v1', '/products', [
		'methods' => 'GET',
		'callback' => 'get_products',
		'permission_callback' => '__return_true',
	]);
});
// Get image product
//http://localhost/wp_project/wp-json/my/v1/products
// id=82 index=1
function get_products($request)

{
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

	$creds = [];
	$creds['user_login'] = $_SERVER['PHP_AUTH_USER'];
	$creds['user_password'] =  $_SERVER['PHP_AUTH_PW'];
	$creds['remember'] = true;
	$user = wp_signon($creds, false);
	$is_not_authenticated = (!$has_supplied_credentials || is_wp_error($user)
	);
	// $is_not_authenticated=false;
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		return rest_ensure_response(['status' => 401, 'msg' => 'Authorization required']);
		exit;
	} else {
		$params = wp_parse_args($request->get_params(), [
			'page' => '',
		]);
		$paged = $params['page'];

		$productsQr = new WP_Query([
			"post_type"	=> "product",
			"posts_per_page" => -1,
			'orderby' => 'post_date',
			'post_status' => array(                 //(string / array)       
				'publish',
				'private',                      // - Bài viết đang trong trạng thái riêng tư
			),
		]);
		$count = 10 * $paged;
		if ($count > $productsQr->post_count) {
			$count = $productsQr->post_count;
		}

		$products =  get_posts([
			'post_type' => 'product',
			'posts_per_page' => 10 * $paged,
			'paged' => 1,
		]);

		foreach ($products as &$p) {
			$gallery_data = get_post_meta($p->ID, 'gallery_data', false);
			$gia = get_post_meta($p->ID, 'gia', true);
			$p->gallery_data = $gallery_data;
			$p->gia = $gia;
			$mo_ta = get_post_meta($p->ID, 'mo_ta', true);
			$p->mo_ta = $mo_ta;
			$so_san_pham_da_ban = get_post_meta($p->ID, 'so_san_pham_da_ban', true);
			$p->so_san_pham_da_ban = $so_san_pham_da_ban;
			$con_hang = get_post_meta($p->ID, 'con_hang', true);
			$p->con_hang = $con_hang;
			$p->author_avatar = get_avatar_url($p->ID);
			$p->author_name   = get_userdata($p->post_author)->display_name;
			$p->url_thumnail = get_the_post_thumbnail_url($p->ID);
			$favorite = get_post_meta($p->ID, 'favorite', false);
			$p->favorite = $favorite;
			$meta=get_the_category($p->ID);
			$p->category =$meta;
		}
		return [
			'data' => [
				'products' => $products,
				'status' => 200,
				'message' => 'get products successfully',
				'productsQR' => $productsQr
			],
		];
	}
}



// // login

add_action('rest_api_init', function () {
	register_rest_route('my/v1', '/login', [
		'methods' => 'POST',
		'callback' => 'login',
		'permission_callback' => '__return_true',
	]);
});
// Get image product
//http://localhost/wp_project/wp-json/my/v1/token
// id=82 index=1
function require_auth()
{
}


//Dynamic Route at /product/[id]

add_action('rest_api_init', function () {
	register_rest_route('my/v1', '/product/(?P<id>\d+)', [
		'methods' => 'GET',
		'callback' => 'get_product',
		'permission_callback' => '__return_true',
	]);
	register_rest_route('my/v1', '/login-account', [
		'methods' => 'POST',
		'callback' => 'login_account',
		'permission_callback' => '__return_true',
	]);
	register_rest_route('my/v1', '/register-account', [
		'methods' => 'POST',
		'callback' => 'register_account',
		'permission_callback' => '__return_true',
	]);
	register_rest_route('my/v1', '/order-product', [
		'methods' => 'POST',
		'callback' => 'order_product',
		'permission_callback' => '__return_true',
	]);
	register_rest_route('my/v1', '/ordering', [
		'methods' => 'get',
		'callback' => 'get_ordering',
		'permission_callback' => '__return_true',
	]);
	register_rest_route('my/v1', '/history-order', [
		'methods' => 'get',
		'callback' => 'get_order_history',
		'permission_callback' => '__return_true',
	]);
	register_rest_route('my/v1', '/user/(?P<id>\d+)', [
		'methods' => 'get',
		'callback' => 'get_user',
		'permission_callback' => '__return_true',
	]);
	register_rest_route('my/v1', '/favorite/product', [
		'methods' => 'post',
		'callback' => 'set_favorite_product',
		'permission_callback' => '__return_true',
	]);
	register_rest_route('my/v1', '/follow/user', [
		'methods' => 'post',
		'callback' => 'update_follow_user',
		'permission_callback' => '__return_true',
	]);
	register_rest_route('my/v1', '/profile-image', [
		'methods' => 'post',
		'callback' => 'update_profile_image',
		'permission_callback' => '__return_true',
	]);
	register_rest_route('my/v1', '/update-user-profile', [
		'methods' => 'post',
		'callback' => 'updateUserInfor',
		'permission_callback' => '__return_true',
	]);
	register_rest_route('my/v1', '/upload-product', [
		'methods' => 'post',
		'callback' => 'uploadProduct',
		'permission_callback' => '__return_true',
	]);
	register_rest_route('my/v1', '/category', [
		'methods' => 'get',
		'callback' => 'getCategory',
		'permission_callback' => '__return_true',
	]);
	register_rest_route('my/v1', '/update-product', [
		'methods' => 'post',
		'callback' => 'updateProduct',
		'permission_callback' => '__return_true',
	]);
	
});
// Get single product
function get_product($params)
{
	$AUTH_USER = 'admin';
	$AUTH_PASS = 'admin';
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
	$is_not_authenticated = (!$has_supplied_credentials ||
		$_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
		$_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
	);
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		return [
			'data' => [
				'status' => 400,
				'message' => 'Authorization Required'
			],
		];

		exit;
	}

	$product = get_post($params['id']);
	$gallery_data = get_post_meta($params['id'], 'gallery_data', false);
	$product->gallery_data = $gallery_data;
	$product->author_avatar = get_avatar_url($params['id']);
	$product->author_name   = get_userdata($product->post_author)->display_name;
	return [
		'data' => [
			'product' => $product,
			'status' => 200,
			'message' => 'get products successfully'
		],
	];
}

// login account
function login_account($request)
{
	$creds = [];
	$creds['user_login'] = $request["user"];
	$creds['user_password'] =  $request["password"];
	$creds['remember'] = true;
	$user = wp_signon($creds, false);

	if (is_wp_error($user))
		return rest_ensure_response([
			'login' => 0,
			'msg'   => $user->get_error_message()
		]);


	wp_set_current_user($user->ID);
	wp_set_auth_cookie($user->ID, true);
	$nonce = wp_create_nonce('wp_rest');

	if (is_user_logged_in()) {
		$current_user = 'Y';
	} else {
		$current_user = 'N';
	}

	return rest_ensure_response([
		'login' => 1,
		'id' => $user->ID,
		'nonce' => $nonce,
		'is_user_logged_in' => $current_user,
		'msg'   => 'You have successfully logged in'
	]);
}

// register account
function register_account($request)
{
	//create user and get ID
	$new_user = [];
	$new_user['username'] = $request['username'];
	$new_user['email'] = $request['email'];
	$new_user['password'] = $request['password'];

	$user = wp_insert_user([
		'user_login' => $new_user['username'],
		'user_email' => $new_user['email'],
		'user_pass' => $new_user['password']
	]);

	if (is_wp_error($user)) {
		return rest_ensure_response([
			'status' => 2,
			'msg' => $user->get_error_message()
		]);
	}

	return rest_ensure_response(['status' => 1, 'msg' => 'User created']);
}

// order products
function order_product($request)
{
	// authencation
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

	$creds = [];
	$creds['user_login'] = $_SERVER['PHP_AUTH_USER'];
	$creds['user_password'] =  $_SERVER['PHP_AUTH_PW'];
	$creds['remember'] = true;
	$user = wp_signon($creds, false);
	$is_not_authenticated = (!$has_supplied_credentials || is_wp_error($user)
	);
	// $is_not_authenticated=false;
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		return rest_ensure_response(['status' => 401, 'msg' => 'Authorization required']);
		exit;
	}
	//create user and get ID
	$new_order = [];
	$new_order['productArray'] 	= $request['productArray'];
	$new_order['total_price'] 	= $request['total_price'];

	$new_order['customerId'] 	= $request['customerId'];
	$new_order['address'] 		= $request['address'];
	$new_order['paid']			= $request['paid'];
	$new_order['received']		= $request['received'];

	$new_order['sellerId'] 		= $request['sellerId'];
	$new_order['shipped'] 		= $request['shipped'];

	//create new post


	$uniqid = uniqid();

	$rand_start = rand(1, 5);

	$rand_8_char = substr($uniqid, $rand_start, 8);
	$t = time();
	$title = $t . $rand_8_char;

	$args = array(
		'post_type' => 'order',
		'post_title' => strtoupper($title),
		'post_status'   => 'private',
	);
	$post_id = wp_insert_post($args);

	// $a = [];

	// // customer
	update_post_meta($post_id, 'customer_id', $new_order['customerId']);
	update_post_meta($post_id, 'get_address', $new_order['address']);
	update_post_meta($post_id, 'paid', $new_order['paid']);
	update_post_meta($post_id, 'received', $new_order['received']);
	// // // seller
	update_post_meta($post_id, 'seller_id', $new_order['sellerId']);
	update_post_meta($post_id, 'shipped', $new_order['shipped']);
	// // //product
	update_post_meta($post_id, 'total_price', $new_order['total_price']);
	foreach ($new_order['productArray'] as $k => $v) {
		$link_product = get_permalink((int)$v['productId']);
		$url_thumnail = get_the_post_thumbnail_url((int)$v['productId']);
		$title = get_the_title((int)$v['productId']);
		add_post_meta($post_id, 'order_product', ([
			'product_name' 			=> $title,
			'url_thumnail'			=> $url_thumnail,
			'product_link' 			=> $link_product,
			'product_number' 		=> $v['number'],
			'product_price' 		=> $v['price'],
			'product_total_price' 	=> $v['result_price'],
			'post_author'			=> $user->ID,
		]));
	}

	return rest_ensure_response(['status' => 201, 'msg' => 'Order created', 'data' => $new_order]);
}

// Get ordering
function get_ordering($request)
{
	// authencation
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

	$creds = [];
	$creds['user_login'] = $_SERVER['PHP_AUTH_USER'];
	$creds['user_password'] =  $_SERVER['PHP_AUTH_PW'];
	$creds['remember'] = true;
	$user = wp_signon($creds, false);
	$is_not_authenticated = (!$has_supplied_credentials || is_wp_error($user)
	);
	// $is_not_authenticated=false;
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		return rest_ensure_response(['status' => 401, 'msg' => 'Authorization required']);
		exit;
	}
	$wp_posts = new WP_Query([
		"post_type"	=> "order",
		"posts_per_page" => -1,
		'orderby' => 'post_date',
		// 'order' => 'asc',
		'post_status' => array(                 //(string / array)       
			'publish',
			'private',                      // - Bài viết đang trong trạng thái riêng tư
		),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'customer_id',
				'value' => $user->ID,
				'compare' => 'LIKE'
			),
			array(
				'key' => 'received',
				'value' => '1',
				'compare' => 'NOT LIKE'
			)
		),
	]);
	$posts = $wp_posts->posts;
	$post_meta = [];
	foreach ($posts as &$p) {
		$p->order_product 		= get_post_meta($p->ID, 'order_product', false);
		$p->total_price 		= get_post_meta($p->ID, 'total_price', true);
		$p->status_order 		= get_post_meta($p->ID, 'status_order', true);
		$p->customer_id 		= (int)get_post_meta($p->ID, 'customer_id', true);
		$p->address_get_product =  get_post_meta($p->ID, 'get_address', true);
		$p->paid 				= get_post_meta($p->ID, 'paid', true);
		$p->received 			=  get_post_meta($p->ID, 'received', true);
		$p->seller_id 			= (int)get_post_meta($p->ID, 'seller_id', true);
		$p->shipped 			= get_post_meta($p->ID, 'shipped', true);
	}


	return rest_ensure_response(['status' => 1, 'msg' => 'get data success!', 'data' => $posts,]);
}
// Get ordering history
function get_order_history($request)
{
	// authencation
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

	$creds = [];
	$creds['user_login'] = $_SERVER['PHP_AUTH_USER'];
	$creds['user_password'] =  $_SERVER['PHP_AUTH_PW'];
	$creds['remember'] = true;
	$user = wp_signon($creds, false);
	$is_not_authenticated = (!$has_supplied_credentials || is_wp_error($user)
	);
	// $is_not_authenticated=false;
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		return rest_ensure_response(['status' => 401, 'msg' => 'Authorization required']);
		exit;
	}
	$wp_posts = new WP_Query([
		"post_type"	=> "order",
		"posts_per_page" => -1,
		'orderby' => 'post_date',
		// 'order' => 'asc',
		'post_status' => array(                 //(string / array)       
			'publish',
			'private',                      // - Bài viết đang trong trạng thái riêng tư
		),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'customer_id',
				'value' => $user->ID,
				'compare' => 'LIKE'
			),
			array(
				'key' => 'received',
				'value' => '1',
				'compare' => 'LIKE'
			)
		),
	]);
	$posts = $wp_posts->posts;
	$post_meta = [];
	foreach ($posts as &$p) {
		$p->order_product 		= get_post_meta($p->ID, 'order_product', false);
		$p->total_price 		= get_post_meta($p->ID, 'total_price', true);
		$p->status_order 		= get_post_meta($p->ID, 'status_order', true);
		$p->customer_id 		= (int)get_post_meta($p->ID, 'customer_id', true);
		$p->address_get_product =  get_post_meta($p->ID, 'get_address', true);
		$p->paid 				= get_post_meta($p->ID, 'paid', true);
		$p->received 			=  get_post_meta($p->ID, 'received', true);
		$p->seller_id 			= (int)get_post_meta($p->ID, 'seller_id', true);
		$p->shipped 			= get_post_meta($p->ID, 'shipped', true);
	}


	return rest_ensure_response(['status' => 1, 'msg' => 'get data success!', 'data' => $posts,]);
}
// Get user
function get_user($request)
{
	// authencation
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

	$creds = [];
	$creds['user_login'] = $_SERVER['PHP_AUTH_USER'];
	$creds['user_password'] =  $_SERVER['PHP_AUTH_PW'];
	$creds['remember'] = true;
	$user = wp_signon($creds, false);
	$is_not_authenticated = (!$has_supplied_credentials || is_wp_error($user)
	);
	// $is_not_authenticated=false;
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		return rest_ensure_response(['status' => 401, 'msg' => 'Authorization required']);
		exit;
	}
	$id = $request["id"];
	$user =  get_userdata($id);
	// add_user_meta( $id, 'following',1);
	$user_meta = get_user_meta($id);
	$user = $user->data;
	$following = get_user_meta($id, 'following', false);
	$follower = get_user_meta($id, 'follower', false);
	$user->following = $following;
	$user->follower = $follower;
	$avatar = get_user_meta($id, 'avatar', false);
	if (!$avatar) {
		$avatar = get_avatar_url($id);
	}
	$user->avatar = $avatar;

	$products = new WP_Query([
		"post_type"	=> "product",
		"posts_per_page" => -1,
		'orderby' => 'post_date',
		// 'order' => 'asc',
		'post_status' => array(                 //(string / array)       
			'publish',
			'private',                      // - Bài viết đang trong trạng thái riêng tư
		),
		'author' => $id
	]);
	$pro = $products->posts;
	foreach ($pro as &$p) {
		$gallery_data = get_post_meta($p->ID, 'gallery_data', false);
		$gia = get_post_meta($p->ID, 'gia', true);
		$p->gallery_data = $gallery_data;
		$p->gia = $gia;
		$mo_ta = get_post_meta($p->ID, 'mo_ta', true);
		$p->mo_ta = $mo_ta;
		$so_san_pham_da_ban = get_post_meta($p->ID, 'so_san_pham_da_ban', true);
		$p->so_san_pham_da_ban = $so_san_pham_da_ban;
		$con_hang = get_post_meta($p->ID, 'con_hang', true);
		$p->con_hang = $con_hang;
		$p->author_avatar = get_user_meta($p->ID, 'avatar', false);
		if (!$p->author_avatar) {
			$p->author_avatar = get_avatar_url($p->ID);
		}
		$p->author_name   = get_userdata($p->post_author)->display_name;
		$p->url_thumnail = get_the_post_thumbnail_url($p->ID);
		$favorite = get_post_meta($p->ID, 'favorite', false);
		$p->favorite = $favorite;
		$meta=get_the_category($p->ID);
		$p->category =$meta;
		$p->address = get_post_meta($p->ID, 'address', true);
		$p->province = get_post_meta($p->ID, 'province', true);
		$p->district = get_post_meta($p->ID, 'district', true);
		$p->ward = get_post_meta($p->ID, 'ward', true);
		$thumnail_id = get_post_meta($p->ID,'_thumbnail_id',true);
		$p->thumbnail_id = $thumnail_id;
	}
	$user->products = $pro;
	$user->number_products = $products->found_posts;
	$user_meta = get_user_meta($id);
	$user->phone = get_user_meta($id, 'phone', true);
	$user->address = get_user_meta($id, 'address', true);
	$user->email = get_user_meta($id, 'email', true);
	$user->facebook = get_user_meta($id, 'facebook', true);
	$user->description = get_user_meta($id, 'description', true);
	$user->money = get_user_meta($id, 'money', true);
	
	return rest_ensure_response(['status' => 200, 'msg' => 'get data success!', 'data' => $user,'meta'=>$meta]);
}
// set favorite product
function set_favorite_product($request)
{
	// authencation
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

	$creds = [];
	$creds['user_login'] = $_SERVER['PHP_AUTH_USER'];
	$creds['user_password'] =  $_SERVER['PHP_AUTH_PW'];
	$creds['remember'] = true;
	$user = wp_signon($creds, false);
	$is_not_authenticated = (!$has_supplied_credentials || is_wp_error($user)
	);
	// $is_not_authenticated=false;
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		return rest_ensure_response(['status' => 401, 'msg' => 'Authorization required']);
		exit;
	}
	$productId = $request["productId"];
	$favorite = get_post_meta($productId, 'favorite', false);
	if (count($favorite) == 0 || !in_array((string)$user->ID, $favorite)) {
		add_post_meta($productId, 'favorite', (string) $user->ID);
	} else {
		delete_post_meta($productId, 'favorite', (string)$user->ID);
	}
	$favorite = get_post_meta($productId, 'favorite', false);
	return rest_ensure_response(['status' => 200, 'msg' => 'update data success!', 'favorite' => $favorite]);
}
// update follow user
function update_follow_user($request)
{
	// authencation
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

	$creds = [];
	$creds['user_login'] = $_SERVER['PHP_AUTH_USER'];
	$creds['user_password'] =  $_SERVER['PHP_AUTH_PW'];
	$creds['remember'] = true;
	$user = wp_signon($creds, false);
	$is_not_authenticated = (!$has_supplied_credentials || is_wp_error($user)
	);
	// $is_not_authenticated=false;
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		return rest_ensure_response(['status' => 401, 'msg' => 'Authorization required']);
		exit;
	}
	// update who follow them
	$followerId = $request["followerId"];
	$follower = get_user_meta($followerId, 'follower', false);
	if (count($follower) == 0 || !in_array((string)$user->ID, $follower)) {
		add_user_meta($followerId, 'follower', (string) $user->ID);
	} else {
		delete_user_meta($followerId, 'follower', (string)$user->ID);
	}
	// update following for user
	$following = get_user_meta($user->ID, 'following', false);
	if (count($following) == 0 || !in_array((string)$user->ID, $following)) {
		add_user_meta($user->ID, 'following', (string) $followerId);
	} else {
		delete_user_meta($user->ID, 'following', (string)$followerId);
	}
	$following = get_user_meta($user->ID, 'following', false);
	$follower = get_user_meta($followerId, 'follower', false);
	return rest_ensure_response(['status' => 200, 'msg' => 'update data success!', 'data' => $follower, 'following' => $following]);
}

// update profile image
function update_profile_image($request)
{
	// authencation
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

	$creds = [];
	$creds['user_login'] = $_SERVER['PHP_AUTH_USER'];
	$creds['user_password'] =  $_SERVER['PHP_AUTH_PW'];
	$creds['remember'] = true;
	$user = wp_signon($creds, false);
	$is_not_authenticated = (!$has_supplied_credentials || is_wp_error($user)
	);
	// $is_not_authenticated=false;
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		return rest_ensure_response(['status' => 401, 'msg' => 'Authorization required']);
		exit;
	}

	uploadFile($user);
}

function uploadFile($user)
{
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/media.php');
	$file_extension_type = array('jpg', 'jpeg', 'jpe', 'gif', 'png');
	$file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
	if (!in_array($file_extension, $file_extension_type)) {
		return rest_ensure_response(['status' => 401, 'msg' => 'uploaded file is not a valid file Please try again!', 'data' => $_FILES['image']['name']]);
	}
	$attachment_id = media_handle_upload('image', null, []);
	if (is_wp_error($attachment_id)) {
		return wp_send_json(
			['status' => 401, 'msg' => $attachment_id->get_error_message(), 'data' => $_FILES['image']['name']]
		);
	}

	if (isset($post_data['context']) && isset($post_data['theme'])) {
		if ('custom-background' == $post_data['context']) {
			update_post_meta($attachment_id, '_wp_attachment_is_custom_background', $post_data['theme']);
		}
		if ('custom-header' == $post_data['context']) {
			update_post_meta($attachment_id, '_wp_attachment_is_custom_header', $post_data['theme']);
		}
	}

	$attachment = wp_prepare_attachment_for_js($attachment_id);

	if (!$attachment) {
		return wp_send_json(
			['status' => 200, 'msg' => 'image cannot be uploaded', 'data' => $_FILES['image']['name']]
		);
	}

	update_user_meta($user->ID, 'avatar', $attachment['sizes']['full']['url']);
	$avatar = get_user_meta($user->ID, 'avatar', false);
	$ava = get_avatar_url($user->ID);

	return wp_send_json(
		['status' => 200, 'msg' => 'success', 'data' => $attachment, 'user' => $user, 'avatar' => $avatar, 'ava' => $ava]
	);
}

// update user infor

function updateUserInfor($request)
{
	// authencation
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

	$creds = [];
	$creds['user_login'] = $_SERVER['PHP_AUTH_USER'];
	$creds['user_password'] =  $_SERVER['PHP_AUTH_PW'];
	$creds['remember'] = true;
	$user = wp_signon($creds, false);
	$is_not_authenticated = (!$has_supplied_credentials || is_wp_error($user));
	// $is_not_authenticated=false;
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		return rest_ensure_response(['status' => 401, 'msg' => 'Authorization required']);
		exit;
	}
	$new_infor = [];
	$new_infor['user_name'] 		= $request['user_name'];
	$new_infor['user_phone'] 		= $request['user_phone'];
	$new_infor['user_email'] 		= $request['user_email'];
	$new_infor['user_address'] 		= $request['user_address'];
	$new_infor['user_facebook'] 	= $request['user_facebook'];
	$new_infor['user_description'] 	= $request['user_description'];

	$userdata = array(
		'ID' => $user->ID,
		'display_name' => $new_infor['user_name'],
	);
	wp_update_user($userdata);
	update_user_meta($user->ID, 'address', $new_infor['user_address']);
	update_user_meta($user->ID, 'phone', $new_infor['user_phone']);
	update_user_meta($user->ID, 'email', $new_infor['user_email']);
	update_user_meta($user->ID, 'facebook', $new_infor['user_facebook']);
	update_user_meta($user->ID, 'description', $new_infor['user_description']);



	return rest_ensure_response(['status' => 201, 'msg' => 'update user infor success', 'data' => $new_infor]);
}



// update user infor

function uploadProduct($request)
{
	// authencation
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

	$creds = [];
	$creds['user_login'] = $_SERVER['PHP_AUTH_USER'];
	$creds['user_password'] =  $_SERVER['PHP_AUTH_PW'];
	$creds['remember'] = true;
	$user = wp_signon($creds, false);
	$is_not_authenticated = (!$has_supplied_credentials || is_wp_error($user));
	// $is_not_authenticated=false;
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		return rest_ensure_response(['status' => 401, 'msg' => 'Authorization required']);
		exit;
	}

	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');


	$files = $_FILES['images'];
	$attachment = [];
	$attachment_id_arr = [];

	foreach ($files['name'] as $key => $value) {
		if ($files['name'][$key]) {
			$file = array(
				'name' => $files['name'][$key],
				'type' => $files['type'][$key],
				'tmp_name' => $files['tmp_name'][$key],
				'error' => $files['error'][$key],
				'size' => $files['size'][$key]
			);
			$_FILES = array("upload_file" => $file);
			$attachment_id = media_handle_upload("upload_file", 0);
			$attachment_id_arr[] = $attachment_id; 
			$attachment[] =  wp_prepare_attachment_for_js($attachment_id);
		}
	}

	$productAddress = $request['productAddress'];
	$productCategory = $request['productCategory'];
	$productDescription = $request['productDescription'];
	$productName = $request['productName'];
	$productPrice = $request['productPrice'];
	$province = $request['productProvince'];
	$district = $request['productDistrict'];
	$ward = $request['productWard'];

	$my_post = array(
		'post_title'    => wp_strip_all_tags($productName),
		'post_content'  => $productDescription,
		'post_status'   => 'publish',
		'post_author'   => $user->ID,
		'post_category' => array($productCategory),
		'post_type' 	=> 'product',
	);

	// Insert the post into the database
	$p = wp_insert_post($my_post);

	CFS()->save([
		'gia' => $productPrice,
		'mo_ta' => $productDescription,
		'so_san_pham_da_ban' => 0,
		'con_hang' => 1,
		'address' => $productAddress,
		'province' => $province,
		'district' => $district,
		'ward' => $ward,

	], [
		'ID' => $p
	]);

	
	update_post_meta($p, '_thumbnail_id', $attachment_id_arr[0]);


	for ($i = 0; $i < count($attachment); $i++) {

		if (str_contains($attachment[$i]['sizes']['full']['url'], 'localhost')) {
			$img = str_replace('https', 'http', $attachment[$i]['sizes']['full']['url']);
			add_post_meta(
				$p,
				'gallery_data',
				([
					'image_url' => $img,
					'description' => '',
					'note' => '',
				])
			);
		} else {
			add_post_meta(
				$p,
				'gallery_data',
				([
					'image_url' => $attachment[$i]['sizes']['full']['url'],
					'description' => '',
					'note' => '',
				])
			);
		}
	}


	return rest_ensure_response(['status' => 201, 'msg' => 'upload image success', 'files' => $attachment, 'product' => $p,'district' => $district]);
}


// get category

function getCategory($request)
{
	// authencation
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

	$creds = [];
	$creds['user_login'] = $_SERVER['PHP_AUTH_USER'];
	$creds['user_password'] =  $_SERVER['PHP_AUTH_PW'];
	$creds['remember'] = true;
	$user = wp_signon($creds, false);
	$is_not_authenticated = (!$has_supplied_credentials || is_wp_error($user));
	// $is_not_authenticated=false;
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		return rest_ensure_response(['status' => 401, 'msg' => 'Authorization required']);
		exit;
	}



	$categories = get_terms(array(
		'taxonomy' => 'category',
		'hide_empty' => false,
		'parent' => 0,
	));
	// get_term_featured_image($term_id);
	foreach ($categories as &$c) {
		$meta = get_term_meta($c->term_id, false);
		$c->featured_image_id = wp_get_attachment_image_src((int) $meta['featured_image_id'][0]);
	}

	return rest_ensure_response(['status' => 200, 'msg' => 'get category success', 'data' => $categories]);
}



function updateProduct($request)
{
	// authencation
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

	$creds = [];
	$creds['user_login'] = $_SERVER['PHP_AUTH_USER'];
	$creds['user_password'] =  $_SERVER['PHP_AUTH_PW'];
	$creds['remember'] = true;
	$user = wp_signon($creds, false);
	$is_not_authenticated = (!$has_supplied_credentials || is_wp_error($user));
	// $is_not_authenticated=false;
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		return rest_ensure_response(['status' => 401, 'msg' => 'Authorization required']);
		exit;
	}
	$productId = $request['productId'];
	$productAddress = $request['productAddress'];
	$productCategory = $request['productCategory'];
	$productDescription = $request['productDescription'];
	$productName = $request['productName'];
	$productPrice = $request['productPrice'];
	$province = $request['productProvince'];
	$district = $request['productDistrict'];
	$ward = $request['productWard'];
	$productThumnail = $request['productThumnail'];

	$object = [
		'productId'=>$productId,
		'productAddress'=>$productAddress,
		'productCategory'=>$productCategory,
		'productDescription'=>$productDescription,
		'productName'=>$productName,
		'productPrice'=>$productPrice,
		'province'=>$province,
		'district'=>$district,
		'ward'=>$ward,
		'productThumnail' => $productThumnail,
	];

	CFS()->save([
		'gia' => $productPrice,
		'mo_ta' => $productDescription,
		'so_san_pham_da_ban' => 0,
		'con_hang' => 1,
		'address' => $productAddress,
		'province' => $province,
		'district' => $district,
		'ward' => $ward,

	], [
		'ID' => $productId
	]);


	wp_update_post( array(
		'ID'           => $productId,
		'post_title'    => wp_strip_all_tags($productName),
		'post_content'  => $productDescription,
		
	) );
	update_post_meta($productId, '_thumbnail_id', $productThumnail);
	wp_set_post_categories( $productId, array( $productCategory ), false );


	return rest_ensure_response(['status' => 200, 'msg' => 'update product success','object'=>$object]);
}