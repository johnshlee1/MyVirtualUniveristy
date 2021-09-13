<!-- the name of this file and folder doesn't matter so long as it matches the value in get_template_part(). -->
<div class="post-item">
    <li class="professor-card__list-item">
        <a class="professor-card" href="<?php the_permalink(); ?>">
            <img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscape'); ?>">
            <span class="professor-card__name"><?php the_title(); ?></span>
        </a>
    </li>
</div>