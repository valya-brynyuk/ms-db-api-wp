<?php get_header(); ?>

<div class="content">

	<?php get_template_part('template-partials/banner-home'); ?>


	<!-- if (!is_user_logged_in()) : -->

	<?php if (!SwpmMemberUtils::is_member_logged_in()): ?>

		<section class="section section--pre-overlap">
			<div class="wrap">


				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

						<div class="text-center highlight-copy content-inner load-hidden slide-in">


							<?php
							$currentTheme = wp_get_theme();
							if ($currentTheme == 'NI Legal') {
								//echo esc_html( $currentTheme );
								$themeName = 'NILegal.com';
							} else {
								$themeName = 'tmsconveyancing.com';
							}
							?>


							<p class="large-copy accent-copy"><?php echo $themeName; ?> is a conveyancing service for estate agents and financial advisers in Northern Ireland.</p>
							<?php the_content(); ?>

						</div>

					<?php endwhile;
				else : ?>
					<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
				<?php endif; ?>

			</div>
		</section>

		<section class="section--accent">

			<div class="section__overlap-top">
				<div class="wrap">
					<div class="cols load-hidden slide-in">
						<div class="col--1of2">
							<div class="portal-tile">
								<div class="portal-tile__inner">
									<div class="button-carousel">
										<div>
											<div class="portal-tile__title">Realtime Case Tracking</div>
											<div class="portal-tile__icon portal-tile__icon--case-tracking"></div>
										</div>
										<div>
											<div class="portal-tile__title">Adjective Instructing?</div>
											<div class="portal-tile__icon portal-tile__icon--introduce"></div>
										</div>
										<div>
											<div class="portal-tile__title">Adjective calculator?</div>
											<div class="portal-tile__icon portal-tile__icon--calculator"></div>
										</div>
									</div>

								</div>
							</div>
						</div>
						<div class="col--1of2">
							<a href="<?php echo site_url('contact-us'); ?>" class="portal-tile">
								<div class="portal-tile__overlay"></div>
								<div class="portal-tile__inner">
									<div class="portal-tile__title ">Register for Access</div>
									<div class="portal-tile__icon portal-tile__icon--access"></div>
								</div>
								<div class="portal-tile__footer">
									<span class="button-link">Contact Us</span>
								</div>
							</a>
						</div>
					</div>
					<br><br>
					<div class="content-inner">
						<div class="quote-carousel">
							<div class="quote">
								<div class="quote__copy">
									They are exceptionally good. They handle hundreds of transactions with us each year.
								</div>
								<div class="quote__citation">
									Director of Remortgage Operations, Enact
								</div>
							</div>
							<div class="quote">
								<div class="quote__copy">
									They are exceptionally good. They handle hundreds of transactions with us each year.
								</div>
								<div class="quote__citation">
									Director of Remortgage Operations, Enact2
								</div>
							</div>
							<div class="quote">
								<div class="quote__copy">
									They are exceptionally good. They handle hundreds of transactions with us each year.
								</div>
								<div class="quote__citation">
									Director of Remortgage Operations, Enact3
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</section>

	<?php else : ?>

		<section class="section--grey section__overlap-top">

			<div class="wrap">

				<div class="cols">

					<!-- <div class="col--1of3  load-hidden sequenced"> -->
					<div class="col--1of3">
						<a class="portal-tile"  href="<?php echo site_url('quote-calculator'); ?>">
							<div class="portal-tile__overlay"></div>
							<di class="portal-tile__inner">
								<div class="portal-tile__title">Quote Calculator</div>
								<div class="portal-tile__icon portal-tile__icon--plain portal-tile__icon--calculator"></div>
							</di>
						</a>
					</div>

					<!-- <div class="col--1of3  load-hidden sequenced"> -->
					<div class="col--1of3">
						<a class="portal-tile" href="<?php echo site_url('instruct-a-case'); ?>">
							<div class="portal-tile__overlay"></div>
							<div class="portal-tile__inner">
								<div class="portal-tile__title">Instruct a Case</div>
								<div class="portal-tile__icon portal-tile__icon--plain portal-tile__icon--introduce"></div>
							</div>
						</a>
					</div>

					<!-- <div class="col--1of3 load-hidden sequenced"> -->
					<div class="col--1of3">
						<a class="portal-tile " href="<?php echo site_url('case-tracking'); ?>">
							<div class="portal-tile__overlay"></div>
							<div class="portal-tile__inner">
								<div class="portal-tile__title ">Case Tracking</div>
								<div class="portal-tile__icon portal-tile__icon--plain portal-tile__icon--case-tracking"></div>
							</div>
						</a>
					</div>



				</div>

			</div>

		</section>

		<section class="section">
			<div class="wrap">
				<h3 class="section__title">Other Services</h3>
				<div class="cols ">
					<div class="col--1of3">

						<a href="<?php echo site_url('contact-us'); ?>" class="portal-tile portal-tile--alt load-hidden sequenced">
							<div class="portal-tile__inner">
								<div class="portal-tile__title">Submit a Query</div>

								<div class="portal-tile__icon portal-tile__icon--plain portal-tile__icon--query"></div>
							</div>
						</a>

					</div>
					<div class="col--1of3">

						<a href="<?php echo site_url('useful-information'); ?>" class="portal-tile portal-tile--alt load-hidden sequenced ">
							<div class="portal-tile__inner">
								<div class="portal-tile__title ">Useful Information</div>

								<div class="portal-tile__icon portal-tile__icon--plain portal-tile__icon--info"></div>
							</div>
						</a>

					</div>
					<div class="col--1of3">

						<a href="<?php echo site_url('other-services'); ?>" class="portal-tile portal-tile--alt load-hidden sequenced">
							<div class="portal-tile__inner">
								<div class="portal-tile__title">Other Services</div>
								<div class="portal-tile__icon portal-tile__icon--plain portal-tile__icon--services"></div>
							</div>
						</a>

					</div>
				</div>
			</div>
		</section>

	<?php endif; ?>


</div>


<?php get_footer(); ?>