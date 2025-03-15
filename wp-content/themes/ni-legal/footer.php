		<footer id="footer">
			<div class="wrap">
			
				<p>
					Copyright © nilegal.com &copy; <?php echo date("Y"); ?>. All rights reserved
				</p>
			
			</div>
		</footer>
				
		<?php wp_footer(); ?>

		<?php if (is_page (92)): ?>

		<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js-pages/instruct-a-case.js?v=3"></script>

		<?php elseif ( is_page (76)): ?>

		<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js-pages/case-tracking.js?v=3"></script>
			
		<?php endif; ?>

	</body>
</html>
