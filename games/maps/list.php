<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[1]);
	if ($gameID == 0) { header('Location: '.SITEROOT.'/403'); exit; }
	
	$gmCheck = $mysql->query("SELECT 1 isGM FROM gms WHERE gameID = $gameID AND userID = $userID UNION SELECT 0 isGM FROM characters WHERE gameID = $gameID AND userID = $userID");
	if (!$gmCheck->rowCount()) { header('Location: '.SITEROOT.'/403'); exit; }
	$isGM = $gmCheck->fetchColumn();
	$fixedMenu = TRUE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Maps</h1>
		
		<a href="<?=SITEROOT?>/tools/maps/new?gameID=<?=$gameID?>">New Map</a>
<?
	$mapList = $mysql->query('SELECT mapID, name, rows, columns FROM maps WHERE gameID = '.$gameID);
	
	$firstMap = TRUE;
	foreach ($mapList as $mapInfo) {
		echo "\t\t<div class=\"tr".($firstMap?' firstTR':'')."\">\n";
		echo "\t\t\t<div class=\"mapLink\"><a href=\"".SITEROOT."/tools/maps/view/{$mapInfo['mapID']}\">{$mapInfo['name']}</a></div>\n";
		echo "\t\t\t<div class=\"mapSize\">{$mapInfo['rows']} x {$mapInfo['columns']}</div>\n";
		if ($isGM) echo "\t\t\t<div><a href=\"".SITEROOT."/tools/maps/edit/{$mapInfo['mapID']}\">Edit</a></div>\n";
		else echo "\t\t\t<div>&nbsp;</div>\n";
		echo "\t\t</div>\n";
		if ($firstMap) $firstMap = FALSE;
	}
	
	if ($mapList->rowCount() == 0) echo "\t\t<h2>Doesn't seem like there are any maps available at this time.</h2>\n";
?>
<? require_once(FILEROOT.'/footer.php'); ?>