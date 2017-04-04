<?php

if(!defined("BASEPATH")) 
	exit('No direct script access allowed');

class Updater extends CI_Controller {
	public function __construct(){
		parent::__construct();
	}

	public function parseCsfd($name){
		$this -> load -> model("csfd_model");
		//pre_r($this -> csfd_model -> searchActor($name));
		pre_r($this -> csfd_model -> searchMovie($name));
	}

	public function statistics(){
		$this -> load -> model("movies_model");
		$arr = array();

		$model = $this -> movies_model;

		$arr["filmyBezSK"] 			= $model -> getCountFromWhere("movies.movies", 	"title_sk IS NULL");
		$arr["filmyBezCSFD"] 		= $model -> getCountFromWhere("movies.movies_view", 	"csfd_id IS NULL");
		$arr["filmyBezHercov"] 		= $model -> getCountFromWhere("movies.movies_detail_view", 	"actors IS NULL");
		$arr["filmyBezRezisera"]	= $model -> getCountFromWhere("movies.movies_view", 	"director IS NULL");
		$arr["filmyBezHodnotenia"]	= $model -> getCountFromWhere("movies.movies_view", 	"rating IS NULL");
		$arr["filmyBezPostera"]		= $model -> getCountFromWhere("movies.movies", 	"poster IS NULL");
		$arr["filmyBezDlzky"]		= $model -> getCountFromWhere("movies.movies_view", 	"length IS NULL");
		$arr["filmyBezRoku"] 		= $model -> getCountFromWhere("movies.movies", 	"year IS NULL");
		$arr["filmyBezZanrov"] 		= $model -> getCountFromWhere("movies.movies_view", 	"genres IS NULL");
		$arr["filmyBezVeku"] 		= $model -> getCountFromWhere("movies.movies", 			"content IS NULL");
		$arr["filmyBezKrajin"] 		= $model -> getCountFromWhere("movies.movies_detail_view", 	"countries IS NULL");
		$arr["herciBezAvatara"] 	= $model -> getCountFromWhere("movies.makers", 	"avatar IS NULL");
		//$arr["herciBezFilmov"] 		= $model -> getCountFromWhere("movies.makers_detail_view", 	"movies IS NULL");
		$arr["herciBezNarodenia"]	= $model -> getCountFromWhere("movies.makers_view", 	"d_birthday IS NULL");
		$arr["herciBezCSFD"] 		= $model -> getCountFromWhere("movies.makers", 	"csfd_id IS NULL");
		$arr["tagiBezSK"] 			= $model -> getCountFromWhere("movies.tags_view", 	 	"name_sk IS NULL");
		$arr["tagiBezFilmov"]		= $model -> getCountFromWhere("movies.tags_view", 	 	"movies = 0");
		$arr["zanreBezSK"] 			= $model -> getCountFromWhere("movies.genres_view", 	"name_sk IS NULL");
		$arr["zanreBezFilmov"] 		= $model -> getCountFromWhere("movies.genres_view", 	"movies = 0");
		$arr["krajnyBezSK"] 		= $model -> getCountFromWhere("movies.countries_view",	"name_sk IS NULL");
		$arr["krajnyBezFilmov"]		= $model -> getCountFromWhere("movies.countries_view",	"movies = 0");


		pre_r($arr);
	}

	public function updateMovieCsfdId($limit = 5){
		$this -> load -> model("movies_model");
		$this -> load -> helper("text");
		$this -> load -> model("csfd_model");

		$data = $this -> movies_model -> getMoviesWithoutCsfd();
		$num = count($data);
		for($i=0 ; $i < $num && $i < $limit ; $i++){
			$movies = $this -> csfd_model -> searchMovie(convert_accented_characters($data[$i]["title"]));

			$arr = array();
			$zhoda = 0;
			foreach($movies as $actMovie){
				if(isset($actMovie["year"]) && 
				   intval($actMovie["year"]) == intval($data[$i]["year"]) &&
				   isset($actMovie["director"]) &&
				   strtolower(trim($actMovie["director"])) == strtolower(trim(explode(":", $data[$i]["director"])[0]))){
					$zhoda = 1;
					$movies = $actMovie;
					break;
				}
			}
			if($zhoda){
				$arr = array();
				if(isset($movies["csfd_id"]))
					$arr["csfd_id"] = $movies["csfd_id"];
				$this -> movies_model -> updateMovie($data[$i]["movie_id"], $arr);
			}
		}
	}

	public function updateMakerCsfdId($limit = 5){
		$this -> load -> model("movies_model");
		$this -> load -> model("csfd_model");

		$data = $this -> movies_model -> getMakersWithoutCsfd();

		$num = count($data);
		for($i=0 ; $i < $num && $i < $limit ; $i++){
			$actor = $this -> csfd_model -> searchActor($data[$i]["name"]);
			$arr = array();
			if(isset($actor["csfd_id"]))
				$arr["csfd_id"] = $actor["csfd_id"];
			$this -> movies_model -> updateMaker($data[$i]["imdb_id"], $arr);
			sleep(3);
		}
	}

	public function index(){
		$this -> load -> model("imdb_model");
		$this -> load -> model("movies_model");

		$data = $this -> movies_model -> getMakersForUpdate();
		if($data)
			foreach($data as $maker){
				$id   	=  $maker["imdb_id"];
				$res  	=  $this -> imdb_model -> parseMaker($id);
				$arr 	= array();

				if(isset($res["birthday"]))
					$arr["d_birthday"] = $res["birthday"];

				if(isset($res["avatar"]))
					$arr["avatar"] = $res["avatar"];


	

				$this -> movies_model -> updateMaker($id, $arr);
			}

		redirect("/");
	}
}