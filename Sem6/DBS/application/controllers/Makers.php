<?php

if(!defined("BASEPATH")) 
	exit("No direct script access allowed");

class Makers extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$this -> load -> model("movies_model");
		$this -> load -> model("imdb_model");

		$this -> columns = array("maker_id" 	=> word("id"),
								 "name"			=> word("name"),
								 "d_birthday" 	=> word("birthday"),
								 "movies_num"	=> word("workOnMovies"),
								 "d_created"	=> FALSE,//word("created")
								 );
	}

	public function index(){
		$this -> load -> view('makers_view.php', array("makers" => $this -> movies_model -> getAllMakers(),
					  								   "data"   => $this -> columns));
	}

	public function search($q = ""){
		if(empty($q))
			$this -> index();
		else{
			$this -> load -> model("movies_model");
			$data = $this -> movies_model -> getSearchMakers($q);
			$this -> load -> view('makers_view.php', array("makers" => $data,
						  								   "data"   => $this -> columns,
						  								   "search" => $q));
		}
	}

	public function detail($makerId, $hideHeadAndFoot = 0){
		$data = $this -> movies_model -> getMaker($makerId);
		if(!$data)
			die("nenašiel sa maker s ID: " . $makerId);

		if($hideHeadAndFoot)
			$data["hideHeader"] = $data["hideFooter"] = 1;
		
		$this -> load -> view("maker_detail_view.php", $data);
	}

	public function edit($id){
		
	}

	public function posters($numY = 4, $numX = 6){
		$this -> load -> model("statistics_model");
		$arr = array("movies" 	=> $this -> statistics_model -> getNthRecentMakers($numX * $numY, true),
					 "numX" 	=> $numX,
					 "numY" 	=> $numY);
		$this -> load -> view("makers_posters_view", $arr);
	}

	/*
	public function searchIMDB($name, $val = 1){
		$data = $this -> imdb_model -> findMaker($name);

		echo "<table>";

		$names = array("name_popular" 	=> word("popular"),
					   "name_exact"		=> word("exect"),
					   "name_substring"	=> word("substring"));
		
		if(property_exists($data, "name_popular")):
			wrapToTag(wrapToTag(wrapToTag("Popular", "h3"),"td"),"tr", 1);
			foreach($data -> name_popular as $row):
				$line = wrapToTag(makeLink($row -> name, imdbMakerURL . $row -> id, 0, 1), "td");
				wrapToTag($line . wrapToTag($row -> description, "td"), "tr", 1);
			endforeach;
		endif;

		if(!property_exists($data, "name_popular") ||$val > 1 && property_exists($data, "name_exact")):
			wrapToTag(wrapToTag(wrapToTag("Exact", "h3"),"td"),"tr", 1);
			foreach($data -> name_exact as $row):
				$line = wrapToTag(makeLink($row -> name, imdbMakerURL . $row -> id, 0, 1), "td");
				wrapToTag($line . wrapToTag($row -> description, "td"), "tr", 1);
			endforeach;
		endif;
		
		if($val > 2 && property_exists($data, "name_substring")):
			wrapToTag(wrapToTag(wrapToTag("Substring", "h3"),"td"), "tr", 1);
			foreach($data -> name_substring as $row):
				$line = wrapToTag(makeLink($row -> name, imdbMakerURL . $row -> id, 0, 1), "td");
				wrapToTag($line . wrapToTag($row -> description, "td"), "tr", 1);
			endforeach;
		endif;
		echo "</table>";
	}
	*/
}