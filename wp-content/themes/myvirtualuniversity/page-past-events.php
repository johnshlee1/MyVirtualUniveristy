<?php 
// <!-- the name of this file doesn't matter so long as there is an added page with the name matches the href value. -->
get_header(); 

// TOP BANNER
pageBanner(array(
    'title' => 'Past Events',
    'subtitle' => 'A recap of our past events'
));
?>

<!-- LIST OF ALL THE EVENTS -->
<div class="container container--narrow page-section">
    <?php 
       $today = date('Ymd');
       $pastEvents = new WP_Query(array(   // => is used for 'Associative Arrays'
            'paged' => get_query_var('paged', 1),   // 'paged' tell wp the paged result or the current page we are on, and the function can retrieve information about the current URL, and '1' is a fallback if no paged/# can be found
            'post_type' => 'event',
            'meta_key' => 'event_date', // this is required for 'meta_value_num' in the line below
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(      //'meta_query' is more advanced with nested array and specific arguments passed in
                array(
                    'key' => 'event_date',
                    'compare' => '<',
                    'value' => $today,
                    'type' => 'numeric'
                )
            )
       ));

        while($pastEvents->have_posts()) {
            $pastEvents->the_post(); 
            get_template_part('template-parts/content-event');
        }
        echo paginate_links(array(
            'total' => $pastEvents->max_num_pages
        )); // this function only works out of the box with the default-queries that wp makes on its own that are tied to the current URL
                                            // if the query is custom-query like 'new WP_Query', then paginate_links() does not work
    ?>

</div>

<?php get_footer();

?>