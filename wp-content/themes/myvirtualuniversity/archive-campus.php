<?php 
// <!-- the name of this file matters. -->
get_header(); 

// TOP BANNER 
pageBanner(array(
    'title' => 'Our Campuses',
    'subtitle' => 'We have several conveniently located campuses'
));
?>

<!-- LIST OF ALL THE CAMPUSES -->
<div class="container container--narrow page-section">
    <ul class="link-list min-list">
        
    <?php 
    // the WP default-query is tweaked a little bit just like archive-event.php, rather than making a whole new custom-query
    // defined in functions.php, university_adjust_queries()

        while(have_posts()) {
            the_post(); 
            $post_id = get_the_ID();
            $post = get_post($post_id);
            $blocks = parse_blocks($post->post_content);
            
    ?>
            <li>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <!-- <div><?php echo print_r(get_post_meta($post_id)); ?></div> -->
                <div><?php 
                    foreach ($blocks as $block) {
                        if ($block['blockName'] == 'mappress/map') { // I found this in the source code
                            echo render_block($block);
                        }
                    }?></div>
            </li>
    <?php
            // echo print_r(getBlocks());
        }
        echo paginate_links(); // look up WP DOCS
    ?>
    </ul>

</div>

<?php get_footer();

?>