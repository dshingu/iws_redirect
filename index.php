<?php 

/*
* Plugin Name: Redirect by IWS
* Description: Redirects from the jobs page if the user has not uploaded a resume
* Version: 1.0
* Author: Dane Shingu
* Author URI: mailto:dane.shingu@gmail.com
*/


if (!function_exists('add_action')) {

    echo "You are not allowed here!";
    exit;

}

add_action('init', function() {
    ob_start();
});

add_action('wp_head', function() {

    global $wpdb;

    if (is_user_logged_in()) {

        $user = wp_get_current_user();

        $resume  = new WP_Query([
            'post_type' => 'resume',
            'author' => $user->ID,
            'posts_per_page' => 1
        ]);

        if (is_page('jobs') && (count($resume->posts) < 1)) {
            $submit_url = get_bloginfo('url') . '/submit-resume';
            set_transient('iws_redirect_error', "You need to upload a resume before viewing the requested page!", 60*60*12);
            wp_redirect($submit_url);
            exit;
        }


        if (get_transient('iws_redirect_error')) {

            $msg = get_transient('iws_redirect_error');

            echo <<< IWS_REDIRECT_MSG
                <div class='iws_redirect_error' style='background-color:red; color:#fff; text-align:center;'><p>$msg</p></div>
            IWS_REDIRECT_MSG;

        }

        delete_transient('iws_redirect_error');

    }
    
});