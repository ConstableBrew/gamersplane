$(function () {
	var $mainColumn = $('div.mainColumn');

	if ($('#page_acp_users').length) {
		var currentTab = 'active';
		$('#controls a').click(function (e) {
			e.preventDefault();

			currentTab = this.id.substring(9, this.id.length);
			$.post('/acp/ajax/listUsers/', { show: currentTab }, function (data) {
				$('div.mainColumn ul').html(data);
			});
		});

		$suspendDate = $('#suspendDate');
		$suspendDate.ajaxForm({
			beforeSubmit: function (arr, $form) {
				var error = false;
				$form.find('input[type="text"]').each(function () {
					if ((this.name == 'hour' && ($(this).val() < 0 || $(this).val() > 23)) || (this.name == 'minutes' && ($(this).val() < 0 || $(this).val() > 60))) error = true;
				});

				if (error) return false;
			},
			success: function (data) {
				if (data == 'suspended' && currentTab == 'active') {
					$li = $suspendDate.closest('li');
					$suspendDate.appendTo($mainColumn);
					$li.remove();
				}
//				window.location.reload();
			}
		});
		$('ul.prettyList').on('click', 'a.suspend', function (e) {
			e.preventDefault();

			$li = $(this).closest('li');

			if ($(this).text() == 'Suspend' && $li.find('form').length == 0) {
				$li.append($suspendDate);
				$suspendDate.find('#userID').val($li.data('id'));
			} else if ($(this).text() == 'Suspend' && $li.find('form').length == 1) {
				$suspendDate.appendTo($mainColumn);
			}
		});
	}
});

controllers.controller('acp_autocomplete', ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {
	$scope.$emit('pageLoading');
	$scope.newItems = [];
	$scope.addToSystem = [];
	$http.post(API_HOST + '/characters/getUAI/').then(function (data) {
		data = data.data;
		$scope.$emit('pageLoading');
		$scope.newItems = data.newItems;
		addToSystem = {};
		data.addToSystem.forEach(function (item) {
			if (typeof addToSystem[item.type] == 'undefined') 
				addToSystem[item.type] = [];
			addToSystem[item.type].push(item);
		});
		angular.forEach(addToSystem, function (items, key) {
			$scope.addToSystem.push({ 'type': key, 'items': items });
		});
	});

	$scope.processUAI = function (item, action) {
		item.action = action;
		$http.post(API_HOST + '/characters/processUAI/', { 'item': item, 'action': action }).then(function (data) {
			data = data.data;
			if (data.success) {
				if (item.itemID) {
					var sub = null;
					$scope.addToSystem.forEach(function (set) {
						if (set.type == item.type) 
							sub = set.items;
					});
					removeEle(sub, item);
				} else 
					removeEle($scope.newItems, item);
			}
		});
	}
/*		$('#newItems').on('click', '.actions a', function (e) {
			e.preventDefault();

			var $itemRow = $(this).closest('.newItem'), postData = { uItemID: $itemRow.attr('id').split('_')[1], name: $itemRow.children('input').val() };
			if ($(this).hasClass('check')) postData['action'] = 'add';
			else if ($(this).hasClass('cross')) postData['action'] = 'reject';
			$.post('/acp/process/newItem/', postData, function (data) {
				$itemRow.remove();
			});
		});
		$('#addToSystem').on('click', '.actions a', function (e) {
			e.preventDefault();

			var $itemRow = $(this).closest('.item'), postData = { uItemID: $itemRow.attr('id').split('_')[1], name: $itemRow.children('input').val() };
			if ($(this).hasClass('check')) postData['action'] = 'add';
			else if ($(this).hasClass('cross')) postData['action'] = 'reject';
			$.post('/acp/process/addToSystem/', postData, function (data) {
				$itemRow.remove();
			});
		});*/
}]).controller('acp_systems', ['$scope', '$http', '$sce', '$timeout', 'SystemsService', function ($scope, $http, $sce, $timeout, SystemsService) {
	$scope.selectSystem = {
		'data': [],
		'value': {},
		'search': ''
	};
	function loadSystems() {
		SystemsService.get({ 'getAll': true, 'basic': true }).then(function (data) {
			systems = data.systems;
			$scope.selectSystem.data = [];
			for (key in systems) {
				if (systems[key].shortName != 'custom') 
					$scope.selectSystem.data.push({
						'value': systems[key].shortName, 
						'display': systems[key].fullName
					});
			}
			$scope.selectSystem.data.push({ 'value': 'custom', 'display': 'Custom' });
		});
	}
	loadSystems();
	$scope.newGenre = {
		'data': [],
		'value': {},
		'search': ''
	};
	function getGenres() {
		SystemsService.getGenres().then(function (data) {
			$scope.allGenres = [];
			$scope.newGenre.data = [];
			for (key in data) {
				$scope.allGenres.push(data[key]);
				$scope.newGenre.data.push(data[key]);
			}
		});
	}
	getGenres();
	$scope.newSystem = true;
	$scope.edit = {};
	$scope.allGenres = [];
	$scope.saveSuccess = false;

	$scope.loadSystem = function () {
		if ($scope.selectSystem.value.value == null) 
			return;
		SystemsService.get({ 'shortName': $scope.selectSystem.value.value }).then(function (data) {
			$scope.newSystem = false;
			$scope.edit = data.systems[0];
//			$scope.selectSystem.search = '';
			$scope.newGenre.search = '';
			updateGenres();
		});
	};
	$scope.setNewSystem = function () {
		$scope.newSystem = true;
		$scope.edit = {};
	};

	$scope.saveStatusBtn = 'cancel';
	$scope.setEditBtn = function (type) {
		$scope.saveStatusBtn = type;
	}

	function updateGenres() {
		$scope.newGenre.data = [];
		for (key in $scope.allGenres) 
			if ($scope.edit.genres.indexOf($scope.allGenres[key]) == -1)
				$scope.newGenre.data.push({
					'value': $scope.allGenres[key],
					'display': $scope.allGenres[key]
				});
	}
	$scope.addGenre = function () {
		if (typeof $scope.edit.genres == 'undefined') 
			$scope.edit.genres = [];
		if ($scope.newGenre.value.display.length == 0) 
			return;
		$scope.edit.genres.push($scope.newGenre.value.display);
		updateGenres();
	}
	$scope.removeGenre = function (genre) {
		index = $scope.edit.genres.indexOf(genre);
		if (index >= 0) 
			$scope.edit.genres.splice(index, 1);
		updateGenres();
	}
	$scope.addBasic = function () {
		if (typeof $scope.edit.basics == 'undefined') 
			$scope.edit.basics = [];
		if (typeof $scope.edit.newBasic == 'undefined' || $scope.edit.newBasic.text.length == 0 || $scope.edit.newBasic.site.length == 0) 
			return false;
		$scope.edit.basics.push($scope.edit.newBasic);
		$scope.edit.newBasic = { 'text': '', 'site': '' };
	}
	$scope.removeBasic = function (basic) {
		index = $scope.edit.basics.indexOf(basic);
		if (index >= 0) 
			$scope.edit.basics.splice(basic, 1);
	}
	$scope.saveSystem = function () {
		if ($scope.saveStatusBtn != 'save') 
			return;

		SystemsService.save($scope.edit).then(function (data) {
			$scope.edit = {};
			$scope.saveSuccess = true;
			$scope.newSystem = true;
			getGenres();
			loadSystems();
			$timeout(function () { $scope.saveSuccess = false; }, 1500);
		});
	}
}]).controller('acp_links', ['$scope', '$http', '$sce', '$filter', 'Links', function ($scope, $http, $sce, $filter, Links) {
	$scope.links = [];
	$scope.newLink = {};
	$scope.search = '';
	Links.get().then(function (data) {
		$scope.links = data.data.links;
		$scope.links.forEach(function (ele) {
			ele.level = { 'value': ele.level, 'display': ele.level };
		});
		$scope.pagination.numItems = data.data.totalCount;
	});
	$scope.pagination = { numItems: 0, itemsPerPage: 20 };
	if ($.urlParam('page')) 
		$scope.pagination.current = parseInt($.urlParam('page'));
	else 
		$scope.pagination.current = 1;

	$scope.$watch(function () { return $scope.search; }, function () {
		$scope.pagination.numItems = $filter('filter')($scope.links, { 'title': $scope.search }).length;
	});
}]).directive('linksEdit', ['$filter', '$http', 'Upload', function ($filter, $http, Upload) {
	return {
		restrict: 'E',
		templateUrl: '/angular/directives/acp/links.php',
		scope: {
			'data': '=data',
		},
		link: function (scope, element, attrs) {
			scope.editing = false;
			scope.showEdit = false;
			scope.showDelete = false;
			scope.levels = ['Link', 'Affiliate', 'Partner'];
			scope.categories = ['Blog', 'Podcast', 'Videocast', 'Liveplay', 'Devs', 'Accessories'];
			if (!isUndefined(attrs.new)) {
				scope.new = true;
				scope.editing = true;
				scope.data = {
					'title': '',
					'url': '',
					'level': { 'id': 'link', 'display': 'Link' },
					'networks': [],
					'categories': []
				};
			} else 
				scope.new = false;

			scope.toggleEditing = function () {
				scope.showEdit = !scope.showEdit;
				scope.editing = !scope.editing;
			}
			scope.saveLink = function () {
				data = copyObject(scope.data);
				delete data.image;
				data.level = data.level.display;
				Upload.upload({
					'url': API_HOST + '/links/save/',
					'file': scope.data.newImage,
					'fields': data,
					'sendFieldsAs': 'form'
				}).success(function (data) {
					if (scope.new) 
						window.location.reload();
					else {
						if (data.image) 
							scope.data.image = data.image;
						scope.toggleEditing();
					}
				});
			}
			scope.deleteImage = function () {
				$http.post(API_HOST + '/links/deleteImage/', { '_id': scope.data._id }).success(function (data) {
					delete scope.data.image;
				})
			}
			scope.deleteLink = function () {
				$http.post(API_HOST + '/links/deleteLink/', { '_id': scope.data._id }).success(function (data) {
					window.location.reload();
				})
			}
		}
	}
}]).controller('acp_music', ['$scope', '$http', '$sce', function ($scope, $http, $sce) {
	$scope.music = [];
	$scope.newSong = { 'url': '', 'title': '', 'lyrics': false, 'battlebards': false, 'genres': [], 'notes': '' };
	$scope.pagination = { numItems: 0, itemsPerPage: 10 };
	if ($.urlParam('page')) 
		$scope.pagination.current = parseInt($.urlParam('page'));
	else 
		$scope.pagination.current = 1;
	$scope.showPagination = true;
	function loadMusic() {
		$http.post(API_HOST + '/music/get/', { 'page': $scope.pagination.current }).success(function (data) {
			if (data.success) {
				$scope.music = data.music;
				$scope.pagination.numItems = data.count;
			}
		});
	}
	loadMusic();

	$scope.showEdit = null;
	$scope.addSong = function () {
		$scope.showEdit = 'new';
		$scope.$broadcast('resetSongForm', 'new');
	};
	$scope.editSong = function (id) {
		$scope.showEdit = $scope.showEdit != id?id:null;
		if ($scope.showEdit != null) 
			$scope.$broadcast('resetSongForm', id);
	};
	$scope.toggleApproval = function (song) {
		$http.post(API_HOST + '/music/toggleApproval/', { 'id': song._id, approved: song.approved }).success(function (data) {
			if (data.success) 
				song.approved = !song.approved;
		})
	};
	$scope.$on('closeSongEdit', function (event) {
		$scope.showEdit = null;
	});
	$scope.$on('addNew', function (event) {
		loadMusic();
		$scope.newSong = { 'url': '', 'title': '', 'lyrics': false, 'battlebards': false, 'genres': [], 'notes': '' };
	});
}]).controller('acp_faqs', ['$scope', '$http', '$filter', 'faqs', function ($scope, $http, $filter, faqs) {
	$scope.categories = [];
	$scope.catMap = {};
	for (key in faqs.categories) {
		$scope.categories.push({ 'value': faqs.categories[key], 'display': key });
		$scope.catMap[faqs.categories[key]] = key;
	}
	$scope.aFAQs = [];
	faqs.get().then(function (data) {
		if (data.faqs) 
			$scope.aFAQs = data.faqs;
	});
	$scope.editing = null;
	$scope.editHold = null;
	$scope.editFAQ = function(faq) {
		$scope.editing = faq._id;
		$scope.editHold = faq;
	};
	$scope.moveUp = function (faq, cFAQs) {
		faqs.changeOrder(faq._id, 'up').then(function (data) {
			order = faq.order;
			sFAQ = $filter('filter')(cFAQs, { 'order': order - 1 });
			faq.order = faq.order - 1;
			sFAQ[0].order = sFAQ[0].order + 1;
		});
	};
	$scope.moveDown = function (faq, cFAQs) {
		faqs.changeOrder(faq._id, 'down').then(function (data) {
			order = faq.order;
			sFAQ = $filter('filter')(cFAQs, { 'order': order + 1 });
			faq.order = faq.order + 1;
			sFAQ[0].order = sFAQ[0].order - 1;
		});
	};
	$scope.saveFAQ = function (faq) {
		faqs.update(faq).then(function (data) {
			if (data.success) {
				faq = data.faq;
				$scope.editing = null;
				$scope.editHold = null;
			}
		});
	}
	$scope.cancelSave = function () {
		$scope.editing = null;
		$scope.editHold = null;
	}
	$scope.deleteFAQ = function (id, cFAQs, index) {
		faqs.delete(id).then(function (data) {
			if (data.success) 
				cFAQs.splice(index, 1);
		});
	}

	$scope.newFAQ = {
		'category': '',
		'question': '',
		'answer': ''
	}
	$scope.createFAQ = function () {
		if ($scope.newFAQ.question.length == 0 || $scope.newFAQ.answer.length == 0) 
			return false;
		faqs.create($scope.newFAQ).then(function (data) {
			$scope.aFAQs[data.faq.category].push(data.faq);
		});
	}
}]);