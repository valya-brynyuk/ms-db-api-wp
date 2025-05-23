<?php
/*
Template Name: Quote Calculator
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


                    <?php 
                    $member_id = SwpmMemberUtils::get_logged_in_members_id();
                    $field_name = 'email';
                    $email_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);
                    // $field_name = 'address_zipcode';
                    $field_name = 'subscr_id';
                    $password_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);
                    $field_name = 'first_name'; 
					$firstname_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name); 
					$field_name = 'last_name'; 
					$lastname_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);  
					$field_name = 'company_name'; 
					$company_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);

                    $currentUser = wp_get_current_user();
                    $userID = $member_id;
?>


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

<!-- PREFILL COGNITO FORM IN WORDPRESS EDITOR -->

    <script>
        //Cognito.prefill({"Name":{"First":`<?php //echo $firstname_value; ?>//`,"Last":`<?php //echo $lastname_value; ?>//`},"Email": `<?php //echo $email_value; ?>//`}).on('ready', function () {
        //    const emailField = document.querySelector('#cog-3');
        //    emailField.value = `<?php //echo $email_value; ?>//`;
        //    emailField.dispatchEvent(new Event('change'));
        //});

    </script>

<!-- END PREFILL COGNITO FORM IN WORDPRESS EDITOR -->

<?php get_footer(); ?>