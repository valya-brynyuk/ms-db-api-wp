<?php
/*
Template Name: Instruct A Case (v2)
*/
?>

<?php get_header(); ?>

<?php //session_start();  ?>


<div class="content">

	<?php get_template_part( 'template-partials/banner' ); ?>

	<div class="wrap">

		<div class="page-content">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<nav class="functions-nav"><?php wp_nav_menu(array('theme_location' => 'functions-nav', 'container' => false, 'depth' => '1', 'menu_class' => ' ')); ?></nav>
				<?php the_content(); ?>
				<?php endwhile; ?><?php else : ?>
				<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
			<?php endif; ?>




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
				?>


				<?php

				/*----------------------------------------------------------------------------------*\
					API
				\*----------------------------------------------------------------------------------*/

				$apiBaseAddress = 'https://wn.azurewebsites.net/';
				// $apiBaseAddress = 'https://wnpreprod.focisportal.co.uk/';
				// $username = 'JosephBloggs@TestAccount.co.uk';
				// $password = 'TestAccount01!';
				$username = $email_value;  
				$password = $password_value;

				/*----------------------------------------------------------------------------------*\
					GET TOKEN - FILE GET CONTENTS
				\*----------------------------------------------------------------------------------*/

				$urlGetToken = $apiBaseAddress . 'api/User/GetToken';
				// $urlGetToken = $apiBaseAddress . 'API/api/User/GetToken';

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

				$accessToken = $objGetToken->access_token;
				$accessToken = trim($accessToken);

				//Save to Session variables
				$_SESSION['accessToken'] = $accessToken;
				

				/*----------------------------------------------------------------------------------*\
					USE TOKEN TO GET MATTERS - USING CURL DUE TO FILE GET CONTENTS ISSUES
				\*----------------------------------------------------------------------------------*/
                $currentUser = wp_get_current_user();
                $response = MsApi::getTransactionDetails($currentUser->user_email, $accessToken);
				?>
				
				<div id="sortable-bs-2" class="sortable-container">

				<div class="text-center"><input class="search" placeholder="Search - client name" /></div>
				<br>

				<select name="filter-new" id="filter-new">
					<option value="all">All</option>
					<option value="instructed">Instructed</option>
					<option selected="selected" value="pending">Pending</option>
				</select> 
	

					<table class="data-table">
						<thead>
							<tr>
								<th><strong>Quote Type</strong></th>				
								<th><button class="sort" data-sort="applicant">Client</button></th>
								<th><strong>Price</strong></th>
								<th><button class="sort" data-sort="quote-date">Quote Date</button></th>
								<th><strong>Instructed Date</strong></th>
								<th class="hidden-field"><strong>Inst.Flag</strong></th>
								<th><strong>Instruct</strong></th>
							</tr>
						</thead>
						<tbody class="list">
							


						

						<?php if (is_array($response) || is_object($response)): //php insists on this now ?>
							<?php foreach ( $response as $key => $responseItem) : ?>
							<tr>
								<td class="type">
									<?php echo $response[$key]->TransactionType; ?>
								</td>

								<td class="applicant"><?php echo $response[$key]->ClientName; ?></td>
	
								<td class="price">
									£<?php echo $response[$key]->PropertyValue; ?>
								</td>

								<td class="quote-date">
									<span class="table-hide-date"><?php echo $response[$key]->QuoteGeneratedDate; ?></span>
									<?php if (!empty($response[$key]->QuoteGeneratedDate)): ?>
										<?php $formattedQuoteGeneratedDate =  date_create($response[$key]->QuoteGeneratedDate); ?>
										<?php echo date_format($formattedQuoteGeneratedDate, 'd/m/y'); ?>
									<?php endif; ?>
								</td>

								<td>
									<?php if (!empty($response[$key]->InstructedDate)): ?>
										<?php $formattedInstructedDate =  date_create($response[$key]->InstructedDate); ?>
										<?php echo date_format($formattedInstructedDate, 'd/m/y'); ?>
									<?php endif; ?>
	
								</td>
								<td class="instructed hidden-field"><?php echo $response[$key]->InstructedFlag; ?></td>
								<td>
								<?php $instructedMatterNumber = $response[$key]->InstructedMatterNum; ?>
								<?php /* if ($instructedMatterNumber): ?>
									<a href="<?php echo site_url('case-tracking'); ?>/case?casenumber=<?php echo $response[$key]->InstructedMatterNum; ?>">
										View Case <?php echo $response[$key]->InstructedMatterNum; ?>
									</a> | 
								<?php endif; */ ?>

								<?php
									$brokerName =  $firstname_value.' '.$lastname_value;  
									$brokerEmail = $response[$key]->UsernameEmail;
									$brokerMemberID = $response[$key]->PortalID;			
									$quoteType = $response[$key]->TransactionType;
									$clientName = $response[$key]->ClientName;
									$quoteID = $response[$key]->QuoteID;
									$formattedQuoteGeneratedDate =  date_create($response[$key]->QuoteGeneratedDate); 
									$quoteDate = date_format($formattedQuoteGeneratedDate, 'd/m/y');
								?>
								<?php $instructedFlag = $response[$key]->InstructedFlag; ?>
								<?php if ($instructedFlag ): ?>
									<span class="no-wr">
										Instructed
									</span>
								<?php else: ?>
									<span class="no-wr">
										<a href="<?php echo site_url('/instruct-a-case/instruct/'); ?>?brokername=<?php echo $brokerName; ?>&brokeremail=<?php echo $brokerEmail; ?>&brokermemberid=<?php echo $brokerMemberID; ?>&quotetype=<?php echo $quoteType; ?>&clientname=<?php echo $clientName; ?>&quotedate=<?php echo $quoteDate; ?>&quoteid=<?php echo $quoteID; ?>">Instruct</a>
									</span>
								<?php endif; ?>
								</td>
							</tr>
							<?php endforeach; ?>
							<?php endif; ?>

						</tbody>
					</table>
				</div>

		</div>
		<!--page-content-->

	</div>
	<!--wrap-->

</div>
<!--content-->

<?php get_footer(); ?>