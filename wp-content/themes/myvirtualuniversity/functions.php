<?php
// <!-- the name of this file matters. -->
    require get_theme_file_path('/inc/like-route.php');
    require get_theme_file_path('/inc/search-route.php');
    // 4 Reasons why we create our own new REST API URL
        // 1. Custom search logic (WP doesn't know to look for relations between data in the custom fields)
        // 2. Respond with less JSON data, and only GET the specified data for the visitors
        // 3. Send only 1 getJSON request instead of 6 (posts, pages, events, programs, professors, campuses)

    function university_custom_rest() { 
        register_rest_field('post', 'authorName', array(    // very important function to add a custom field to REST API
            'get_callback' => function() {return get_the_author();} // not sure if the property name is so specific
        ));

        register_rest_field('note', 'userNoteCount', array(
            'get_callback' => function() {return count_user_posts(get_current_user_id(), 'note');}
        ));

    }

    add_action('rest_api_init', 'university_custom_rest');

    function pageBanner($args = NULL) {  // $args=null makes it so that $args is optional, because at least null is provided as the default
            if (!$args['title']) {
                $args['title'] = get_the_title();
            }

            if(!$args['subtitle']) {
                $args['subtitle'] = get_field('page_banner_subtitle');
            }

            if(!$args['photo']) {
                if (get_field('page_banner_background_image') AND !is_archive() AND !is_home()) {
                    $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
                } else {
                    $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
                }
               
            }
        ?>
         <div class="page-banner">
            <div class="page-banner__bg-image" 
                style="background-image: url(<?php 
                echo $args['photo']; ?>)">   <!-- get_field() always returns an array. 'url' and 'sizes' are a prop of the array $pageBannerImage. 'pageBanner' is defined in functions.php -->
            </div>
            <div class="page-banner__content container container--narrow">
                <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
                <div class="page-banner__intro">
                    <p><?php 
                        echo $args['subtitle'];
                    ?></p>
                </div>
            </div>
        </div> 
        <?php
    }

    function university_files() {
        wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
        wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

        wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), /*array('jquery')*/ NULL, '1.0', true);
        wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
        wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));

        wp_localize_script('main-university-js', 'universityData', array( // this is a very useful API method. 1st arg is the $handle for wp_enqueue_script(), and 2nd arg is the $objectName to be grabbed in js.
            'root_url' => get_site_url(), // the name of this property isn't so specific
            'nonce' => wp_create_nonce('wp_rest') // the name of this property isn't so specific. Nonce is created for authorization
        ));
    }

    add_action('wp_enqueue_scripts', 'university_files');

    // enable features for the "editor" page
    function university_features() {
        // register_nav_menu('headerMenuLocation', 'Header Menu Location');
        // register_nav_menu('footerLocationOne', 'Footer Location One');
        // register_nav_menu('footerLocationTwo', 'Footer Location Two');
        add_theme_support('title-tag'); 
        add_theme_support('post-thumbnails');    // add_theme_support tells wp what kind of features we want enabled, like 'featured-image' in the editor window
        add_image_size('professorLandscape', 400, 260, true);   // 'crop' value in the 4th arg can also be array('left', 'top')
        add_image_size('professorPortrait', 480, 650, true);
        add_image_size('pageBanner', 1500, 350, true);
    }
    add_action('after_setup_theme', 'university_features');

    // On archive pages, set the list order and what type of information about each item will show.
    function university_adjust_queries($query) {
        if(!is_admin() AND is_post_type_archive('campus') AND is_main_query()) {
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
            $query->set('posts_per_page', -1);
        }
        
        if(!is_admin() AND is_post_type_archive('program') AND is_main_query()) {
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
            $query->set('posts_per_page', -1);
        }

        if (!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()) {    // the 3rd condition is for preventing $query argument from being applied to a 'custom-query'
                                                                                                                                                // is_main_query() makes sure that the query is based on the default URL
            $today = date('Ymd');
            $query->set('meta_key', 'event_date');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'ASC');
            $query->set('meta_query', array(      //'meta_query' is more advanced with nested array and specific arguments passed in
                array(  // property names in this array are specific
                    'key' => 'event_date',
                    'compare' => '>=',
                    'value' => $today,
                    'type' => 'numeric'
                )
            ));
        }
    }

    add_action('pre_get_posts', 'university_adjust_queries');


// Redirect subscriber accounts out of admin and onto homepage
add_action('admin_init', 'redirectSubsToFrontend');

function redirectSubsToFrontend() {
    $ourCurrentUser = wp_get_current_user();

    if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
        wp_redirect(site_url('/'));
        exit;   // always run exit afterward so once the site url is redirected, it stops and rests, and not use server resources.
    }
}

add_action('wp_loaded', 'noSubsAdminBar');

function noSubsAdminBar() {
    $ourCurrentUser = wp_get_current_user();

    if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
        show_admin_bar(false);  // hides the admin bar at the top when logged in.
    }
}

// Customize Login Screen Settings
add_filter('login_headerurl', 'ourHeaderUrl');

function ourHeaderUrl() {
    return esc_url(site_url('/'));
}

add_action('login_enqueue_scripts', 'ourLoginCSS'); // wp_enqueue_scripts() and login_enqueue_scripts() are different. In order to change the login page, look into themes/myvirtualuniversity/css/modules/login.scss

function ourLoginCSS() {
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}

add_filter('login_headertitle', 'ourLoginTitle');

function ourLoginTitle() {
    return get_bloginfo('name');
}

// Force Note Posts to be Private
add_filter('wp_insert_post_data', 'makeNotePrivate', 10/*priority of the callback funcs if there are multiple to the same hook, in this case 'wp_inser_post_data'*/, 2/*tells add_filter to work with two parameters*/); 
// 'wp_insert_post_data' is one of the most powerful and flexible filter hooks in all of wp. 2nd arg name doesn't matter.

function makeNotePrivate($data, $postarr) { // 2nd arg $postarr' contains the ID of the post
    if($data['post_type'] == 'note'){
        if(count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']){
            die("You have reached your note limit.");
        }
        $data['post_content'] = sanitize_textarea_field($data['post_content']); // 'sanitize_..._field' really makes sure that wp escapes any kinds of raw html, blocking any types of injection
        $data['post_title'] = sanitize_text_field($data['post_title']);
    }
    if($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
        $data['post_status'] = "private";
    }

    return $data;
}

// add_filter('ai1wm_exclude_content_from_export', 'ignoreCertainFiles');
// function ignoreCertainFiles($exclude_filters) {
//     $exclude_filters[] = 'themes/fictional-university-theme/node_modules';
//     return $exclude_filters;
// }