<?php get_header(); ?>

<div class="content">

<?php get_template_part( 'template-partials/banner' ); ?>
<!-- 
	<section class="section--accent">
		<div class="wrap">
			<div class="text-center">
				<div class="cols">
					<div class="col--1of3">
						<a class="portal-tile">
							<div class="portal-tile__inner">
								<div class="portal-tile__title">Introduce a Client</div>
								<div class="portal-tile__icon portal-tile__icon--introduce"></div>
							</div>
						</a>
					</div>
					<div class="col--1of3">
						<div class="portal-tile ">
							<div class="portal-tile__inner">
								<div class="portal-tile__title ">Case Tracking</div>
								<div class="portal-tile__icon portal-tile__icon--case-tracking"></div>
							</div>
						</div>
					</div>
					<div class="col--1of3">
						<div class="portal-tile ">
							<di class="portal-tile__inner">
								<div class="portal-tile__title">Quote Calculator</div>
								<div class="portal-tile__icon portal-tile__icon--calculator"></div>
							</di>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="wrap">
			<div class="text-center">
				<div class="cols">
					<div class="col--1of3">

							<a class="portal-tile portal-tile--alt">
								<div class="portal-tile__inner">
									<div class="portal-tile__title">Introduce a Client</div>
									<div class="portal-tile__icon portal-tile__icon--introduce"></div>
								</div>
							</a>

					</div>
					<div class="col--1of3">

							<div class="portal-tile portal-tile--alt ">
								<div class="portal-tile__inner">
									<div class="portal-tile__title ">Case Tracking</div>
									<div class="portal-tile__icon portal-tile__icon--phone"></div>
								</div>
							</div>
		
					</div>
					<div class="col--1of3">
		
							<div class="portal-tile portal-tile--alt ">
								<div class="portal-tile__inner">
									<div class="portal-tile__title">Quote Calculator</div>
									<div class="portal-tile__icon portal-tile__icon--phone"></div>
								</div>
							</div>
			
					</div>
				</div>
			</div>
		</div>
	</section> -->

	<div class="wrap">

		<div class="page-content">

			<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>
			<?php the_content(); ?>
			<?php endwhile; else: ?>
			<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
			<?php endif; ?>

		</div>

	</div>

</div>

</div>

<?php get_footer(); ?>