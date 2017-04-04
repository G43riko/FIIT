<?php 
	if(!isset($hideHeader))$this -> load -> view('header_view.html'); 
	if(!is_login())
		redirect("movies/detail/" . $movie_id);
	
?>
	<div class="row">
		<form method="post" action="<?= movieURL ?>updateMovie">
			<div class="modal-header">
				<h1>
  					<input type="text" class="form-control input-lg" style="width:200px; display: inline;" value="<?= (isset($title_sk) ? $title_sk : $title) ?>" name="title">
					<?= "<input style='width:100px; display: inline;' min='1800' max='2020' " .
						"type='number' class='form-control input-lg' name='year' value='" . $year . "'>" 
					?>
				</h1>
			</div>
			<div class="modal-body">
					<table>
						<tr>
			<?php if(isset($poster)): ?>
				<td rowspan=10>
					<img style="width:90%" class="img-thumbnail" usemap="#s" src="<?= getImage($poster) ?>">
				</td>
			<?php endif; ?>
						<td>
							<h4>
								<?= word("rating") ?>:
							</h4>
						</td>
						<td>
							<?= "<input style='width:70px;' name='rating' min='0' max='100' type='number' class='form-control' value='" 
							. $rating * 10  . "'>" ?>
						</td>
						<td>
							<h4>
								<?= word("length") ?>:
							</h4>
						</td>
						<td>
							<?= "<input style='width:70px;' name='length' min='0' type='number' class='form-control' value='$length'>" ?>
						</td>
						<td>
							<h4>
								<?= word("director") ?>:
							</h4>
						</td>
						<td>
							<select class='multiselectable-short' name='director[]' multiple='multiple'>
								<?= echoArray($director, "") ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<h4>
								<?= word("genres") . ":" ?>
							</h4>
						</td>
						<td colspan="7">
							<select class='multiselectable' name='genres[]' multiple='multiple'>
								<?= echoArray($genres, "") ?> 
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<h4>
								<?= word("countries") . ":"?>
							</h4>
						</td>
						<td colspan="7">
							<select class='multiselectable' name='countries[]' multiple='multiple'>
								<?= echoArray($countries, "") ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<h4>
								<?= word("tags") . ":"?>
							</h4>
						</td>
						<td colspan="7">
							<select class='multiselectable' name='tags[]' multiple='multiple'>
								<?=echoArray($tags, "") ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<h4>
								<?= word("actors") . ":"?>
							</h4>
						</td>
						<td colspan="7">
							<select class='multiselectable' name='actors[]' multiple='multiple'>
								<?=echoArray($actors, "") ?> 
							</select>
						</td>
					</tr>
				</table>
			</div>
			<div class="modal-footer">
			<input type="hidden" name="movie_id" value="<?= $movie_id ?>">
			<?php
				$class = 'class="btn btn-default"';
				if($movie_id)
					makeLink("<input type='button' value='IMDB' $class>", imdbMovieURL . $imdb_id, 1, 1);
				if(isset($csfd_id))
					makeLink("<input type='button' value='CSFD' $class>", csfdMovieURL . $csfd_id, 1, 1);
				echo "<input type='submit' value='Dokončiť' $class";
			?>
			</div>
		</form>
	</div>
<?php if(!isset($hideFooter))$this -> load -> view('footer_view.html'); ?>