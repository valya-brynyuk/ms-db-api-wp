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
			
					
                    /*----------------------------------------------------------------------------------*\
					API
                    \*----------------------------------------------------------------------------------*/

                    $apiBaseAddress = 'https://wn.azurewebsites.net/';
                    // $username = 'JosephBloggs@TestAccount.co.uk';
                    // $password = 'TestAccount01!';
                    $username = $email_value;
                    $password = $password_value;

                    /*----------------------------------------------------------------------------------*\
                        GET TOKEN - FILE GET CONTENTS
                    \*----------------------------------------------------------------------------------*/

                    $urlGetToken = $apiBaseAddress . 'api/User/GetToken';

                    $dataGetToken = array('username' => $username, "password" => $password, 'deviceToken' => 'string');

                    $optionsGetToken = array(
                        'http' => array(
                            'header' => "Content-type: application/x-www-form-urlencoded",
                            'method' => 'POST',
                            'content' => http_build_query($dataGetToken)
                        )
                        // "ssl" => array(
                        // 	"verify_peer"=>false,
                        // 	"verify_peer_name"=>false,
                        // )
                    );

                    $contextGetToken = stream_context_create($optionsGetToken);
                    $respGetToken = file_get_contents($urlGetToken, false, $contextGetToken);

                    $objGetToken = json_decode($respGetToken);

                    $userID = $objGetToken->User->UserId;
                    $userEmail = $objGetToken->User->UserEmail;

                    $userID = trim($userID);
                    $userEmail = trim($userEmail);

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
		Cognito.prefill({"Source":"Broker","BrokerID": "<?php echo $userID; ?>", "BrokerEmail":"<?php echo $userEmail; ?>", "BrokerCompany":"<?php echo $company_value; ?>", "BrokerFirstName":"<?php echo $firstname_value; ?>","BrokerLastName":"<?php echo $lastname_value; ?>"});
	</script>

<!-- END PREFILL COGNITO FORM IN WORDPRESS EDITOR -->

<?php get_footer(); ?>