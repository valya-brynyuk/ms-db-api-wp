<?php
/*
Template Name: Case Tracking
*/
?>

<?php get_header(); ?>

<div class="content">

	<div class="wrap">

		<div class="page-content">


			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
				<?php endwhile; ?><?php else : ?>
				<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
			<?php endif; ?>




				<?php 
					$member_id = SwpmMemberUtils::get_logged_in_members_id();
					$field_name = 'email';
					$email_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);
					$field_name = 'address_zipcode';
					$password_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name); 
				?>

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


                $response = MsApi::getBrokerListing($username, $accessToken);

				// echo $response;

				// print "<pre>";
				// print_r($response);
				// print "</pre>";

				// echo ($objGetMatters->access_token);

				?>

				<div id="sortable-bs">

				<input class="search" placeholder="Search" />
				<button class="sort" data-sort="client">Sort by Client</button>
				<button class="sort" data-sort="matter-number">Sort by Matter Number</button>
				<button class="sort" data-sort="progress">Sort by Progress</button>
					<table class="data-table">
						<thead>
							<tr>
								<th><strong>Client</strong></th>
								<th><strong>Case Details</strong></th>
								<th><strong>SOL Ref Number</strong></th>
								<th><strong>Title Deeds Received</strong></th>
								<th><strong>Mortgage Offer Received</strong></th>
								<th><strong>Progress</strong></th>
								<th><strong>Case Handler</strong></th>
								<th></th>
							</tr>
						</thead>
						<tbody class="list">

							
							
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
									<?php echo $response[$key]->Context; ?>
								</td>
								<td>
									<?php echo $response[$key]->MatterDescription; ?>
								</td>
								<td class="matter-number">
									<?php echo $response[$key]->MatterNumber; ?>
								</td>
								
								<?php foreach ($responseMilestones as $key2 => $responseItem) : ?>
									<?php $mileStoneDescription = $responseMilestones[$key2]->MilestoneDescription; ?>
									<?php $mileStoneCompletedDated = $responseMilestones[$key2]->CompletedDate; ?>
									<?php if ($mileStoneDescription == 'Title Deeds Received'): ?>
										<?php if ( $mileStoneCompletedDated ){
											echo '<td>'.$mileStoneCompletedDated.'</td>';
										}
										else {
											echo '<td></td>';
										}
										?>
									<?php endif; ?>
									<?php if ($mileStoneDescription == 'Mortgage Offer Received'): ?>
										<?php if ( $mileStoneCompletedDated ){
											echo '<td>'.$mileStoneCompletedDated.'</td>';
										}
										else {
											echo '<td></td>';
										}
										?>
									<?php endif; ?>
									
								<?php endforeach; ?>
							
							
								<td class="progress">
									<?php echo $response[$key]->Progress . '%'; ?>
								</td>
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

		</div>
		<!--page-content-->

	</div>
	<!--wrap-->

</div>
<!--content-->

<?php get_footer(); ?>