<?php
	$gameID = intval($pathOptions[0]);
	$game = $mongo->games->findOne(
		['gameID' => $gameID, 'players.user.userID' => $currentUser->userID],
		['projection' => ['title' => true, 'players.$' => true]]
	);
	if (!$game) { header('Location: /403'); exit; }
	$title = $game['title'];
	$approved = $game['players'][0]['approved'];
?>
<?php	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Leave Game</h1>
		<div class="hbMargined">
			<p>Are you sure you want to leave "<a href="<?='/games/' . $gameID?>"><?=$title?></a>"?</p>
<?php	if ($approved) { ?>
			<p>If you have any characters currently in this game (approved or not), they will be removed.</p>
<?php	} ?>
			<form method="post" action="/games/process/leaveGame/" class="alignCenter">
				<input type="hidden" name="gameID" value="<?=$gameID?>">
				<input type="hidden" name="playerID" value="<?=$currentUser->userID?>">
				<button type="submit" name="leave" class="fancyButton">Leave</button>
			</form>
		</div>
<?php	require_once(FILEROOT.'/footer.php'); ?>
