<!-- the name of this file and folder doesn't matter so long as it matches the value in get_template_part(). -->
<div class="event-summary">
    <a class="event-summary__date t-center" href="#">
        <span class="event-summary__month">
            <?php 
                $eventDate = new DateTime(get_field('event_date'));     // DateTime Class returns the current date if the argument given to it returns nothing 
                echo $eventDate->format('M')
            ?>
        </span>
        <span class="event-summary__day">
            <?php echo $eventDate->format('d') ?>
        </span>
    </a>
    <div class="event-summary__content">
        <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
        <p> 
            <?php if (has_excerpt()) {
                echo get_the_excerpt();
            } else {
                echo wp_trim_words(get_the_content(), 18);
            }; ?>
            <a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a>
        </p>
    </div>
</div>