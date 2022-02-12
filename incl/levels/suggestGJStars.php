<?php
//error_reporting(0);
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
require_once "../../config/webhook.php";
$gs = new mainLib();

$gjp = ExploitPatch::remove($_POST["gjp"]);
$stars = ExploitPatch::remove($_POST["stars"]);
$feature = ExploitPatch::remove($_POST["feature"]);
$levelID = ExploitPatch::remove($_POST["levelID"]);
$accountID = GJPCheck::getAccountIDOrDie();
$difficulty = $gs->getDiffFromStars($stars);

if($gs->checkPermission($accountID, "actionRateStars")){
	$gs->rateLevel($accountID, $levelID, $stars, $difficulty["diff"], $difficulty["auto"], $difficulty["demon"]);
	$gs->featureLevel($accountID, $levelID, $feature);
	$gs->verifyCoinsLevel($accountID, $levelID, 1);
	if($webhook != "" && $hookRates) {
		$json_data = json_encode([
			"tts" => false,
			"embeds" => [
				[
					"title" => "Star Rate",
					"type" => "rich",
					"color" => hexdec("5a62ff"),
					"fields" => [
						[
							"name" => "Level ID:",
							"value" => $levelID,
							"inline" => true
						],
						[
							"name" => "Feature:",
							"value" => $feature ? "Yes" : "No",
							"inline" => true
						],
						[
							"name" => "Stars:",
							"value" => $stars,
							"inline" => true
						]
					]
				]
			]

		], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		$ch = curl_init($webhook);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);
	}

if($gs->checkPermission($accountID, "actionRateStars")){
	$gs->rateLevel($accountID, $levelID, $stars, $difficulty["diff"], $difficulty["auto"], $difficulty["demon"]);
	$gs->featureLevel($accountID, $levelID, $feature);
	$gs->verifyCoinsLevel($accountID, $levelID, 1);
	echo 1;
}else if($gs->checkPermission($accountID, "actionSuggestRating")){
	$gs->suggestLevel($accountID, $levelID, $difficulty["diff"], $stars, $feature, $difficulty["auto"], $difficulty["demon"]);
	echo 1;
}else{
	echo -2;
}
?>
