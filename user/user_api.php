<?php
/*
Plugin Name: My User Plugin
Description: Custom user management plugin.
Version: 1.0
*/

function custom_user_shortcode($atts) {
    $attributes = shortcode_atts(array(
        'user_id' => get_current_user_id(),
    ), $atts);

    $user_id = intval($attributes['user_id']);
    
    $user_data = get_userdata($user_id);
    
    if (!$user_data) {
        return "User not found.";
    }
    
    $output = '<div class="user-info">';
    $output .= '<p><strong>Name:</strong> ' . esc_html($user_data->display_name) . '</p>';
    $output .= '<p><strong>Email:</strong> ' . esc_html($user_data->user_email) . '</p>';
    // Add more user data fields as needed
    
    $output .= '</div>';
    
    return $output;
}

add_shortcode('custom_user', 'custom_user_shortcode');

// function custom_user_api_endpoint() {
//     register_rest_route('custom-user-plugin/v1', '/user/(?P<id>\d+)', array(
//         'methods' => 'GET',
//         'callback' => 'custom_user_api_callback',
//     ));

//     register_rest_route('custom-user-plugin/v1', '/create-user', array(
//         'methods' => 'POST',
//         'callback' => 'create_user_api_callback',
//     ));
// }

// function create_user_api_callback($request) {
//     $params = $request->get_params();
    
//     // Perform validation and sanitize input data
//     $username = sanitize_user($params['username']);
//     $email = sanitize_email($params['email']);
//     $password = $params['password'];

//     // Check if the username or email is already taken
//     if (username_exists($username) || email_exists($email)) {
//         return new WP_Error('user_already_exists', 'Username or email already exists.', array('status' => 400));
//     }

//     // Create the user
//     $user_id = wp_create_user($username, $password, $email);

//     if (is_wp_error($user_id)) {
//         return new WP_Error('user_creation_failed', 'User creation failed.', array('status' => 500));
//     }

//     $response = array(
//         'message' => 'User created successfully.',
//         'user_id' => $user_id,
//     );

//     return rest_ensure_response($response);
// }

// add_action('rest_api_init', 'custom_user_api_endpoint');



function delete_user_api_callback($request) {
    $params = $request->get_params();
    $user_id = intval($params['id']);

    // Check if the user exists
    $user = get_user_by('ID', $user_id);

    if (!$user) {
        return new WP_Error('user_not_found', 'User not found.', array('status' => 404));
    }

    // Delete the user
    $deleted = wp_delete_user($user_id);

    if (!$deleted) {
        return new WP_Error('user_deletion_failed', 'User deletion failed.', array('status' => 500));
    }

    $response = array(
        'message' => 'User deleted successfully.',
    );

    return rest_ensure_response($response);
}

function custom_user_api_endpoint() {
    register_rest_route('user/v1', '/delete/(?P<id>\d+)', array(
        'methods' => 'DELETE',
        'callback' => 'delete_user_api_callback',
    ));
}

add_action('rest_api_init', 'custom_user_api_endpoint');
