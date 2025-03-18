<?php
/*
Template Name: Instruct A Case (v2)
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
				$username = 'JosephBloggs@TestAccount.co.uk';
				$password = 'TestAccount01!';
				// $username = $email_value;  
				// $password = $password_value; !111NEED PASSWORD FOR "testing104@wilson-nesbitt.co.uk" example!!1

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

                $response = MsApi::getTransactionDetails($username, $accessToken);

				// echo $response;

				// print "<pre>";
				// print_r($response);
				// print "</pre>";

				// echo ($objGetMatters->access_token);

				?>
				
				<div id="sortable-bs-2">

				<input class="search" placeholder="Search" />
				<button class="sort" data-sort="type">Sort by Type</button>
				<button class="sort" data-sort="applicant">Sort by Applicant</button>
				<button class="sort" data-sort="price">Sort by Price</button>
					<table class="data-table">
						<thead>
							<tr>
								<th><strong>Quote Type</strong></th>
								<th><strong>Email</strong></th>
								<th><strong>Applicants</strong></th>
								<!-- <th><strong>PortalID</strong></th> -->
								<th><strong>Price</strong></th>
								<th><strong>Broker Fee</strong></th>
								<!-- <th><strong>TotalFee</strong></th> -->
								<th><strong>QuoteGeneratedDate</strong></th>
								<th><strong>Inst.Flag</strong></th>
								<th><strong>Inst.Date</strong></th>
								<th><strong>Inst.MatterNum</strong></th>
							</tr>
						</thead>
						<tbody class="list">

							
							
							<?php foreach ( $response as $key => $responseItem) : ?>
							<tr>
								<td class="type">
									<?php echo $response[$key]->TransactionType; ?>
								</td>
								<td>
									<?php echo $response[$key]->UsernameEmail; ?>
								</td>
								<td class="applicant">
									<?php echo $response[$key]->ClientName; ?>
								</td>
								<!-- <td>
									 echo $response[$key]->PortalID;
								</td> -->
								<td class="price">
									£<?php echo $response[$key]->PropertyValue; ?>
								</td>
								<td>
									£<?php echo $response[$key]->BaseFee; ?>
								</td>
								<!-- <td class="client">
									echo $response[$key]->TotalFee;
								</td> -->
								<td>
								<?php $formattedQuoteGeneratedDate =  date_create($response[$key]->QuoteGeneratedDate); ?>
							<?php echo date_format($formattedQuoteGeneratedDate, 'dS F Y'); ?>
									
								</td>
								<td>
									<?php echo $response[$key]->InstructedFlag; ?>
								</td>
								<td><?php $formattedInstructedDate =  date_create($response[$key]->InstructedDate); ?>
							<?php echo date_format($formattedInstructedDate, 'dS F Y'); ?>
									<!-- echo $response[$key]->InstructedDate; -->
								</td>
								<td>
									<?php echo $response[$key]->InstructedMatterNum; ?>
								</td>
								<td>
									<span class="no-wr">
										<a href="#">Intruct</a>
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