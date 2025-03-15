<?php
/*
Template Name: Case Summary
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
				$accessToken = $_SESSION['accessToken'];
				$caseNumber = $_GET['casenumber'];

				/*----------------------------------------------------------------------------------*\
					USE TOKEN TO GET MATTERS - USING CURL DUE TO FILE GET CONTENTS ISSUES
				\*----------------------------------------------------------------------------------*/
				

				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://wn.focisportal.co.uk/FocisCoreAPI/api/Matter/GetMatterHeader',
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

				$response = json_decode($response);

				// print "<pre>";
				// print_r($response);
				// print "</pre>";


				// echo ($objGetMatters->access_token);

				?>

				<table class="data-table">
					<tr>
						<th><strong>Client</strong></th>
						<th><strong>Property</strong></th>
						<th><strong>Matter Number</strong></th>
						<th><strong>Progress</strong></th>
					</tr>
					<tr>
						<td><?php echo $response->FeeEarnerName; ?></td>
						<td><?php echo $response->MatterDescription; ?></td>
						<td><?php echo $response->MatterNumber; ?></td>
						<td><?php echo $response->Progress . '%'; ?></td>
					</tr>
				</table>


				<?php

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

				
				$response = json_decode($response);

				// print("<pre>".print_r($response,true)."</pre>");
				
				?>


				<table class="data-table">
					<tr>
						<th><strong>MilestoneDescription</strong></th>
						<th><strong>CompletedDate</strong></th>
						<th><strong>milestonestatus</strong></th>
						<th><strong>sortorder</strong></th>
						<th><strong>tooltip</strong></th>
					</tr>
					<?php foreach ($response as $key => $responseItem) : ?>
						<tr>
							<td><?php echo $response[$key]->MilestoneDescription; ?></td>
							<td><?php echo $response[$key]->CompletedDate; ?></td>
							<td><?php echo $response[$key]->milestonestatus; ?></td>
							<td><?php echo $response[$key]->sortorder; ?></td>
							<td><?php echo $response[$key]->tooltip; ?></td>
						</tr>
					<?php endforeach; ?>
				</table>





				<?php

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



				<table class="data-table">
					<tr>
						<th><strong>MatterNumber</strong></th>
						<th><strong>MatterDescription</strong></th>
						<th><strong>FeeEarnerName</strong></th>
						<th><strong>FeeEarnerImagePath</strong></th>
						<!-- <th><strong>FeeEarnerPhoto</strong></th> -->
						<th><strong>FeeEarnerDirectDial</strong></th>
						<th><strong>FeeEarnerEmail</strong></th>
						<th><strong>FeeEarnerUserId</strong></th>
						<th><strong>MatterOpenedDate</strong></th>
						<th><strong>FeeEarnerCode</strong></th>
						<th><strong>Progress</strong></th>
						<th><strong>Context</strong></th>
					</tr>
					<tr>
					<td><?php echo $responseHeader['MatterNumber']; ?></td>
					<td><?php echo $responseHeader['MatterDescription']; ?></td>
					<td><?php echo $responseHeader['FeeEarnerName']; ?></td>
					<td>
					<?php
						$imagePath = $responseHeader['FeeEarnerImagePath']; 
						$imagePath = str_replace('~','',$imagePath); ?>
						<img src="https://wn.focisportal.co.uk/FocisCoreAPI/ImageStorage/PHIGGINSON.jpg" alt="">	
						
					</td>
					<td><?php echo $responseHeader['FeeEarnerDirectDial']; ?></td>
						<td><?php echo $responseHeader['FeeEarnerEmail']; ?></td>
						<td><?php echo $responseHeader['FeeEarnerUserId']; ?></td>
						<td><?php echo $responseHeader['MatterOpenedDate']; ?></td>
						<td><?php echo $responseHeader['FeeEarnerCode']; ?></td>
						<td><?php echo $responseHeader['Progress']; ?>%</td>
						<td><?php echo $responseHeader['Context']; ?></td>
					</tr>
				</table>


				<?php $responseHistory = ($response['History']); ?>


				<table class="data-table">
					<tr>
						<th><strong>Date</strong></th>
						<th><strong>Description</strong></th>
					</tr>
					<?php foreach ($responseHistory as $key => $responseItem) : ?>
						<?php $formattedDate =  date_create($responseHistory[$key]['HistoryDate']); ?>
						<tr>
							<td><?php echo date_format($formattedDate, 'd-m-Y'); ?></td>
							<td><?php echo $responseHistory[$key]['HistoryDescription']; ?></td>
						</tr>
					<?php endforeach; ?>
				</table>

		</div>
		<!--page-content-->

	</div>
	<!--wrap-->

</div>
<!--content-->

<?php get_footer(); ?>