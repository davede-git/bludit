<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if (!checkRole(array('admin','moderator'), false)) {
	$pageKey = isset($_POST['key']) ? $_POST['key'] : $layout['parameters'];
	$page = buildPage($pageKey);
	if (!$page || $page->username()!==$login->username()) {
		$syslog->add(array(
			'dictionaryKey'=>'access-deny',
			'notes'=>$login->username()
		));
		Alert::set($Language->g('You do not have sufficient permissions'));
		Redirect::page('dashboard');
	}
}

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($_POST['status']==='delete') {
		if (deletePage($_POST['key'])) {
			Alert::set( $Language->g('The changes have been saved') );
		}
	} else {
		// If the checkbox is not selected the form doesn't send the field
		$_POST['noindex'] = isset($_POST['noindex'])?true:false;
		$_POST['nofollow'] = isset($_POST['nofollow'])?true:false;
		$_POST['noarchive'] = isset($_POST['noarchive'])?true:false;

		$key = editPage($_POST);
		if ($key!==false) {
			Alert::set( $Language->g('The changes have been saved') );
			Redirect::page('edit-content/'.$key);
		}
	}

	Redirect::page('content');
}

// ============================================================================
// Main after POST
// ============================================================================
$pageKey = $layout['parameters'];
$page = buildPage($pageKey);
if ($page===false) {
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the page: '.$pageKey);
	Redirect::page('content');
}

// Title of the page
$layout['title'] .= ' - '.$Language->g('Edit content').' - '.$page->title();