<?php
// <!-- the name of this file matters. -->
    get_header();

    while(have_posts()) {
        the_post(); 
        pageBanner();
?>
       

<!-- BACK TO PARENT LINK BUTTON -->
        <div class="container container--narrow page-section">
            <div class="metabox metabox--position-up metabox--with-home-link">
                <p>
                    <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>">   <!-- this isn't that much more dynamic than manual '/event' slug -->
                        <i class="fa fa-home" aria-hidden="true"></i> All Programs
                    </a> 
                    <span class="metabox__main">
                        <?php the_title();?>
                    </span>
                </p>
            </div>
<!-- THE CONTENT -->
            <div class="generic-content"> <?php the_field('main_body_content'); ?></div>

            <?php 
// ALL PROFESSORS TEACHING IN THE SPECIFIC PROGRAM
                $relatedProfessors = new WP_Query(array(   // => is used for 'Associative Arrays'
                    'posts_per_page' => -1, // '-1' value tell wp to return all the posts that meets the condition of the query 'post_type' in the line below 
                    'post_type' => 'professor',
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'meta_query' => array(      // 'meta_query' is more advanced with nested array and specific arguments passed in
                        // definitely play around with this array for meta_query, and how the query loops til it gets the ID of the custom-field
                        array(
                            'key' => 'related_programs',
                            'compare' => 'LIKE',    // 'LIKE' is like saying 'contains'
                            'value' => '"' . get_the_ID() . '"'     // concatenation is used for the quotation marks
                        )
                    )
                ));

                if($relatedProfessors->have_posts()) {
                    echo '<hr class="section-break">';
                    echo '<h2 class="headline headline--medium">' . get_the_title() . ' Professors </h2>';
                    
                    echo '<ul class="professor-cards>';
                    while($relatedProfessors->have_posts()) {      // -> is used to access 'props' and 'methods' for 'Objects'
                        $relatedProfessors->the_post();        // whenever the_item is used, it always refers to the item of the current URL or slug, and echos the value
                        
                ?>
                    
                        <li class="professor-card__list-item">
                            <a class="professor-card" href="<?php the_permalink(); ?>">
                                <img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscape'); ?>">
                                <span class="professor-card__name"><?php the_title(); ?></span>
                            </a>
                        </li>
                <?php 
                    }
                    echo '</ul>';
                }

                wp_reset_postdata();    // custom-query hijacks the global post data, so it needs to be reset back to the global default-URL-query before being used again by another custom-query

// ALL UPCOMING EVENTS RELATED TO THE SPECIFIC PROGRAM
                $today = date('Ymd');
                $homepageEvents = new WP_Query(array(   // => is used for 'Associative Arrays'
                    'posts_per_page' => 2, // '-1' value tell wp to return all the posts that meets the condition of the query 'post_type' in the line below 
                    'post_type' => 'event',
                    'meta_key' => 'event_date', // this is required for 'meta_value_num' in the line below
                    'orderby' => 'meta_value_num',
                    'order' => 'ASC',
                    'meta_query' => array(      // 'meta_query' is more advanced with nested array and specific arguments passed in
                        array(
                            'key' => 'event_date',
                            'compare' => '>=',
                            'value' => $today,
                            'type' => 'numeric'
                        ),
                        array(
                            'key' => 'related_programs',
                            'compare' => 'LIKE',
                            'value' => '"' . get_the_ID() . '"'     // concatenation is used for the quotation marks for wp to serialize the data(array)
                        )
                    )
                ));

                if($homepageEvents->have_posts()) {
                    echo '<hr class="section-break">';
                    echo '<h2 class="headline headline--medium">Upcoming ' . get_the_title() . ' Events </h2>';

                    while($homepageEvents->have_posts()) {      // -> is used to access 'props' and 'methods' for 'Objects'
                        $homepageEvents->the_post();        // whenever the_item is used, it always refers to the item of the current URL or slug, and echos the value
                        get_template_part('template-parts/content-event');
                    }
                }

                wp_reset_postdata();

                $relatedCampuses = get_field('related_campus');

                if($relatedCampuses){
                    echo '<hr class="section-break">';
                    echo '<h2 class="headline headline--medium">' . get_the_title() . ' is Available At These Campuses</h2>';
                    echo '<ul class="min-list link-list">';
                    foreach($relatedCampuses as $campus){
                        ?>
                        <li><a href="<?php echo get_the_permalink($campus); ?>"><?php echo get_the_title($campus); ?></a></li>
                         <?php
                    echo '</ul>';
                    }
                }

            ?>

        </div>

    <?php }

    get_footer();

?>