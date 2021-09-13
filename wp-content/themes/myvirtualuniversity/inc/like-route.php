<?php

add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes() {
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'POST',
        'callback' => 'createLike'
    ));

    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'DELETE',
        'callback' => 'deleteLike'
    ));
}

function createLike($data) {    // when this callback function is called, 
                                            // wp register_rest_rout() has a built-in ability to pass along a bit of data (WP_REST_Request Object) about the [params] of the request,
                                            // hence the $data passed in 'createLike' function is already under the radar of wp engine to recognize it
    
    if(is_user_logged_in()){   // current_user_can() is another good function to check for the user capability. Ex. current_user_can('publish_posts') or current_user_can('delete_events')
        $professor = sanitize_text_field($data['professorId']); // professorId is the prop of POST data initiated in 'Like.js' 
        
        $existQuery = new WP_Query(array(
            'author' => get_current_user_id(),  // this makes it so that $existQuery will only contain results if the current user has already liked the current professor
            'post_type' => 'like',
            'meta_query' => array(
                array(
                    'key' => 'liked_professor_id',
                    'compare' => '=',
                    'value' => $professor
                )
            )
        )); 

        if ($existQuery->found_posts == 0 AND get_post_type($professor) == 'professor') {    // this condition along with $existQuery is only here to make sure that the same user cannot like the same professor twice. 
                                                                        // It's set here as an extra logic to prevent any bugs, because once the same user likes a professor, the $existStatus of like is already set to 'yes', and clicking it again will actually delete the post, instead of adding another like and set $existStatus back to 'no'.
            return wp_insert_post(array( // wp_insert_post() programatically makes a new post without doing it through the dashboard.
                                                            // also wp_inser_post() returns the id# of the post if successfully made.
                'post_type' => 'like',
                'post_status' => 'publish',
                'post_title' => '2nd PHP Test',
                'post_content' => 'Hellow Worldd',
                'meta_input' => array(
                'liked_professor_id' => $professor  // after a chain of requests from 'Like.js' to 'like-route.php' this ultimately inserts the professor id into the custom field of 'liked_professor_id'
                )
            ));
        } else {
            die('Invalid professor id');
        }
        
    } else {
        die('Only logged in users can create a like.');
    }
    
}

function deleteLike($data) {    // WP_REST_Request Object(
                                            // [method:protected]=>DELETE, 
                                            // [params]=>array( 
                                                    // [URL]=>array(), 
                                                    // [GET]=>array(), 
                                                    // [POST]=>array( [like]=>"id" ), 
                                                    // ... 
                                                // )
                                            // )

    $likeId = sanitize_text_field($data['like']);   // ['like'] simply matches the prop of the $data sent from js deleteLike() http-request
    if (get_current_user_id() == get_post_field('post_author', $likeId) AND get_post_type($likeId) == 'like') {
        wp_delete_post($likeId, true);  // 1st arg is what I want to delete, 2nd arg is whether it should skip the 'trash' step and permanently delete.
        return 'Contrats, like deleted!';
    } else {
        die("You do not have permission to delete that.");
    }

}