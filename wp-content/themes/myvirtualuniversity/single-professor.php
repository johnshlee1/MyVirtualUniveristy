<?php
// <!-- the name of this file matters. -->
    get_header();

    while(have_posts()) {
        the_post(); 
        pageBanner();
?>
       

<!-- BACK TO PARENT LINK BUTTON -->
        <div class="container container--narrow page-section">
            
<!-- THE CONTENT -->
            <div class="generic-content"> 
                <div class="row group">
                    <div class="one-third">
                        <?php the_post_thumbnail('professorPortrait'); ?>
                    </div>

                    <div class="two-thirds">

                        <?php 
                            $likeCount = new WP_Query(array(
                                'post_type' => 'like',
                                'meta_query' => array(
                                    array(  // this logic of 'meta_query' sets the parameter for the query that it should only return if the 'key' matches the 'value'.
                                        'key' => 'liked_professor_id',
                                        'compare' => '=',
                                        'value' => get_the_ID()
                                    )
                                )
                            )); 

                            // $existStatus is the toggle function for either filling or emptying the HEART
                            $existStatus = 'no';

                            if(is_user_logged_in()){
                                $existQuery = new WP_Query(array(
                                    'author' => get_current_user_id(),  // this parameter makes it so that $existQuery will only contain results if the current user has already liked the current professor
                                    'post_type' => 'like',
                                    'meta_query' => array(
                                        array(
                                            'key' => 'liked_professor_id',
                                            'compare' => '=',
                                            'value' => get_the_ID() // this is simply the ID of the page in view(single)
                                        )
                                    )
                                )); 
    
                                if($existQuery->found_posts) {  // found_posts returns the absolute total number of posts that match the query and also returns true if not 0
                                    $existStatus = 'yes';
                                }
                            }

                        ?>
                        <span class="like-box" 
                            data-like="<?php echo $existQuery->posts[0]->ID; // this returns the ID of the like-post ?>"
                            data-professor="<?php the_ID(); // this is simply the ID of the page in view(single)?>"
                            data-exists="<?php echo $existStatus; // this attr value, 'yes' or 'no' is what triggers the css class 'fa-heart' ?>" 
                        >
                            <i class="fa fa-heart-o" aria-hidden="true"></i>    <!-- the heart is blank-->
                            <i class="fa fa-heart" aria-hidden="true"></i>  <!-- the heart is filled -->
                            <span class="like-count"><?php echo $likeCount->found_posts/*returns the absolute total number of posts that match the query*/; ?></span>
                        </span>
                        <?php the_content(); ?>
                    </div>
                </div>    
            </div>

            <?php 
            $relatedPrograms = get_field('related_programs');   // get_field() always returns an array
            if ($relatedPrograms) {
                // print_r($relatedPrograms);      // print_r() returns the information about the object, and we learned that $relatedProgram above is an array 
                echo '<hr class="section-break>';
                echo '<h2 class="headline headline--medium">Subject(s) Taught</h2>';
                echo '<ul class="link-list min-list">';
                foreach($relatedPrograms as $program) {
            ?>
                <li>
                    <a href="<?php echo get_the_permalink($program) ?>">
                        <?php echo get_the_title($program)?>
                    </a>
                </li>
                
            <?php
                }
                echo '</ul>';
            }
            ?>
        </div>

    <?php }

    get_footer();

?>
