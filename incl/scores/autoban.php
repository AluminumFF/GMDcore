<?php
include "../lib/connection.php";
$query = $db->prepare("
	SELECT 10+IFNULL(FLOOR(coins.coins*1.25),0) as coins, 3+IFNULL(FLOOR(levels.demons*1.0625),0) as demons, 200+FLOOR((IFNULL(levels.stars,0)+IFNULL(gauntlets.stars,0)+IFNULL(mappacks.stars,0))*1.25) as stars FROM
		(SELECT SUM(coins) as coins FROM levels WHERE starCoins <> 0) coins
	JOIN
		(SELECT SUM(starDemon) as demons, SUM(starStars) as stars FROM levels) levels
	JOIN
	(
		SELECT (level1.stars + level2.stars + level3.stars + level4.stars + level5.stars) as stars FROM
			(SELECT SUM(starStars) as stars FROM gauntlets
			INNER JOIN levels on levels.levelID = gauntlets.level1) level1
		JOIN
			(SELECT SUM(starStars) as stars FROM gauntlets
			INNER JOIN levels on levels.levelID = gauntlets.level2) level2
		JOIN
			(SELECT SUM(starStars) as stars FROM gauntlets
			INNER JOIN levels on levels.levelID = gauntlets.level3) level3
		JOIN
			(SELECT SUM(starStars) as stars FROM gauntlets
			INNER JOIN levels on levels.levelID = gauntlets.level4) level4
		JOIN
			(SELECT SUM(starStars) as stars FROM gauntlets
			INNER JOIN levels on levels.levelID = gauntlets.level5) level5
	) gauntlets
	JOIN
		(SELECT SUM(stars) as stars FROM mappacks) mappacks

	");
$query->execute();
$levelstuff = $query->fetch();
$stars = $levelstuff['stars']; $coins = $levelstuff['coins']; $demons = $levelstuff['demons']; 
$query = $db->prepare("UPDATE users SET isBanned = '1' WHERE stars > :stars OR demons > :demons OR coins > :coins");
$query->execute([':stars' => $stars, ':demons' => $demons, ':coins' => $coins]);
$query = $db->prepare("SELECT userID, userName FROM users WHERE stars > :stars OR demons > :demons OR coins > :coins");
$query->execute([':stars' => $stars, ':demons' => $demons, ':coins' => $coins]);
$result = $query->fetchAll();
?>
