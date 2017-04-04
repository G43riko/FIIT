<?php

if(!defined("BASEPATH")) 
	exit("No direct script access allowed");

class Movies extends CI_Controller {
	public function __construct(){
		parent::__construct();

		$this -> load -> model("imdb_model");
		$this -> load -> model("movies_model");

		$this -> columns = array("movie_id" 	=> FALSE,//"ID",
								 "title" 		=> word("title"),
								 "title_sk" 	=> FALSE,//word("titleSK"),
								 "year" 		=> word("year"),
								 "length" 		=> word("length"),
								 "rating" 		=> word("rating"),
								 "genres" 		=> word("genres"),
								 "tags" 		=> FALSE,//"Tagy",
								 "countries" 	=> FALSE,//word("countries"),
								 "actors" 		=> FALSE,//"Herci",
								 "d_created" 	=> FALSE,//word("created"),
								 "director" 	=> word("director"),
								 "imdb_id" 		=> word("imdbID"));


		$this -> linksArray = array("countries"	=> countryURL,
									"genres" 	=> genreURL,
									"year" 		=> yearURL,
									"tags"		=> tagURL,
									"actors"	=> makerDetailURL,
									"director" 	=> makerDetailURL);
	}

	public function parse($id){
		$data = $this -> imdb_model -> parse($id);
		$data["imdb_id"] = $id;
		$data["hideHeader"] = 1;
		$data["hideFooter"] = 1;
		$this -> load -> view("movie_detail_view", $data);
	}

	public function search($q = ""){
		if(empty($q))
			$this -> index();
		else{
			$data = $this -> movies_model -> getSearchMovies($q);
			
			if($data)
				$data = prepareLocalData($data, $this -> linksArray);

			$this -> load -> view('movies_view.html', array("movies" => $data,
						  									"data"   => $this -> columns,
						  									"search" => $q));
		}
	}

	public function searchIMDB($name = "", $val = 1){
		$names = array("title_popular" 		=> word("popular"),
					   "title_exact"		=> word("exact"),
					   "title_substring"	=> word("substring"));
		$name = urldecode($name);
		$data = empty($name) ? array() : get_object_vars($this -> imdb_model -> findMovie($name));

		if($data && isset($data["title_popular"]))
			foreach($data["title_popular"] as $key => $value)
				if($movie = $this -> movies_model -> getMovieByImdbId($value -> id))
					$data["title_popular"][$key] -> dbId = $movie[0]["movie_id"];
			
		$this -> load -> view("movies_search_view", array("data"	=> $data,
														  "names"	=> $names,
														  "name"	=> $name,
														  "vypis"	=> 0,
														  "val"		=> $val,
														  "i" 		=> 0,
														  "link"	=> imdbMovieURL));
	}

	private function genArray($title, $type, $fix){
		return array("query" => array(
				$type => array(
					$title => $fix . $_REQUEST["title"] . $fix
				)
			)
		);
	}

	public function qsearch($hideView = 0){
		if(!$hideView){
			$data = $this -> movies_model -> getMoviesDatas();
			$this -> load -> view("movies_es_view", $data);
		}
		else{
			$or = array();
			$fix = "*";
			$type = "wildcard";
			if($_REQUEST["only_prefix"]){
				$fix = "";
				$type = "prefix";
			}
			if($_REQUEST["search_title"])
				$or[] = $this -> genArray("title", $type, $fix);
			if($_REQUEST["search_actor"])
				$or[] = $this -> genArray("actors", $type, $fix);
			if($_REQUEST["search_tag"])
				$or[] = $this -> genArray("tags", $type, $fix);
			if($_REQUEST["search_country"])
				$or[] = $this -> genArray("countries", $type, $fix);
			if($_REQUEST["search_genre"])
				$or[] = $this -> genArray("genres", $type, $fix);
			$q = array(
				"query" => array(
					"filtered" => array(
						"query" => array(
							"match_all" => array()
						),
						"filter" => array(
							"and" => array(
								array("or" => $or),
								array("range" => array(
										"length" => array(
											"from" 	=> $_REQUEST["length"][0],
											"to"	=> $_REQUEST["length"][1]
										)
									)
								),
								array("range" => array(
										"year" => array(
											"from" 	=> $_REQUEST["year"][0],
											"to"	=> $_REQUEST["year"][1]
										)
									)
								),
								array("range" => array(
										"rating" => array(
											"from" 	=> $_REQUEST["rating"][0],
											"to"	=> $_REQUEST["rating"][1]
										)
									)
								)
							)
						)
					)
				)
			);
			$url = 'http://127.0.0.1:9200/movies/movies/_search?pretty=true&size=10000';
			echo $this -> execCurl($url, json_encode($q));
		}
	}

	public function dashboard(){
		?>
		<iframe id="kibana" src="http://127.0.0.1:5601/app/kibana#/dashboard/MainMovies?embed=true&_g=(refreshInterval:(display:Off,pause:!f,value:0),time:(from:now-15m,mode:quick,to:now))&_a=(filters:!(),options:(darkTheme:!t),panels:!((col:1,id:graph-rating,panelIndex:2,row:8,size_x:12,size_y:3,type:visualization),(col:1,id:graph-years,panelIndex:3,row:3,size_x:12,size_y:3,type:visualization),(col:1,id:graph-length,panelIndex:4,row:13,size_x:12,size_y:5,type:visualization),(col:1,id:YearData,panelIndex:5,row:1,size_x:12,size_y:2,type:visualization),(col:1,id:RatingData,panelIndex:6,row:6,size_x:12,size_y:2,type:visualization),(col:1,id:LengthData,panelIndex:7,row:11,size_x:12,size_y:2,type:visualization)),query:(query_string:(analyze_wildcard:!t,query:'*')),title:MainMovies,uiState:())"></iframe>
		<script type="text/javascript">
		document.getElementById("kibana").width = window.innerWidth - 20;
		document.getElementById("kibana").height = window.innerHeight - 20;
		</script>
		<?php
	}

	public function addToES(){
		$data = $this -> movies_model -> getAllMoviesDeatils();
		$url = 'http://127.0.0.1:9200/movies';
		echo "mazanie: ";
		pre_r($this -> execCurl($url, "", 9200, "DELETE"));
		echo "<br/>\n";
		$mapp = array(
					"mapping" => array(
						"movies" => array(
							"properties" => array(
								"movie_id" 	=> array("type" => "integer"),
								"csfd_id" 	=> array("type" => "integer"),
								"year" 		=> array("type" => "integer"),
								"length" 	=> array("type" => "integer"),
								"rating" 	=> array("type" => "float"),
								"genres"	=> array(
									"type"	=> "string",
									"index" => "not_analyzed"
								),
								"actors"	=> array(
									"type"	=> "string",
									"index" => "not_analyzed"
								)
							)
						)
					)
				);

		$result = $this -> execCurl($url, json_encode($mapp), 9200, "PUT");
		pre_r($mapp);
		$output = array();
		foreach ($data as $value) {
			$temp["movie_id"] = $value["movie_id"];
			$temp["title"] = $value["title"];
			$temp["year"] = (int)$value["year"];
			$temp["rating"] = (float)$value["rating"];
			$temp["csfd_id"] = $value["csfd_id"];
			$temp["imdb_id"] = $value["imdb_id"];
			$temp["length"] = (int)$value["length"];
			$temp["genres"] = $this -> processValue($value["genres"]);
			$temp["director"] = $this -> processValue($value["director"]);
			$temp["tags"] = $this -> processValue($value["tags"]);
			$temp["countries"] = $this -> processValue($value["countries"]);
			$temp["actors"] = $this -> processValue($value["actors"]);
			$temp["title_sk"] = $value["title_sk"];
			
			$output[] = $temp;
			$result = $this -> execCurl($url, json_encode($temp));
			pre_r($temp);
			//pre_r($result);
		}
		//pre_r($output);
	}

	private function execCurl($url, $json = NULL, $port = 9200, $type = "POST"){
		$ci = curl_init();
		curl_setopt($ci, CURLOPT_URL, $url);
		curl_setopt($ci, CURLOPT_PORT, $port);
		curl_setopt($ci, CURLOPT_TIMEOUT, 200);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ci, CURLOPT_FORBID_REUSE, 0);
		curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $type);
		if(!is_null($json))
			curl_setopt($ci, CURLOPT_POSTFIELDS, $json);
		return curl_exec($ci);
	}

	private function processValue($value){
		$result = array();
		$tmp = explode(",", $value);
		foreach($tmp as $val){
			$data = explode(":::", $val);
			//$res["name"] = $data[0];
			//$res["id"] = (int)$data[1];
			//$result[] = $res;
			$result[] = $data[0];
		}
		return $result;
	}

	public function add($id, $redirect = true, $csfd_id = false){
		$data = $this -> imdb_model -> parse($id);
		$data["imdb_id"] = $id;

		if($csfd_id)
			$data["csfd_id"] = $csfd_id;

		$id = $this -> movies_model -> addMovieArray($data);
		if($redirect)
			redirect("movies/detail/$id");
	}

	public function searchDetail($imdb_id){
		$data =  $this -> imdb_model -> parse($imdb_id);
		$this -> load  -> view("movie_detail_view.php", $data);
	}

	public function detail($movieId, $hideHeadAndFoot = 0){
		$data  = $this  -> movies_model -> getMovieById($movieId);
		if(!$data)
			die("nenašiel sa film s ID: " . $movieId);

		$data = prepareLocalData($data, $this -> linksArray, 0);
		
		if($hideHeadAndFoot)
			$data["hideHeader"] = $data["hideFooter"] = 1;

		$this -> load -> view("movie_detail_view.php", $data);
	}

	public function edit($movieId = 0, $hideHeadAndFoot = 0){
		if(!$movieId){
			$arr = array("title" => "Názov filmu",
						 "title_sk" => "Slovenský názov",
						 "year" => 1800,
						 "rating" => 0,
						 "length" => 0,
						 "movie_id" => 0,
						 "imdb_id" => "");

			$arr["director"] = $this -> prepareEditableData("", "maker_id", $this  -> movies_model -> getAllDirectors());
			$arr["tags"] = $this -> prepareEditableData("", "tag_id",$this -> movies_model -> getAllTags());	
			$arr["genres"] = $this -> prepareEditableData("", "genre_id", $this  -> movies_model -> getAllGenres());
			$arr["countries"] = $this -> prepareEditableData("", "country_id", $this  -> movies_model -> getAllCountries());
			$arr["actors"] = $this -> prepareEditableData("", "maker_id", $this  -> movies_model -> getAllActors());
			$this -> load -> view("movie_detail_edit.php", $arr);
			return;
		}

		$data  = $this  -> movies_model -> getMovieById($movieId);
		if(!$data)
			die("nenašiel sa film s ID: " . $movieId);
		
		/*
		$dir = explode(":", $data["director"]);
		$data["director"]  = "<input type='text' name='director' class='form-control' value='";
		$data["director"] .= $dir[0] . "' alt='" . $dir[1] . "'>";

		*/
		$data["director"] = $this -> prepareEditableData($data["director"], "maker_id", 
														 $this  -> movies_model -> getAllDirectors());
	
		$data["tags"] = $this -> prepareEditableData($data["tags"], "tag_id",
													 $this -> movies_model -> getAllTags());	

		$data["genres"] = $this -> prepareEditableData($data["genres"], "genre_id", 
													   $this  -> movies_model -> getAllGenres());

		$data["countries"] = $this -> prepareEditableData($data["countries"], "country_id", 
													 	  $this  -> movies_model -> getAllCountries());

		$data["actors"] = $this -> prepareEditableData($data["actors"], "maker_id", 
													   $this  -> movies_model -> getAllActors());

		if($hideHeadAndFoot)
			$data["hideHeader"] = $data["hideFooter"] = 1;

		$this -> load -> view("movie_detail_edit.php", $data);
	}

	private function prepareEditableData($data, $id, $allData){
		$tmp = array();
		foreach($allData as $maker)
			$tmp[$maker[$id]] = array("name" => /*strtolower*/($maker["name"]), "exist" => "");

		if(!empty($data)){
			$data = explode(",", $data);
			foreach($data as $mainKey => $val)
				if(strpos($val, ":::") !== false)
					$tmp[explode(":::", $val)[1]]["exist"] = "selected='selected'";
		}
		$data = array();
		foreach($tmp as $key => $val)
			$data[] = "<option value='$key' " . $val["exist"] . ">" . $val["name"] . "</option>";

		return $data;
	}

	public function updateMovie(){
		$this -> db -> trans_start();
		if(!$_REQUEST["movie_id"]){
			$id = $this -> movies_model -> createNewMovie($_REQUEST);
			$this -> db -> trans_complete();
			redirect("movies/detail/$id");
			return;
		}


		$id = $this -> movies_model -> createUpdatedMovie($_REQUEST["movie_id"], $_REQUEST);
		$this -> movies_model -> deleteMovie($_REQUEST["movie_id"]);
		//$this -> movies_model -> updateMovie($_REQUEST["movie_id"], $_REQUEST);

		$this -> db -> trans_complete();
		redirect("movies/detail/$id");
	}

	public function index(){
		$data = $this -> movies_model -> getAllMovies();
		if($data)
			$data = prepareLocalData($data, $this -> linksArray);

		$this -> load -> view('movies_view.html', array("movies" => $data,
					  									"data"   => $this -> columns));

	}

	public function delete($id, $delete = false){
		if($delete == $id){
			$data = $this -> movies_model -> deleteMovie($id);
			redirect("/");
		}
		redirect("/movies/detail/$id");
	}

	public function posters($numY = 4, $numX = 6){
		$this -> load -> model("statistics_model");
		$arr = array("movies" 	=> $this -> statistics_model -> getNBestMovies($numX * $numY),
					 "numX" 	=> $numX,
					 "numY" 	=> $numY);
		$this -> load -> view("movies_posters_view", $arr);
	}

	public function searchInDb($name){
		$data = $this -> movies_model -> getSearchMovies($name);
		if($data):
			echo "<ul class='list-group'>";
			foreach($data as $value):
				$a = "alt='" . $value["movie_id"] . "' price='" . $value["value"];
				$a .= "' onclick='addMovie(this, 1)' class='glist list-group-item'";
				$text = $value["title"] . "(" . $value["year"] . ")";
				wrapToTag($text, "li", 1, $a);
			endforeach;
			echo "</ul>";
		else:
			echo "<ul class='list-group'><li class='list-group-item'>" . word("noResults") . "</li></ul>";
		endif;
	}

	/*
	public function addByCSFD($csfd_id){
		$this -> load -> model("csfd_model");
		$data = $this -> csfd_model -> getMovieInfo($csfd_id);
		pre_r($data);
		if(isset($data["imdb_id"]))
			$this -> add($data["imdb_id"], false, $csfd_id);
	}

	public function addMovie($num = 1){
		for($i=0 ; $i<$num ; $i++){
			$data = $this -> movies_model -> getMovieToAdd();
			pre_r($data);
			if($data)
				$this -> addByCSFD($data[0]["csfd_id"]);
		}
	}

	public function decode($id){
		$content = get_headers("http://www.csfd.cz/film/" . $id);
		$url = explode(": ", $content[19])[1];
		echo gzdecode(file_get_contents($url));
	}

	public function decode2($name){
		$name = str_replace(" ", "+", urldecode($name));
		echo file_get_contents("http://www.csfd.cz/hledat/?q=$name");
	}
	
	public function addArray($ides, $redirect = true){
		foreach($ides as $id){
			$data = $this -> imdb_model -> parse($id);
			$data["imdb_id"] = $id;
			$this -> movies_model -> addMovieArray($data);
		}
		if($redirect)
			redirect("/");
	}
	*/
}	