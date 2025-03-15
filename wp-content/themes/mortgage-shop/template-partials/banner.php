<?php
	$bannerImage = get_field('banner_image');
?>

<?php if ($bannerImage): ?>
<div class="banner lazyload" data-bgset="<?php echo $bannerImage['sizes']['medium_large']?> 768w, <?php echo $bannerImage['sizes']['responsive_large'] ?> 1600w" data-sizes="auto">
<?php else: ?>
<div class="banner">
<?php endif; ?>
	<div class="banner__outer">
		<div class="banner__inner">
			
			 <?php if( is_front_page() ) : ?>

				<?php  if( !is_user_logged_in() ): ?>

				<h4>Portal Login</h4>
				<?php echo do_shortcode('[swpm_login_form]'); ?>

				<?php else: ?>

					<div class="welcome">

					
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

						echo '<div class="welcome-message">Hi, <span class="login-value--name">'.$fname_value.'.</span> Welcome to the WNILegal Portal</div>';

					?>

					<div class="welcome-sub-message">Choose an option below to get started:</div>

					</div>


				<?php endif; ?>

			<?php else: ?>

				<h1><?php the_title(); ?></h1>
				<div class="sub-title">
					<?php the_field('banner_sub_title'); ?>
				</div>

			<?php endif; ?>

		</div>
	</div>

</div>

