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
                    <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('event'); ?>">   <!-- this isn't that much more dynamic than manual '/event' slug -->
                        <i class="fa fa-home" aria-hidden="true"></i> Events Home
                    </a> 
                    <span class="metabox__main">
                        <?php the_title();?>
                    </span>
                </p>
            </div>
<!-- THE CONTENT -->
            <div class="generic-content"> <?php the_content(); ?></div>

            <?php 
            $relatedPrograms = get_field('related_programs');
            if ($relatedPrograms) {
                // print_r($relatedPrograms);      // print_r() returns the information about the object, and we learned that $relatedProgram above is an array 
                echo '<hr class="section-break>';
                echo '<h2 class="headline headline--medium">Related Program(s)</h2>';
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