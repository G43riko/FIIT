<?php

class Csfd_model extends CI_Model {
	public function searchActor($name){
		include_once("simple_html_dom.php");
		$this -> load -> helper("text");
		$name = convert_accented_characters(str_replace(" ", "+", urldecode($name)));
		$html = file_get_html("http://www.csfd.cz/hledat/?q=$name");


		$result = array();
		$data = $html -> find("#search-creators .ui-image-list li");

		if(count($data)):
			$actor = $data[0];
			$link = $actor -> find("a");

			if(count($link))
				$result["csfd_id"] = explode("-", explode("/", $link[0] -> href)[2])[0];

			$link = $actor -> find("img");
			if(count($link))
				$result["avatar"] = explode("?", $link[0] -> src)[0];

			$info = $actor -> find("p");
			if(isset($info[1]))
				$result["birthday"] = str_replace("nar. ", "", $info[1] -> plaintext);
		endif;

		return $result;
	}

	public function searchMovie($name, $year = 0, $director = 0){
		include_once("simple_html_dom.php");
		$html = file_get_html("http://127.0.0.1/movies/movies/decode2/" . $name);


		$result = array();
		$data = $html -> find("#search-films .ui-image-list li");

		foreach($data as $movie):
			$movieResult = array();

			$link = $movie -> find("a");
			if(count($link))
				$movieResult["csfd_id"] = explode("-", explode("/", $link[0] -> href)[2])[0];

			$link = $movie -> find("h3");
			if(count($link))
				$movieResult["title"] = $link[0] -> plaintext;

			$link = $movie -> find("img");
			if(count($link))
				$movieResult["poster"] = explode("?", $link[0] -> src)[0];

			$link = $movie -> find("p");
			if(isset($link[0])){
				$info = explode(",", $link[0] -> plaintext);
				if(isset($info[0]))
					$movieResult["genres"] = $info[0];
				if(isset($info[1]))
					$movieResult["country"] = $info[1];
				if(isset($info[2]))
					$movieResult["year"] = $info[2];

				if(isset($link[1])){
					$director = $link[1] -> find("a");
					if(count($director))
						$movieResult["director"] = $director[0] -> plaintext;
				}
			}
			$result[] = $movieResult;
		endforeach;

		return $result;
	}

	public function getMovieInfo($id){
		include_once("simple_html_dom.php");
		$html = file_get_html("http://127.0.0.1/movies/movies/decode/". $id);

		$result = array();

		//IMDB_ID

		$imdb_id = $html -> find("#share .links a[title='profil na IMDb.com']");
		if(count($imdb_id))
			$result["imdb_id"] = explode("/", $imdb_id[0])[4];

		return $result;
	}
}
