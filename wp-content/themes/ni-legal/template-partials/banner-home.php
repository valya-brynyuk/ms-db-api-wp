<?php
$bannerImage = get_field('banner_image');
$bannerImageLoggedIn = get_field('home_banner_logged_in');
?>

<?php if (!SwpmMemberUtils::is_member_logged_in()) : ?>
	<?php if ($bannerImage) : ?>
		<div class="banner lazyload" data-bgset="<?php echo $bannerImage['sizes']['medium_large'] ?> 384w, <?php echo $bannerImage['sizes']['responsive_large'] ?> 800w,  <?php echo $bannerImage['sizes']['responsive_xlarge'] ?> 1000w" data-sizes="auto">
		<?php else : ?>
			<div class="banner">
			<?php endif; ?>
		<?php else : ?>
			<?php if ($bannerImageLoggedIn) : ?>
				<div class="banner lazyload" data-bgset="<?php echo $bannerImageLoggedIn['sizes']['medium_large'] ?> 384w, <?php echo $bannerImageLoggedIn['sizes']['responsive_large'] ?> 800w,  <?php echo $bannerImageLoggedIn['sizes']['responsive_xlarge'] ?> 1000w" data-sizes="auto">
				<?php else : ?>
					<div class="banner">
					<?php endif; ?>
				<?php endif; ?>
				<div class="banner__overlay"></div>
				<div class="banner__outer">
					<div class="banner__inner">
						<?php if (!SwpmMemberUtils::is_member_logged_in()): ?>
							<!-- <p class="login-title">Portal Login</p> -->
							<div class="wp_register_script"><?php echo do_shortcode('[swpm_login_form]'); ?></div>
							<script type="text/javascript">
								document.getElementById("swpm_user_name").placeholder="Username or Email";
								document.getElementById("swpm_password").placeholder="Password";
							</script>
						
						<?php else : ?>

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

								echo '<div class="welcome-message">Hi <span class="login-value--name">' . $fname_value . '.</span> Welcome to the NILegal Portal</div>';

								?>

								<div class="welcome-sub-message">Choose an option below to get started:</div>

							</div>


						<?php endif; ?>

					</div>
				</div>

					</div>