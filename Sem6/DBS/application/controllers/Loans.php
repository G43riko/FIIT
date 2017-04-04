<?php

if(!defined("BASEPATH")) 
	exit("No direct script access allowed");

class Loans extends CI_Controller {
	public function __construct(){
		parent::__construct();

		$this -> load -> model("loan_model");

		$this -> columns = array("loan_id" 		=> FALSE,//"Loan Id",
								 "first_name" 	=> word("firstName"),
								 "second_name" 	=> word("secondName"),
								 "d_created" 	=> word("created"),
								 "before" 		=> word("length"),
								 "d_returned"	=> word("returned"),
								 "movies"		=> FALSE//word("movies")
								 );
	}
	public function index(){
		$data = $this -> loan_model -> getAllLoans();
		if($data)
			foreach($data as $key => $loan){
				$data[$key]["movies"] = prepareData($loan["movies"], movieDetailURL);
				$date  = ($loan["months"] ? $loan["months"] . " " . word("months") . ", ": NULL);
				$date .= ($loan["days"]   ? $loan["days"]   . " " . word("days")   . ", ": NULL);
				$date .= ($loan["hours"]  ? $loan["hours"]  . " " . word("hours")  . " " : NULL);
				if(empty($date))
					$date = word("now");
				$data[$key]["before"] = $date;
			}
		$this -> load -> view("loans_view", array("loans" 	=> $data,
												  "data"	=> $this -> columns));
	}

	public function detail($id, $hideHeadAndFoot = 0){
		$this -> load -> model("loan_model");
		$data = $this  -> loan_model -> getLoanById($id);
		if(!$data)
			die("nenašla sa pôžička s ID: " . $id);
		
		if($hideHeadAndFoot)
			$data["hideHeader"] = $data["hideFooter"] = 1;

		$this -> load -> view("loan_detail_view.php", $data);
	}

	public function add($movies = false){
		if(!$movies)
			$this -> load -> view("loan_add_view");
		else{
			$this -> loan_model -> addLoad($movies);
			redirect("/loans");
		}
	}

	public function finish($id){
		$this -> loan_model -> finishLoan($id);
		redirect("/loans");
	}
}
