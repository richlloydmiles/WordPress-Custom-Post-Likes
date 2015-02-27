<?php

global $wpdb;
$results = $wpdb->get_results( 'SELECT `post_type` FROM wp_posts' );
$post_types = array();
foreach ($results as $post_type) {
	array_push($post_types, $post_type->post_type);
}
$post_types = array_unique($post_types);

?>

<div class="wrap">
<h2>Favourite Posts</h2>
	<form action="" method="POST">
<table class="form-table">
	<tr>
		<th scope="row"><label for="content_element">Select Element to append to</label></th>
		<td><input type="text" name="content_element" placeholder="<?php if(get_option( 'favourates_element', 'post' )) {
			echo get_option( 'favourates_element' );
			} ?>"></td>
	</tr>
	<tr>
		<th scope="row"><label for="post_type">Post Types</label></th>
		<td>	<select name="post_type" id="post_type">
	<?php
	foreach ($post_types as $post_type) {
		?>
	<option value="<?php echo $post_type; ?>" <?php if(get_option( 'favourates_post_type' ) == $post_type) {
			echo 'selected';
			} ?>><?php echo $post_type ?></option>
		<?php
	}
	?>
	</select></td>
	</tr>
</table>
	<p>
	<input type="submit" value="Update" class="button button-primary button-large">
	</p>
</form>
<?php 
	if (isset($_POST['post_type'])) {
		$type = $_POST['post_type'];
		$post_ids = $wpdb->get_results( "SELECT `ID` FROM wp_posts WHERE post_type = '$type'" );
			update_option( 'favourates_post_type', $_POST['post_type'], '', 'yes' );
	}
	if (isset($_POST['content_element'])) {
		$type = $_POST['content_element'];
			update_option( 'favourates_element', $type , '', 'yes' );
	}
?>



</div>
