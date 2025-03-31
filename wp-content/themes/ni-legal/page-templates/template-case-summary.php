<?php
/*
Template Name: Case Tracking Summary
*/
?>

<?php get_header(); ?>

<div class="content">

	<?php get_template_part( 'template-partials/banner' ); ?>

	<div class="wrap">

		<div class="page-content">

			<?php if (SwpmMemberUtils::is_member_logged_in()): ?>
				
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<nav class="functions-nav"><a href="<?php echo site_url('case-tracking'); ?>">< Return to Case list</a></nav>
			<?php endwhile; ?><?php else : ?>
				<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
			<?php endif; ?>



			<?php 
				$accessToken = $_SESSION['accessToken'];
				$caseNumber = $_GET['casenumber'];
				$matterCancelled = $_GET['mc'];
			?>

			<?php if ($accessToken){

			$member_id = SwpmMemberUtils::get_logged_in_members_id();
			$field_name = 'email';
			$email_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name);
			// $field_name = 'address_zipcode';
			$field_name = 'subscr_id';
			$password_value = SwpmMemberUtils::get_member_field_by_id($member_id, $field_name); 


			/*----------------------------------------------------------------------------------*\
			ONE PERSONS MANY CASES
			\*----------------------------------------------------------------------------------*/

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

			$accessToken = $objGetToken->access_token;
			$accessToken = trim($accessToken);

			//Save to Session variables
			$_SESSION['accessToken'] = $accessToken;



			}

			/*----------------------------------------------------------------------------------*\
				USE TOKEN TO GET MILESTONES - USING CURL DUE TO FILE GET CONTENTS ISSUES
			\*----------------------------------------------------------------------------------*/
            $currentUser = wp_get_current_user();
            $brokerListing = MsApi::getBrokerListing($currentUser->user_email, $accessToken);
            $matter = $brokerListing[0] ?? [];
            $matterId = $matter->MatterNumber ?? null;
            $responseMilestones = MsApi::getMilestoneDates($matterId, $accessToken);
//            $responseMilestones = MsApi::getMilestoneDates('331491', $accessToken);
			// print "<pre>";
			// print_r($responseMilestones);
			// print "</pre>";
			// print("<pre>".print_r($response,true)."</pre>");

			/*----------------------------------------------------------------------------------*\
				USE TOKEN TO GET DETAILS (MATTER HEADER AND CASE LOG) - USING CURL DUE TO FILE GET CONTENTS ISSUES
			\*----------------------------------------------------------------------------------*/
			
			$response = MsApi::getMatterDetails($matterId, $accessToken);
			// print("<pre>".print_r($response,true)."</pre>");
            $respArr = (array)($response[0] ?? new stdClass());
            $responseHeader = array_merge($respArr, (array)$matter);
            $historyResponse = MsApi::getMatterHistory($matterId, $accessToken);

            $matterDetail = MsApi::getMatterDetail($matterId, $accessToken);
            ?>

			<div class="cols--wg">

				<div class="col--1of2--wg">

					<div class="column-inner-with-line">

						<!-- CASE DETAILS-->

						<div class="case-module">
							<h3>Case</h3>
							<p class="case-title">
								<strong><?php echo $responseHeader['MatterDescription']; ?><br></strong>
							</p>
						</div>
						
						<?php 
							$client = $_GET['client'];
							if( !empty( $client ) ): ?>
								<div class="case-module">
									<h3>Client</h3>
									<?php echo $client; ?>
								</div>
						<?php endif; ?>

						<div class="case-module">
							<h3>Solicitor Ref No</h3>
							<?php echo $responseHeader['MatterNumber']; ?>
						</div>

						<div class="case-module">
							<h3>Case Opened</h3>
							<?php $formattedCaseOpenedDate =  date_create($responseHeader['MatterOpenedDate']); ?>
							<?php echo date_format($formattedCaseOpenedDate, 'd/m/y'); ?>
						</div>

						<div class="case-module">

							<div class="case-handler-details">

								<div class="cols--s">

									<div class="col--2of3--s">

										<p class="case-handler-details__title">
											Case Handler
										</p>
										<p class="case-handler-details__name">
											<?php echo $matterDetail[0]->CaseHandlerName ?? ''; ?>
										</p>
										<p class="case-handler-details__phone">
											<a href="tel:<?php echo $matterDetail[0]->CaseHandlerTel ?? ''; ?>"><?php echo $matterDetail[0]->CaseHandlerTel ?? ''; ?></a>
										</p>
										<p class="case-handler-details__email">
											<a href="mailto:<?php echo $matterDetail[0]->CaseHandlerEmail ?? ''; ?>?subject=<?php echo $matterDetail[0]->MatterDesc ?? ''; ?> - <?php echo $matterDetail[0]->CaseHandlerEmail ?? ''; ?>">
												<?php echo $matterDetail[0]->CaseHandlerEmail ?? ''; ?>
											</a>
										</p>

									</div>

									<div class="col--1of3--s">

										<?php
										$imagePath = $matterDetail[0]->CaseHandlerPhotograph ?? '';
										$imagePath = str_replace('~','',$imagePath); ?>
										<div class="case-handler-details__portrait-containter">
											<?php if ( $imagePath): ?>
												<div class="case-handler-details__portrait" style="background-image: url('data:image/jpg;base64,<?php echo $imagePath; ?>');"></div>
												<!-- <img class="case-handler-details__portrait" src="https://wn.focisportal.co.uk/API/<?php // echo $imagePath; ?>" alt=""> -->
											<?php else: ?>
												<div class="case-handler-details__portrait" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/images/default-casehandler.jpg');"></div>
											<!-- <img class="case-handler-details__portrait" src="<?php echo get_stylesheet_directory_uri(); ?>/images/default-casehandler.jpg" alt=""> -->
											<?php endif; ?>
										</div>

									</div>

								</div>

							</div>

						</div>

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
								<?php if ($matterCancelled == 1): ?><div class="progress-bar__cancelled">CANCELLED</div><?php endif; ?>

							</div>
							
						</div>

						<div class="case-module">
							<h3>Milestones</h3>
							<!-- MILESTONES-->
							<?php
							$numMilestones = count($responseMilestones); 
							$milestoneCount = 0;
							?>
							<ul class="milestone-list">
							<?php foreach ($responseMilestones as $key => $responseItem) : ?>
							
								<?php
									$mileStoneClass = '';
									$milestoneStatus = !empty($responseMilestones[$key]->CompletedDate) ? 'completed' : 'required';
									$mileStoneCompletedDated = $responseMilestones[$key]->CompletedDate;
									$mileStoneCompletedDated = preg_replace('/\s+/', '', $mileStoneCompletedDated);

									if ( $milestoneStatus == 'completed' ){
										if ( $mileStoneCompletedDated != '01-01-1900'){
											$mileStoneClass = "milestone-status--completed";
										}
										else {
											$mileStoneClass = "milestone-status--required";
										}
									}
									elseif  ( $milestoneStatus == 'required' ){
										$mileStoneClass = "milestone-status--required";
									}
								?>
								<li class="<?php echo $mileStoneClass; ?>">
									<span class="tool-tip-source">
										<?php echo $responseMilestones[$key]->Milestone; ?>

										 <?php if(++$milestoneCount === $numMilestones) { 
											
											if ( $mileStoneCompletedDated ){
												if ( $mileStoneCompletedDated != '01-01-1900'){
													echo '- <strong>'.$mileStoneCompletedDated.'</strong>';
												}
											}
										}; ?>
										
											<?php $mileStoneCompletedDated = $responseMilestones[$key]->CompletedDate;
											
											$mileStoneCompletedDated = preg_replace('/\s+/', '', $mileStoneCompletedDated);
											if ( $mileStoneCompletedDated ){
												if ( $mileStoneCompletedDated != '01-01-1900'){
													echo '<span class="tool-tip-text"><strong>Completed Date:<br>'.$mileStoneCompletedDated.'</strong></span>';
												}
											}
											?>
										
									</span>
								</li>
							<?php endforeach; ?>
							</ul>
						</div>
					
					</div>

				</div>
			
			</div>


            <?php
            if (!empty($historyResponse)) {
                ?>
                <h3>Case Log</h3>
                <!-- CASE LOG / HISTORY -->

                <div class="case-log">

                    <div id="data-container">

                    </div>
                    <div id="pagination"></div>

                </div>
                <?php
            } else {
                ?>
                <h3>No Case Log</h3>
                <?php
            }

            ?>

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

<script>
    jQuery(function () {
        let container = jQuery('#pagination');
        container.pagination({
            dataSource: [
				<?php foreach ($historyResponse as $key => $responseItem) : ?>

					<?php $formattedDate =  date_create($historyResponse[$key]->HistoryDate); ?>

					<?php echo "{name: \"<span class='case-log__date'>".date_format($formattedDate, "d-m-Y")."</span><span class='case-log__description'>".$historyResponse[$key]->HistoryDesc."</span><br>\"}," ?>


			<?php endforeach; ?>
			
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