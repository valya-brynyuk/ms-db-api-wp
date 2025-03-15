<?php get_header(); ?>

<?php //get_template_part( 'template-partials/modules' ); ?>



	<div class="page-content">

		<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

		<div class="centered-content">

			<?php the_content(); ?>

		</div>

		<?php endwhile; else: ?>
		<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
		<?php endif; ?>

<?php  if( !is_user_logged_in() ): ?>

		<section class="section">

				<div class="wrap">

					<div class="centered-content">

						<?php echo do_shortcode('[swpm_login_form]'); ?>
					
					</div>

				</div>

			</section>

<?php else: ?>




			<section class="section">

				<div class="wrap">

					<div class="page-intro">

						<?php 
							$member_id = SwpmMemberUtils::get_logged_in_members_id();

							$field_name = 'email';
							$email_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);

							$field_name = 'first_name';
							$fname_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);

							$field_name = 'last_name';
							$lname_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);

							$field_name = 'company_name';
							$company_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);

							echo '<p>Hi, <strong>'.$fname_value.'/'.$company_value.',</strong> welcome to the Wilson Nesbitt Property Portal.<p>';

						?>
					</div>


					<div class="cols--ng">

						<div class="col--1of3--ng">
							<a href="<?php echo site_url('broker/introduce-a-client'); ?>" class="portal-tile  lazyload">
								<div class="portal-tile__overlay"></div>
								<div class="portal-tile__inner">
									<div class="portal-tile__icon portal-tile__icon--client"></div>
									<div class="portal-tile__footer">
										Introduce a Client
									</div>
								</div>
							</a>
						</div>
								
						<div class="col--1of3--ng">
							<a target="_blank" href="https://wn.focisportal.co.uk/" class="portal-tile  lazyload">
								<div class="portal-tile__overlay"></div>
								<div class="portal-tile__inner">
									<div class="portal-tile__icon portal-tile__icon--portal"></div>
									<div class="portal-tile__footer">
										Case Tracking
									</div>
								</div>
							</a>
						</div>

						<?php $encodedQuoteLink = '/quote-calculator/?entry={%22Source%22:%22Broker%22}'; ?>

						<div class="col--1of3--ng">
							<a target="_blank" href="<?php echo site_url($encodedQuoteLink); ?>" class="portal-tile lazyload">
								<div class="portal-tile__overlay"></div>
								<div class="portal-tile__inner">
									<div class="portal-tile__icon portal-tile__icon--calculator"></div>
									<div class="portal-tile__footer">
										Quote Calculator
									</div>
								</div>
							</a>
						</div>
					
					</div>

				</div>

			</section>

			<section class="section section--grey">
				<div class="wrap">

					<h3 class="section__heading">
						Other Information
					</h3>

					<div class="cols--ng">

								<div class="col--1of3--ng">
							<a href="<?php echo site_url('broker/submit-a-query'); ?>" class="portal-tile lazyload">
							<div class="portal-tile__overlay"></div>
								<div class="portal-tile__inner">
									<div class="portal-tile__icon portal-tile__icon--query"></div>
									<div class="portal-tile__footer">
										Submit a Query
									</div>
								</div>
							</a>
						</div>


						<div class="col--1of3--ng">
							<a target="_blank" href="#" class="portal-tile  lazyload">
								<div class="portal-tile__overlay"></div>
								<div class="portal-tile__inner">
									<div class="portal-tile__icon portal-tile__icon--info"></div>
									<div class="portal-tile__footer">
										Useful Information
									</div>
								</div>
									
									
							</a>
						</div>

						<div class="col--1of3--ng">
							<a href="<?php echo site_url('broker/guides'); ?>" class="portal-tile  lazyload">
								<div class="portal-tile__overlay"></div>
								<div class="portal-tile__inner">
									<div class="portal-tile__icon portal-tile__icon--services"></div>
									<div class="portal-tile__footer">
										Other Services
									</div>
								</div>
									
									
							</a>
						</div>
					</div>

				</div>
			</section>

	<?php endif; ?>

	</div>

<?php get_footer(); ?>