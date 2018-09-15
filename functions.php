<?php
/**
 * All functions for main site
 */
class MainSite
{
	private $con = null;

	private $DB_HOST = "localhost";
	private $DB_USER = "root";
	private $DB_PASSWORD = "root";
	private $DB_NAME = "wpcontest_db";

	private $DB_TABLE_PREFIX = "mvai2_";

	private function dbConnect() {
		$this->con = mysqli_connect($this->DB_HOST, $this->DB_USER, $this->DB_PASSWORD, $this->DB_NAME);
		if (mysqli_connect_errno()) {
			$this->con = null;
		}
	}

	private function dbClose() {
		mysqli_close($this->con);
	}

	private function queryExecute($query) {
		$this->dbConnect();

		if ($this->con === null){
			return [];
		}
		$data = array();
		$result = mysqli_query($this->con, $query);
		while ($row = mysqli_fetch_assoc($result)) {
			$data[] = $row;
		}
		$this->dbClose();

		return $data;
	}

	function getSearchableKeywords() {
		$keywordsArray = array();
		$query = "select post_title, post_content from ".$this->DB_TABLE_PREFIX."posts where post_status = \"publish\" limit 500";
		$result = $this->queryExecute($query);
		foreach ($result as $key => $post) {
			$titleWords = (explode(" ",$this->getCleanText(strtolower($post['post_title']))));
			$contentWords = (explode(" ",$this->getCleanText(strtolower($post['post_content']))));

			$keywordsArray = array_unique(array_merge($keywordsArray, $titleWords, $contentWords));

			if (count($keywordsArray) > 1000)
				break;
		}

		$query2 = "select name from ".$this->DB_TABLE_PREFIX."terms limit 500";
		$result = $this->queryExecute($query2);
		foreach ($result as $key => $tags) {
			$tagsArray = explode(" ",$this->getCleanText(strtolower($tags['name'])));

			$keywordsArray = array_unique(array_merge($keywordsArray, $tagsArray));

			if (count($keywordsArray) > 1500)
				break;
		}

		return $keywordsArray;
	}

	private function getCleanText($text) {
		$text = str_replace('<', ' <', $text);
		$text = str_replace(array("\"","'"), ' ', $text);
		$text = strip_tags($text);
		$text = preg_replace("/[^a-z]+/i"," ",($text));
		return $text;
	}

	function highlightWord($text, $word) {
		$text = htmlentities($text);
		$highlighter = '<span class="highlight">';
		$offset = 0;
		$allpos = array();
		while (($pos = stripos($text, $word, $offset)) !== FALSE) {
			$text = substr_replace($text, $highlighter, $pos, 0);
			$pos = strlen($word) + $pos + strlen($highlighter);
			$text = substr_replace($text, '</span>', $pos, 0);
			$offset = $pos + 5;
		}
		return $text;
	}

	private function escapeString($str) {
		$this->dbConnect();
		$str = mysqli_real_escape_string($this->con, $str);
		$this->dbClose();
		return $str;
	}

	function getPostLink($postId) {
		$query = "select CONCAT(
				(select TRIM(BOTH '/' FROM option_value) from ".$this->DB_TABLE_PREFIX."options where option_name=\"siteurl\"),
				\"/\", case when (mt.slug is not null) THEN mt.slug ELSE \"uncategorized\" END, \"/\",
				mp.post_name,\"/\"
			) as link
			from ".$this->DB_TABLE_PREFIX."posts as mp
			left outer join ".$this->DB_TABLE_PREFIX."term_relationships as mtr
			on mp.ID = mtr.object_id
			left outer join ".$this->DB_TABLE_PREFIX."term_taxonomy as mtt
			on mtr.term_taxonomy_id=mtt.term_taxonomy_id
			left outer join ".$this->DB_TABLE_PREFIX."terms as mt
			on mtt.term_id=mt.term_id and mtt.taxonomy=\"category\"
			where mp.ID = $postId
			order by mtr.term_order
			limit 1;"
		;

		$result = $this->queryExecute($query);
		return $result[0]['link'];
	}

	function getSearchResultByWord($word) {
		$word = $this->escapeString($word);
		$query = 'select mp.ID, mp.post_date, mp.post_title, mp.post_content, pt_tbl1.post_tags, pc_tbl2.post_categories
			from '.$this->DB_TABLE_PREFIX.'posts as mp
			left outer join (
				select mtr.object_id as post_id, group_concat(" ", mt.name) as post_tags
				from '.$this->DB_TABLE_PREFIX.'term_relationships as mtr
				join '.$this->DB_TABLE_PREFIX.'term_taxonomy as mtt
				join '.$this->DB_TABLE_PREFIX.'terms as mt
				on mtr.term_taxonomy_id=mtt.term_taxonomy_id and mtt.term_id=mt.term_id and mtt.taxonomy="post_tag"
				group by mtr.object_id
			) as pt_tbl1 on mp.ID=pt_tbl1.post_id

			left outer join (
				select mtr.object_id as post_id, group_concat(" ", mt.name) as post_categories
				from '.$this->DB_TABLE_PREFIX.'term_relationships as mtr
				join '.$this->DB_TABLE_PREFIX.'term_taxonomy as mtt
				join '.$this->DB_TABLE_PREFIX.'terms as mt
				on mtr.term_taxonomy_id=mtt.term_taxonomy_id and mtt.term_id=mt.term_id and mtt.taxonomy="category"
				group by mtr.object_id
			) as pc_tbl2 on mp.ID=pc_tbl2.post_id

			where mp.post_status = "publish" and (mp.post_title like "%'.$word.'%" || mp.post_content like "%'.$word.'%"
			|| pt_tbl1.post_tags like "%'.$word.'%" || pc_tbl2.post_categories like "%'.$word.'%")
			order by mp.ID DESC;
		';

		return $this->queryExecute($query);
	}
}

$mainSite = new MainSite();
?>