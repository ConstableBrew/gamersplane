<?
	$search = sanitizeString($_POST['search'], 'search_format');
	$characterID = intval($_POST['characterID']);

	$talents = $mysql->prepare("SELECT tl.talentID, tl.name FROM starwarsffg_talentsList tl LEFT JOIN starwarsffg_talents ct ON tl.talentID = ct.talentID AND ct.characterID = $characterID WHERE name LIKE ? AND ct.talentID IS NULL LIMIT 5");
	$talents->execute(array("%$search%"));
	foreach ($talents as $info)
		echo "<a href=\"\">".mb_convert_case($info['name'], MB_CASE_TITLE)."</a>\n";
?>
