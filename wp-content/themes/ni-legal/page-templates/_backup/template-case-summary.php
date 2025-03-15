<?php
/*
Template Name: Case Summary
*/
?>

<?php get_header(); ?>

<div class="content">

	<?php get_template_part( 'template-partials/banner' ); ?>

	<div class="wrap">

		<div class="page-content">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<nav class="functions-nav"><a href="<?php echo site_url('case-tracking'); ?>">< Return to Case list</a></nav>
			<?php endwhile; ?><?php else : ?>
				<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
			<?php endif; ?>

			<!-- <a href="<?php echo site_url('case-tracking'); ?>" class="button-link">back</a> -->
			<!-- <br><br> -->

			<?php 
				$accessToken = $_SESSION['accessToken'];
				$caseNumber = $_GET['casenumber'];
			?>

			<?php if ($accessToken){



$member_id = SwpmMemberUtils::get_logged_in_members_id();
$field_name = 'email';
$email_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);
$field_name = 'address_zipcode';
$password_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name); 


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



			}
			?>

			<?php 
			/*----------------------------------------------------------------------------------*\
				USE TOKEN TO GET MILESTONES - USING CURL DUE TO FILE GET CONTENTS ISSUES
			\*----------------------------------------------------------------------------------*/

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://wn.focisportal.co.uk/FocisCoreAPI/api/Matter/GetMatterMilestones',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $caseNumber,
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer ' . $accessToken,
					'Content-Type: application/json'
				),
			));

			$response = curl_exec($curl);

			curl_close($curl);

			$responseMilestones = json_decode($response);
			// print("<pre>".print_r($response,true)."</pre>");
			
			?>

			<?php

			/*----------------------------------------------------------------------------------*\
				USE TOKEN TO GET DETAILS (MATTER HEADER AND CASE LOG) - USING CURL DUE TO FILE GET CONTENTS ISSUES
			\*----------------------------------------------------------------------------------*/

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://wn.focisportal.co.uk/FocisCoreAPI/api/Matter/GetMatterDetails',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $caseNumber,
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer ' . $accessToken,
					'Content-Type: application/json'
				),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			
			$response = json_decode($response, true);
			//print("<pre>".print_r($response,true)."</pre>");
			
			?>

			<?php $responseHeader = ($response['MatterHeader']); ?>


			<div class="cols--wg">

				<div class="col--1of2--wg">

					<div class="column-inner-with-line">

						<!-- CASE DETAILS-->

						<div class="case-module">
							<h3>Case</h3>
							<p class="case-title">
								<strong><?php echo $responseHeader['MatterDescription']; ?><br></strong>
								<?php echo $responseHeader['Context']; ?>
							</p>
						</div>

						<div class="case-module">
							<h3>Solicitor Ref No</h3>
							<?php echo $responseHeader['MatterNumber']; ?>
						</div>

						<div class="case-module">
							<h3>Case Opened</h3>
							<?php $formattedCaseOpenedDate =  date_create($responseHeader['MatterOpenedDate']); ?>
							<?php echo date_format($formattedCaseOpenedDate, 'dS F Y'); ?>
						</div>

						<div class="case-module">

							<div class="case-handler-details">

								<div class="cols--s">

									<div class="col--2of3--s">

										<p class="case-handler-details__title">
											Case Handler
										</p>
										<p class="case-handler-details__name">
											<?php echo $responseHeader['FeeEarnerName']; ?>
										</p>
										<p class="case-handler-details__phone">
											<a href="tel:<?php echo $responseHeader['FeeEarnerDirectDial']; ?>"><?php echo $responseHeader['FeeEarnerDirectDial']; ?></a>
										</p>
										<p class="case-handler-details__email">
											<a href="mailto:<?php echo $responseHeader['FeeEarnerEmail']; ?>">
												<?php echo $responseHeader['FeeEarnerEmail']; ?>
											</a>
										</p>

									</div>

									<div class="col--1of3--s">

										<?php
										$imagePath = $responseHeader['FeeEarnerImagePath']; 
										$imagePath = str_replace('~','',$imagePath); ?>
										<div class="case-handler-details__portrait-containter">
											<img class="case-handler-details__portrait" src="https://wn.focisportal.co.uk/FocisCoreAPI/<?php echo $imagePath; ?>" alt="">
										</div>

									</div>

								</div>

							</div>

						</div>

						<?php 
							//echo $responseHeader['FeeEarnerUserId'];
							//echo $responseHeader['FeeEarnerCode'];
							//echo $responseHeader['Context'];
						?>

					</div>
				</div>

				<div class="col--1of2--wg">

					<div class="column-inner-with-line">

						<div class="case-module">
							<h3>Progress</h3>
							<div class="progress-bar-container">

								<div class="progress-bar">
									<div class="progress-bar__fill" style="width:<?php echo $responseHeader['Progress']; ?>%"></div>
								</div>

								<div class="progress-bar-text"><?php echo $responseHeader['Progress']; ?>%</div>

							</div>
							
						</div>

						<div class="case-module">
							<h3>Milestones</h3>
							<!-- MILESTONES-->
							<ul class="milestone-list">
							<?php foreach ($responseMilestones as $key => $responseItem) : ?>
							
								<?php
									$mileStoneClass;
									$milestoneStatus = $responseMilestones[$key]->milestonestatus;
									if ( $milestoneStatus == 'completed' ){
										$mileStoneClass = "milestone-status--completed";
									}
									elseif  ( $milestoneStatus == 'required' ){
										$mileStoneClass = "milestone-status--required";
									}
								?>
								<li class="<?php echo $mileStoneClass; ?>">
									<span class="tool-tip-source">
										<?php echo $responseMilestones[$key]->MilestoneDescription; ?>
										<span class="tool-tip-text">
											<?php echo $responseMilestones[$key]->tooltip; ?>
											<?php $mileStoneCompletedDated = $responseMilestones[$key]->CompletedDate; ?>
											<?php if ( $mileStoneCompletedDated ){
												echo '<br><br><strong>Completed Date:<br>'.$mileStoneCompletedDated.'</strong>';
											}
											?>
										</span>
									</span>
								</li>
							<?php endforeach; ?>
							</ul>
						</div>
					
					</div>

				</div>
			
			</div>


			
				<h3>Case Log</h3>
				<!-- CASE LOG / HISTORY -->

				<?php $responseHistory = ($response['History']); ?>



				
				<div class="case-log">

					<div id="data-container">

		

				
					</div>
					<div id="pagination"></div>

				</div>					


				

		</div>
		<!--page-content-->

	</div>
	<!--wrap-->

</div>
<!--content-->

<?php get_footer(); ?>

<script>
    jQuery(function () {
        let container = jQuery('#pagination');
        container.pagination({
            dataSource: [
				<?php foreach ($responseHistory as $key => $responseItem) : ?>

					<?php $formattedDate =  date_create($responseHistory[$key]['HistoryDate']); ?>

					<?php echo '{name: \'<span class="case-log__date">'.date_format($formattedDate, 'd-m-Y').'</span><span class="case-log__description">'.$responseHistory[$key]['HistoryDescription'].'</span><br>\'},' ?>


			<?php endforeach; ?>
			
                // {name: "hello1"},
				// {name: "hello2"},
				// {name: "hello3"},
				// {name: "hello4"},
				// {name: "hello5"},
				// {name: "hello6"},
				// {name: "hello6"},
				// {name: "hello7"},
				// {name: "hello8"},
				// {name: "hello9"},
				// {name: "hello10"},
				// {name: "hello11"},
				// {name: "hello12"},
            ],
			pageSize: 20,
            callback: function (data, pagination) {
                var dataHtml = '<ul>';

                jQuery.each(data, function (index, item) {
                    dataHtml += '<li>' + item.name + '</li>';
                });

                dataHtml += '</ul>';

                jQuery("#data-container").html(dataHtml);
            }
        })
    })
				</script>