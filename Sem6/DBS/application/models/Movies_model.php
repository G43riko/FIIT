<?php

if(!defined("BASEPATH")) 
	exit("No direct script access allowed");

class Movies_model extends CI_Model {

	/**************************************
	MAKERS
	**************************************/

	public function getAllMakers(){
		//SELECT * FROM movies.makers
		return $this -> getFromWhere("movies.makers_view");
	}

	public function getMakersWithoutCsfd(){
		return $this -> getFromWhere("movies.makers", "csfd_id IS NULL");
	}

	public function getAllActors(){
		$sql = "SELECT DISTINCT mm.* 
				FROM 	movies.makers_view mm
				JOIN 	movies.mtm_movie_maker mmmm 
				ON 		mmmm.maker_id = mm.maker_id
				WHERE	mmmm.role = 'actor'";

		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getAllDirectors(){
		$sql = "SELECT DISTINCT mm.* 
				FROM 	movies.makers_view mm
				JOIN 	movies.mtm_movie_maker mmmm 
				ON 		mmmm.maker_id = mm.maker_id
				WHERE	mmmm.role = 'director'";

		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getSearchMakers($key){
		$key = strtolower(urldecode($key));
		return $this -> getFromWhere("movies.makers_view", "lower(name) LIKE '%$key%'");
	}

	public function updateMaker($id, $data){
		$id = quotte($id);
		$sql = "UPDATE movies.makers SET ";
		$query = array();

		$this -> log("Aktualizuje sa tvorca s id" . $id);

		if(!isset($id) || is_null($id) || empty($id) || !count($data))
			return false;

		if(isset($data["csfd_id"]))
			$query[] = "csfd_id = " . $data["csfd_id"];

		if(isset($data["d_birthday"]))
			$query[] = "d_birthday = to_date('" . $data["d_birthday"] . "', 'YYYY-MM-DD')";

		if(isset($data["avatar"]))
			$query[] = "avatar = '" . $data["avatar"] . "'";

		$sql .= join(", ", $query);
		$sql .= " WHERE imdb_id = $id";

		
		pre_r($data);
		echo $sql . "<br>\n";
		$this -> db -> query($sql);

		$columns = array();
		foreach($data as $key => $val)
			$columns[] = $key . ": " . $val;
		$this -> log("Tvorca s id: " . $id . " sa aktualizovali stlpce " . join(", ", $columns), 1);

		//$this -> db -> update('movies.makers', $data, "imdb_id = $id");
	}

	public function getMakersForUpdate(){
		return $this -> getFromWhere("movies.makers", "d_birthday IS NULL OR avatar IS NULL");
	}

	public function getMaker($id){
		if(is_numeric($id)):
			return $this -> getMakerById($id);
		else:
			//return $this -> getMakerByName(urldecode($id));
			return $this -> getSearchMakers($id);
		endif;
	}

	/*
	private function getMakerByName($name){
		//SELECT * FROM movies.makers WHERE name = 'N'
		$q = $this -> db -> get_where("movies.makers_view", array("lower(name)" => urldecode(strtolower($name))));
		return $q -> num_rows() ? $q -> result_array() : false;
	}
	*/

	private function getMakerByImdbID($id){
		//SELECT * FROM movies.makers WHERE imdb_id = ID
		return $this -> getFromWhere("movies.makers_detail_view", "imdb_id = '$id'", TRUE);
	}

	private function getMakerById($id){
		//SELECT * FROM movies.makers WHERE maker_id = ID
		return $this -> getFromWhere("movies.makers_detail_view", "maker_id = $id", TRUE);
	}

	/**************************************
	MOVIES
	**************************************/

	public function getAllMoviesDeatils(){
		return $this -> getFromWhere("movies.movies_detail_view");
	}

	public function getAllMovies(){
		//SELECT * FROM movies.movies
		return $this -> getFromWhere("movies.movies_view");
	}

	public function updateMovie($id, $data){
		$id = quotte($id);
		$sql = "UPDATE movies.movies SET ";
		$query = array();

		if(!isset($id) || is_null($id) || empty($id) || !count($data))
			return false;

		/*
		if(isset($data["csfd_id"]))
			$query[] = "csfd_id = " . $data["csfd_id"];

		if(isset($data["replaced_by"]))
			$query[] = "replaced_by = " . $data["replaced_by"];
		*/
		foreach($data as $key => $val)
			if($key == "rating")
				$query[] = "$key = " . (floatval($val) / 10);
			else
				$query[] = "$key = " . $data[$key];	

		$sql .= join(", ", $query);
		$sql .= " WHERE movie_id = $id";
		pre_r($data);
		echo $sql . "<br>\n";

		$this -> log("Film s id: $id sa aktualizoval", 1);
		$this -> db -> query($sql);
	}
	public function getMoviesWithoutCsfd(){
		return $this -> getFromWhere("movies.movies_view", "csfd_id IS NULL");
	}

	public function deleteMovie($id){
		$this -> db -> query("SELECT movies.deletemovie($id)");
	}

	public function getMovieByImdbId($id){
		return $this -> getFromWhere("movies.movies", "imdb_id = '$id'");
	}

	public function getSearchMovies($name){
		$name = strtolower(urldecode($name));

		$q = $this -> db -> like("lower(title)", $name)
						 //-> or_like("lower(title_sk)", $name) 
						 -> limit(8)
						 -> from("movies.movies")
						 -> join('movies.c_prices', 'movies.c_prices.price_id = movies.movies.price_id')
						 -> get();

		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getMoviesByYear($year, $name = ""){
		$cond = "year = $year";
		if(!empty($name))
			$cond .= " AND lower(title) LIKE '%" . urldecode(strtolower($name)) . "%'";
		return $this -> getFromWhere("movies.movies_view", $cond);
	}


	public function getMoviesByGenre($genre, $name = "", $posters = 0){
		$genre = urldecode(strtolower($genre));
		$name  = urldecode(strtolower($name));
		$sql = "SELECT mm.*
				FROM   movies.movies_view mm
				JOIN   movies.mtm_movie_genre mmg ON mm.movie_id = mmg.movie_id
				JOIN   movies.c_genres mcg ON mmg.genre_id = mcg.genre_id ";

		if(is_numeric($genre))
			$sql .= "WHERE mcg.genre_id = $genre";
		else
			$sql .= "WHERE lower(mcg.name) = '$genre' ";

		if(!empty($name))
			$sql .=" AND lower(mm.title) LIKE '%$name%'";

		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getMoviesByCountry($country, $name = ""){
		$country = strtolower(urldecode($country));
		$name 	 = strtolower(urldecode($name));

		$sql = "SELECT mm.*
				FROM   movies.movies_view mm
				JOIN   movies.mtm_movie_country mmg ON mm.movie_id = mmg.movie_id
				JOIN   movies.c_countries mcg ON mmg.country_id = mcg.country_id ";

		if(is_numeric($country))
			$sql .= "WHERE mcg.country_id = $country";
		else
			$sql .= "WHERE lower(mcg.name) = '$country' ";

		if(!empty($name))
			$sql .=" AND lower(mm.title) LIKE '%$name%'";

		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getMoviesByTag($tag, $name = ""){
		$tag = strtolower(urldecode($tag));
		$name = strtolower(urldecode($name));
		$sql = "SELECT mm.*
				FROM   movies.movies_view mm
				JOIN   movies.mtm_movie_tag mmg ON mm.movie_id = mmg.movie_id
				JOIN   movies.c_tags mcg ON mmg.tag_id = mcg.tag_id ";

		if(is_numeric($tag))
			$sql .= "WHERE mcg.tag_id = $tag";
		else
			$sql .= "WHERE lower(mcg.name) = '$tag' ";

		if(!empty($name))
			$sql .=" AND lower(mm.title) LIKE '%$name%'";

		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getMovieById($id){
		//SELECT * FROM movies.moveis WHERE movie_id = ID
		return $this -> getFromWhere("movies.movies_detail_view", "movie_id = $id", TRUE);
	}

	/**************************************
	CLEARS
	**************************************/

	/**************************************
	OTHERS
	**************************************/

	public function getAllYears(){
		$sql = "SELECT year, count(*) AS movies 
				FROM movies.movies_detail_view
				GROUP BY year 
				ORDER BY movies DESC, year DESC";
		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getAllGenres(){
		return $this -> getFromWhere("movies.genres_view");
	}

	public function getGenreById($id){
		return $this -> getFromWhere("movies.genres_view", "genre_id = $id", 1);
	}

	public function updateGenre($id, $data){
		$this -> db -> where("genre_id", $id) -> update("movies.c_genres", $data);
	}

	public function getAllCountries(){
		return $this -> getFromWhere("movies.countries_view");
	}

	public function getCountryById($id){
		return $this -> getFromWhere("movies.countries_view", "country_id = $id", 1);
	}

	public function updateCountry($id, $data){
		$this -> db -> where("country_id", $id) -> update("movies.c_countries", $data);
	}

	public function getAllTags(){
		return $this -> getFromWhere("movies.tags_view");
	}

	public function getTagById($id){
		return $this -> getFromWhere("movies.tags_view", "tag_id = $id", 1);
	}

	public function updateTag($id, $data){
		$this -> db -> where("tag_id", $id) -> update("movies.c_tags", $data);
	}

	/**************************************
	UTILS
	**************************************/

	public function addMovieArray($data, $force = false){
		//pripravý dáta
		if(isset($data["countries"]))
			$data["countries"] 	= quotteArray($data["countries"]);
		if(isset($data["genres"]))
			$data["genres"] 	= quotteArray($data["genres"]);
		if(isset($data["tags"]))
			$data["tags"] 		= quotteArray($data["tags"]);
		if(isset($data["title"]))
			$data["title"] 		= str_replace("'", "\"", $data["title"]);
		//BEGIN;
		$this -> db -> trans_start();
		//pozrie sa či film už neexistuje
		$result = $this -> getMovieByImdbId($data["imdb_id"]);

		if(!$force && $result)
			return;
			//die("film " . $data["title"] . " (". $data["year"]. ") už existuje");

		$movie_id = NULL;
		//pridá film
		if(!$result){
			$arr = array();

			//získa dalšie ID
			$movie_id = $this -> db -> query("select nextval('movies.\"seq_movie_id\"')");
			$movie_id = intval($movie_id -> result_array()[0]["nextval"]);
			$arr["movie_id"] = $movie_id;

			if(isset($data["year"]))
				$arr["year"] = $data["year"];
			if(isset($data["title"]))
				$arr["title"] = $data["title"];
			if(isset($data["rating"]))
				$arr["rating"] = $data["rating"];
			if(isset($data["length"]))
				$arr["length"] = $data["length"];
			if(isset($data["imdb_id"]))
				$arr["imdb_id"] = $data["imdb_id"];
			if(isset($data["poster"]))
				$arr["poster"] = $data["poster"];
			if(isset($data["title_sk"]))
				$arr["title_sk"] = $data["title_sk"];
			if(isset($data["csfd_id"]))
				$arr["csfd_id"] = $data["csfd_id"];
			if(isset($data["contentRating"]))
				$arr["content"] = $data["contentRating"];

			$this -> db -> insert("movies.movies", $arr);
			$this -> log("Pridal sa film " . $arr["title"] . "s ide: " . $this -> db -> insert_id(), 1);
		}
		else
			$movie_id = $result["movie_id"];

		//pridá režiséra
		$dir = $this -> getMakerByImdbID($data["director"]["imdb_id"]);

		if(!$dir){
			$dir = $this -> db -> query("select nextval('movies.\"seq_maker_id\"')");
			$dir = intval($dir -> result_array()[0]["nextval"]);

			$this -> db -> insert("movies.makers", array("maker_id"	=> $dir,
														 "name" 	=> $data["director"]["name"],
														 "imdb_id"	=> $data["director"]["imdb_id"]));
			$this -> log("Pridal sa režisér " . $data["director"]["name"], 1);
		}
		$arr = array("maker_id" => is_array($dir) ? $dir["maker_id"] : $dir,
					 "movie_id" => $movie_id,
					 "role"		=> "director");
		if(!$this -> db -> where($arr) -> get("movies.mtm_movie_maker") -> num_rows())
			$this -> db -> insert("movies.mtm_movie_maker", $arr);

		//pridá hercov

		$this -> load -> model("imdb_model");
		$this -> load -> model("csfd_model");

		foreach($data["actors"] as $actor):
			$act = $this -> getMakerByImdbID($actor["imdb_id"]);
			if(!$act):
				$res = $this -> imdb_model -> parseMaker($actor["imdb_id"]);

				
				if(isset($res["birthday"]) && !strpos($res["birthday"], "-0") && !strpos($res["birthday"], "0-"))
					$actor["d_birthday"] = $res["birthday"];
				
				if(isset($res["avatar"]))
					$actor["avatar"] = $res["avatar"];

				$res = $this -> csfd_model -> searchActor($actor["name"]);

				if(isset($res["csfd_id"]))
					$actor["csfd_id"] = $res["csfd_id"];
				$this -> db -> insert("movies.makers", $actor);
				$this -> log("Pridal sa herec " . $actor["name"], 1);
			endif;
		endforeach;

		//pridá genre
		$this -> addOtherData($data, "genres", "c_genres", "genre_id");

		//pridá krajny
		$this -> addOtherData($data, "countries", "c_countries", "country_id");


		//pridá tagy
		$this -> addOtherData($data, "tags", "c_tags", "tag_id");

		//pridá k filmu genre

		$result = $this -> getDataIn("genre_id", "movies.c_genres", "name", $data["genres"]);
		if($result)
			$this -> addDataToMovie($result -> result_array(), "mtm_movie_genre", "genre_id", $movie_id);
		
		//pridá k filmu krajny

		$result = $this -> getDataIn("country_id", "movies.c_countries", "name", $data["countries"]);
		if($result)
			$this -> addDataToMovie($result -> result_array(), "mtm_movie_country", "country_id", $movie_id);

		//pridá k filmu tagy
		
		$result = $this -> getDataIn("tag_id", "movies.c_tags", "name", $data["tags"]);
		if($result)
			$this -> addDataToMovie($result -> result_array(), "mtm_movie_tag", "tag_id", $movie_id);

		//pridá k filmu hercov
		
		$actors = array();

		foreach($data["actors"] as $act)
			$actors[] = quotte($act["name"]);

		$sql = "SELECT maker_id
				FROM   movies.makers
				WHERE  name IN (" . join(", ", $actors). ")";

		foreach($this -> db -> query($sql) -> result_array() as $id){
			$arr = array("movie_id"	=> $movie_id,
						 "maker_id" => $id["maker_id"]);
			//ak už film nemá priradený tag
			if(!$this -> db -> where($arr) -> get("movies.mtm_movie_maker") -> num_rows())
				$this -> db -> insert("movies.mtm_movie_maker", $arr);
		}

		//ukončí transakciu
		//COMMIT;
		$this -> db -> trans_complete();

		return $movie_id;
	}

	private function addOtherData($data, $key, $table, $table_id){
		foreach($data[$key] as $name):
			$sql = "INSERT INTO movies.$table (name)
					SELECT $name
					WHERE  NOT EXISTS (
						SELECT 'name'
						FROM   movies.$table
						WHERE  name = $name 
					)
					returning $table_id, 'name'";
			$this -> db -> query($sql);
		endforeach;
	}

	public function addDataToMovie($data, $table, $data_id, $movie_id){
		foreach($data as $id){
			$arr = array("movie_id"	=> $movie_id,
						 $data_id 	=> $id[$data_id]);
			if(!$this -> db -> where($arr) -> get("movies." . $table) -> num_rows())
				$this -> db -> insert("movies." . $table, $arr);
		}


		$this -> log("K filmu s id $movie_id sa pridali filmy do tabulky: $table", 1);
	}

	public function getDataIn($select, $from, $where, $in){
		if(empty($in))
			return false;	
		$sql = "SELECT $select
				FROM   $from 
				WHERE  $where IN (" . join(", ", $in). ")";

		return $this -> db -> query($sql);
	}

	public function getFromWhere($from, $where = FALSE, $onlyOne = FALSE){
		$q = ($where ? $this -> db -> where($where) : $this -> db);
		$q = $q -> get($from);

		if($onlyOne)
			return $q -> num_rows() ? $q -> result_array()[0] : false;
		
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getCountFromWhere($from, $where){
		$sql = "SELECT count(*) FROM $from WHERE $where";
		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array()[0]["count"] : false;
	}

	public function log($text, $type = 0){
		$data = array("content"	=> $text,
					  "page"	=> current_url(),
					  "type"	=> $type);

		$this -> db -> insert("movies.logs", $data);
	}

	/*
	public function getGenreById($id){
		$q = $this -> db -> get_where("movies.genres_view", array("genre_id" => $id));
		return $q -> num_rows() ? $q -> result_array() : false;
	}
	public function getGenreByName($name){
		$q = $this -> db -> get_where("movies.genres_view", array("lower(name)" => strtolower($name)));
		return $q -> num_rows() ? $q -> result_array() : false;
	}
	public function getMovieByNameAndYear($name, $year){
		//SELECT * FROM movies.movies WHERE title = 'T' AND year = 'Y';
		$sql = "SELECT  * 
				FROM    movies.movies 
				WHERE   lower(title) = '" . lowerTrim($this -> db -> escape_like_str($name)) . "'
						AND year  = " . $year;
		$qe = $this -> db -> query($sql);
		return $qe -> num_rows() ? $qe -> result_array() : false;
	}
	public function getMovieByName($name){
		//SELECT * FROM movies.movies WHERE title LIKE '%NAME%';
		//$q = $this -> db -> like('title', $name) -> get("movies.movies");
		$name = strtolower(urldecode($name));
		$sql = "SELECT * FROM movies.movies_view WHERE lower(title) LIKE '%" . $name . "%'";
		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}
	*/

	public function createNewMovie($data){
		$movie_id = $this -> db -> query("select nextval('movies.\"seq_movie_id\"')");
		$movie_id = $movie_id -> result_array()[0]["nextval"];


		//add movie
		$movie = array("movie_id"	=> $movie_id,
					   "imdb_id"	=> NULL,
					   "title"		=> $data["title"],
					   "rating"		=> floatval($data["rating"]) / 10,
					   "poster"		=> NULL,
					   "year"		=> $data["year"],
					   "csfd_id"	=> NULL,
					   "length"		=> $data["length"],
					   "title_sk"	=> NULL);

		$this -> db -> insert("movies.movies", $movie);
		pre_r($data);

		if(isset($data["genres"])){
			foreach($data["genres"] as $key => $genre_id)
				$data["genres"][$key] = "($movie_id, $genre_id)";
			$sql = "INSERT INTO movies.mtm_movie_genre (movie_id, genre_id) 
					VALUES " . join(", ", $data["genres"]);
			$this -> db -> query($sql);
		}

		if(isset($data["countries"])){
			foreach($data["countries"] as $key => $country_id)
				$data["countries"][$key] = "($movie_id, $country_id)";
			$sql = "INSERT INTO movies.mtm_movie_country (movie_id, country_id) 
					VALUES " . join(", ", $data["countries"]);
			$this -> db -> query($sql);
		}

		if(isset($data["tags"])){
			foreach($data["tags"] as $key => $tag_id)
				$data["tags"][$key] = "($movie_id, $tag_id)";
			$sql = "INSERT INTO movies.mtm_movie_tag(movie_id, tag_id) 
					VALUES " . join(", ", $data["tags"]);
			$this -> db -> query($sql);
		}

		if(isset($data["actors"])){
			foreach($data["actors"] as $key => $maker_id)
				$data["actors"][$key] = "($movie_id, $maker_id)";
			$sql = "INSERT INTO movies.mtm_movie_maker (movie_id, maker_id) 
					VALUES " . join(", ", $data["actors"]);
			$this -> db -> query($sql);
		}

		if(isset($data["director"])){
			foreach($data["director"] as $key => $maker_id)
				$data["director"][$key] = "($movie_id, $maker_id, 'director')";
			$sql = "INSERT INTO movies.mtm_movie_maker (movie_id, maker_id, role) 
					VALUES " . join(", ", $data["director"]);
			$this -> db -> query($sql);
		}


		$this -> log("Pridal sa nový film s id: $movie_id ", 1);

		return $movie_id;
	}

	public function getMoviesDatas(){
		$sql = "SELECT 	MAX(year) AS max_year, 
						MIN(year) AS min_year,
						MIN(rating) AS min_rating,
						MAX(rating) AS max_rating,
						MIN(length) AS min_length,
						MAX(length) AS max_length
				FROM 	movies.movies";

		$q = $this -> db -> query($sql);
		
		return $q -> num_rows() ? $q -> result_array()[0] : false;
	}

	public function createUpdatedMovie($id, $dataNew){
		$data = $this -> getMovieById($id);

		$movie_id = $this -> db -> query("select nextval('movies.\"seq_movie_id\"')");
		$movie_id = $movie_id -> result_array()[0]["nextval"];

		//add movie
		$movie = array("movie_id"	=> $movie_id,
					   "imdb_id"	=> $data["imdb_id"],
					   "title"		=> $dataNew["title"],
					   "rating"		=> floatval($dataNew["rating"]) / 10,
					   "poster"		=> $data["poster"],
					   "year"		=> $dataNew["year"],
					   "csfd_id"	=> $data["csfd_id"],
					   "d_created"	=> $data["d_created"],
					   "length"		=> $dataNew["length"],
					   "title_sk"	=> $data["title_sk"]);

		$this -> db -> insert("movies.movies", $movie);
		pre_r($dataNew);

		foreach($dataNew["genres"] as $key => $genre_id)
			$dataNew["genres"][$key] = "($movie_id, $genre_id)";
		$sql = "INSERT INTO movies.mtm_movie_genre (movie_id, genre_id) 
				VALUES " . join(", ", $dataNew["genres"]);
		$this -> db -> query($sql);


		foreach($dataNew["countries"] as $key => $country_id)
			$dataNew["countries"][$key] = "($movie_id, $country_id)";
		$sql = "INSERT INTO movies.mtm_movie_country (movie_id, country_id) 
				VALUES " . join(", ", $dataNew["countries"]);
		$this -> db -> query($sql);


		foreach($dataNew["tags"] as $key => $tag_id)
			$dataNew["tags"][$key] = "($movie_id, $tag_id)";
		$sql = "INSERT INTO movies.mtm_movie_tag(movie_id, tag_id) 
				VALUES " . join(", ", $dataNew["tags"]);
		$this -> db -> query($sql);


		foreach($dataNew["actors"] as $key => $maker_id)
			$dataNew["actors"][$key] = "($movie_id, $maker_id)";
		$sql = "INSERT INTO movies.mtm_movie_maker (movie_id, maker_id) 
				VALUES " . join(", ", $dataNew["actors"]);
		$this -> db -> query($sql);

		foreach($dataNew["director"] as $key => $maker_id)
			$dataNew["director"][$key] = "($movie_id, $maker_id, 'director')";
		$sql = "INSERT INTO movies.mtm_movie_maker (movie_id, maker_id, role) 
				VALUES " . join(", ", $dataNew["director"]);
		$this -> db -> query($sql);


		$this -> log("Film s id: $id je pripravený na aktualizáciu s id: $movie_id ", 1);

		return $movie_id;
	}
}