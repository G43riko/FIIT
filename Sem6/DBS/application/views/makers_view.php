<?php $this -> load -> view('header_view.html'); ?>
	<h3><?= word("makers") ?></h3>
	<a href="<?=makerURL?>posters"><?= word("showPosters")?></a> |
	<label for="movie_key"> <?= word("searchMaker")?>:</label>
	<input 	type="text" 
			class="form-control-input" 
			id="movie_key" 
			placeholder="<?= word("search")?>"  
			<?php if(isset($search))echo "value='$search'"; ?>
			autofocus
			onkeyup="searchMakerDB(event)">
	<table class="table table-striped sortable">
		<thead>
			<tr>
				<?php
					foreach($data as $key => $value)
						if($value)
							wrapToTag($value, "th", 1);
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			if($makers)
				foreach($makers as $maker):
					echo "<tr>";
					foreach($data as $key => $value):
						if($value):
							if($key == "name"){
								/*
								$link = makeLink("$maker[$key]",  makerDetailURL . $maker["maker_id"]);
								wrapToTag($link, "td", 1);
								*/
								$attr = "data-toggle='modal' data-target='#detailModal' ";
								$link = wrapToTag($maker[$key], "a", 0, "style='cursor: pointer;' $attr");
								$attr = "onclick='loadMakerModal(" . $maker["maker_id"] . ")'";
								wrapToTag($link, "td", 1, $attr);
							}
							else
								wrapToTag($maker[$key], "td", 1);
						endif;
					endforeach;
					echo "</tr>";
				endforeach; 
			else
				wrapToTag(wrapToTag(word("noResults"), "td", 0, "colspan='" . count($data). "'"), "tr", 1);
		?>
		</tbody>
	</table>
<?php $this -> load -> view('footer_view.html'); ?>