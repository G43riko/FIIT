<?php

if(!defined("BASEPATH")) 
	exit("No direct script access allowed");

class Loan_model extends CI_Model {
	public function addLoad($movies){
		$list = explode("_", $movies);
		
		$q = $this -> db -> query("select nextval('movies.\"loans_loan_id_seq\"')");
		$id = $q -> result_array()[0]["nextval"];

		$colums = array("person_id" => $this -> session -> userdata('person_id'),
						"loan_id" 	=> $id);
		$this -> db -> insert("movies.loans", $colums);

		$colums = array();
		foreach($list as $movie)
			$data[] = array("movie_id"	=> $movie,
							"loan_id"	=> $id);

		$this -> db -> insert_batch("movies.mtm_movie_loan", $data);
	}

	public function getAllLoans(){
		$q = $this -> db -> get("movies.loans_view");
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getLoanById($id){
		$q = $this -> db -> get_where("movies.loans_view", array("loan_id" => $id));
		return $q -> num_rows() ? $q -> result_array()[0] : false;
	}
	public function finishLoan($id){
		$q = $this -> db -> where("loan_id", $id) -> update("movies.loans", array("d_returned" => "now()"));
	}
}