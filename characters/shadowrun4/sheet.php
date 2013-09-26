<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT cd.*, c.userID, gms.primaryGM IS NOT NULL isGM FROM shadowrun4_characters cd INNER JOIN characters c ON cd.characterID = c.characterID LEFT JOIN (SELECT gameID, primaryGM FROM players WHERE isGM = 1 AND userID = $userID) gms ON c.gameID = gms.gameID WHERE cd.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			foreach ($charInfo as $key => $value) if ($value == '') $charInfo[$key] = '&nbsp;';
			$noChar = FALSE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/shadowrun4.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="actions"><a id="editCharacter" href="<?=SITEROOT?>/characters/shadowrun4/<?=$characterID?>/edit" class="fancyButton">Edit Character</a></div>
			
		<div class="tr">
			<label for="name">Name:</label>
			<div><?=$charInfo['name']?></div>
		</div>
		<div class="tr">
			<label for="metatype">Metatype:</label>
			<div><?=$charInfo['metatype']?></div>
		</div>
		
		<div class="clearfix">
			<div id="stats">
<?
	foreach (array('body' => 'Body', 'agility' => 'Agility', 'reaction' => 'Reaction', 'strength' => 'Strength', 'charisma' => 'Charisma', 'intuition' => 'Intuition', 'logic' => 'Logic', 'willpower' => 'Willpower', 'edge_total' => 'Total Edge', 'edge_current' => 'Current Edge', 'essence' => 'Essence', 'mag_res' => 'Magic or Resonance', 'initiative' => 'Initiative', 'initiative_passes' => 'Initiative Passes', 'matrix_initiative' => 'Matrix Initiative', 'astral_initiative' => 'Astral Initiative') as $stat => $statName) {
		if ($stat == 'body' || $stat == 'edge_total') echo "\t\t\t\t<div class=\"statCol\">\n";
?>
							<div class="tr">
								<label for="<?=$stat?>"><?=$statName?>:</label>
								<div><?=$charInfo[$stat]?></div>
							</div>
<?
		if ($stat == 'willpower' || $stat == 'astral_initiative') echo "\t\t\t\t</div>\n";
	}
?>
			</div>
			
			<div id="qualities">
				<h2 class="headerbar hbDark">Qualities</h2>
				<div class="hbdMargined"><?=$charInfo['qualities']?></div>
			</div>
			
			<div id="damage">
				<h2 class="headerbar hbDark">Damage Tracks</h2>
				<div class="hbdMargined">
					<div class="damageTrack">
						<label for="physical">Physical Damage</label>
						<div><?=$charInfo['physicalDamage']?></div>
					</div>
					<div class="damageTrack">
						<label for="stun">Stun Damage</label>
						<div><?=$charInfo['stunDamage']?></div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="skills" class="twoCol floatLeft">
				<h2 class="headerbar hbDark">Skills</h2>
				<div class="hbdMargined"><?=$charInfo['skills']?></div>
			</div>
			<div id="spells" class="twoCol floatRight">
				<h2 class="headerbar hbDark">Spells</h2>
				<div class="hbdMargined"><?=$charInfo['spells']?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="weapons" class="twoCol floatLeft">
				<h2 class="headerbar hbDark">Weapons</h2>
				<div class="hbdMargined"><?=$charInfo['weapons']?></div>
			</div>
			<div id="armor" class="twoCol floatRight">
				<h2 class="headerbar hbDark">Armor</h2>
				<div class="hbdMargined"><?=$charInfo['armor']?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="augments" class="twoCol floatLeft">
				<h2 class="headerbar hbDark">Augments</h2>
				<div class="hbdMargined"><?=$charInfo['augments']?></div>
			</div>
			<div id="contacts" class="twoCol floatRight">
				<h2 class="headerbar hbDark">Contacts</h2>
				<div class="hbdMargined"><?=$charInfo['contacts']?></div>
			</div>
		</div>
		
		<div id="items">
			<h2 class="headerbar hbDark">Items</h2>
			<div class="hbdMargined"><?=$charInfo['items']?></div>
		</div>
		
		<div id="notes">
			<h2 class="headerbar hbDark">Notes</h2>
			<div class="hbdMargined"><?=$charInfo['notes']?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>