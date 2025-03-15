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
				<!-- <h1><?php //the_title(); ?></h1> -->
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
					$field_name = 'first_name'; 
					$firstname_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name); 
					$field_name = 'last_name'; 
					$lastname_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name); 
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

				// $apiBaseAddress = 'https://wn.focisportal.co.uk/';
				$apiBaseAddress = 'https://wnpreprod.focisportal.co.uk/';
				// $username = 'JosephBloggs@TestAccount.co.uk';
				// $password = 'TestAccount01!';
				$username = $email_value;  
				$password = $password_value;
				// !111NEED PASSWORD FOR "testing104@wilson-nesbitt.co.uk" example!!1

				/*----------------------------------------------------------------------------------*\
					GET TOKEN - FILE GET CONTENTS
				\*----------------------------------------------------------------------------------*/

				// $urlGetToken = $apiBaseAddress . 'FocisCoreAPI/api/User/GetToken';
				$urlGetToken = $apiBaseAddress . 'API/api/User/GetToken';

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
					//CURLOPT_URL => 'https://wn.focisportal.co.uk/FocisCoreAPI/api/Matter/GetClientTransactionDetails',
					CURLOPT_URL => 'https://wnpreprod.focisportal.co.uk/API/api/Matter/GetClientTransactionDetails',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					// CURLOPT_POSTFIELDS => '"JosephBloggs@TestAccount.co.uk"',
					// CURLOPT_POSTFIELDS => '"phigginsontestbroker1@wilson-nesbitt.co.uk"',
					// CURLOPT_POSTFIELDS => '"testing104@wilson-nesbitt.co.uk"',
					CURLOPT_POSTFIELDS => '"'.$username.'"',
					// CURLOPT_POSTFIELDS => '"'.$email_value.'"',
					
					CURLOPT_HTTPHEADER => array(
						'Authorization: Bearer ' . $accessToken,
						'Content-Type: application/json'
					),
				));

				// Lorem ipsum dolor sit amet consectetur adipisicing elit. Hic quisquam fugiat tempore, aliquam impedit aspernatur unde a molestiae iusto dolores eligendi exercitationem ratione cupiditate necessitatibus culpa facilis repellendus doloremque ex!

				$response = curl_exec($curl);

				curl_close($curl);

				$response = json_decode($response);

				// echo $response;

				// print "<pre>";
				// print_r($response);
				// print "</pre>";

				// echo ($objGetMatters->access_token);

				?>
				
				<div id="sortable-bs-2" class="sortable-container">

				<input class="search" placeholder="Search - client name" /><br>
				<input name="filter-instructed" id="filter-instructed" type="checkbox" checked="checked"><label for="filter-instructed">Hide Instructed Cases</label>
	
				
				

				
				 
				<?php 
					// $_SESSION["intructBrokerName"] = $brokerName;
					// $_SESSION["intructBrokerEmail"]="MyValue";
					// $_SESSION["intructBrokerMemberID"]="MyValue";
					// $_SESSION["intructBrokerCaseType"]= $quoteType;
					// $_SESSION["intructBrokerClientName"]="MyValue";
					// $_SESSION["intructBrokerQuoteDate"]="MyValue";
				?>



					<table class="data-table">
						<thead>
							<tr>
								<th><strong>Quote Type</strong></th>
								<!-- <th><strong>Email</strong></th> -->
								<th><button class="sort" data-sort="applicant">Client</button></th>
								<!-- <th><strong>PortalID</strong></th> -->
								<th><strong>Price</strong></th>
								<!-- <th><strong>Broker Fee</strong></th> -->
								<!-- <th><strong>TotalFee</strong></th> -->
								<th><button class="sort" data-sort="quote-date">Sort by Quote Date</button></th>
								<th><strong>Instructed Date</strong></th>
								<!-- <th><strong>Inst.Flag</strong></th> -->
								<th><strong>Matter / Instruct</strong></th>
							</tr>
						</thead>
						<tbody class="list">
							


						

						<?php if (is_array($response) || is_object($response)): //php insists on this now ?>
							<?php foreach ( $response as $key => $responseItem) : ?>
							<tr>
								<td class="type">
									<?php echo $response[$key]->TransactionType; ?>
								</td>
								<!-- <td>
									<a href="mailto: echo $response[$key]->UsernameEmail; ?>"> echo $response[$key]->UsernameEmail; ?></a>	
								</td> -->
								<td class="applicant"><?php echo $response[$key]->ClientName; ?></td>
								<!-- <td>
									 echo $response[$key]->PortalID;
								</td> -->
								<td class="price">
									£<?php echo $response[$key]->PropertyValue; ?>
								</td>
								<!-- <td>
									£ echo $response[$key]->BaseFee; 
								</td> -->
								<!-- <td class="client">
									echo $response[$key]->TotalFee;
								</td> -->
								<td class="quote-date">

								<?php $formattedQuoteGeneratedDate =  date_create($response[$key]->QuoteGeneratedDate); ?>
								<span class="table-hide-date"><?php echo $response[$key]->QuoteGeneratedDate; ?></span>
							<?php echo date_format($formattedQuoteGeneratedDate, 'd/m/y'); ?>
									
								</td>

								<td><?php $formattedInstructedDate =  date_create($response[$key]->InstructedDate); ?>
							<?php echo date_format($formattedInstructedDate, 'd/m/y'); ?>
									<!-- echo $response[$key]->InstructedDate; -->
								</td>
								<!-- <td class="instructed"><?php //echo $response[$key]->InstructedFlag; ?></td> -->
								<td>
								<?php $instructedMatterNumber = $response[$key]->InstructedMatterNum; ?>
								<?php if ($instructedMatterNumber): ?>
									<a href="<?php echo site_url('case-tracking'); ?>/case?casenumber=<?php echo $response[$key]->InstructedMatterNum; ?>">
										View Case <?php echo $response[$key]->InstructedMatterNum; ?>
									</a> | 
								<?php endif; ?>
								<!-- </td>
								<td> -->
								<?php
									$brokerName =  $firstname_value.' '.$lastname_value;  
									$brokerEmail = $response[$key]->UsernameEmail;
									$brokerMemberID = $response[$key]->PortalID;			
									$quoteType = $response[$key]->TransactionType;
									$clientName = $response[$key]->ClientName;
									$formattedQuoteGeneratedDate =  date_create($response[$key]->QuoteGeneratedDate); 
									$quoteDate = date_format($formattedQuoteGeneratedDate, 'd/m/y');
								?>
									<span class="no-wr">
										<a href="<?php echo site_url('/instruct-a-case/instruct/'); ?>?brokername=<?php echo $brokerName; ?>&brokeremail=<?php echo $brokerEmail; ?>&brokermemberid=<?php echo $brokerMemberID; ?>&quotetype=<?php echo $quoteType; ?>&clientname=<?php echo $clientName; ?>&quotedate=<?php echo $quoteDate; ?>">Instruct</a>
									</span>
								</td>
							</tr>
							<?php endforeach; ?>
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

		</div>
		<!--page-content-->

	</div>
	<!--wrap-->

</div>
<!--content-->

<?php get_footer(); ?>