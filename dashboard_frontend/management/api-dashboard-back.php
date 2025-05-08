<?php

$headers = getallheaders();

header('Content-Type: application/json');
include('../config.php');
if (isset($headers['Authorization'])) {
    if (preg_match('/^Bearer\s+(\S+)$/', $headers['Authorization'], $matches)) {
        $accessToken = $matches[1];
    } else {
        http_response_code(400);
		echo json_encode(["error" => "Authorization header present, but incorrectly formatted."]);
		exit();
    }
} else {
	
	session_start();
	require '../sso/autoload.php';

	checkSession("RootAdmin");
}
$link = mysqli_connect($dbhost, $dbuser, $dbpassword);
mysqli_select_db($link, $dbapimanagername);


$input = json_decode(file_get_contents("php://input"), true);
$choice = isset($input['action']) ? $input['action'] : '';

$accessToken = isset($_SESSION['accessToken']) ? $_SESSION['accessToken'] : $accessToken; //use token of session over header, if there is one
if (empty($accessToken)) {
	$accessToken = isset($input['accessToken']) ? $input['accessToken'] : '';
		if (empty($accessToken)) {
		http_response_code(400);
		echo json_encode(["error" => "Access Token not found in session or header of request"]);
		exit();}
}

function checkKeycloakToken($accessToken,$ssoUserinfoEndpoint) {
	if (isset($_SESSION['accessToken'])) return true; # if we are here and the session is set, we are authenticated as root already
    $url = $ssoUserinfoEndpoint;

    $headers = [
        "Authorization: Bearer {$accessToken}"
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Set to true in production

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        return json_decode($response, true); // Token is valid, return user info
    } else {
        return false; // Token is invalid
    }
}
if (!checkKeycloakToken($accessToken,$ssoUserinfoEndpoint)) {
	http_response_code(400);
    echo json_encode(["error" => "Access Token verification failed"]);
    exit();
}

function decodeJwt($jwt) {
    $parts = explode(".", $jwt);

    if (count($parts) !== 3) {
        return ["error" => "Invalid JWT token format"];
    }

    $payload = json_decode(base64UrlDecode($parts[1]), true);

    if (!$payload) {
        return ["error" => "Invalid token encoding"];
    }

    return $payload;
}

function base64UrlDecode($data) {
    $data = str_replace(['-', '_'], ['+', '/'], $data);
    $padding = strlen($data) % 4;
    if ($padding) {
        $data .= str_repeat('=', 4 - $padding);
    }
    return base64_decode($data);
}

function isAdmin($accessToken) {
    $decodedToken = decodeJwt($accessToken);

    if (isset($decodedToken['error'])) {
        return false;
    }

    if (isset($decodedToken['preferred_username']) && $decodedToken['preferred_username'] === "userrootadmin") {
        return true;
    }

    if (isset($decodedToken['realm_access']['roles']) && in_array("userrootadmin", $decodedToken['realm_access']['roles'])) {
        return true;
    }

    return false;
}


if (!isset($_SESSION['accessToken']))  # if we are here and the session is set, we are authenticated as root already
	if (isset($_SESSION['accessToken']).!isAdmin($accessToken)) {
	http_response_code(400);
    echo json_encode(["error" => "User doesn't seem to be userrootadmin".!isAdmin($accessToken)]);
    exit();
}

switch ($choice){
	case "getRules":
		$query = isset($input['query']) ? $input['query'] : '';

		if (empty($query)) {
			http_response_code(500);
			echo json_encode(["error" => "Empty search query"]);
			exit();
		}

		$sql = "SELECT b.apiname as 'Resource name', a.user as 'User', a.kind_of_limit as 'Kind of rule', a.additional_info as 'Details of rules', a.resource as 'Resource id', a.timebegin as 'Valid from', a.timeend as 'Valid to' FROM ratelimit as a join apitable as b on a.resource=b.idapi where a.resource=?";
		$stmt = mysqli_prepare($link, $sql);

		if ($stmt) {
			mysqli_stmt_bind_param($stmt, "s", $query);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			
			$data = [];
			while ($row = mysqli_fetch_assoc($result)) {
				$data[] = $row;
			}

			mysqli_stmt_close($stmt);
			echo json_encode(["results" => $data]);
		} else {
			http_response_code(500);
			echo json_encode(["error" => "Query preparation failed: ". mysqli_error($link)]);
		}

		// Close the database connection
		mysqli_close($link);
		exit;
		break;
	case "deleteRule":
		$deletionid = isset($input['deletionid']) ? $input['deletionid'] : '';
		$deletionuser = isset($input['deletionuser']) ? $input['deletionuser'] : '';

		if (empty($deletionid) || empty($deletionuser)) {
			http_response_code(400);
			echo json_encode(["error" => "At least one parameter seems to be missing"]);
			exit();
		}

		// Start the transaction
		mysqli_begin_transaction($link);

		try {
			// Move data to deletedratelimit table
			$sql_insert = "INSERT INTO `deletedratelimit` 
						   SELECT * FROM `ratelimit` 
						   WHERE `user` = ? AND `resource` = ?";
			$stmt = mysqli_prepare($link, $sql_insert);
			
			if (!$stmt) {
				throw new Exception("Query preparation failed: " . mysqli_error($link));
			}

			mysqli_stmt_bind_param($stmt, "si", $deletionuser, $deletionid);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);

			// Delete data from ratelimit table
			$sql_delete = "DELETE FROM `ratelimit` 
						   WHERE `user` = ? AND `resource` = ?";
			$stmt = mysqli_prepare($link, $sql_delete);

			if (!$stmt) {
				throw new Exception("Query preparation failed: " . mysqli_error($link));
			}

			mysqli_stmt_bind_param($stmt, "si", $deletionuser, $deletionid);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);

			// Commit the transaction
			mysqli_commit($link);
			echo json_encode(["success" => true]);
		} catch (Exception $e) {
			// Rollback transaction on failure
			mysqli_rollback($link);
			http_response_code(500);
			echo json_encode(["error" => $e->getMessage()]);
		}

		// Close the database connection
		mysqli_close($link);
		exit;
		break;
	
	case "getAccesses":
		$resource = isset($input['resource']) ? $input['resource'] : '';
		$user = isset($input['user']) ? $input['user'] : '';
		$sql = "SELECT a.user as User, b.apiname as 'Api Name', a.beginaccess as 'Begin Access', c.endaccess as 'End Access' FROM timedaccess as a join apitable as b on a.resource = b.idapi join requests as c on a.extracted_id = c.extracted_id where a.user=? and a.resource=?;";
		$stmt = mysqli_prepare($link, $sql);

		if ($stmt) {
			mysqli_stmt_bind_param($stmt, "si", $user, $resource);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			
			$data = [];
			while ($row = mysqli_fetch_assoc($result)) {
				$data[] = $row;
			}

			mysqli_stmt_close($stmt);
			echo json_encode(["results" => $data]);
		} else {
			http_response_code(500);
			echo json_encode(["error" => "Query preparation failed: ". mysqli_error($link)]);
			mysqli_close($link);
			exit;
			break;
		}

		// Close the database connection
		mysqli_close($link);
		exit;
		break;
	case "editApi":	
	
		$editAPIInfo = isset($input['editAPIInfo']) ? $input['editAPIInfo'] : '';
		$editExternalAPIUrl = isset($input['editExternalAPIUrl']) ? $input['editExternalAPIUrl'] : '';
		$editInternalAPIUrl = isset($input['editInternalAPIUrl']) ? $input['editInternalAPIUrl'] : '';
		//$editinputNameAPI = isset($input['editinputNameAPI']) ? $input['editinputNameAPI'] : '';
		$editAPIID = isset($input['editAPIID']) ? $input['editAPIID'] : '';
		$editStatus = isset($input['isAPIActive']) ? var_export($input['isAPIActive'], true) : '';
		$editAPICMLData = isset($input['editAPICMLData']) ? $input['editAPICMLData'] : '';
		$editAPICMLDataKongPlugin = isset($input['editAPICMLDataKongPlugin']) ? $input['editAPICMLDataKongPlugin'] : '';
		$editselectAPIkind = isset($input['editselectAPIkind']) ? $input['editselectAPIkind'] : '';
		if (empty($editAPIInfo) || empty($editExternalAPIUrl) || empty($editInternalAPIUrl) || empty($editAPIID) || empty($editStatus)) {
			
			http_response_code(400);
			echo json_encode(["error" => "At least one parameter seems to be missing"]);
			exit();
		}
		
		
		$settingStatus = ($editStatus=="true") ? "active" : "inactive";
		
		$sql_1 = "SELECT apiname FROM apitable WHERE `idapi` = ?";
		$stmt_2 = mysqli_prepare($link, $sql_1);
		$orig_api_name = "";
		$response = "";
		if ($stmt_2) {
			mysqli_stmt_bind_param($stmt_2, "i", $editAPIID);
			mysqli_stmt_execute($stmt_2);
			mysqli_stmt_bind_result($stmt_2, $orig_api_name);
			
			if (!mysqli_stmt_fetch($stmt_2)) {
				http_response_code(500);
				echo json_encode(["error" => "Name of api not found for kong deletion"]);
				mysqli_close($link);
				exit;
			}
			//edit service-kong

			$curl = curl_init();

			$parsedUrl = parse_url($editInternalAPIUrl);

			$protocol = $parsedUrl['scheme'] ?? '';
			$host     = $parsedUrl['host'] ?? '';
			$port     = $parsedUrl['port'] ?? '';
			$path     = $parsedUrl['path'] ?? '';
			if (empty($port)) {
				if ($protocol === 'http') {
					$port = 80;
				} elseif ($protocol === 'https') {
					$port = 443;
				}
			}
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $konghost.':'.$kongport.'/services/'.$orig_api_name.'-service',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'PATCH',
			  CURLOPT_POSTFIELDS =>'{"name":"'.$orig_api_name.'-service","host":"'.$host.'","protocol":"'.$protocol.'","port":'.$port.',"path":"'.$path.'"}',
			  CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			  ),
			));

			$http_response_1 = curl_exec($curl);
			$http_code_response_1= curl_getinfo($curl, CURLINFO_HTTP_CODE);

			curl_close($curl);

			
			if ($http_code_response_1 !== 200) {
				http_response_code(500);
				echo json_encode(["error" => "Could not edit kong service ".$orig_api_name."-service. Code ".$http_code_response_1.", reason: ".$http_response_1]);
				mysqli_close($link);
				exit;
			}


			$curl_2 = curl_init();

			curl_setopt_array($curl_2, array(
			  CURLOPT_URL => $konghost.':'.$kongport.'/routes/'.$orig_api_name.'-route',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
			  CURLOPT_USERPWD => "$kongproxyuser:$kongproxypassword",
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'PATCH',
			  CURLOPT_POSTFIELDS =>'{"paths":["'.$editExternalAPIUrl.'"]}',
			  CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			  ),
			));

			$http_response_2 = curl_exec($curl_2);
			$http_code_response_2= curl_getinfo($curl_2, CURLINFO_HTTP_CODE);
			
			curl_close($curl_2);

			
			if ($http_code_response_2 !== 200) {
				http_response_code(500);
				echo json_encode(["error" => "Could not edit kong route ".$orig_api_name."-service. Code ".$http_code_response_2.", reason: ".$http_response_2]);
				mysqli_close($link);
				exit;
			}
			
			//fix plugin start
			if ($editselectAPIkind == "ClearMLStable"){
				if (empty($editAPICMLDataKongPlugin) || empty($editAPICMLData)) {
					http_response_code(400);
					echo json_encode(["error" => "Missing ClearML parameters"]);
					mysqli_close($link);
					exit;
				}
				$curl_3 = curl_init();

				curl_setopt_array($curl_3, array(
				  CURLOPT_URL => $konghost.':'.$kongport.'/plugins/'.$editAPICMLDataKongPlugin,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'PATCH',
				  CURLOPT_POSTFIELDS =>'{"name":"request-transformer","enabled":true,"protocols":["http"],"config": {"add":{"body":["machine_id:'.$editAPICMLData.'"],"headers":[],"querystring":[]},"append":{"body":[],"headers":[],"querystring":[]},"remove":{"body":[],"headers":[],"querystring":[]},"rename":{"body":[],"headers":[],"querystring":[]},"replace":{"body":[],"headers":[],"querystring":[]}}}',
				  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),));

				$http_response_3 = curl_exec($curl_3);
				$http_code_response_3= curl_getinfo($curl_3, CURLINFO_HTTP_CODE);

				curl_close($curl_3);
				if ($http_code_response_3 !== 200) {
					http_response_code(500);
					echo json_encode(["error" => "Could not edit kong plugin. Code ".$http_code_response_3.", reason: ".$http_response_3, "body" => '{"name":"request-transformer","enabled":true,"protocols":["http"],"config": {"add":{"body":["machine_id:'.$editAPICMLData.'"],"headers":[],"querystring":[]},"append":{"body":[],"headers":[],"querystring":[]},"remove":{"body":[],"headers":[],"querystring":[]},"rename":{"body":[],"headers":[],"querystring":[]},"replace":{"body":[],"headers":[],"querystring":[]}}}']);
					mysqli_close($link);
					exit;
				}
			}
			else if ($editselectAPIkind == "ClearMLSporadic"){
				if (empty($editAPICMLDataKongPlugin) || empty($editAPICMLData)) {
					http_response_code(400);
					echo json_encode(["error" => "Missing ClearML parameters"]);
					mysqli_close($link);
					exit;
				}
				$curl_3 = curl_init();

				curl_setopt_array($curl_3, array(
				  CURLOPT_URL => $konghost.':'.$kongport.'/plugins/'.$editAPICMLDataKongPlugin,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'PATCH',
				  CURLOPT_POSTFIELDS =>'{"name":"request-transformer","enabled":true,"protocols":["http"],"config": {"add":{"body":["task_id:'.$editAPICMLData.'"],"headers":[],"querystring":[]},"append":{"body":[],"headers":[],"querystring":[]},"remove":{"body":[],"headers":[],"querystring":[]},"rename":{"body":[],"headers":[],"querystring":[]},"replace":{"body":[],"headers":[],"querystring":[]}}}',
				  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),));

				$http_response_3 = curl_exec($curl_3);
				$http_code_response_3= curl_getinfo($curl_3, CURLINFO_HTTP_CODE);

				curl_close($curl_3);
				if ($http_code_response_3 !== 200) {
					http_response_code(500);
					echo json_encode(["error" => "Could not edit kong plugin. Code ".$http_code_response_3.", reason: ".$http_response_3, "body" => '{"name":"request-transformer","enabled":true,"protocols":["http"],"config": {"add":{"body":["machine_id:'.$editAPICMLData.'"],"headers":[],"querystring":[]},"append":{"body":[],"headers":[],"querystring":[]},"remove":{"body":[],"headers":[],"querystring":[]},"rename":{"body":[],"headers":[],"querystring":[]},"replace":{"body":[],"headers":[],"querystring":[]}}}']);
					mysqli_close($link);
					exit;
				}
			}
			else {
				http_response_code(500);
				echo json_encode(["error" => "$editselectAPIkind"]);
				mysqli_close($link);
				exit;
			}
			//fix plugin end
			
			
		} else {
			http_response_code(500);
			echo json_encode(["error" => "Query preparation failed: ". mysqli_error($link)]);
			exit;
		}
		mysqli_stmt_close($stmt_2);
		$sql = "";
		$extra_data = ":)";
		if ($editselectAPIkind == "ClearMLStable" || $editselectAPIkind == "ClearMLSporadic") {
			$extra_data = "{'ClearMLValue':'".$editAPICMLData."','KongPluginID':'".$editAPICMLDataKongPlugin."'}";
			$sql = "UPDATE `apitable` SET `apiinternalurl` = ?, `apiexternalurl` = ?, `apiinfo` = ?, `apistatus` = ?, `apiadditionalinfo` = ? WHERE (`idapi` = ?);";
		}
		else {
			$sql = "UPDATE `apitable` SET `apiinternalurl` = ?, `apiexternalurl` = ?, `apiinfo` = ?, `apistatus` = ? WHERE (`idapi` = ?);";
		}
		$stmt = mysqli_prepare($link, $sql);

		if ($stmt) {
			if ($editselectAPIkind == "ClearMLStable" || $editselectAPIkind == "ClearMLSporadic") {
				mysqli_stmt_bind_param($stmt, "sssssi",  $editInternalAPIUrl, $editExternalAPIUrl, $editAPIInfo, $settingStatus, $extra_data, $editAPIID);
			}
			else {
				mysqli_stmt_bind_param($stmt, "ssssi",  $editInternalAPIUrl, $editExternalAPIUrl, $editAPIInfo, $settingStatus, $editAPIID);
			}
			mysqli_stmt_execute($stmt);
			if (mysqli_stmt_affected_rows($stmt) > 0) {
				echo json_encode(["results" => "status set to " . $settingStatus . "; then ".$extra_data]);
			}
			else {
				http_response_code(500);
				echo json_encode(["error" => "Error: ". $editInternalAPIUrl. $editExternalAPIUrl. $editAPIInfo. $settingStatus. $editAPIID. $extra_data]);
			}
		} else {
			http_response_code(500);
			echo json_encode(["error" => "Query preparation failed: ". mysqli_error($link)]);
		}

		// Close the database connection
		mysqli_close($link);
		exit;
		break;
		
	case "editRule":
		$ruleKind = isset($input['editRuleKind']) ? $input['editRuleKind'] : '';
		$resourceID = isset($input['editRuleResourceField']) ? $input['editRuleResourceField'] : '';
		$user = isset($input['editRuleUserField']) ? $input['editRuleUserField'] : '';
		$validFrom = isset($input['editRuleStartingOfValidity']) ? $input['editRuleStartingOfValidity'] : '';
		$validTo = isset($input['editRuleEndingOfValidity']) ? $input['editRuleEndingOfValidity'] : '';
		$editRuleAmount = isset($input['editRuleAmount']) ? $input['editRuleAmount'] : '';
		if ($user != "anonymous") {
			//begin check user
			$ldap_host = "ldap://".$ldapServer;
			$ldap_port = $ldapPort; // Use 636 for LDAPS (SSL)
			$ldap_dn = $ldapAdminDN; // Adjust based on your LDAP settings
			$ldap_password = $ldapAdminPwd;


			// Connect to LDAP
			$ldap_conn = ldap_connect($ldap_host, $ldap_port);
			if (!$ldap_conn) {
				die("Could not connect to LDAP server.");
			}

			// Set LDAP options
			ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

			// Authenticate
			if (!ldap_bind($ldap_conn, $ldap_dn, $ldap_password)) {
				http_response_code(500);
				echo json_encode(["error" => "Ldap bind failed"]);
				ldap_unbind($ldap_conn);
				exit;
			}
			$base_dn = $ldapBaseDN; // Adjust as needed
			$filter = "(objectClass=inetOrgPerson)"; // 
			$attributes = ["cn"]; // Specify attributes to fetch

			$search = ldap_search($ldap_conn, $base_dn, $filter, $attributes);
			if (!$search) {
				http_response_code(500);
				echo json_encode(["error" => "Ldap search failed"]);
				ldap_unbind($ldap_conn);
				exit;
			}

			// Fetch entries
			$entries = ldap_get_entries($ldap_conn, $search);
			$usernames = [];
			if ($entries["count"] > 0) {
				for ($i = 0; $i < $entries["count"]; $i++) {
					if (isset($entries[$i]["cn"][0])) {
						if ($entries[$i]["cn"][0] != "rootfilter") {
							$usernames[] = strtolower($entries[$i]["cn"][0]);
						}
					}
				}
			}
			if (!in_array(strtolower($user), $usernames)) {
				http_response_code(500);
				echo json_encode(["error" => "User does not exist"]);
				ldap_unbind($ldap_conn);
				exit;
		}
		//end check user
		}
		if (empty($ruleKind) || empty($resourceID) || empty($user) || empty($validFrom) || empty($validTo)) {
			http_response_code(500);
			echo json_encode(["error" => "At least one parameter seems to be missing"]);
			exit();
		}
		$preparedjson = "";
		if ($ruleKind == "ContemporaryAccess" || $ruleKind == "TotalAccesses") {
			$preparedjson = '{"amount":'.$editRuleAmount.'}';
		}
		else if ($ruleKind == "AccessesOverTime"){
			$editSelectRuleTimePeriod = isset($input['editSelectRuleTimePeriod']) ? $input['editSelectRuleTimePeriod'] : '';
			if ($editSelectRuleTimePeriod === "") {
				http_response_code(500);
				echo json_encode(["error" => "Time period seems to be missing: ".$selectRuleTimePeriod]);
				exit();
			}
			$preparedjson = '{"amount":'.$editRuleAmount.', "period":'.$editSelectRuleTimePeriod.'}';
		}
		else {
			http_response_code(500);
			echo json_encode(["error" => "Invalid kind of rule"]);
			exit();
		}
		$sql = "UPDATE `apimanager`.`ratelimit` SET `user` = ?, `kind_of_limit` = ?, `additional_info` = ?, `timebegin` = ?, `timeend` = ? WHERE (`user` = ?) and (`resource` = ?);";
		$stmt = mysqli_prepare($link, $sql);
		
		if (!$stmt) {
			throw new Exception("Prepare failed for UPDATE: " . mysqli_error($link));
		}
		
		mysqli_stmt_bind_param($stmt, "sssssss", $user, $ruleKind, $preparedjson, $validFrom, $validTo, $user, $resourceID);
		
		
		if (!mysqli_stmt_execute($stmt)) {
			throw new Exception("Execute failed for UPDATE: " . mysqli_stmt_error($stmt));
		}
		mysqli_stmt_close($stmt_1);
		echo json_encode(["results" => "Rule edited"]);
		mysqli_close($link);
		exit;
		break;
		
	case "makeApi":
		$inputAPIInfo = isset($input['inputAPIInfo']) ? $input['inputAPIInfo'] : '';
		$inputExternalAPIUrl = isset($input['inputExternalAPIUrl']) ? $input['inputExternalAPIUrl'] : '';
		$inputInternalAPIUrl = isset($input['inputInternalAPIUrl']) ? $input['inputInternalAPIUrl'] : '';
		$inputNameAPI = isset($input['inputNameAPI']) ? $input['inputNameAPI'] : '';
		$selectAPIkind = isset($input['selectAPIkind']) ? $input['selectAPIkind'] : '';

		if (empty($inputAPIInfo) || empty($inputExternalAPIUrl) || empty($inputInternalAPIUrl) || empty($inputNameAPI) || empty($selectAPIkind)) {
			http_response_code(400);
			echo json_encode(["error" => "At least one parameter seems to be missing"]);
			exit();
		}
		$curl_service = curl_init();

		curl_setopt_array($curl_service, array(
		  CURLOPT_URL => $konghost.':'.$kongport.'/services',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
		  CURLOPT_USERPWD => "$kongproxyuser:$kongproxypassword",
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => 'name='.$inputNameAPI.'-service&url='.$inputInternalAPIUrl,
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/x-www-form-urlencoded'
		  ),
		));

		$service_curl_response = curl_exec($curl_service);

		$http_code_service = curl_getinfo($curl_service, CURLINFO_HTTP_CODE);

		curl_close($curl_service);
		
		if ($http_code_service !== 201) {
			http_response_code(500);
			echo json_encode(["error" => "Could not create service in Kong: ".$service_curl_response]);
			exit();
		}
	
		$curl_route = curl_init();

		curl_setopt_array($curl_route, array(
		  CURLOPT_URL => $konghost.':'.$kongport.'/services/'.$inputNameAPI.'-service/routes',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
          CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
		  CURLOPT_USERPWD => "$kongproxyuser:$kongproxypassword",
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => 'paths%5B%5D='.$inputExternalAPIUrl.'&name='.$inputNameAPI.'-route',
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/x-www-form-urlencoded'
		  ),
		));
		
		$curl_route_response = curl_exec($curl_route);

		$http_code_route = curl_getinfo($curl_route, CURLINFO_HTTP_CODE);

		curl_close($curl_route);
		
		if ($http_code_service !== 201) {
			
			//try to delete the now useless service
			
			$curl_service_delete = curl_init();

			curl_setopt_array($curl_service_delete, array(
			  CURLOPT_URL => $konghost.':'.$kongport.'/services/'.$inputNameAPI.'-service',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
			  CURLOPT_USERPWD => "$kongproxyuser:$kongproxypassword",
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'DELETE',
			));

			$service_curl_response = curl_exec($curl_service_delete);

			$http_code_service_delete = curl_getinfo($curl_service_delete, CURLINFO_HTTP_CODE);

			curl_close($curl_service_delete);
			
			if ($http_code_service_delete !== 204) {
				http_response_code(500);
				echo json_encode(["error" => "Could not create route in Kong and wasn't able to delete relative service ".$inputNameAPI."-service: ".$service_curl_response]);
				exit();
			}
			http_response_code(500);
			echo json_encode(["error" => "Could not create route in Kong but was able to delete the relative service ".$inputNameAPI."-service: Http code ".$curl_route_response]);
			exit();
		}
		
		
		$stringforpreparedstatement = "";
		$sql = "";
		if ($selectAPIkind == "ClearMLStable") {
			$extraparam = isset($input['createAPICMLData']) ? $input['createAPICMLData'] : '';
			if (empty($extraparam)) {
				http_response_code(500);
				echo json_encode(["error" => "ClearML parameter seems to be missing"]);
				exit();
			}
			$json_data = json_decode($service_curl_response, true);
			if (!isset($json_data['id'])) {
				echo json_encode(["error" => "ID of route not found in Kong response, unable to link ClearML plugin. Route and service are not deleted. API not registered in DB, expect incosistencies: ".$service_curl_response]);
				exit();
			}
			$curl_plugin_create = curl_init();

			curl_setopt_array($curl_plugin_create, array(
			  CURLOPT_URL => $konghost.':'.$kongport.'/plugins',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
		      CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
			  CURLOPT_USERPWD => "$kongproxyuser:$kongproxypassword",
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS =>'{"name":"request-transformer","enabled":true,"service":{"id":"'.$json_data['id'].'"},"protocols":["grpc","grpcs","http","https"],"config":{"add":{"body":["machine_id:'.$extraparam.'"],"headers":[],"querystring":[]},"append":{"body":[],"headers":[],"querystring":[]},"remove":{"body":[],"headers":[],"querystring":[]},"rename":{"body":[],"headers":[],"querystring":[]},"replace":{"body":[],"headers":[],"querystring":[]}}}',
			  CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			  ),
			));
			$plugin_response = curl_exec($curl_plugin_create);
			$http_code_plugin_create = curl_getinfo($curl_plugin_create, CURLINFO_HTTP_CODE);
			$json_data_plugin_response = json_decode($plugin_response, true);
			
			if ($http_code_plugin_create !== 201) {
				http_response_code(500);
				echo json_encode(["error" => "Could not create create plugin in Kong. Expect inconsistencies: ".$plugin_response]);
				curl_close($curl_plugin_create);
				exit();
			}
			curl_close($curl_plugin_create);
			$extraparam = "{'ClearMLValue':'".$extraparam."','KongPluginID':'".$json_data_plugin_response['id']."'}";
			$stringforpreparedstatement = "ssssss";
			$sql = "INSERT INTO `apitable` (`apiname`, `apikind`, `apiinternalurl`, `apiexternalurl`, `apiinfo`, `apiadditionalinfo`) VALUES (?,?,?,?,?,?)";
		}
		if ($selectAPIkind == "ClearMLSporadic") {
			$extraparam = isset($input['createAPICMLData']) ? $input['createAPICMLData'] : '';
			if (empty($extraparam)) {
				http_response_code(500);
				echo json_encode(["error" => "ClearML parameter seems to be missing"]);
				exit();
			}
			$json_data = json_decode($service_curl_response, true);
			if (!isset($json_data['id'])) {
				http_response_code(500);
				echo json_encode(["error" => "ID of route not found in Kong response, unable to link ClearML plugin. Route and service are not deleted. API not registered in DB, expect incosistencies: ".$service_curl_response]);
				exit();
			}
			$curl_plugin_create = curl_init();

			curl_setopt_array($curl_plugin_create, array(
			  CURLOPT_URL => $konghost.':'.$kongport.'/plugins',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
		      CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
			  CURLOPT_USERPWD => "$kongproxyuser:$kongproxypassword",
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS =>'{"name":"request-transformer","enabled":true,"service":{"id":"'.$json_data['id'].'"},"protocols":["grpc","grpcs","http","https"],"config":{"add":{"body":["task_id:'.$extraparam.'"],"headers":[],"querystring":[]},"append":{"body":[],"headers":[],"querystring":[]},"remove":{"body":[],"headers":[],"querystring":[]},"rename":{"body":[],"headers":[],"querystring":[]},"replace":{"body":[],"headers":[],"querystring":[]}}}',
			  CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			  ),
			));
			$plugin_response = curl_exec($curl_plugin_create);
			$http_code_plugin_create = curl_getinfo($curl_plugin_create, CURLINFO_HTTP_CODE);
			$json_data_plugin_response = json_decode($plugin_response, true);
			
			if ($http_code_plugin_create !== 201) {
				http_response_code(500);
				echo json_encode(["error" => "Could not create create plugin in Kong. Expect inconsistencies: ".$plugin_response]);
				curl_close($curl_plugin_create);
				exit();
			}
			curl_close($curl_plugin_create);
			$extraparam = "{'ClearMLValue':'".$extraparam."','KongPluginID':'".$json_data_plugin_response['id']."'}";
			$stringforpreparedstatement = "ssssss";
			$sql = "INSERT INTO `apitable` (`apiname`, `apikind`, `apiinternalurl`, `apiexternalurl`, `apiinfo`, `apiadditionalinfo`) VALUES (?,?,?,?,?,?)";
		}
		else if ($selectAPIkind == "Other") {
			$extraparam = isset($input['createAPIGenericData']) ? $input['createAPIGenericData'] : '';
			if (empty($extraparam)) {
				http_response_code(500);
				echo json_encode(["error" => "Additional parameter seems to be missing"]);
				exit();
			}
			$extraparam = "{'ExtraValue':'".$extraparam."'}";
			$stringforpreparedstatement = "ssssss";
			$sql = "INSERT INTO `apitable` (`apiname`, `apikind`, `apiinternalurl`, `apiexternalurl`, `apiinfo`, `apiadditionalinfo`) VALUES (?,?,?,?,?,?)";
		}
		else {
			$stringforpreparedstatement = "sssss";
			$sql = "INSERT INTO `apitable` (`apiname`, `apikind`, `apiinternalurl`, `apiexternalurl`, `apiinfo`) VALUES (?,?,?,?,?)";
		
		}

		$stmt = mysqli_prepare($link, $sql);

		if ($stmt) {
			if ($selectAPIkind == "ClearMLStable" || $selectAPIkind == "ClearMLSporadic" || $selectAPIkind == "Other") {
				mysqli_stmt_bind_param($stmt, "ssssss", $inputNameAPI, $selectAPIkind, $inputInternalAPIUrl, $inputExternalAPIUrl, $inputAPIInfo, $extraparam);
			}
			else {
				mysqli_stmt_bind_param($stmt, "sssss", $inputNameAPI, $selectAPIkind, $inputInternalAPIUrl, $inputExternalAPIUrl, $inputAPIInfo);
			}
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			
			$data = [];
			while ($row = mysqli_fetch_assoc($result)) {
				$data[] = $row;
			}

			mysqli_stmt_close($stmt);
			echo json_encode(["results" => $data]);
		} else {
			http_response_code(500);
			echo json_encode(["error" => "Query preparation failed, service and route were created anyway: ". mysqli_error($link)]);
		}

		// Close the database connection
		mysqli_close($link);
		exit;
		break;
	case "makeRuleOld":
		$createRuleAmount = isset($input['createRuleAmount']) ? $input['createRuleAmount'] : '';
		$selectRuleKind = isset($input['selectRuleKind']) ? $input['selectRuleKind'] : '';
		$selectRuleResource = isset($input['addRuleResourceFieldSecond']) ? $input['addRuleResourceFieldSecond'] : '';
		$selectUserRule = isset($input['addRuleUserFieldSecond']) ? $input['addRuleUserFieldSecond'] : '';
		$selectAddRuleStartingOfValidity = isset($input['addRuleStartingOfValidity']) ? $input['addRuleStartingOfValidity'] : '';
		$selectAddRuleEndingOfValidity = isset($input['addRuleEndingOfValidity']) ? $input['addRuleEndingOfValidity'] : '';
		
		
		if (empty($createRuleAmount) || empty($selectRuleKind) || empty($selectRuleResource) || empty($selectUserRule) || empty($selectAddRuleEndingOfValidity) || empty($selectAddRuleStartingOfValidity)) {
			http_response_code(400);
			echo json_encode(["error" => "At least one parameter seems to be missing"]);
			exit();
		}
		if ($selectUserRule != "anonymous") {
			//begin check user
			$ldap_host = "ldap://".$ldapServer;
			$ldap_port = $ldapPort; // Use 636 for LDAPS (SSL)
			$ldap_dn = $ldapAdminDN; // Adjust based on your LDAP settings
			$ldap_password = $ldapAdminPwd;


			// Connect to LDAP
			$ldap_conn = ldap_connect($ldap_host, $ldap_port);
			if (!$ldap_conn) {
				die("Could not connect to LDAP server.");
			}

			// Set LDAP options
			ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

			// Authenticate
			if (!ldap_bind($ldap_conn, $ldap_dn, $ldap_password)) {
				http_response_code(500);
				echo json_encode(["error" => "Ldap bind failed"]);
				ldap_unbind($ldap_conn);
				exit;
			}
			$base_dn = $ldapBaseDN; // Adjust as needed
			$filter = "(objectClass=inetOrgPerson)"; // 
			$attributes = ["cn"]; // Specify attributes to fetch

			$search = ldap_search($ldap_conn, $base_dn, $filter, $attributes);
			if (!$search) {
				http_response_code(500);
				echo json_encode(["error" => "Ldap search failed"]);
				ldap_unbind($ldap_conn);
				exit;
			}

			// Fetch entries
			$entries = ldap_get_entries($ldap_conn, $search);
			$usernames = [];
			if ($entries["count"] > 0) {
				for ($i = 0; $i < $entries["count"]; $i++) {
					if (isset($entries[$i]["cn"][0])) {
						if ($entries[$i]["cn"][0] != "rootfilter") {
							$usernames[] = strtolower($entries[$i]["cn"][0]);
						}
					}
				}
			}
			if (!in_array(strtolower($selectUserRule), $usernames)) {
				http_response_code(500);
				echo json_encode(["error" => "User does not exist"]);
				ldap_unbind($ldap_conn);
				exit;
			}
		//end check user
		}
		
		$preparedjson = "";
		if ($selectRuleKind == "ContemporaryAccess" || $selectRuleKind == "TotalAccesses") {
			$preparedjson = '{"amount":'.$createRuleAmount.'}';
		}
		else if ($selectRuleKind == "AccessesOverTime"){
			$selectRuleTimePeriod = isset($input['selectRuleTimePeriod']) ? $input['selectRuleTimePeriod'] : '';
			if ($selectRuleTimePeriod === "") {
				http_response_code(500);
				echo json_encode(["error" => "Time period seems to be missing: ".$selectRuleTimePeriod]);
				exit();
			}
			$preparedjson = '{"amount":'.$createRuleAmount.', "period":'.$selectRuleTimePeriod.'}';
		}
		else {
			http_response_code(500);
			echo json_encode(["error" => "Invalid kind of rule"]);
			exit();
		}
		
		$sql = "INSERT INTO `ratelimit` (`user`, `resource`, `kind_of_limit`, `additional_info`, `timebegin`, `timeend`) VALUES (?, ?, ?, ?, ?, ?);";
		$stmt = mysqli_prepare($link, $sql);

		if ($stmt) {
			mysqli_stmt_bind_param($stmt, "sissss", strtolower($selectUserRule), $selectRuleResource, $selectRuleKind, $preparedjson, $selectAddRuleStartingOfValidity, $selectAddRuleEndingOfValidity);  # lowercase to prevent issues
			
			if (mysqli_stmt_execute($stmt)) {
				$result = mysqli_stmt_get_result($stmt);

				$data = [];
				while ($row = mysqli_fetch_assoc($result)) {
					$data[] = $row;
				}

				mysqli_stmt_close($stmt);
				echo json_encode(["results" => $data]);
			} else {
				$error = mysqli_stmt_error($stmt);
				mysqli_stmt_close($stmt);
				http_response_code(500);
				echo json_encode(["error" => "Insert failed due to: ".$error]);
			}
		} else {
			http_response_code(500);
			echo json_encode(["error" => "Query preparation failed: ". mysqli_error($link)]);
		}

		// Close the database connection
		mysqli_close($link);
		exit;
		break;

	case "makeRule":
		$createRuleAmount = isset($input['createRuleAmount']) ? $input['createRuleAmount'] : '';
		$selectRuleKind = isset($input['selectRuleKind']) ? $input['selectRuleKind'] : '';
		$selectRuleResource = isset($input['addRuleResourceFieldSecond']) ? $input['addRuleResourceFieldSecond'] : '';
		$selectUserRule = isset($input['addRuleUserFieldSecond']) ? $input['addRuleUserFieldSecond'] : '';
		$selectAddRuleStartingOfValidity = isset($input['addRuleStartingOfValidity']) ? $input['addRuleStartingOfValidity'] : '';
		$selectAddRuleEndingOfValidity = isset($input['addRuleEndingOfValidity']) ? $input['addRuleEndingOfValidity'] : '';
		
		
		if (empty($createRuleAmount) || empty($selectRuleKind) || empty($selectRuleResource) || empty($selectUserRule) || empty($selectAddRuleEndingOfValidity) || empty($selectAddRuleStartingOfValidity)) {
			http_response_code(400);
			echo json_encode(["error" => "At least one parameter seems to be missing"]);
			exit();
		}
		$usernames = [];
		//begin check users
		if ($selectUserRule != "anonymous") {
			$ldap_host = "ldap://".$ldapServer;
			$ldap_port = $ldapPort; // Use 636 for LDAPS (SSL)
			$ldap_dn = $ldapAdminDN; // Adjust based on your LDAP settings
			$ldap_password = $ldapAdminPwd;


			// Connect to LDAP
			$ldap_conn = ldap_connect($ldap_host, $ldap_port);
			if (!$ldap_conn) {
				die("Could not connect to LDAP server.");
			}

			// Set LDAP options
			ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

			// Authenticate
			if (!ldap_bind($ldap_conn, $ldap_dn, $ldap_password)) {
				http_response_code(500);
				echo json_encode(["error" => "Ldap bind failed"]);
				ldap_unbind($ldap_conn);
				exit;
			}
			$base_dn = $ldapBaseDN; // Adjust as needed
			$filter = "(objectClass=inetOrgPerson)"; // 
			$attributes = ["cn"]; // Specify attributes to fetch

			$search = ldap_search($ldap_conn, $base_dn, $filter, $attributes);
			if (!$search) {
				http_response_code(500);
				echo json_encode(["error" => "Ldap search failed"]);
				ldap_unbind($ldap_conn);
				exit;
			}

			// Fetch entries
			$entries = ldap_get_entries($ldap_conn, $search);
			if ($entries["count"] > 0) {
				for ($i = 0; $i < $entries["count"]; $i++) {
					if (isset($entries[$i]["cn"][0])) {
						if ($entries[$i]["cn"][0] != "rootfilter") {
							$usernames[] = strtolower($entries[$i]["cn"][0]);
						}
					}
				}
			}
			$normalizedUsernames = array_map('strtolower', $usernames);
			// Normalize and split the rule input
			$selectedUsers = array_map('trim', explode(',', $selectUserRule));

			// Check if all selected usernames exist in the list
			$allUsersExist = true;
			foreach ($selectedUsers as $user) {
				if (!in_array(strtolower($user), $normalizedUsernames)) {
					$allUsersExist = false;
					break;
				}
			}
			if (!$allUsersExist) {
				http_response_code(500);
				echo json_encode(["error" => "At least one user of the provided list does not exist"]);
				ldap_unbind($ldap_conn);
				exit;
			}
		//end check users
		}
		else {
			$usernames = array_map('trim', explode(',', $selectUserRule));
		}
		//make a rule for each user, works even if it's a single user since at this point it would be an array of users with length 1
		$resultsToBeReturned = [];
		$errorsToBeReturned = [];
		
		foreach ($selectedUsers as $user) {
			$preparedjson = "";
			if ($selectRuleKind == "ContemporaryAccess" || $selectRuleKind == "TotalAccesses") {
				$preparedjson = '{"amount":'.$createRuleAmount.'}';
			}
			else if ($selectRuleKind == "AccessesOverTime"){
				$selectRuleTimePeriod = isset($input['selectRuleTimePeriod']) ? $input['selectRuleTimePeriod'] : '';
				if ($selectRuleTimePeriod === "") {
					http_response_code(500);
					echo json_encode(["error" => "Time period seems to be missing: ".$selectRuleTimePeriod]);
					exit();
				}
				$preparedjson = '{"amount":'.$createRuleAmount.', "period":'.$selectRuleTimePeriod.'}';
			}
			else {
				http_response_code(500);
				echo json_encode(["error" => "Invalid kind of rule"]);
				
				exit();
			}
			
			$sql = "INSERT INTO `ratelimit` (`user`, `resource`, `kind_of_limit`, `additional_info`, `timebegin`, `timeend`) VALUES (?, ?, ?, ?, ?, ?);";
			$stmt = mysqli_prepare($link, $sql);

			if ($stmt) {
				mysqli_stmt_bind_param($stmt, "sissss", strtolower($user), $selectRuleResource, $selectRuleKind, $preparedjson, $selectAddRuleStartingOfValidity, $selectAddRuleEndingOfValidity);  # lowercase to prevent issues
				//review this return!!!
				if (mysqli_stmt_execute($stmt)) {
					$result = mysqli_stmt_get_result($stmt);

					$data = [];
					while ($row = mysqli_fetch_assoc($result)) {
						$data[] = $row;
					}
					$resultsToBeReturned[] = $user;
					mysqli_stmt_close($stmt);
				} else {
					$error = mysqli_stmt_error($stmt);
					mysqli_stmt_close($stmt);
					$errorsToBeReturned[] = "Rule not added for user " . $user . ": " . $error;
				}
			} else {
				$errorsToBeReturned[] = "Rule not added for user " . $user . ": " . mysqli_error($link);
			}
		}
		$output = [];

		// Check if $resultsToBeReturned has elements
		if (count($resultsToBeReturned) > 0) {
			// Join all elements of $resultsToBeReturned with ", " and add it as the value for the "results" key
			$output['results'] = implode(", ", $resultsToBeReturned);
		}

		// Check if $errorsToBeReturned has elements
		if (count($errorsToBeReturned) > 0) {
			http_response_code(500);
			// Join all elements of $errorsToBeReturned with ", " and add it as the value for the "errors" key
			$output['errors'] = implode(", ", $errorsToBeReturned);
		}

		// Output the JSON result
		echo json_encode($output);

		// Close the database connection
		mysqli_close($link);
		exit;
		break;

	case "deleteAPI":
		//todo delete service and route in kong (plugin is automatically deleted)
		$apiID = isset($input['apiID']) ? $input['apiID'] : '';
		if (empty($apiID)) {
			http_response_code(500);
			echo json_encode(["error" => "Missing API ID"]);
			exit();
		}
		$sql = "SELECT apiname FROM apitable where idapi = ?";
		$stmt = mysqli_prepare($link, $sql);
		
		if ($stmt) {
			
			mysqli_stmt_bind_param($stmt, "i", $apiID);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $value);

			if (!mysqli_stmt_fetch($stmt)) {
				http_response_code(500);
				echo json_encode(["error" => "Name of api not found for kong deletion"]);
				mysqli_close($link);
				exit;
			}
			mysqli_stmt_close($stmt);
			
			// delete route block
			$curl_route_delete = curl_init();

			curl_setopt_array($curl_route_delete, array(
			  CURLOPT_URL => $konghost.':'.$kongport.'/routes/'.$value.'-route',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
			  CURLOPT_USERPWD => "$kongproxyuser:$kongproxypassword",
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'DELETE',
			));

			$curl_route_delete_response=curl_exec($curl_route_delete);

			$http_code_route_delete = curl_getinfo($curl_route_delete, CURLINFO_HTTP_CODE);

			curl_close($curl_route_delete);
			
			if ($http_code_route_delete !== 204) {
				http_response_code(500);
				echo json_encode(["error" => "Could not delete kong route ".$value."-route because of: ". $curl_route_delete_response]);
				mysqli_close($link);
				exit;
			}
			// end delete route block
			
			
			// delete service block
			$curl_service_delete = curl_init();

			curl_setopt_array($curl_service_delete, array(
			  CURLOPT_URL => $konghost.':'.$kongport.'/services/'.$value.'-service',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
			  CURLOPT_USERPWD => "$kongproxyuser:$kongproxypassword",
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'DELETE',
			));

			$curl_service_delete_response = curl_exec($curl_service_delete);

			$http_code_service_delete = curl_getinfo($curl_service_delete, CURLINFO_HTTP_CODE);

			curl_close($curl_service_delete);
			
			if ($http_code_service_delete !== 204) {
				http_response_code(500);
				echo json_encode(["error" => "Could not delete kong service ".$value."-service. Expect inconsistencies, as the route was deleted. Http code ".$http_code_service_delete]);
				mysqli_close($link);
				exit;
			}
			// end delete service block
			
			mysqli_begin_transaction($link);

			try {
				// Step 1: Update `apitable`
				$sql_1 = "UPDATE .apitable SET apideletiondate = ? WHERE idapi = ?";
				$stmt_1 = mysqli_prepare($link, $sql_1);
				
				if (!$stmt_1) {
					throw new Exception("Prepare failed for UPDATE: " . mysqli_error($link));
				}
				
				mysqli_stmt_bind_param($stmt_1, "si", $deletionDate, $apiID);
				$deletionDate = date("Y-m-d H:i:s");
				
				if (!mysqli_stmt_execute($stmt_1)) {
					throw new Exception("Execute failed for UPDATE: " . mysqli_stmt_error($stmt_1));
				}
				mysqli_stmt_close($stmt_1);

				// Step 2: Insert into `apitabledeleted`
				$sql_2 = "INSERT INTO apitabledeleted SELECT * FROM apitable WHERE idapi = ?";
				$stmt_2 = mysqli_prepare($link, $sql_2);

				if (!$stmt_2) {
					throw new Exception("Prepare failed for INSERT INTO apitabledeleted: " . mysqli_error($link));
				}

				mysqli_stmt_bind_param($stmt_2, "i", $apiID);

				if (!mysqli_stmt_execute($stmt_2)) {
					throw new Exception("Execute failed for INSERT INTO apitabledeleted: " . mysqli_stmt_error($stmt_2));
				}
				mysqli_stmt_close($stmt_2);

				// Step 3: Delete from `apitable`
				$sql_3 = "DELETE FROM apitable WHERE idapi = ?";
				$stmt_3 = mysqli_prepare($link, $sql_3);

				if (!$stmt_3) {
					throw new Exception("Prepare failed for DELETE FROM apitable: " . mysqli_error($link));
				}

				mysqli_stmt_bind_param($stmt_3, "i", $apiID);

				if (!mysqli_stmt_execute($stmt_3)) {
					throw new Exception("Execute failed for DELETE FROM apitable: " . mysqli_stmt_error($stmt_3));
				}
				mysqli_stmt_close($stmt_3);

				// Step 4: Insert into `deletedratelimit`
				$sql_4 = "INSERT INTO deletedratelimit SELECT * FROM ratelimit WHERE resource = ?";
				$stmt_4 = mysqli_prepare($link, $sql_4);

				if (!$stmt_4) {
					throw new Exception("Prepare failed for INSERT INTO deletedratelimit: " . mysqli_error($link));
				}

				mysqli_stmt_bind_param($stmt_4, "i", $apiID);

				if (!mysqli_stmt_execute($stmt_4)) {
					throw new Exception("Execute failed for INSERT INTO deletedratelimit: " . mysqli_stmt_error($stmt_4));
				}
				mysqli_stmt_close($stmt_4);

				// Step 5: Delete from `ratelimit`
				$sql_5 = "DELETE FROM ratelimit WHERE resource = ?";
				$stmt_5 = mysqli_prepare($link, $sql_5);

				if (!$stmt_5) {
					throw new Exception("Prepare failed for DELETE FROM ratelimit: " . mysqli_error($link));
				}

				mysqli_stmt_bind_param($stmt_5, "i", $apiID);

				if (!mysqli_stmt_execute($stmt_5)) {
					throw new Exception("Execute failed for DELETE FROM ratelimit: " . mysqli_stmt_error($stmt_5));
				}
				mysqli_stmt_close($stmt_5);

				// If everything is successful, commit transaction
				mysqli_commit($link);

				echo json_encode(["success" => true]);

				} catch (Exception $e) {
					// Rollback transaction if any query fails
					mysqli_rollback($link);

					http_response_code(500);
					echo json_encode(["error" => "Error in deletion of api: ".$e->getMessage()]);
				}
		}
		else {
			http_response_code(500);
			echo json_encode(["error" => "Could not retrieve name of api for kong deletion"]);
		}

		// Close the database connection
		mysqli_close($link);
		exit;
		break;
	default:
		http_response_code(500);
		echo json_encode(["error" => "No action provided"]);
		exit;
		break;
}
?>