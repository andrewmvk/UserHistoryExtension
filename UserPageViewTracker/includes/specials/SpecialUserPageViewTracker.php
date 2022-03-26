<?php

class SpecialUserPageViewTracker extends SpecialPage
{

	function __construct()
	{
		parent::__construct('UserPageViewTracker', 'editinterface');
	}

	public static function onBeforePageDisplay(OutputPage $out, Skin $skin)
	{
		$dbw = wfGetDB(DB_PRIMARY);
		if (method_exists($skin, 'getUserIdentity')) {
			// MW 1.36+
			$user = $skin->getUserIdentity();
		} else {
			$user = $skin->getUser();
		}
		$user_id = $user->getID();
		$page_id = $skin->getTitle()->getArticleID();

		if (!$user_id || !$page_id) {
			return;
		}

		$hits = $dbw->selectField('user_page_views', 'hits', "user_id = $user_id AND page_id = $page_id");
		if ($hits) {
			$hits++;
		}
		$dbw->upsert(
			'user_page_views',
			['user_id' => $user_id, 'page_id' => $page_id, 'hits' => 1],
			[['user_id', 'page_id']],
			['hits' => $hits]
		);
		$page_title = $dbw->selectField('user_page_hits', 'page_title', "user_id = $user_id");
		$user_name = $dbw->selectField('user_page_hits', 'user_name', "user_id= $user_id");

		$dbw->insert(
			'user_page_history',
			['user_id' => $user_id,'user_name' => $user_name, 'page_title' => $page_title],
			__METHOD__
		);
	}

	public static function onLoadExtensionSchemaUpdates(DatabaseUpdater $updater)
	{
		$updater->addExtensionTable('user_page_views', __DIR__ . '/../../sql/UserPageViewTracker.sql');
		// View user_page_hits is in UserPageViewTracker.sql and created together with user_page_views table
	}

	function execute($parser = null)
	{
		$request = $this->getRequest();
		$out = $this->getOutput();
		$user = $this->getUser();

		$out->setPageTitle('Rastreador de páginas por usuários');

		if (method_exists($request, 'getLimitOffsetForUser')) {
			// MW 1.35+
			list($limit, $offset) = $request->getLimitOffsetForUser($user);
		} else {
			list($limit, $offset) = $request->getLimitOffset();
		}

		$userTarget = isset($parser) ? $parser : $request->getVal('username');

		$pager = new UserPageViewTrackerPager($this->getContext(), $user);
		$form = $pager->getForm();
		$body = $pager->getBody();
		$html = $form;

		if ($body) {
			$html .= $pager->getNavigationBar();
			$html .= "<table class='wikitable' width='75%' cellspacing='0' cellpadding='0' style='text-align:center; margin-left: auto; margin-right: auto'>";
			$html .= '<tr><th>Usuário</th><th>Página</th><th>Visualizações</th><th>Última visita</th></tr>';
			$html .= $body;
			$html .= '</table>';
			$html .= $pager->getNavigationBar();
		} else {
			$html .= '<p>' . $this->msg('listusers-noresult')->escaped() . '</p>';
		}
		$out->addHTML($html);
	}
}
