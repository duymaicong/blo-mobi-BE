<?php
/**
 * Template Name: Product Page Template
 *
 * Template for displaying a blank page.
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<?php

get_header(); ?>
<div id="primary" class="content-area <?php do_action('nitro_primary-width') ?>">
    <main id="main" class="site-main" role="main">
        <header class="page-header">
            <?php
            the_archive_title('<h1 class="page-title">Viblo.asia </h1>');
            the_archive_description('<div class="taxonomy-description">', '</div>');
            ?>
        </header><!-- .page-header -->

        <?php
        $args = array('post_type' => 'product', 'posts_per_page' => 10);
        $the_query = new WP_Query($args);
        ?>
        <?php if ($the_query->have_posts()) : ?>
            <?php while ($the_query->have_posts()) :
                $the_query->the_post(); ?>
                <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
                <div class="thumbnail">
                    <?php the_post_thumbnail() ?>
                </div>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php endwhile; ?>
        <?php else : ?>
            <p><?php _e('Sorry, no posts found'); ?></p>
        <?php endif; ?>
    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>