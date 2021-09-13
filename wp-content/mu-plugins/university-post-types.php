<?php 

function university_post_types(){
// EVENT POST TYPE
    //after registering a new custom-post-type, the wp-admin => settings => permalink 
    //has to be 'saved' or 'updated', because WP doesn't update it periodically for performance reasons
        register_post_type('event', array(
            'show_in_rest' => true,      //Gutenburg Block Enabled, also shows the post_type in REST API
            'capability_type' => 'event',   // enable specific custom_post_type to be granted permission to user roles
            'map_meta_cap' => true, // tells wp to reasonably apply right capabilities at the right time.
            'supports' => array('title', 'editor', 'excerpt'),     // if 'custom-fields' is added, it has to be enabled in the preferences/options of the editor pages
                                                                                    // but in this case, ACF plugin was used
            'rewrite' => array('slug' => 'events'),
            'has_archive' => true,
            'public' => true, 
            'labels' => array(      // deals with all types of Labeling for the Custom Post Type
                'name' => 'Events',
                'add_new_item' => 'Add New Event',
                'edit_item' => 'Edit Event',
                'all_items' => "All Events",
                'singular_name' => "Event"
            ),
            'menu_icon' => 'dashicons-calendar'
        ));
// PROGRAM POST TYPE
        register_post_type('program', array(
            'show_in_rest' => true,      //Gutenburg Block Enabled, also shows the post_type in REST API
            'supports' => array('title', /* 'editor', 'custom-fields' */),     // if 'custom-fields' is added, it has to be enabled in the preferences/options of the editor pages
                                                                                    // but in this case, ACF plugin was used
                                                                                    // also 'editor', responsible for the text content like <p>, is removed, replaced by 'main_body_content'
                                                                                    // this replaces Gutenberg with Classic Editor
            'rewrite' => array('slug' => 'programs'),
            'has_archive' => true,
            'public' => true, 
            'labels' => array(      // deals with all types of Labeling for the Custom Post Type
                'name' => 'Programs',
                'add_new_item' => 'Add New Program',
                'edit_item' => 'Edit Program',
                'all_items' => "All Programs",
                'singular_name' => "Program"
            ),
            'menu_icon' => 'dashicons-awards'
        ));

// PROFESSOR POST TYPE
        register_post_type('professor', array(
            'show_in_rest' => true,      //Gutenburg Block Enabled, also shows the post_type in REST API
            'supports' => array('title', 'editor', 'thumbnail'),     //if 'custom-fields' is added, it has to be enabled in the preferences/options of the editor pages
                                                                                    //but in this case, ACF plugin was used
            'public' => true, 
            'labels' => array(      //deals with all types of Labeling for the Custom Post Type
                'name' => 'Professors',
                'add_new_item' => 'Add New Professor',
                'edit_item' => 'Edit Professor',
                'all_items' => "All Professors",
                'singular_name' => "Professor"
            ),
            'menu_icon' => 'dashicons-welcome-learn-more'
        ));

// CAMPUS POST TYPE
        register_post_type('campus', array(
            'show_in_rest' => true,      //Gutenburg Block Enabled, also shows the post_type in REST API
            'capability_type' => 'campus',
            'map_meta_cap' => true,
            'supports' => array('title', 'editor', 'excerpt'),
            'rewrite' => array('slug' => 'campuses'),
            'has_archive' => true,
            'public' => true, 
            'labels' => array(      //deals with all types of Labeling for the Custom Post Type
                'name' => 'Campuses',
                'add_new_item' => 'Add New Campus',
                'edit_item' => 'Edit Campus',
                'all_items' => "All Campuses",
                'singular_name' => "Campus"
            ),
            'menu_icon' => 'dashicons-location-alt'
        ));

// NOTE POST TYPE
        register_post_type('note', array(
            'capability_type' => 'note',
            'map_meta_cap' => true,
            'show_in_rest' => true,      //Gutenburg Block Enabled, also shows the post_type in REST API
            'supports' => array('title', 'editor'),     //if 'custom-fields' is added, it has to be enabled in the preferences/options of the editor pages
                                                                                    //but in this case, ACF plugin was used
            'public' => false,  // we want to keep 'note' private. We don't want the 'note' post to show up in any public queries or search results.
            'show_ui' => true,  // setting 'public' => false also hides this post_type from admin dashboard. And 'show_ui' => true shows it.
            'labels' => array(      //deals with all types of Labeling for the Custom Post Type
                'name' => 'Notes',
                'add_new_item' => 'Add New Note',
                'edit_item' => 'Edit Note',
                'all_items' => "All Notes",
                'singular_name' => "Note"
            ),
            'menu_icon' => 'dashicons-welcome-write-blog'
        ));

// LIKE POST TYPE
        register_post_type('like', array(
             'supports' => array('title'),     //if 'custom-fields' is added, it has to be enabled in the preferences/options of the editor pages
                                                                                    //but in this case, ACF plugin was used
            'public' => false,  // we want to keep 'note' private. We don't want the 'note' post to show up in any public queries or search results.
            'show_ui' => true,  // setting 'public' => false also hides this post_type from admin dashboard. And 'show_ui' => true shows it.
            'labels' => array(      //deals with all types of Labeling for the Custom Post Type
                'name' => 'Likes',
                'add_new_item' => 'Add New Like',
                'edit_item' => 'Edit Like',
                'all_items' => "All Likes",
                'singular_name' => "Like"
            ),
            'menu_icon' => 'dashicons-heart'
        ));
    }




    add_action('init', 'university_post_types');

