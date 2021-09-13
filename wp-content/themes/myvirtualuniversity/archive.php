<?php 
// <!-- the name of this file matters. -->
get_header(); 

// TOP BANNER
pageBanner(array(
    'title' => get_the_archive_title(),
    'subtitle' => get_the_archive_description()
));
?>

<!-- LIST OF ALL POST RELATED TO A SPECIFIC ARCHIVE TYPE -->
<div class="container container--narrow page-section">
<?php 
    while(have_posts()) {
        the_post(); ?>
        <div class="post-item">
            <h2 class="headline headline--medium headline--post-title">
                <a href="<?php the_permalink(); ?>">
                    <?php the_title(); ?>
                </a>
            </h2>

            <div class="metabox"> 
                <p> 
                    Posted by <?php the_author_posts_link(); ?> 
                    on <?php the_time('n.j.y'); ?> 
                    in <?php echo get_the_category_list(', '); ?>   <!-- why is the comma needed in the argument? -->
                </p>
            </div>

            <div class="generic-content">
                <?php the_excerpt(); ?>
                <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">Continue reading &raquo; </a> </p>
            </div>
        </div>
    <?php }

        echo paginate_links();      // this function only works out of the box with the default-queries that wp makes on its own that are tied to the current URL
                                                // if the query is custom-query like 'new WP_Query', then paginate_links() does not work

?>

</div>

<?php get_footer();

?>