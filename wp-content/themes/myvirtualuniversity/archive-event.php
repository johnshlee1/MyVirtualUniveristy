<?php 
// <!-- the name of this file matters. -->
get_header();
pageBanner(array(
    'title' => 'All Events',
    'subtitle' => 'See what is going on in our world'
));
?>

<!-- TOP BANNER -->


<!-- LIST OF ALL THE EVENTS -->
<div class="container container--narrow page-section">
    <?php 
        // instead of making a custom-query like how it's done in the front-page.php, for this file's purpose, the WP default query is good enough with a few tweaks
        // and this slight adjustment to the URL-based-default-query or main-query is defined in functions.php, university_adjust_queries()
        while(have_posts()) {       // have_posts() are main-wp-query or main-wp-loop
            the_post(); 
            get_template_part('template-parts/content-event');
        }
        echo paginate_links(); // look up WP DOCS
    ?>
    <hr class="section-break">
    <p>
        Looking for a recap of past events? 
        <a href="<?php echo site_url('/past-events'); ?>">Check out our past events archive</a>   <!-- instead of hardtyping the URL, site_url() generates an absolute URL to bypass different directory trees -->
        .
    </p>  

</div>

<?php get_footer();

?>