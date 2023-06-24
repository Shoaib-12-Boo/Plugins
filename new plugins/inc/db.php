<?php defined('ABSPATH') || die("Nice Try"); ?>
<php

register_activation_hook(PLUGIN_FILE, function(){
    global $wpdb;

    $sql = "CREATE TABLE `new-plugins`.`wp_likedislike` 
    ( `Id` INT NOT NULL , 
    `user_id` INT NOT NULL , 
    `post_id` INT NOT NULL , 
    `like` INT NOT NULL , 
    `dislike` INT NOT NULL , 
    `add_added` TIMESTAMP NOT NULL ) 
    

});
register_deactivation_hook();
