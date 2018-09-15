<?php //echo "TEsting"; die; ?>
<?php include_once "functions.php"; ?>
<?php

	$word = (isset($_GET['search']) ? $_GET['search'] : "");

	if ($word != "") {
		$data = $mainSite->getSearchResultByWord($word);
		$html = "<div class='totalRecords'>Total Results: ".count($data)."</div>";
		$html .= "<div class='dataWrapper list-group'>";
		foreach ($data as $key => $value) {
			$html .= "<div class='singleData list-group-item list-group-item-action flex-column align-items-start'>";

			$html .= "<h3 class='title'>".$mainSite->highlightWord($value['post_title'], $word)."</h3>";

			$html .= "<div class='date text-muted'>Date: ".date("d-m-Y",strtotime($value['post_date']))."</div>";
			$html .= "<div class='categories text-muted'>Categories: ".$mainSite->highlightWord($value['post_categories'], $word)."</div>";
			$html .= "<div class='tags text-muted'>Tags: ".$mainSite->highlightWord($value['post_tags'], $word)."</div>";

			$html .= "<div class='link text-muted'>Link: ".$mainSite->getPostLink($value['ID'])."</div>";

			$html .= "<div class='content mb-1'>".$mainSite->highlightWord($value['post_content'], $word)."</div>";
			$html .= "</div>";
		}
		$html .= "</div>";

		echo $html;
	}
?>