<?
	class users {
		function __construct() {
			global $pathOptions;

			if ($pathOptions[0] == 'search') 
				$this->search();
			elseif ($pathOptions[0] == 'getCurrentUser') 
				$this->getCurrentUser();
			else 
				displayJSON(array('failed' => true));
		}

		public function search() {
			global $mysql, $currentUser;

			$search = sanitizeString(preg_replace('/[^\w.]/', '', $_GET['search']));
			$searchBy = isset($_GET['searchBy']) && in_array($_GET['searchBy'], array('username', 'userID'))?$_GET['searchBy']:'username';
			if (isset($_GET['exact']) && (bool) $_GET['exact'] == true) {
				if ($searchBy == 'userID') {
					$search = intval($search);
					$user = $mysql->query("SELECT userID, username, email FROM users WHERE userID = {$search}")->fetch();
				} else 
					$user = $mysql->query("SELECT userID, username, email FROM users WHERE username = '{$search}'")->fetch();

				if ($user) 
					displayJSON(array('users' => array($user)));
				else 
					displayJSON(array('noUsers' => true));
			} else {
//				$valid = $mysql->query("SELECT userID, username, email FROM users WHERE username LIKE '%{$search}%'");
			}
		}

		public function getCurrentUser() {
			global $currentUser;

			$cleanUser = array(
				'userID' => $currentUser->userID,
				'username' => $currentUser->username,
				'email' => $currentUser->email,
				'joinDate' => $currentUser->joinDate,
				'activatedOn' => $currentUser->activatedOn,
				'timezone' => $currentUser->timezone,
				'usermeta' => $currentUser->usermeta,
				'acpPermissions' => $currentUser->acpPermissions
			);
			return displayJSON($cleanUser);
		}
	}
?>