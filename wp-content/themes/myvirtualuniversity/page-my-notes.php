<?php
// <!-- the name of this file matters. -->
    if(!is_user_logged_in()){
        wp_redirect(esc_url(site_url('/')));
        exit;   // always run exit afterward so once the site url is redirected, it stops and rests, and not use server resources.
    }
    get_header();

    while(have_posts()) {
        the_post(); 
        pageBanner();
?>
        
<!-- BACK TO PARENT LINK BUTTON -->
        <div class="container container--narrow page-section">
            <div class="create-note">
                <h2>Create New Note</h2>
                <input class="new-note-title" placeholder="Title">
                <textarea class=" new-note-body" placeholder="Your note here..."></textarea>
                <span class="submit-note">Create Note</span>
                <span class="note-limit-message">Note limit reached: delete an existing note to make room for a new one.</span>
            </div>

            <ul class="min-list link-list" id="my-notes">
                <?php
                    $userNotes = new WP_Query(array(
                        'post_type' => 'note',
                        'posts_per_page' => -1,
                        'author' => get_current_user_id()
                    ));

                    while($userNotes->have_posts()) {
                        $userNotes->the_post(); 
                ?>
                        <li data-id="<?php the_ID(); ?>"> <!-- this is such a simple way to get the ID for each post. I guess you can name an attribute essentially anything -->
                            
                            <input readonly class="note-title-field" value="<?php echo str_replace('Private: ', '', esc_attr(get_the_title())); /* whenever you use information from the database for html attribute, you always want to esc_attr() for security*/?>">
                            <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"> Edit</i></span>
                            <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"> Delete</i></span>
                            <textarea readonly class="note-body-field" ><?php echo esc_textarea(wp_strip_all_tags(get_the_content())); ?></textarea> <!-- readonly is a good simple attr. wp esc_methods are useful and important for security. -->
                            <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"> Save</i></span>
                        </li>
                <?php
                    }
                ?>
            </ul>
        </div>

<?php 
    }

    get_footer();

?>