<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<!-- title and meta: YEOST -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?php wp_head(); ?>
	<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/images/fav/favicon-196x196.png" sizes="196x196" />
	<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/images/fav/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/images/fav/favicon-32x32.png" sizes="32x32" />
	<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/images/fav/favicon-16x16.png" sizes="16x16" />
	<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/images/fav/favicon-128.png" sizes="128x128" />
	<link rel="stylesheet" href="https://use.typekit.net/tee6zov.css">

</head>
<body <?php body_class(); ?> >
	
	
	<header id="header">

		<div class="wrap--header-1">
			<div class="login-items">

				<?php  if( is_user_logged_in() ): ?>
				<ul class="login-list">
					<li><a class="profile-link" href="<?php echo site_url(); ?>/broker-login/membership-profile/">Profile</a></li>
					<li><a class="logout-link" href="<?php echo site_url(); ?>/?swpm-logout=true">Logout</a></li>
				</ul>
				<?php else: ?>
				<ul class="login-list">
					<li><a class="logout-link" href="<?php echo site_url(); ?>">Login</a></li>
				</ul>
				<?php endif; ?>
				
			</div>
		</div>

		<div class="wrap--header-2">

			<a href="<?php echo site_url(); ?>">
				<img class="site-logo" src="<?php echo get_stylesheet_directory_uri(); ?>/images/the-mortgage-shop-logo.png" alt="">
			</a>

			<div class="header__rhs">

				<nav class="top-nav">
					<?php wp_nav_menu( array( 'theme_location' => 'main-nav','container' => false, 'depth' => '1', 'menu_class' => ' ' ) ); ?>
				</nav>

				<div class="nav-trigger">
					<span class="nav-trigger__icon"></span>
				</div>

			</div>

			

		</div>



	</header>

