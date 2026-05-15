<?php

/* Dashboard Builder.
   Copyright (C) 2025 DISIT Lab https://www.disit.org - University of Florence
   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

require '../management/editACL.php';
require '../management/editorganization.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
   //decode input
   $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
   if (strpos($contentType, 'application/json') !== false) {
      $input = json_decode(file_get_contents('php://input'), true);
   } else {
      $input = $_POST;
   }

   //check if input params are defined
   if (!isset($input['profilename'])) {
      http_response_code(400);
      echo json_encode(buildResponse("Missing required parameter: profilename", "400"));
      exit;
   }
   if (!isset($input['organization'])) {
      http_response_code(400);
      echo json_encode(buildResponse("Missing required parameter: organization", "400"));
      exit;
   }
   if (empty($input['acls']) || !is_array($input['acls'])) {
      http_response_code(400);
      echo json_encode(buildResponse("Missing required parameter: profilename", "400"));
      exit;
   }
   //check organization
   try {
      $payload = normalizeInput($input);

      //build response
      $process_result = [];
      $message = [];

      //create acl
      $aclResultFail = [];
      $aclResultSuccess = [];
      foreach($payload["acls"] as $acl){
         $acl_result = createACL($acl);
         if(isset($acl_result['error']))
            $aclResultFail[$acl["name"]] = $acl_result;
         else
            $aclResultSuccess[$acl["name"]] = $acl_result;
      }
      if(!empty($aclResultFail)){
         $process_result["acls"] = $aclResultFail;
         $message["acls"] = "Error adding ACLs";
      }else{
         $message["acls"] = "ACLs successfully added";
      }

      //create profile
      $profile_result = createProfile($payload["profilename"], $aclResultSuccess);
      if(isset($profile_result['error'])){
         $process_result["profile"] = $profile_result;
         $message["profile"] = "Error creating profile";
      }else{
         $message["profile"] = "Profile created successfully";
      }

      if(isset($payload["groupname"])){
         //create group
         $group_result = createGroup($payload["groupname"], $payload["organization"]);
         if(isset($group_result['error'])){
            $process_result["group"] = $group_result;
            $message["group"] = "Error creating group";
         }else{
            $message["group"] = "Group created successfully";
         }
      }

      http_response_code(200);
      echo json_encode(buildResponse(implode("; ", $message), "200", "ok", $process_result));
      exit;
   } catch (Exception $e) {
      http_response_code(400);
      echo json_encode(buildResponse($e->getMessage(), "400"));
      exit;
   }
} elseif ($method === 'OPTIONS') {
   // CORS preflight response
   header('Access-Control-Allow-Methods: POST, OPTIONS');
   header('Access-Control-Allow-Headers: Content-Type');
   http_response_code(204); // No Content
   exit;
} else {
   http_response_code(405);
   echo json_encode(buildResponse("Method not allowed", "405"));
   exit;
}

function createACL(array $acl): array {
   $originalPost = $_POST;
   $_POST = $acl;
   global $link;
   ob_start();
   add_access_definition($link);
   $output = ob_get_clean();
   $_POST = $originalPost;
   return json_decode($output, true);
}

function createProfile(string $profilename, array $acls): array {
   $originalPost = $_POST;
   $_POST["name"] = $profilename;
   $_POST["authIDs"] = array_column($acls, 'id');
   global $link;
   $res = add_profile($link);
   $_POST = $originalPost;
   return $res;
}

function createGroup(string $groupname, string $organization): array {
   $originalRequest = $_REQUEST;
   $_REQUEST["group"] = $groupname;
   $_REQUEST["organization"] = $organization;
   global $link, $ldapServer, $ldapPort, $ldapAdmin2DN, $ldapAdmin2Pwd, $ldapBaseRootCN, $ldapBaseDN;
   ob_start();
   addGroup($link, $ldapServer, $ldapPort, $ldapAdmin2DN, $ldapAdmin2Pwd, $ldapBaseRootCN, $ldapBaseDN);
   $output = ob_get_clean();
   $_REQUEST = $originalRequest;
   if($output == "Created")
      return ["message" => $output];
   return ["error" => $output];
}

function normalizeInput(array $payload){
   $topOrg = $payload['organization'] ?? null;
   $acls  = $payload['acls'] ?? [];
   $profilename = $payload['profilename'] ?? null;
   //check names
   $alreadyUsedNames = checkNames($profilename, $acls);
   if(!empty($alreadyUsedNames)){
      throw new Exception("These names already existed. Please change them. ". json_encode($alreadyUsedNames));
   }
   //normalize orgs
   $data = normalizeOrganizations($topOrg, $acls);
   $payload['acls'] = $data['acls'];
   return $payload;
}

function checkNames(string $profilename, array $acls): array {
   global $link;

   $matched = [];
   //profilename
   if(checkIfNameAlreadyExists($link, $profilename))
      $matched["profilename"] = $profilename;

   //acls
   $names = array_column($acls, 'name');
   foreach ($names as $index => $name) {
      if(checkIfNameAlreadyExists($link, $name))
         $matched["acls"][$index] = $name;

   }
   //unique names in input
   $names[] = $profilename;
   $counts = array_count_values($names);
   $inputNames = [];
   foreach ($counts as $value => $count) {
      if ($count != 1) {
         $inputNames[] = $value;
      }
   }
   if(!empty($inputNames))
      $matched["inputNames"] = $inputNames;
   return $matched;
   }

function normalizeOrganizations(?string $topOrg, array $acls): array{
   foreach ($acls as $i => $item) {
      // Case 1: orgs exists and valid
      if (!empty($item['orgs']) && is_array($item['orgs'])) {
         continue;
      }
      if (empty($topOrg)) {
         throw new Exception("Missing top-level and acl-level organization for acl ". $i);
      }
      // Case 2: fallback to topOrg
      $acls[$i]['orgs'] = [$topOrg];
   }
   $payload['acls'] = $acls;
   return $payload;
}

function checkIfNameAlreadyExists(mysqli $link, string $name): bool{
   $sql = "SELECT 1 FROM ACNames WHERE name = ? LIMIT 1";
   $stmt = mysqli_prepare($link, $sql);
   if (! $stmt) {
      throw new Exception("checkIfNameAlreadyExists(): " . mysqli_error($link));
   }
   mysqli_stmt_bind_param($stmt, "s", $name);
   if (! mysqli_stmt_execute($stmt)) {
      throw new Exception("checkIfNameAlreadyExists(): " . mysqli_error($link));
   }
   mysqli_stmt_store_result($stmt);
   $exists = mysqli_stmt_num_rows($stmt) > 0;
   mysqli_stmt_close($stmt);
   return $exists;
}

function buildResponse(string $message, string $code, string $status = "ko", array $data = []){
   $response_message["message"] = $message;
   $response_message["code"] = $code;
   $response_message["status"] = $status;
   $response_message["data"] = $data;
   return $response_message;
}
