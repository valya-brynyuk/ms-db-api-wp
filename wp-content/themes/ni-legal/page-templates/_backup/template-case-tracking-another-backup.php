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

				<?php// echo 'API password is: '.$APIPassword; ?>

				<?php 
				// echo $member_id;
				// echo $email_value;
				// echo $password_value;
				?>

				<?php
				/*----------------------------------------------------------------------------------*\
					ONE PERSONS MANY CASES
				\*----------------------------------------------------------------------------------*/

				/*----------------------------------------------------------------------------------*\
					API
				\*----------------------------------------------------------------------------------*/

				$apiBaseAddress = 'https://wn.focisportal.co.uk/';
				// $username = 'JosephBloggs@TestAccount.co.uk';
				// $password = 'TestAccount01!';
				$username = $email_value;
				$password = $password_value;

				/*----------------------------------------------------------------------------------*\
					GET TOKEN - FILE GET CONTENTS
				\*----------------------------------------------------------------------------------*/

				$urlGetToken = $apiBaseAddress . 'FocisCoreAPI/api/User/GetToken';

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

				// print "<pre>";
				// print_r($objGetToken);
				// print "</pre>";

				$accessToken = $objGetToken->access_token;
				$accessToken = trim($accessToken);

				//Save to Session variables
				$_SESSION['accessToken'] = $accessToken;
				

				//echo $accessToken;
				// $accessToken = 'WacTRxlAm3eCCxtxoA2gpdI1MMFmx_sRk85lfIg5hU2xWoMWwLmbIvFb06YtzDxwPec1abq9zFTTnW-PV8qP_-Pdq35nwjjUT6LNIGLF2stqD6M-7I7u_qm00-nFOL-TyPYPNyCD3j7bgaLUB4nQZ_buknHZKtjYbkAIq9ms5v1R1h1ph9Wevury_qzv4Y2eziWglOBK8VttjQSoaRouar8RqYhy7u6cC00bIM0Hrgnqsx6tOSVygXcOkEaOnb7jphqKM3W6-HfLCYIyvpVVaOD6oxOgzflq19cE2HEXEfnCqbt1dIQM8JPKzkeP-Z0sasAh8OQVyErEwVERB0DbNfD6Yi7Cp01vCSJKNxpLMCxb1SAGehCoHh53dpvBknmeAwgu4QkQnU5QIL_xTTnw5w';


				/*----------------------------------------------------------------------------------*\
					USE TOKEN TO GET MATTERS - USING CURL DUE TO FILE GET CONTENTS ISSUES
				\*----------------------------------------------------------------------------------*/

				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://wn.focisportal.co.uk/FocisCoreAPI/api/Matter/GetMatters',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					// CURLOPT_POSTFIELDS => '"JosephBloggs@TestAccount.co.uk"',
					// CURLOPT_POSTFIELDS => '"phigginsontestbroker1@wilson-nesbitt.co.uk"',
					
					CURLOPT_POSTFIELDS => '"'.$username.'"',
					// CURLOPT_POSTFIELDS => '"'.$email_value.'"',
					
					CURLOPT_HTTPHEADER => array(
						'Authorization: Bearer ' . $accessToken,
						'Content-Type: application/json'
					),
				));


				$response = curl_exec($curl);

				curl_close($curl);

				$response = json_decode($response);

				// echo $response;

				// print "<pre>";
				// print_r($response);
				// print "</pre>";

				// echo ($objGetMatters->access_token);
				?>

				<div id="sortable-bs" class="sortable-container">

				<input class="search" placeholder="Search" /><br>
				<input name="filter-100-percent" id="filter-100-percent" type="checkbox"><label for="filter-100-percent">Hide Completed Cases</label>

				
				<!-- <button class="sort" data-sort="matter-number">Sort by Matter Number</button> -->
				
				
				
					<table class="data-table">
						<thead>
							<tr>
								<th><strong><button class="sort" data-sort="client">Client</button></strong></th>
								<th><strong>Case Details</strong></th>
								<th><strong><button class="sort" data-sort="matter-opened-date">Date Case Opened</button></strong></th>
								<th><strong>Title Deeds Received</strong></th>
								<th><strong>Mortgage Offer Received</strong></th>
								<th><strong><button class="sort" data-sort="progress">Progress</button></strong></th>
								<th><strong>Case Handler</strong></th>
								<th></th>
							</tr>
						</thead>
						<tbody class="list">

						
						<?php if ($response->Message !== 'Authorization has been denied for this request.'): ?>
							
							<?php foreach ( $response as $key => $responseItem) : ?>

								<?php 
			/*----------------------------------------------------------------------------------*\
				USE TOKEN TO GET MILESTONES - USING CURL DUE TO FILE GET CONTENTS ISSUES
			\*----------------------------------------------------------------------------------*/
			
			$currentMater = $response[$key]->MatterNumber;

			$curl2 = curl_init();

			curl_setopt_array($curl2, array(
				CURLOPT_URL => 'https://wn.focisportal.co.uk/FocisCoreAPI/api/Matter/GetMatterMilestones',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $currentMater,
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer ' . $accessToken,
					'Content-Type: application/json'
				),
			));

			$response2 = curl_exec($curl2);

			curl_close($curl2);

			$responseMilestones = json_decode($response2);
			// print("<pre>".print_r($response,true)."</pre>");
			
			?>

							<tr>
								<td>
									(Context?) Missing from API
									<?php //echo $response[$key]->Context; ?>
								</td>
								<td class="case-details">
									<?php echo $response[$key]->MatterDescription; ?>
								</td>
								<td class="matter-opened-date">
								<?php $formattedMatterOpenedDate =  date_create($response[$key]->MatterOpenedDate); ?>
								<span class="table-hide-date"><?php echo $response[$key]->MatterOpenedDate; ?></span>
								<?php echo date_format($formattedMatterOpenedDate, 'd/m/y'); ?>
								</td>
								<!-- <td class="matter-number">
									<?php //echo $response[$key]->MatterNumber; ?>
								</td> -->
								<?php if ($responseMilestones): ?>
									<?php foreach ($responseMilestones as $key2 => $responseItem) : ?>
										<?php $mileStoneDescription = $responseMilestones[$key2]->MilestoneDescription; ?>
										<?php $mileStoneCompletedDated = $responseMilestones[$key2]->CompletedDate; ?>
										
										<?php if ($mileStoneDescription == 'Title Deeds Received'): ?>
											<?php if ( $mileStoneCompletedDated ){
												$formattedmileStoneCompletedDated =  date_create($mileStoneCompletedDated);
												echo '<td class="title-deeds-received">'.date_format($formattedmileStoneCompletedDated, 'd/m/y').'</td>';
											}
											else {
												echo '<td class="title-deeds-received"></td>';
											}
											?>
										<?php endif; ?>
										<?php if ($mileStoneDescription == 'Mortgage Offer Received' || $mileStoneDescription == 'Offer Received'): ?>
											<?php if ( $mileStoneCompletedDated ){
												$formattedmileStoneCompletedDated =  date_create($mileStoneCompletedDated);
												echo '<td class="date-mortgage-offer-received">'.date_format($formattedmileStoneCompletedDated, 'd/m/y').'</td>';
											}
											else {
												echo '<td class="date-mortgage-offer-received"></td>';
											}
											?>
										<?php endif; ?>
										
									<?php endforeach; ?>
								<?php else: ?>
									<td></td><td></td>
								<?php endif; ?>
							
							
								<td class="progress"><?php echo $response[$key]->Progress . '%'; ?></td>
								<td class="client">
									<?php echo $response[$key]->FeeEarnerName; ?>
								</td>
								<td>
									<span class="no-wr">
										<a href="<?php echo site_url('case-tracking/case?casenumber='); ?><?php echo $response[$key]->MatterNumber; ?>">View Case</a>
									</span>
								</td>
							</tr>
							<?php endforeach; ?>
							<?php else: ?>
								<p>No authorisation, please login</p>
							<?php endif; ?>
							<!-- <tr>
								<td class="client">John Smith</td>
								<td>Description, description, description</td>
								<td class="matter">123456</td>
								<td class="progress">10%</td>
								<td><a href="<?php //echo site_url('case-tracking/case?casenumber='); ?><?php// echo $response[$key]->MatterNumber; ?>">Show Details</a></td>
							</tr>
							<tr>
								<td class="client">Jayne Jones</td>
								<td>Description, description, description</td>
								<td class="matter">654321</td>
								<td class="progress">80%</td>
								<td><a href="<?php// echo site_url('case-tracking/case?casenumber='); ?><?php //echo $response[$key]->MatterNumber; ?>">Show Details</a></td>
							</tr> -->
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