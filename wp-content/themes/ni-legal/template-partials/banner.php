<?php
	$bannerImage = get_field('banner_image');
?>

<?php if ($bannerImage): ?>
<div class="banner lazyload" data-bgset="<?php echo $bannerImage['sizes']['medium_large']?> 768w, <?php echo $bannerImage['sizes']['responsive_large'] ?> 1600w" data-sizes="auto">

	<div class="banner__overlay"></div>
	<div class="banner__outer">
		<div class="banner__inner">
<?php else: ?>
<div class="no-banner">
<?php endif; ?>
				<?php if ( !get_field('hide_title')): ?>
				<h1><?php the_title(); ?></h1>
				<?php endif; ?>
				<div class="sub-title">
					<?php the_field('banner_sub_title'); ?>
				</div>

<?php if ($bannerImage): ?>
		</div>
	</div>
<?php endif; ?>
</div>

