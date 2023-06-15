<?php
/**
 * Plugin Name: My Plugin
 * Description: Here is the descripton of the plugin and this plugin is creating for practice for this site.
 * Version: 1.0.0
 * Author: Shoaib
 * Author URI: https://www.fiverr.com/majenboo?up_rollout=true
 */

//  if(!defined('ABSPATH')){
//     header("Location: /");
//     die ("");
//  }

function my_plugin_activation(){
    global $wpdb, $table_prefix;
    $wp_emp= $table_prefix. 'emp';

    $q= "CREATE TABLE IF NOT EXISTS `$wp_emp` ( 
        `Id` INT NOT NULL AUTO_INCREMENT , 
        `Name` VARCHAR(50) NOT NULL , 
        `Email` VARCHAR(100) NOT NULL , 
        `Status` BOOLEAN NOT NULL , PRIMARY KEY (`Id`)) ENGINE = InnoDB;";
    $wpdb->query($q);

    // $q = "INSERT INTO `$wp_emp` (`Name`, `Email`, `Status`)
    //     VALUES ('M.Shoaib', 'shoaibshafique784@gmail.com', 1);";
    
    $data = array(
        'name'  => 'Boo',
        'email' => 'Boo@gmail.com',
        'status'=> '1'
    );

    
    $wpdb->insert($wp_emp, $data);

}


function youtube(){
    global $wpdb, $table_prefix;
    $wp_emp= $table_prefix. 'emp';

    $q = "SELECT * FROM `wp_emp`";
    $results = $wpdb->get_results($q);

    // for in array foramte
    // echo'<pre>';
    // print_r($results);
    // echo'</pre>';

    ob_start();
    ?>
        <table>
            <thead>
            <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
        </tr>
            </thead>
            <tbody>
                <?php
                    // foreach($results as $row):
                ?>
                <!-- <tr>
                    <td><//?php echo $row->ID; ?></td>
                    <td><//?php echo $row->Name; ?></td>
                    <td><//?php echo $row->Email; ?></td>
                    <td><//?php echo $row->Phone; ?></td>
                </tr> -->
                <?php
                    // endforeach();
                ?>
            </tbody>
        </table>
    <?php
    $html = ob_get_clean();

    return $html;
}

    add_shortcode('youtube','youtube');

// register_activation_hook(__FILE__, 'my_plugin_activation');

// register_deactivation_hook(__FILE__, 'my_plugin_deactivation');

// function my_custom_scripts(){
//     $path = plugins_url('js/main.js', __FILE__);
//     $dep = array('jquery','');
//     $ver = filemtime(plugin_dir_path(__FILE__). 'js/main.js');
//     wp_enqueue_script('my-custom-js', $path, $dep , $ver, true);        
// }
// add_action('wp_enqueue_scripts', 'my_custom_scripts');
function register_custom_menu_page() {
    add_menu_page(
        'custom menu title', 
        'custom menu', 
        'add_users', 
        'custompage', 
        '_custom_menu_page', 
        null, 6
    ); 
}
add_action('admin_menu', 'register_custom_menu_page');

function _custom_menu_page(){
   echo "Admin Page Test";  
}

function bobcares_plugin_top_menu(){
   add_menu_page('My Plugin', 'My Plugin', 'manage_options', __FILE__, 'bobcares_render_plugin_page', plugins_url('/img/icon.png',__DIR__));
 }
 add_action('admin_menu','bobcares_plugin_top_menu');

function show_business_hours_text() {
    // Set timezone if necessary, replace 'America/New_York' with your timezone
    date_default_timezone_set('America/New_York');

    $current_time = date('H:i');
    $open_time = '09:00';
    $close_time = '17:00';

    if ($current_time >= $open_time && $current_time < $close_time) {
        return 'We are Open. Closed at 5PM.';
    } else {
        return 'We are Closed. Open at 9AM.';
    }
}
add_shortcode('business_hours_text', 'show_business_hours_text');

function register_my_cpt(){
    $labels = array(
        'name' => 'Cars',
        'singular_name' => 'Car'
    );
    $supports = array('title', 'editor', 'thumbnail', 'comments', 'excerpts');
    $options = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug'=> 'Cars'),
        'show_in_rest' => true,
        'supports' => $supports,
        'taxonomies' => array('Car_types')
    );
    register_post_type('Cars',$options);
}

add_action('init' , 'register_my_cpt');

$labels = array(
    'name' => 'Car Type',
    'singular_name' => 'Car Types'
);
function register_Car_types(){

    $options = array(
        'labels' => $labels,
        'hierarchical' => true,
        'rewrite' => array('slug'=> 'Car_type'),
    );
    register_taxonomy('Car_type', array('Cars'), $options);
}

add_action('init' , 'register_Car_types');

function my_register_form(){
    ob_start();
    include 'public/register.php';
    return ob_get_clean();
}
add_shortcode('my_register_form', 'my_register_form' );

function my_login(){
    if(isset($_POST['user_login'])){
        $username = esc_sql($_POST['username']);
        $pass = esc_sql($_POST['pass']);
        $credentials = array(
            'user_login' => $username,
            'user_password' => $pass,
        );
        $user = wp_signon($credentials);
        if(!is_wp_error($user)){
            echo 'Login Success';
        }
        else{
            echo $user->get_error_message();
        }
    }
}
add_action('template_redirect', 'my_login');

// Hook into the cart page display
function custom_cart_weight_column() {
    // Get the cart contents
    $cart = WC()->cart->get_cart();

    // Display the weight column header
    echo '<th class="product-weight">Weight</th>';

    // Loop through each cart item
    foreach ($cart as $cart_item_key => $cart_item) {
        // Get the product weight
        $product = $cart_item['data'];
        $weight = $product->get_weight();

        // Display the weight column value
        echo '<td class="product-weight">' . $weight . '</td>';
    }
}
add_action('woocommerce_cart_contents', 'custom_cart_weight_column');

// Customize the cart page layout
function custom_cart_page_layout() {
    // Enqueue custom CSS for styling the weight column
    wp_enqueue_style('custom-cart-css', plugin_dir_url(__FILE__) . 'css/custom-cart.css');
}
add_action('wp_enqueue_scripts', 'custom_cart_page_layout');

?>

<?php
  
  // Register Custom Post Type Services
function create_services_cpt() {

	$labels = array(
		'name' => _x( 'Custom Posts', 'Post Type General Name', 'textdomain' ),
		'singular_name' => _x( 'Services', 'Post Type Singular Name', 'textdomain' ),
		'menu_name' => _x( 'Custom Posts', 'Admin Menu text', 'textdomain' ),
		'name_admin_bar' => _x( 'Services', 'Add New on Toolbar', 'textdomain' ),
		'archives' => __( 'Services Archives', 'textdomain' ),
		'attributes' => __( 'Services Attributes', 'textdomain' ),
		'parent_item_colon' => __( 'Parent Services:', 'textdomain' ),
		'all_items' => __( 'All Custom Posts', 'textdomain' ),
		'add_new_item' => __( 'Add New Services', 'textdomain' ),
		'add_new' => __( 'Add New', 'textdomain' ),
		'new_item' => __( 'New Services', 'textdomain' ),
		'edit_item' => __( 'Edit Services', 'textdomain' ),
		'update_item' => __( 'Update Services', 'textdomain' ),
		'view_item' => __( 'View Services', 'textdomain' ),
		'view_items' => __( 'View Custom Posts', 'textdomain' ),
		'search_items' => __( 'Search Services', 'textdomain' ),
		'not_found' => __( 'Not found', 'textdomain' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'textdomain' ),
		'featured_image' => __( 'Featured Image', 'textdomain' ),
		'set_featured_image' => __( 'Set featured image', 'textdomain' ),
		'remove_featured_image' => __( 'Remove featured image', 'textdomain' ),
		'use_featured_image' => __( 'Use as featured image', 'textdomain' ),
		'insert_into_item' => __( 'Insert into Services', 'textdomain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Services', 'textdomain' ),
		'items_list' => __( 'Custom Posts list', 'textdomain' ),
		'items_list_navigation' => __( 'Custom Posts list navigation', 'textdomain' ),
		'filter_items_list' => __( 'Filter Custom Posts list', 'textdomain' ),
	);
	$args = array(
		'label' => __( 'Services', 'textdomain' ),
		'description' => __( 'This is services for custom post type with category and tags', 'textdomain' ),
		'labels' => $labels,
		'menu_icon' => 'dashicons-desktop',
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail'),
		'taxonomies' => array('category','post_tag'),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => true,
		'hierarchical' => false,
		'exclude_from_search' => false,
		'show_in_rest' => true,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);
	register_post_type( 'services', $args );

}
add_action( 'init', 'create_services_cpt', 0 );
?>