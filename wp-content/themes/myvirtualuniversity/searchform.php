<!-- the name of this file matters. get_search_form() refers to searchform.php. This is like get_template_part(template file url), but predefined by wp-->
<form class="search-form" method="get" action="<?php echo esc_url(site_url('/'/*this replaces the '/search' slug with '/' */)); ?>">   <!-- esc_url() protects the visitors if the site has been hacked -->
    <label class="headline headline--medium" for="s">Perform a New Search</label>
    <div class="search-form-row">
        <input placeholder="What are you looking for?" class="s" id="s" type="search" name="s">
        <input class="search-submit" type="submit" value="Search">
    </div>
</form>