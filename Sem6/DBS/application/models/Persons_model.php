<?php

if(!defined("BASEPATH")) 
	exit("No direct script access allowed");

class Persons_model extends CI_Model {
	function register(){
		//INSERT INTO movies.persons (f_n, s_n, e, p) VALUES('F_N', 'S_N', 'E', 'P');
		$data = array(
			"first_name" 	=> $_POST["meno"],
			"second_name" 	=> $_POST["priezvisko"],
			"email" 		=> $_POST["email"],
			"password" 		=> sha1($_POST["heslo"]),
		);
		return $this -> db -> insert("movies.persons", $data);
	}

	function check(){
		//SELECT * FROM movies.persons WHERE email = 'E' AND heslo = 'H';
		$data = $this -> db -> where("email", $_POST[strtolower(word("email"))])
							-> where("password", sha1($_POST[strtolower(word("pass"))]))
							-> get("movies.persons");
		
		return $data -> num_rows();
	}

	function getUserData($email){
		//SELECT first_name, second_name, email, d_birthday FROM movies.persons WHERE email = 'E' LIMIT 1
		$data = $this -> db -> select("first_name, second_name, email, d_birthday, person_id")
							-> where("email", $email) 
							-> limit(1) 
							-> get("movies.persons");
		return $data -> result_array();
	}
}