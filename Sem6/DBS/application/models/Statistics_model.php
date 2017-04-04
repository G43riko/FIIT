<?php

class Statistics_model extends CI_Model {
/*
+počet filmou
+počet hercou
+počet žánrou
+počet tagov
+počet štatov
-počet požičiek(aktualnych / všetkych)
-počet uživatelov

+najlepšie filmy
+najnovšie pridane filmy
+najdlhšie filmy

-najnovšie natočené filmy
+najlepší herci
+najlepšie žánre
+najlepšie tagy
+najlepšie štáty

najnovší úživatelia
*/
	public function getDataIn($select, $from, $where, $in){
		$sql = "SELECT $select
				FROM   $from 
				WHERE  $where IN (" . join(", ", $in). ")";
		return $this -> db -> query($sql);
	}

	public function getNumberOf($string){
		$q = $this -> db -> count_all($string);
		return $q;
	}

	private function getNFromBy($num, $from, $by){
		$sql = "SELECT * FROM $from ORDER BY $by DESC LIMIT $num";
		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getNLongestMovies($num){
		return $this -> getNFromBy($num, "movies.movies", "length");
	}

	public function getNBestMovies($num){
		return $this -> getNFromBy($num, "movies.movies", "rating");
	}

	public function getNNewestMovies($num){
		$sql = "SELECT movie_id, title, to_char(d_created, 'DD.MM.YYYY') as d_created 
				FROM movies.movies
				ORDER BY d_created DESC 
				LIMIT " . $num;
		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getNthRecentGenres($num){
		$sql = "SELECT mg.name, count(*) as num
				FROM movies.c_genres mg
				JOIN movies.mtm_movie_genre mmg on mg.genre_id = mmg.genre_id
				GROUP BY mg.name 
				ORDER BY num DESC, mg.name 
				LIMIT $num";
		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getNthRecentTags($num){
		$sql = "SELECT mg.name, count(*) as num
				FROM movies.c_tags mg
				JOIN movies.mtm_movie_tag mmg on mg.tag_id = mmg.tag_id
				GROUP BY mg.name 
				ORDER BY num DESC, mg.name 
				LIMIT $num";
		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getNthRecentYears($num){
		$sql = "SELECT year as name, count(*) as num
				FROM movies.movies_view
				GROUP BY year
				ORDER BY num DESC, name DESC 
				LIMIT $num";
		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getNthRecentCountries($num){
		$sql = "SELECT mg.name, count(*) as num
				FROM movies.c_countries mg
				JOIN movies.mtm_movie_country mmg on mg.country_id = mmg.country_id
				GROUP BY mg.name 
				ORDER BY num DESC, mg.name 
				LIMIT $num";
		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getNthRecentMakers($num, $withAvatar = false){
		$sql = "SELECT mg.name, mg.maker_id, count(*) as num, mg.d_birthday, mg.avatar
				FROM movies.makers mg
				JOIN movies.mtm_movie_maker mmg on mg.maker_id = mmg.maker_id ";
		if($withAvatar)
			$sql .= "WHERE mg.avatar IS NOT NULL ";
		$sql .= "GROUP BY mg.name, mg.maker_id, mg.d_birthday, mg.avatar
				ORDER BY num DESC, mg.name 
				LIMIT $num";
		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getNthRecentActors($num){
		$sql = "SELECT mg.name, mg.maker_id, count(*) as num
				FROM movies.makers mg
				JOIN movies.mtm_movie_maker mmg on mg.maker_id = mmg.maker_id
				WHERE mmg.role = 'actor'
				GROUP BY mg.name, mg.maker_id
				ORDER BY num DESC, mg.name 
				LIMIT $num";
		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getNthRecentDirectors($num){
		$sql = "SELECT mg.name, mg.maker_id, count(*) as num
				FROM movies.makers mg
				JOIN movies.mtm_movie_maker mmg on mg.maker_id = mmg.maker_id
				WHERE mmg.role = 'director'
				GROUP BY mg.name, mg.maker_id
				ORDER BY num DESC, mg.name 
				LIMIT $num";
		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}

	public function getNthRecentLoans($num){
		$sql = "";
		$q = $this -> db -> query($sql);
		return $q -> num_rows() ? $q -> result_array() : false;
	}
}