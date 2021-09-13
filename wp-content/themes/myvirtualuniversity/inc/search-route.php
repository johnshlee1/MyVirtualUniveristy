<?php

add_action('rest_api_init', 'universityRegisterSearch');    // rest_api_init is used whenever we want to register a new route or add a new field to a route

function universityRegisterSearch() {
    register_rest_route('university/v1' /*by default "wp/v#" is the namespace*/ , 'search' /*this is the route*/, array(
        'methods' => WP_REST_SERVER::READABLE, // 99% of the time, 'GET' works just fine, but some hosting may be very specific. 
                                                                                        // "WP_REST_SERVER::READABLE" is a wp constant that replaces 'GET'.
        'callback' => 'universitySearchResults' 
    ));
}

function universitySearchResults($data) {   // when this callback function is called, 
                                                                // wp has a built-in ability to pass along a bit of data (WP_REST_Request Object) about the parameter of the search request,
                                                                // hence the $data passed in 'universitySearchResults' function is already under the radar of wp engine to recognize it
    $mainQuery = new WP_Query(array(
        'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
        's' => sanitize_text_field($data['term']) // 's' stands for search in WP and it is the key property here for the search query. sanitize_text_field() prevents SQL Injection.
    )); 

    $results = array(
        'generalInfo' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array()
    );

// $mainQuery
    while($mainQuery->have_posts()) {
        $mainQuery->the_post(); // the_post() make all the relevant data for the current post ready and accessible
        if(get_post_type() == 'post' OR get_post_type() == 'page') {
            array_push($results['generalInfo'], array(  // unlike WP_Query class, in this array, the property can be named anything
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),
                'authorName' => get_the_author()
            ));
        }

        if(get_post_type() == 'professor') {
            array_push($results['professors'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0, 'professorLandscape')    // first arg is for 'which post' and '0' is wp way of saying 'the current post'
            ));
        }

        if(get_post_type() == 'program') {
            $relatedCampuses = get_field('related_campus'); // 'related_campus' custom field defined by ACF refers the set registered post_type, in this case 'campus'
            if ($relatedCampuses) {
                foreach($relatedCampuses as $campus) {  // within the 'campus' post_type array is a multiple items of posts
                    array_push($results['campuses'], array(
                        'title' => get_the_title($campus),  // get_the_title of the specified post
                        'permalink' => get_the_permalink($campus)
                    ));
                }
            }
            array_push($results['programs'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_id()    // this is used in $programsMetaQuery. The 'id' must be queried in order to search the database for the matching 'id' of the 'programs' post,
                                                // and not the matching word of the search-term.
            ));
        }

        if(get_post_type() == 'campus') {
            array_push($results['campuses'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_id()
            ));
        }

        if(get_post_type() == 'event') {
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;
            if (has_excerpt()) {
                $description = get_the_excerpt();
            } else {
                $description = wp_trim_words(get_the_content(), 18);
            }

            array_push($results['events'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description
            ));
        }

    }    

// $programRelationshipQuery
    if($results['programs']) {
        $programsMetaQuery = array('relation' => 'OR');
    
        foreach($results['programs'] as $item){
            array_push($programsMetaQuery, array(
                'key' => 'related_programs',    // this key value is what make it possible to search the relationships between posts
                'compare' => 'LIKE',
                'value' => '"' . $item['id'] . '"'  // this loop is necessary because unlike 'key' and 'compare', the id can be accessed in the sub-array items of multiple posts
            ));
        }
    
        $programRelationshipQuery = new WP_Query(array( 
            'post_type' => array('professor', 'event'),
            'meta_query' => $programsMetaQuery  // because of 'meta_query' parameter, $programRelationshipQuery only retrieves data that meet the criteria $programsMetaQuery
        ));
    
        while($programRelationshipQuery->have_posts()) {
            $programRelationshipQuery->the_post();

            if(get_post_type() == 'event') {
                $eventDate = new DateTime(get_field('event_date'));
                $description = null;
                if (has_excerpt()) {
                    $description = get_the_excerpt();
                } else {
                    $description = wp_trim_words(get_the_content(), 18);
                }
    
                array_push($results['events'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'month' => $eventDate->format('M'),
                    'day' => $eventDate->format('d'),
                    'description' => $description
                ));
            }

            if(get_post_type() == 'professor') {
                array_push($results['professors'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'image' => get_the_post_thumbnail_url(0, 'professorLandscape')    // first arg is for 'which post' and '0' is wp way of saying 'the current post'
                ));
            }
        }
    
        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR)); // wp looks within each sub-item of the array '$results['professors']', when trying to determine if there are duplicate or not
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
    }

// $campusRelationshipQuery
    if($results['campuses']) {
        $campusesMetaQuery = array('relation' => 'OR');
    
        foreach($results['campuses'] as $item){
            array_push($campusesMetaQuery, array(
                'key' => 'related_campus',    // this key value is what make it possible to search the relationships between posts
                'compare' => 'LIKE',
                'value' => '"' . $item['id'] . '"'
            ));
        }

        $campusRelationshipQuery = new WP_Query(array(
            'post_type' => array('program'),
            'meta_query' => $campusesMetaQuery
        ));
    
        while($campusRelationshipQuery->have_posts()) {
            $campusRelationshipQuery->the_post();

            if(get_post_type() == 'program') {
                array_push($results['programs'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'id' => get_the_id()
                ));
            }
        }
    }

    
    return $results;
}