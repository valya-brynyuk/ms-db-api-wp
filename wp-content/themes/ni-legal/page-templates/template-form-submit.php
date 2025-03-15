<?php
/*
Template Name: Form Submission
*/
?>

<?php get_header(); ?>

<div class="content">

    <?php get_template_part( 'template-partials/banner' ); ?>

    <div class="wrap">

        <div class="page-content">

            <?php if (SwpmMemberUtils::is_member_logged_in()): ?>

            <?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

            <nav class="functions-nav"><?php wp_nav_menu(array('theme_location' => 'functions-nav', 'container' => false, 'depth' => '1', 'menu_class' => ' ')); ?></nav>

                <?php if (SwpmMemberUtils::is_member_logged_in()): ?>

                    <?php the_content(); ?>
                
                <?php else: ?>

                    Please login

                <?php endif; ?>

            <?php endwhile; else: ?>
            <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
            <?php endif; ?>

        </div>

		<?php else: //login check ?> 
			<p>Not logged in </p>
		<?php endif; //login check ?>		

        </div><!--page-content-->

    </div><!--wrap-->

</div><!--content-->


<?php get_footer(); ?>