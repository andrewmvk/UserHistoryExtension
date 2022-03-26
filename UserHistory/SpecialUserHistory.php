<?php

class SpecialUserHistory extends IncludableSpecialPage
{

	function __construct()
	{
		parent::__construct('UserHistory', 'edit');
	}

	function execute($srcName)
	{
		$dbw = wfGetDB(DB_PRIMARY);

		if ((!isset($_GET['user_id']) && (isset($_COOKIE["wikidbUserID"])))) {
			$dbname = $dbw->getDBname();
			$userIDindex = $dbname . 'UserID';
			$userID = $_COOKIE[$userIDindex];
			$page = 0;
		} else if (isset($_GET['user_id'])) {
			$userID = $_GET['user_id'];
			$page = $_GET['page'];
		}
		$output = $this->getOutput();
		$this->setHeaders();

		if (isset($userID)) {
			$res = $dbw->select('user_page_history', '*', "user_id=" . $userID, __METHOD__, ['ORDER BY' => 'last DESC']);

			$isFirst = true;

			$i = 0;
			while ($values[$i] = $dbw->fetchRow($res)) {
				$i++;
			}

			$qtdLines = $i;
			$i = $page * 50;
			$table_limit = $i + 50;

			$maxPageLength = count($values) / 50;
			$maxPageLengthCountContent = count($values) % 50;

			while ((isset($values[$i]['user_name'])) && ($i <= $table_limit)) {
				if ($isFirst) {
					$html = '<h3 style="text-align:center">Histórico de ' . $values[$i]['user_name'] . '</h3></br>';
					$html .= '<table class="wikitable" width="50%" style="text-align:center; margin-left:auto; margin-right:auto; table-layout:fixed" >';
					$html .= '<thead><tr><th>Página</th><th>Última visita</th></tr></thead>';
				}
				$html .= '<tbody><tr><td>' . $values[$i]['page_title'] . '</td><td "style=padding:200px">' . $values[$i]['last'] . '</td></tr></tbody>';
				$isFirst = false;
				$i++;
			}

			$html .= '</table>';
			$page_next = $page + 1;
			$page_prev = $page - 1;

			if ($qtdLines > 50) {
			$html .= '<div style="text-align:center; display:block ruby">';
				if ($page > 0) {
					$html .= '<a href="./Especial:UserHistory?user_id=' . $userID . '&page=' . $page_prev . '" style="margin-right:12px">
							Anterior
						  </a></br>';
				} else {
					$html .= '<p style="margin-right:12px">
							Anterior
						  </p></br>';
				}
				if (($page_next < $maxPageLength - 1) || (($page_next == $maxPageLength - 1) && ($maxPageLengthCountContent > 1))) {
					$html .= '<a href="./Especial:UserHistory?user_id=' . $userID . '&page=' . $page_next . '" style="margin-left:12px">
						Próximo
					  </a></br></div>';
				} else {
					$html .= '<p style="margin-left:12px">
							Próximo
						  </p></br></div>';
				}
			}

			$html .= '<a href="./Especial:UserPageViewTracker">Voltar ao rastreador de páginas por usuários</a>';
			$html .= '<br>';
		} else {
			$html = 'É necessário <b>aceitar os cookies</b> da página para acessá-la diretamente.</br>Caso contrário selecione a data da última ';
			$html .= 'visita à alguma página do usuário ao qual você deseja visualizar o histórico: ';
			$html .= '<a href="./Especial:UserPageViewTracker">Rastreador de Visualizações</a>';
		}
		$output->addHTML($html);
	}

	/**
	 * @return string
	 */
	protected function getGroupName()
	{
		return 'other';
	}
}
