<?php
/*
Template Name: Instruct a Case Detail (v2) 
*/
?>

<?php get_header(); ?>

<div class="content">

<?php get_template_part( 'template-partials/banner' ); ?>

<div class="wrap">

	<div class="page-content">

		<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

			<nav class="functions-nav"><a href="<?php echo site_url('instruct-a-case'); ?>">< back to Instruct a Case Summary</a></nav>

			<?php if (SwpmMemberUtils::is_member_logged_in()): ?>

			<?php 
			$member_id = SwpmMemberUtils::get_logged_in_members_id();
			$field_name = 'email';
			$email_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);
			$field_name = 'subscr_id';
			$password_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);
			$field_name = 'first_name'; 
			$firstname_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name); 
			$field_name = 'last_name'; 
			$lastname_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);  
			$field_name = 'company_name'; 
			$company_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);  
			$field_name = 'address_street'; 
			$streetname_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);  
			$field_name = 'address_city'; 
			$city_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);
			$field_name = 'address_state';
			$state_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name); 
			$field_name = 'address_zipcode';
			$postcode_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name); 
			?>

			<?php the_content(); ?>
					
			<?php else: ?>

				Please login

			<?php endif; ?>


		<?php endwhile; else: ?>
		<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
		<?php endif; ?>

        </div>

        </div>

    </div>

</div>

<!-- PREFILL COGNITO FORM IN WORDPRESS EDITOR -->

<?php 
	$brokerName =  $_GET['brokername'];
	$brokerEmail = $_GET['brokeremail'];
	$brokerMemberID = $_GET['brokermemberid'];
	$quoteType = $_GET['quotetype'];
	$clientName = $_GET['clientname'];
	$quoteDate  = $_GET['quotedate'];
	$quoteID  = $_GET['quoteid'];
?>


	
	<script>
		Cognito.prefill({"BrokerName":"<?php echo $brokerName; ?>","BrokerEmail": "<?php echo $brokerEmail; ?>", "MemberID":"<?php echo $brokerMemberID; ?>", "CaseType":"<?php echo $quoteType; ?>","QuoteDate":"<?php echo $quoteDate; ?>","BrokerCompanyName":"<?php echo $company_value; ?>","ClientName":"<?php echo $clientName; ?>","QuoteID":"<?php echo $quoteID; ?>","BrokerAddress":"<?php echo $streetname_value.', '.$city_value.', '.$state_value.', '.$postcode_value ?>"});
	</script>

<!-- END PREFILL COGNITO FORM IN WORDPRESS EDITOR -->

<?php get_footer(); ?>