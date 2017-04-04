<?php
	define("folderAddress", 	"/movies/");
	define("movieURL", 			"/movies/movies/");
	define("movieDetailURL",	"/movies/movies/detail/");
	define("movieEditURL",		"/movies/movies/edit/");
	define("genreURL", 			"/movies/genres/");
	define("yearURL", 			"/movies/years/");
	define("countryURL",		"/movies/countries/");
	define("makerURL", 			"/movies/makers/");
	define("makerDetailURL",	"/movies/makers/detail/");
	define("loanURL", 			"/movies/loans/");
	define("tagURL", 			"/movies/tags/");
	define("loginURL", 			"/movies/login/");
	define("regURL", 			"/movies/register/");
	define("logoutURL", 		"/movies/logout/");
	define("otherURL", 			"/movies/others/");
	define("othersEditURL", 	"/movies/others/edit/");

	define("imdbMovieURL", 		"http://www.imdb.com/title/");
	define("imdbMakerURL", 		"http://www.imdb.com/name/");

	define("csfdMovieURL", 		"http://www.csfd.cz/film/");
	define("csfdMakerURL", 		"http://www.csfd.cz/tvurce/");
	

	function pre_r($data){
		echo "<pre>";
		print_r($data);
		echo "<pre/>";
	}

	function getLang(){
		return "sk";
	}

	function showSimpleMovies($array, $coll, $path, $currency = ""){
		foreach($array as $val){
			$title = checkStringLength($val["title"]);
			$title = "<a href='" . $path . $val["movie_id"] . "'>" . $title . "</a>";
			wrapToTag(wrapToTag($title, "td") . wrapToTag($val[$coll] . $currency, "td"), "tr", TRUE);
		}
	}

	function checkStringLength($string, $num = 14, $append = "..."){
		if(strlen($string) > $num)
			$string = substr($string, 0, $num) . $append;
		return $string;
	}

	function word($word){
		$dictionary = array();

		$dictionary["actors"]["sk"]			= "Herci";
		$dictionary["addLoan"]["sk"] 		= "Pridať novú pôžičku";
		$dictionary["addMovie"]["sk"]		= "Pridať film";
		$dictionary["best"]["sk"]			= "Najlepšie";
		$dictionary["birthday"]["sk"]		= "Dátum narodenia";
		$dictionary["clearBasket"]["sk"] 	= "Vyprázdniť koší";
		$dictionary["countries"]["sk"] 		= "Krajny";
		$dictionary["countries"]["sk"]		= "Krajny";
		$dictionary["country"]["sk"]		= "Krajna";
		$dictionary["count"]["sk"]			= "Počet";
		$dictionary["counts"]["sk"]			= "Počty";
		$dictionary["created"]["sk"]		= "Vytvorené";
		$dictionary["createMovie"]["sk"]	= "Vytvoriť film";
		$dictionary["director"]["sk"]		= "Režisér";
		$dictionary["directors"]["sk"]		= "Režiséry";
		$dictionary["days"]["sk"]			= "Dní";
		$dictionary["email"]["sk"] 			= "Email";
		$dictionary["edit"]["sk"]			= "Upraviť";
		$dictionary["enterMovies"]["sk"]	= "Zadaj názov filmu";
		$dictionary["enter"]["sk"]			= "Vlož";
		$dictionary["exact"]["sk"]			= "Presná zhoda";
		$dictionary["finishLoan"]["sk"]		= "Dokončiť pôžičku";
		$dictionary["firstName"]["sk"] 		= "Krsné meno";
		$dictionary["genre"]["sk"] 			= "Žáner";
		$dictionary["genres"]["sk"] 		= "Žánre";
		$dictionary["genres"]["sk"]			= "Žánre";
		$dictionary["hours"]["sk"]			= "Hodiny";
		$dictionary["id"]["sk"]				= "ID";
		$dictionary["imdbID"]["sk"]			= "IMDb";
		$dictionary["inMovies"]["sk"]		= "Vo filmoch";
		$dictionary["length"]["sk"]			= "Dĺžka";
		$dictionary["loans"]["sk"] 			= "Pôžičky";
		$dictionary["login"]["sk"] 			= "Prihlásiť";
		$dictionary["logout"]["sk"] 		= "Odhlásiť";
		$dictionary["longest"]["sk"]		= "Najdlhšie";
		$dictionary["maker"]["sk"] 			= "Tvorca";
		$dictionary["makers"]["sk"] 		= "Tvorcovia";
		$dictionary["movies"]["sk"] 		= "Filmy";
		$dictionary["name"]["sk"]			= "Meno";
		$dictionary["newest"]["sk"]			= "Najnovšie";
		$dictionary["noResults"]["sk"]		= "Žiade záznamy";
		$dictionary["now"]["sk"]			= "Teraz";
		$dictionary["numOfMovies"]["sk"] 	= "Počet filmov";
		$dictionary["overview"]["sk"]		= "Prehľad";
		$dictionary["pass"]["sk"] 			= "Heslo";
		$dictionary["pass"]["sk"] 			= "Heslo";
		$dictionary["persons"]["sk"]		= "Uživatelov";
		$dictionary["popular"]["sk"]		= "Populárne";
		$dictionary["rating"]["sk"]			= "Hodnotenie";
		$dictionary["register"]["sk"] 		= "Registrovať";
		$dictionary["remove"]["sk"]			= "Odstrániť";
		$dictionary["returned"]["sk"]		= "Vrátené";
		$dictionary["save"]["sk"]			= "Uložiť";
		$dictionary["searchMaker"]["sk"]	= "Hladať tvorcu";
		$dictionary["searchMovie"]["sk"]	= "Hladať film";
		$dictionary["searchMovies"]["sk"]	= "Vyhladávanie filmou";
		$dictionary["search"]["sk"]			= "Hľadať";
		$dictionary["secondName"]["sk"] 	= "Priezvisko";
		$dictionary["showPosters"]["sk"] 	= "Zobraziť postery";
		$dictionary["substring"]["sk"]		= "Čiastočná zoda";
		$dictionary["tag"]["sk"] 			= "Tag";
		$dictionary["tags"]["sk"] 			= "Tagy";
		$dictionary["title"]["sk"]			= "Názov";
		$dictionary["titleSK"]["sk"]		= "SK názov";
		$dictionary["totalPrice"]["sk"] 	= "Celková cena";
		$dictionary["workOnMovies"]["sk"]	= "Pracoval na filmoch";
		$dictionary["year"]["sk"]			= "Rok";
		$dictionary["years"]["sk"] 			= "Roky";
		
		$dictionary["undefinedWord"]["sk"]	= "Neznáme slovo";

		$dictionary["drama"]["sk"]			= "Dráma";
		$dictionary["crime"]["sk"]			= "krimy";
		$dictionary["comedy"]["sk"]			= "Komédia";
		$dictionary["thriller"]["sk"]		= "Thriller";
		$dictionary["adventure"]["sk"]		= "Dobrodružný";
		$dictionary["action"]["sk"]			= "Akčný";
		$dictionary["mystery"]["sk"]		= "Mysteriózny";
		$dictionary["family"]["sk"]			= "Rodinný";
		$dictionary["sci-fi"]["sk"]			= "Sci-Fi";
		$dictionary["westers"]["sk"]		= "Western";
		$dictionary["horror"]["sk"]			= "Horor";
		$dictionary["fantasy"]["sk"]		= "Fantazy";
		$dictionary["romance"]["sk"]		= "Romantický";

		if(!isset($dictionary[$word]))
			return $dictionary["undefinedWord"][getLang()] . ": " . $word;

		return $dictionary[$word][getLang()];
	}

	function is_login(){
		return getSession("logged_in");
	}

	function getSession($string){
		$CI = & get_instance();
		return $CI -> session -> userdata($string);
	}

	function quotte($string, $quotte = "'"){
		if(isset($string) && !empty($string))
			return $quotte . $string . $quotte;
	}

	function quotteArray($data, $recursive = TRUE){
		$num = count($data);
		for($i=0 ; $i<$num ; $i++)
			if($recursive && is_array($data[$i]))
				$data[$i] = quotteArray($data[$i]);
			else
				$data[$i] = $actor = "'" . str_replace("'", "\"", trim($data[$i])) . "'";
		return $data;
	}

	function nvl($in, $res){
		return is_null($in) ? $res : $in;
	}

	function echoArray($val, $delimiter = ", ", $show = FALSE){
		if(is_array($val))
			foreach($val as $key => $value)
				if(is_array($val[$key]))
					$val[$key] = $val[$key]["name"];

		$res = is_array($val) ? join($delimiter, $val) : $val;
		if($show)
			echo $res;
		return $res;
	}

	function getImage($url){
		if(is_null($url) || empty($url) || !is_connected())
			return false;
		try{
			$contents = file_get_contents($url);
			$base64   = base64_encode($contents);
			$ext = explode(".", $url)[1];
			return ('data: image/' . $ext . ' ;base64,' . $base64);
		}
		catch(Exception $e){
			return false;
		}
	}

	function is_connected(){
		$connected = @fsockopen("www.google.com", 80); 
		if (!$connected)
			return false;

		fclose($connected);
	    return true;
	}

	function makeLink($text, $link, $show = FALSE, $blank = FALSE, $attr = ""){
		$res = wrapToTag($text, "a", 0, "$attr href='$link' " . ($blank ? "target='_blank'" : ""));
		if($show)
			echo $res;
		return $res;
	}

	function prepareData($data, $path, $key = NULL, $delimiter = ", "){
		if(is_null($key)){
			$data = explode(", ", $data);
			foreach($data as $key => $val){
				$tmp = explode(":::", $val);
				if(isset($tmp[0]) && isset($tmp[1]))
					$data[$key] = wrapToTag($tmp[0], "a", false, "href='" . $path . $tmp[1] . "'");
			}
			return join($delimiter, $data);
		}
		else{
			$data[$key] = explode(", ", $data[$key]);
			foreach($data[$key] as $key => $val){
				$tmp = explode(":::", $val);
				if(isset($tmp[0]) && isset($tmp[1]))
					$data[$key][$key] = wrapToTag($tmp[0], "a", false, "href='" . $path . $tmp[1] . "'");
			}
			return join($delimiter, $data[$key]);
		}
	}

	function drawInputField($name, $id, $label, $type = "input"){
		$attributes = array("class" => "form-control", "id" => $id, "placeholder" => word("enter") . " $name");
		echo '<div class="form-group">';
    	echo form_label($label . ":", $id);
    	switch($type):
    		case "input":
				echo form_input("$name", set_value("$name", ""), $attributes);
				break;
			case "password":
				echo form_password("$name", set_value("$name", ""), $attributes);
				break;
		endswitch;
		echo "</div>";
	}

	function wrapToTag($value, $tag, $show = false, $params = NULL){
		$res = "<$tag" . (is_null($params) ? " " : " " . $params) . ">" . $value . "</$tag>";
		if($show)
			echo $res;

		return $res;
	}

	function lowerTrim($string){
		return strtolower(trim($string));
	}

	function prepareLocalData($data, $columns, $isArray = true){
		if($isArray){
			foreach($data as $key => $movie)
				foreach($columns as $column => $url)
					if(isset($data[$key][$column])){
						if($column == "year")
							$data[$key][$column] = makeLink($data[$key][$column], $url . $data[$key][$column]);
						else{
							$data[$key][$column] = prepareData($data[$key][$column], $url);
						}
					}
		}
		else
			foreach($columns as $column => $url)
				if(isset($data[$column])){
					if($column == "year")
						$data[$column] = makeLink($data[$column], $url . $data[$column]);
					else
						$data[$column] = prepareData($data[$column], $url);
				}
		return $data;
	}