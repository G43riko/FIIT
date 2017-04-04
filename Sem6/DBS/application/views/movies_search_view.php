<?php $this -> load -> view('header_view.html'); ?>
	<h3><?= word("searchMovies") ?></h3>
	<div>
		<div class="row">
			<div class="well well-sm form-group">
				<label for="search_input">Search:</label>
				<input 	type="text" 
						class="form-control" 
						placeholder="key" 
						style="display: inline; width:200px;"
						id="search_input" <?php nvl($name, "value='$name'"); ?>>
				<input class= "btn btn-default" type="button" value="hladat" onclick="searchMovie()">
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 well gScrollable">
				<table style='width: 100%' class="table table-striped">
<?php
	foreach($names as $key => $value):
		if((!$vypis || $val > ++$i) && isset($data[$key])):
			$vypis = 1;
			wrapToTag(wrapToTag(wrapToTag($value, "h3"),"td", 0, "colspan='3'"),"tr", 1);

			foreach($data[$key] as $row):
				$v = get_object_vars($row);
				$line = wrapToTag(makeLink($v["title"], "$link" . $v["id"], 0, 1), "td");
				$line .= wrapToTag(explode(",", $v["description"])[0], "td");
				$tmp = wrapToTag("<i class='fa fa-refresh fa-spin'></i>", "span", 0, "class='spinner'");
				$d = "class='btn btn-default btn-large has-spinner spinerable' ";
				$d .= "onclick='loadMovieDetail(\"" . $v["id"] . "\", this)'";
				$tmp = wrapToTag($tmp . "parseInfo", "button",0 , $d);
				if(isset($v["dbId"]))
					$tmp .= " " . makeLink("profil", movieDetailURL . $v["dbId"], 0, 1);
				$line .= wrapToTag($tmp, "td", 0, "style='width: 160px;'");
				wrapToTag($line, "tr", 1);
			endforeach;
		endif;			
	endforeach;
?>
				</table>
			</div>
			<div class="col-sm-6 well" id="movie_detail_holder" style="min-height:60px"></div>
		</div>
	</div>
<?php $this -> load -> view('footer_view.html'); ?>