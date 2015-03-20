<?
	class systems {
		function __construct() {
			global $pathOptions;

			if ($pathOptions[0] == 'search') 
				$this->search();
			elseif ($pathOptions[0] == 'save') 
				$this->save();
			else 
				displayJSON(array('failed' => true));
		}

		public function search() {
			global $mongo;

			if (isset($_POST['for']) && $_POST['for'] == 'genres') {
				$genres = array();
				$rGenres = $mongo->systems->find(array(), array('_id' => -1, 'genres' => 1));
				var_dump($rGenres);
			} else {
				$search = array();
				if (isset($_POST['excludeCustom']) && $_POST['excludeCustom']) 
					$search['_id'] = array('$ne' => 'custom');
				$numSystems = $mongo->systems->find($search, array('_id' => 1))->count();
				if (isset($_POST['getAll']) && $_POST['getAll']) 
					$rSystems = $mongo->systems->find($search)->sort(array('sortName' => 1));
				else {
					$page = isset($_POST['page']) && intval($_POST['page'])?intval($_POST['page']):1;
					$rSystems = $mongo->systems->find($search)->sort(array('sortName' => 1))->skip(10 * ($page - 1))->limit(10);
				}
				$systems = array();
				foreach ($rSystems as $system) {
					if ($system['_id'] == 'custom') 
						continue;
					$systems[] = (object) array(
						'shortName' => $system['_id'],
						'fullName' => $system['name'],
						'publisher' => $system['publisher']
					);
				}
				$systems[] = (object) array(
					'shortName' => 'custom',
					'fullName' => 'Custom',
					'publisher' => null
				);
				displayJSON(array('numSystems' => $numSystems, 'systems' => $systems));
			}
		}

		public function save() {
			global $mongo, $currentUser;

			if ($currentUser->checkACP('systems', false)) {
				$system = array(
					'name' => sanitizeString($_POST['system']->fullName),
					'sortName' => sanitizeString($_POST['system']->fullName, 'lower'),
					'publisher' => (object) array(
						'name' => strlen($_POST['system']->publisher->name)?sanitizeString($_POST['system']->publisher->name):null,
						'site' => strlen($_POST['system']->publisher->site)?$_POST['system']->publisher->site:null
					)
				);
				$system = $mongo->systems->findAndModify(array('_id' => $_POST['system']->shortName), array('$set' => $system), null, array('new' => true));
				$system['shortName'] = $system['_id'];
				$system['fullName'] = $system['name'];
				unset($system['_id'], $system['name']);
				displayJSON($system);
			}
		}
	}
?>