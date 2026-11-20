<?php get_header(); ?>
<main id="main" tabindex="-1" class="st-home">
    <?php while (have_posts()) : the_post(); ?>
        <?php the_content(); ?>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
