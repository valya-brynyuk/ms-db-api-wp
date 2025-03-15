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