<?php
/**
 * Plugin Name: Simple Featured post likes
 * Description: Adds Likes/Favorites to Wordpress post or custom post type.
 * Author: Richard Miles
 * Version: 1.0.1
 * Author URI: http://richymiles.wordpress.com
 */


function get_users_array($post_id) {
    $new_array = array();
    $array = get_post_meta($post_id,'favourite_users');
    if (!$array) {
     	return false;
     } 
    foreach($array[0] as $key => $value)
     $new_array[$key] = $value;
    return $new_array;
}

function count_users_array($post_id) {
    $new_array = array();
    $array = get_post_meta($post_id,'favourite_users');
    if (! $array) {
     	return false;
     } 
     $count = 0;
    foreach($array[0] as $key => $value) {
    	$new_array[$key] = $value;
    	$count++;
    }
       return $count;
    }



function favourates_ajax() {
		if (isset($_REQUEST['post_id'])) {
			$users = get_users_array($_REQUEST['post_id']);
			$liked;
			if ($users) {
				if(!in_array(get_user_id(), $users)) {
					?>
					<?php
					$liked = false;
			array_push($users, get_user_id()); 
			update_post_meta($_REQUEST['post_id'], 'favourite_users', $users);
				} else {
					$liked = true;
					$key = array_search(get_user_id(), $users);
					unset($users[$key]);
					update_post_meta($_REQUEST['post_id'], 'favourite_users', $users);
				}
			} else {
			$users = array(get_user_id());
			update_post_meta($_REQUEST['post_id'], 'favourite_users', $users);
			}
			$count = count_users_array($_REQUEST['post_id']);
			echo json_encode(array('count'=>$count,'liked' => $liked));
	}
	die();
}

add_action('wp_ajax_myFunction' , 'favourates_ajax');
add_action("wp_ajax_nopriv_myFunction", "favourates_ajax"); 

function enqueue_likes_scripts() {
	// wp_localize_script( 'ajax-script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	wp_enqueue_style( 'style', plugins_url('assets/css/style.css', __FILE__) );
}

add_action( 'wp_enqueue_scripts', 'enqueue_likes_scripts' );


// add_action( 'admin_menu', 'register_my_custom_menu_page' );
// function register_my_custom_menu_page() {
// 	// add_menu_page( 'Post Likes', 'Miles Dev', 'manage_options', 'post_likes/admin.php', '', '', 6 );
// }

add_action('admin_menu', 'add_likes_menu');

function add_likes_menu() {
	add_options_page('My Options', 'Favourite Posts', 'manage_options', plugin_dir_path( __FILE__ ) . 'admin.php');
}


add_action('wp_head','simple_likes_ajaxurl');

function simple_likes_ajaxurl() {
?>
<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<?php
}


function get_user_id() {
	if (get_current_user_id()) {
		return get_current_user_id() . "";
	} else {
		$ip;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}
function add_favourates_button($content) {
	global $post;
	$option = get_option( 'favourates_post_type', 'post' ); 
	$class = get_option( 'favourates_element', 'body' );
	    if ($post->post_type == $option) {
	    	ob_start();
	?>
<script>
jQuery(document).ready(function($) {
jQuery('<?php echo $class; ?>').append('<input id="user_favourite" type="button" value="<?php echo count_users_array($post->ID); ?>">');
 
jQuery(document).on('click', '#user_favourite', function(event) {
	event.preventDefault();
jQuery.ajax({
	url: ajaxurl, //key variable that is set in the php enqueue
	type: 'POST',
	data: {
            'action':'myFunction', //myFunction needs to match the function in the php file as well as the add_action for it
            'post_id' : '<?php echo $post->ID; ?>'
        },
})
.done(function(data , status) {
	var obj = jQuery.parseJSON(data);
	var value = jQuery.trim(obj.count);
//status is the http request status - 200 for all good, 404 not found e.t.c
	jQuery('#user_favourite').val(value);
	console.log("success");
	if (obj.liked) {
		jQuery('#user_favourite').css('background' , 'tomato');
	} else {
		jQuery('#user_favourite').css('background' , 'green');
	}
})
.fail(function() {
	alert('Server Error, Could not save Information');
});
});
});
	    	</script>
	    	<?php
	    	// echo get_user_id();
	    	$content .= ob_get_contents();
	    	ob_end_clean();
	    }
	    return $content;
    }
	add_filter('the_content', 'add_favourates_button');