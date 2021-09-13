<?php 
// <!-- the name of this file matters. -->
get_header(); 

// TOP BANNER 
pageBanner(array(
    'title' => 'All Programs',
    'subtitle' => 'There is something for everyone. Have a look around'
));
?>

<!-- LIST OF ALL THE EVENTS -->
<div class="container container--narrow page-section">
    <ul class="link-list min-list">
        
    <?php 
    // the WP default-query is tweaked a little bit just like archive-event.php, rather than making a whole new custom-query
    // defined in functions.php, university_adjust_queries()
        while(have_posts()) {
            the_post(); 
    ?>
            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
    <?php
        }
        echo paginate_links(); // look up WP DOCS
    ?>
    </ul>

</div>

<?php get_footer();

?>