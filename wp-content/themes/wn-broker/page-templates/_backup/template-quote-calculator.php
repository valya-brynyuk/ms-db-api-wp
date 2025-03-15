<?php
/*
Template Name: Quote Calculator
*/
?>

<?php get_header(); ?>

<div class="content">

    <?php get_template_part( 'template-partials/banner' ); ?>

    <div class="wrap">

        <div class="page-content">

            <?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>


                <?php if (SwpmMemberUtils::is_member_logged_in()): ?>

                    <?php the_content(); ?>
                
                <?php else: ?>

                    Please login

                <?php endif; ?>


            <?php endwhile; else: ?>
            <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
            <?php endif; ?>

        </div>

        </div>

    </div>

</div>

<?php get_footer(); ?>