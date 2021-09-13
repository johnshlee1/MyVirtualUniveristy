<?php
// <!-- the name of this file matters. -->
    get_header(); 

// TOP BANNER
    pageBanner(array(
        'title' => 'Search Results',
        'subtitle' => 'You searched for &ldquo;' . esc_html(get_search_query(false)) /* get_search_query is set to 'true' by default on escaping html upon detecting malicious code, however, esc_html really ensures it. */. '&rdquo;'
    ));
?>

<div class="container container--narrow page-section">
<?php 
    if (have_posts()) {
        while(have_posts()) {
            the_post();
            get_template_part('template-parts/content', get_post_type());
            echo paginate_links();
        }
    } else {
        echo '<h2 class="headline headline--small-plus">No results match that search.</h2>';
    }

    get_search_form();

?>
</div>

<?php get_footer(); ?>