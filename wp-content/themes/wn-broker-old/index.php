<?php get_header(); ?>

<?php //get_template_part( 'template-partials/modules' ); ?>

<div class="wrap">

	<div class="page-content">

		<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>
		<h1><?php the_title(); ?></h1>
		<?php the_content(); ?>
		<?php endwhile; else: ?>
		<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
		<?php endif; ?>


		

	</div>

</div>

<?php get_footer(); ?>