<?php
/*
Plugin Name: MD Responsive WordPress Slider
Plugin URI: http://md-responsive.freeiz.com/
Description: MD Responsive jQuery Slider for WordPress
Author: Marginean Doru
Author URI: http://md-responsive.freeiz.com/
Version: 1.0
License: GPLv2
*/
define("PLUGIN_URL", plugins_url() .'/md-responsive-slider');

add_action('init', 'md_add_post_type');
// Custom slider menu 
function md_add_post_type(){
		register_post_type( 'md-slider', array(
			'public' => true,
			'label' => "MD Responsive",
			'labels' => array(
				'add_new_item' => "Add New Slide",
				'edit_item'          => __( 'Edit Slide' ),
				'new_item'           => __( 'New Slide' ),
				'all_items'          => __( 'All Slides' ),
				'view_item'          => __( 'View Slide' ),
				'search_items'       => __( 'Search Slides' ),
				'not_found'          => __( 'No Slides found' ),
				'not_found_in_trash' => __( 'No Slides found in the Trash' ), 
			),
			'supports' => array('title', 'editor', 'excerpt', 'thumbnail'),
			//'taxonomies' => array('post_tag', 'category'),	// uncomment if you want to add tags and categories to  the slides
			'menu_icon' => plugins_url( 'md-slider.png', __FILE__ ),
));

add_shortcode('md_slider', 'md_slider_output');

add_action('admin_menu', 'register_my_custom_submenu_page');

function register_my_custom_submenu_page() {
	add_submenu_page( 'edit.php?post_type=md-slider', 'Settings', 'Settings', 'manage_options', 'md-slider-submenu-page', 'md_slider_submenu_page_callback' ); 
}

function md_slider_submenu_page_callback() {
	$options = get_option('md_slider_options');
	
	wp_register_style( 'myOptionsPageStylesheet', plugins_url('/css/md_slider_admin.css', __FILE__) );
	wp_enqueue_style( 'myOptionsPageStylesheet' );
	?>

	<div class="wrap md-slider">
    	<h2>Settings</h2>
			<?php if (isset( $_GET['message'] ) && $_GET['message'] == '1') { ?>
            <div id='message' class='updated fade'><p><strong>Settings Saved</strong></p></div>
            <?php } ?>
        <form method="post" action="admin-post.php">
        <input type="hidden" name="action"
        value="save_md_slider_options" />
        <!-- Adding security through hidden referrer field -->
        <?php wp_nonce_field( 'md_slider' ); ?>
        <p>
            <label for="autoplay">Autoplay</label>
            <input type="checkbox" name="autoplay" id="autoplay" <?php if ( $options['autoplay'] ) echo ' checked="checked" '; ?>/> 
        </p>
        <p>
            <label for="width">Width</label>
            <input type="text" name="width" id="width" value="<?php echo esc_html( $options['width'] ); ?>"/> 
        </p>
        <p>
            <label for="height">Height</label>
            <input type="text" name="height" id="height" value="<?php echo esc_html( $options['height'] ); ?>"/> 
        </p>
        <p>
            <label for="speed">Transition speed</label>
            <input type="text" name="speed" id="speed" value="<?php echo esc_html( $options['speed'] ); ?>"/> 
        </p>
        <p>
            <label for="title">Title</label>
            <input type="checkbox" name="title" id="title" <?php if ( $options['title'] ) echo ' checked="checked" '; ?> /> 
        </p>
        <p>
            <label for="description">Description</label>
            <input type="checkbox" name="description" id="description" <?php if ( $options['description'] ) echo ' checked="checked" '; ?> /> 
        </p>
        <p>
            <label for="timer">Time delay</label>
            <input type="text" name="timer" id="timer" value="<?php echo esc_html( $options['timer'] ); ?>"/> 
        </p>
        <p>
            <label for="bullets">Bullet navigation</label>
            <input type="checkbox" name="bullets" id="bullets" <?php if ( $options['bullets'] ) echo ' checked="checked" '; ?>/> 
        </p>
        <p>
            <label for="arrows">Arrow navigation</label>
            <input type="checkbox" name="arrows" id="arrows" <?php if ( $options['arrows'] ) echo ' checked="checked" '; ?> /> 
        </p>
        <p>
       		<label for="pages">Show on page</label>
            <select name="pages">
           	<option value="select">Select a page</option>
			<?php
            // args
			$args2 = array(
				'post_type' => 'page'
			);
			// The Query
			$query = new WP_Query( $args2 );
            
            // The Loop
            while ( $query->have_posts() ) : $query->the_post();
			if($options['pages'] == get_the_title()){
				$selected = "selected";	
				echo '<option value="'. get_the_title() .'" '. $selected .'>'; 
			}else{
                echo '<option value="'. get_the_title() .'">';
			}
				the_title();
                echo '</option>';
            endwhile;
            
            // Reset Query
            wp_reset_query();
			wp_reset_postdata();
            
            ?>      
            </select>  
        </p>     
        <input type="submit" value="Save" class="button-primary"/>
        </form>
		<h3>Show the slider</h3>
        <p>Copy and paste this code into your page:</p>
        <pre>
&lt;?php md_slider_output()&#59; ?>
        </pre>
    </div>
<?php }  
  
} 
    /**
     * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
     */
    add_action( 'wp_enqueue_scripts', 'md_add_plugin_stylesheet' );
    /**
     * Enqueue plugin style-file
     */
    function md_add_plugin_stylesheet() {
        // Respects SSL, Style.css is relative to the current file
        wp_register_style( 'md-plugin-style', plugins_url('/css/md_slider.css', __FILE__) );
        wp_enqueue_style( 'md-plugin-style' );
    }
	
	/**
	* Add the javascript for the plugin
	*/
	function md_add_javascript() {
		wp_enqueue_script(
			'newscript',
			plugins_url('/js/md_slider.js', __FILE__),
			array('jquery')
		);
	}    
    /**
     * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
     */
	add_action('wp_enqueue_scripts', 'md_add_javascript');
	
function md_slider_output(){
	?>
	<script>
		<?php 
		$options = get_option('md_slider_options');
		?>
			jQuery.noConflict();
			jQuery(document).ready( function md_responsive_slider($){
				// Settings
				var width = $('#md-slider-container').parent().width(); 														// set slider width
				var maxWidth = <?php echo esc_html( $options['width'] ); ?>; 													// set maximum slider width
				var maxHeight = <?php echo esc_html( $options['height'] ); ?>; 													// set maximum slider height
				var height = width * maxHeight / maxWidth; 	
				var transitionSpeed = <?php echo esc_html( $options['speed'] ); ?>; 											// slide transition speed in miliseconds  
				var timer = <?php echo esc_html( $options['timer'] ); ?>;														// auto slide speed in miliseconds (4 seconds)
				var current = 1;
				var bullets = <?php if ( $options['bullets'] ){ echo 'true'; }else{ echo 'false';} ?>;							// set the visibilty of bullets (true/false)
				var auto =  <?php if ( $options['autoplay'] ){ echo 'true'; }else{ echo 'false';} ?>;							// set the auto slide (true/false)
				var slides = $('#md-slider li').length; 			// count the number of slides
			md_slider_function(width, maxWidth, maxHeight, height, transitionSpeed, timer, current, bullets, auto, slides);
			});	
		</script>
	<?php	
 
	
	$options = get_option('md_slider_options');

	echo '<div id="md-slider-container"><div id="md-slider">';
	if ( $options['arrows'] ){ 

				echo '<div id="prev"><img src="'. PLUGIN_URL .'/images/prev.png" /></div>
		<div id="next"><img src="'. PLUGIN_URL .'/images/next.png" /></div>';
	} 
    echo '<ul id="md-content">';
	$args = array( 'post_type' => 'md-slider', 'posts_per_page' => 10, 'order' => 'ASC' );
	$loop = new WP_Query( $args );
		
	while ( $loop->have_posts() ) : $loop->the_post();
		$theTitle = get_the_title();
		$thePermalink = get_permalink($id);
		$theThumbnail = get_the_post_thumbnail();
		echo '<li>';
		if ( $options['title'] ){
			echo "<a href='$thePermalink'><h1>$theTitle</h1></a>";
		}
		if ( $options['description'] ){
			the_excerpt();
		}
			echo "<a href='$thePermalink'>$theThumbnail</a>";	         
		echo '</li>';

	endwhile;
	// Reset Query
    wp_reset_query();
	wp_reset_postdata();
	
	echo '</ul></div>';

    echo '<ul id="navigation">
    <div style="clear:both; display:block"></div>
    </ul></div>';
	
	}

/*****************************************************************
 * Storing slider settings using arrays *
 *****************************************************************/

register_activation_hook( __FILE__, 'md_set_default_options_array' );

function md_set_default_options_array() {
	if ( get_option( 'md_slider_options' ) === false ) {
		$new_options['autoplay'] = false;
		$new_options['width'] = "960";
		$new_options['height'] = "390";
		$new_options['speed'] = "1000";
		$new_options['timer'] = "5000";
		$new_options['bullets'] = true;
		$new_options['arrows'] = true;	
		$new_options['title'] = true;	
		$new_options['description'] = true;	
		$new_options['pages'] = "";	
		$new_options['version'] = "1.1";
		add_option( 'md_slider_options', $new_options );
	} else {
		$existing_options = get_option( 'md_slider_options' );
		if ( $existing_options['version'] < 1.1 ) {
			$existing_options['version'] = "1.1";
			update_option( 'md_slider_options', $existing_options );
		}
	}
}

/*****************************************************************
 * Processing and storing admin page post data *
 *****************************************************************/

add_action( 'admin_init', 'md_slider_admin_init' );

function md_slider_admin_init() {
	add_action( 'admin_post_save_md_slider_options',
		 'process_md_slider_options' );
}

function process_md_slider_options() {
	// Check that user has proper security level

	if ( !current_user_can( 'manage_options' ) )
	wp_die( 'Not allowed' );

	// Check that nonce field created in configuration form
	// is present

	check_admin_referer( 'md_slider' );

	// Retrieve original plugin options array
	$options = get_option( 'md_slider_options' );

	// Cycle through all text form fields and store their values
	// in the options array

	foreach ( array( 'width','height','speed','timer' ) as $option_name ) {
		if ( isset( $_POST[$option_name] ) ) {
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
	}
	
	// Cycle through all text select boxes and store their values
	// in the options array

	foreach ( array( 'pages' ) as $option_name ) {
		if ( isset( $_POST[$option_name] ) ) {
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
	}

	// Cycle through all check box form fields and set the options
	// array to true or false values based on presence of
	// variables

	foreach ( array( 'autoplay', 'bullets', 'arrows','title', 'description' ) as $option_name ) {
		if ( isset( $_POST[$option_name] ) ) {
			$options[$option_name] = true;
		} else {
			$options[$option_name] = false;
		}
	}

	// Store updated options array to database
	update_option( 'md_slider_options', $options );

	// Redirect the page to the configuration form that was
	// processed
	wp_redirect( add_query_arg( array( 'page' => 'md-slider-submenu-page', 'message' => '1' ), admin_url( 'edit.php?post_type=md-slider&page=md-slider-submenu-page' ) ) );
	exit;
}
function show_md_slider_on_page(){
$options = get_option('md_slider_options');
if($options['pages'] !=""){
	
	$page_title = $options['pages'];
	//echo $page_title;
	if(is_page( $page_title )){	
		$content = do_shortcode(get_the_content());		
		md_slider_output();
		echo $content;	
	}else{
		 echo do_shortcode(get_the_content());	
	}
	
}
}
add_filter('the_content', show_md_slider_on_page);
?>