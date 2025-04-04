<?php
/*
Template Name: Case Tracking
*/
?>

<?php get_header(); ?>

<div class="content">

	<?php get_template_part( 'template-partials/banner' ); ?>

	<div class="wrap">

		<div class="page-content">
			
			<?php if (SwpmMemberUtils::is_member_logged_in()): ?>

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
					// $field_name = 'address_zipcode';
					$field_name = 'subscr_id';
					$password_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name); 
					
					//$APIPassword = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name); 
				?>

				<?php

				/*----------------------------------------------------------------------------------*\
					API
				\*----------------------------------------------------------------------------------*/

				$apiBaseAddress = 'https://wn.azurewebsites.net/';
				//$apiBaseAddress = 'https://wnpreprod.focisportal.co.uk/';
				// $username = 'JosephBloggs@TestAccount.co.uk';
				// $password = 'TestAccount01!';
				$username = $email_value;
				$password = $password_value;


				/*----------------------------------------------------------------------------------*\
					GET TOKEN - FILE GET CONTENTS
				\*----------------------------------------------------------------------------------*/

				$urlGetToken = $apiBaseAddress . 'api/User/GetToken';
				//$urlGetToken = $apiBaseAddress . 'API/api/User/GetToken';

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
                $response = MsApi::getBrokerListing($currentUser->user_email, $accessToken);

                if (!empty($objGetMatters)) {
                    echo ($objGetMatters->access_token);
                }
				?>

				<div id="sortable-bs" class="sortable-container">

				<div class="text-center"><input class="search" placeholder="Search" /><br></div>
				<label for="filter-new">
					Status
					<select name="filter-new" id="filter-new">
						<option selected="selected" value="active">Active</option>
						<option value="completed">Completed</option>
						<option value="cancelled">Cancelled</option>
					</select>
				</label>

				
					<table class="data-table">
						<thead>
							<tr>
					
								<th class="client"><strong>Client</strong></th>
								<th class="case-details"><strong>Case Details</strong></th>
								<th><strong><button class="sort" data-sort="matter-opened-date">Date Case Opened</button></strong></th>
								<th><strong>Title Deeds Received</strong></th>
								<th><strong>Mortgage Offer Received</strong></th>
								<th><strong><button class="sort" data-sort="progress">Progress</button></strong></th>
								<th class="hidden-field"><strong>Progress Hidden</strong></th>
								<th class="hidden-field"><strong>Cancelled Hidden</strong></th>
								<th class="hidden-field"><strong>MatterCompleted></strong></th>
								<th class="hidden-field"><strong>MatterCancelled></strong></th>
								<th><strong>Case Handler</strong></th>
								<th></th>
							</tr>
						</thead>
						<tbody class="list">

						
						<?php if (!isset($response->Message) || $response->Message !== 'Authorization has been denied for this request.'): ?>
							
							<?php $tempCounter = 0; ?>

							<?php foreach ( $response as $key => $responseItem) : ?>

								<?php $tempCounter++; ?>

							<tr id="foo">
								<td class="client">

									<?php echo $response[$key]->Client; ?>
								</td>
								<td class="case-details">
									<?php echo $response[$key]->MatterDescription; ?>
								</td>
								<td class="matter-opened-date">
									<span class="table-hide-date"><?php echo $response[$key]->MatterOpened; ?></span>
									<?php if (!empty($response[$key]->MatterOpened)): ?>
										<?php $formattedMatterOpenedDate =  date_create($response[$key]->MatterOpened); ?>
										<?php echo date_format($formattedMatterOpenedDate, 'd/m/y'); ?>
									<?php endif; ?>
								</td>

								<td class="title-deeds-received">
									<?php if (!empty($response[$key]->TitleDeedsReceived)): ?>
										<?php $formattedTitleDeedsReceived =  date_create($response[$key]->TitleDeedsReceived); ?>
										<?php echo date_format($formattedTitleDeedsReceived, 'd/m/y'); ?>
									<?php endif; ?>
								</td>
								
								<td class="date-mortgage-offer-received">
									<?php if (!empty($response[$key]->MortgageOfferReceived)): ?>
									<?php $formattedMortgageOfferReceived =  date_create($response[$key]->MortgageOfferReceived); ?>
									<?php echo date_format($formattedMortgageOfferReceived, 'd/m/y'); ?>
									<?php endif; ?>
								</td>
							
								<td class="progress">
								<?php if ($response[$key]->MatterCancelled): ?>
									<span class="cancelled-notify">CANCELLED</span>
								<?php else: ?>
									<?php echo $response[$key]->Progress . '%'; ?>
								<?php endif; ?>

								</td>
								<td class="progresshidden hidden-field">
									<?php echo $response[$key]->Progress . '%'; ?>
								</td>
								<td class="cancelledhidden hidden-field">
									<?php echo $response[$key]->MatterCancelled; ?>
								</td>
                                <td class="MatterCancelled hidden-field">
                                    <?php echo $response[$key]->MatterCancelled; ?>
                                </td>
                                <td class="MatterCompleted hidden-field">
                                    <?php echo $response[$key]->MatterCompleted; ?>
                                </td>
								<td class="client">
									<?php echo $response[$key]->CaseHandler; ?>
								</td>

								<td>
									<span class="no-wr">
										<a href="<?php echo site_url('case-tracking/case?casenumber='); ?><?php echo $response[$key]->MatterNumber; ?><?php if ($response[$key]->MatterCancelled): ?>&mc=1<?php endif; ?>&client=<?php echo $response[$key]->Client; ?>">View Case</a>
									</span>
								</td>

							</tr>
							<?php endforeach; ?>
							<?php else: ?>
								<p>No authorisation, please login</p>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

		<?php else: //login check ?> 
			<p>Not logged in </p>
		<?php endif; //login check ?>	

		</div>
		<!--page-content-->

	</div>
	<!--wrap-->

</div>
<!--content-->

<?php get_footer(); ?>