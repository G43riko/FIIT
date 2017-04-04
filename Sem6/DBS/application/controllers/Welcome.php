<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	public function __construct(){
		parent::__construct();
	}

	public function index($id = 0){
		$this -> load -> model("statistics_model");
		$body = array(word("movies") 	=> $this -> statistics_model -> getNumberOf("movies.movies"),
					  word("genres") 	=> $this -> statistics_model -> getNumberOf("movies.c_genres"),
					  word("tags")		=> $this -> statistics_model -> getNumberOf("movies.c_tags"),
					  word("countries")	=> $this -> statistics_model -> getNumberOf("movies.c_countries"),
					  word("makers") 	=> $this -> statistics_model -> getNumberOf("movies.makers"),
					  word("loans")		=> $this -> statistics_model -> getNumberOf("movies.loans"),
					  word("persons") 	=> $this -> statistics_model -> getNumberOf("movies.persons"));
		$data = array();
		$data["number"] = array("head" => array(word("type"), word("count")), 
								"body" => $body);

		$num = 7;

		$data["movies"] = array("longest" 	=> $this -> statistics_model -> getNLongestMovies($num),
								"best" 		=> $this -> statistics_model -> getNBestMovies($num),
								"newest" 	=> $this -> statistics_model -> getNNewestMovies($num));

		$data["genres"] = array("head" 	=> array(word("genre"), word("count")),
								"body"	=> $this -> statistics_model -> getNthRecentGenres($num - 1));

		$data["tags"] = array("head" 	=> array(word("tag"), word("count")),
								"body"	=> $this -> statistics_model -> getNthRecentTags($num - 1));

		$data["years"]  = array("head" 	=> array(word("year"), word("count")),
								"body"	=> $this -> statistics_model -> getNthRecentYears($num - 1));

		$data["makers"] = array("head" 	=> array(word("maker"), word("count")),
								"body"	=> $this -> statistics_model -> getNthRecentMakers($num - 1));

		$data["actors"] = array("head" 	=> array(word("maker"), word("count")),
								"body"	=> $this -> statistics_model -> getNthRecentActors($num - 1));

		$data["directors"] 	= array("head" 	=> array(word("maker"), word("count")),
									"body"	=> $this -> statistics_model -> getNthRecentDirectors($num - 1));
		
		$data["countries"]  = array("head" 	=> array(word("country"), word("count")),
									"body"	=> $this -> statistics_model -> getNthRecentCountries($num - 1));

		
		
		$this -> load -> view("statistics_view", array("page" => $id, 
													   "data" => $data));
		
	}
}
