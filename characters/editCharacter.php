<?php
	$characterID = intval($pathOptions[1]);
	$noChar = true;

	define('SYSTEM', $pathOptions[0]);
	if ($systems->verifySystem(SYSTEM)) {
		require_once(FILEROOT . "/includes/packages/" . SYSTEM . "Character.package.php");
		$charClass = Systems::systemClassName(SYSTEM) . 'Character';
		if ($character = new $charClass($characterID)) {
			$active = $character->load();
			if ($active) {
				$angular = DB::conn('mongo')->systems->findOne(['_id' => SYSTEM], ['projection' => ['angular' => true]])['angular'];
				if ($angular) {
					$dispatchInfo['ngController'] = 'editCharacter';
					$angular = 'editCharacter_' . SYSTEM;
				}
				$dispatchInfo['title'] = 'Edit ' . $character->getLabel() . ' | ' . $dispatchInfo['title'];
				$charPermissions = $character->checkPermissions($currentUser->userID);
				if ($charPermissions == 'edit') {
					$noChar = false;
					$addJSFiles[] = 'characters/_edit.js';
					if (is_subclass_of($character, 'd20Character')) {
						$addJSFiles[] = 'characters/_d20Character.js';
					}
					if (file_exists(FILEROOT . '/javascript/characters/' . SYSTEM . '/edit.js')) {
						$addJSFiles[] = 'characters/' . SYSTEM . '/edit.js';
					}
				}
			} else { header('Location: /characters/my/'); exit; }
		}
	} else { header('Location: /404/'); exit; }
?>
<?php	require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
<?php	if (file_exists(FILEROOT . '/images/logos/' . SYSTEM . '.png')) { ?>
		<div id="charSheetLogo"><img src="/images/logos/<?=SYSTEM?>.png"></div>
<?php	} ?>

<?php	if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<?php	} else { ?>
		<form<?=$angular?" ng-controller=\"{$angular}\" ng-submit=\"save()\"":' method="post" action="/characters/process/editCharacter/"'?>>
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<input id="system" type="hidden" name="system" value="<?=$character::SYSTEM?>">

			<div id="charDetails">
				<div id="charAvatar"><a href="/characters/avatar/<?=SYSTEM?>/<?=$characterID?>/">Change Avatar</a> (Avatar Set: <div class="sprite <?=$character->getAvatar($characterID)?'check green':'cross'?> small"></div>)</div>
<?php		$character->showEdit(); ?>
			</div>

			<div id="submitDiv">
				<button type="submit" name="save" class="fancyButton">Save</button>
			</div>
		</form>
<?php	} ?>
<?php	require_once(FILEROOT . '/footer.php'); ?>
