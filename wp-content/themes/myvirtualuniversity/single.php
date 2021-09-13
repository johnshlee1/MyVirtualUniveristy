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
                    <!-- unlike PAGE, 'site_url()' is used here instead of 'get_permalink($theParent)' because POST is organized by 'category' instead of 'parent page'-->
                    <a class="metabox__blog-home-link" href="<?php echo site_url('/blog'); ?>">     
                        <i class="fa fa-home" aria-hidden="true"></i> Blog Home
                    </a> 
                    <span class="metabox__main">
                        Posted by <?php the_author_posts_link(); ?> on <?php the_time('n.j.y'); ?> 
                        in <?php echo get_the_category_list(', '); ?>
                    </span>
                </p>
            </div>
            <div class="generic-content"> <?php the_content(); ?></div>
        </div>

    <?php }

    get_footer();

?>