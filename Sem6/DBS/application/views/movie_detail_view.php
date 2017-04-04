<?php if(!isset($hideHeader))$this -> load -> view('header_view.html'); ?>
	<div class="row">
		<div class="modal-header">
			<h1>
				<?= (isset($title_sk) ? $title_sk : $title) . " (" . $year . ")" ?>
			</h1>
		</div>
		<div class="modal-body">
			<?php if(isset($movie_id)) echo "<blockquote>" ?>
			<table style="width: 100%">
				<tr>
	<?php if(isset($poster)): ?>
		<td rowspan=10 width="25%">
			<img style="width:90%" class="img-thumbnail" usemap="#s" src="<?= getImage($poster) ?>">
		</td>
	<?php endif; ?>
					<td valign="top"><p><?= $rating * 10  . "% | " . $length  . " min | " . 
					(is_array($director) ? $director["name"] : $director) ?></td>
				</tr>
				<tr><td valign="top"><p><?= word("genres")	 . ": " . echoArray($genres) ?></p></td></tr>
				<tr><td valign="top"><p><?= word("countries") . ": " . echoArray($countries) ?></p></td></tr>
				<tr><td valign="top"><p><?= word("tags") 	 . ": " . echoArray($tags) ?></p></td></tr>
				<tr><td valign="top"><p><?= word("actors")    . ": </br>". echoArray($actors) ?></p></td></tr>
			</table>
			<?php if(isset($movie_id)): ?>
				<footer>Film bol pridaný: <?= $d_created ?></footer>
				</blockquote>
			<?php endif; ?>
		</div>
		<div class="modal-footer">
			<?php
				$class = 'class="btn btn-default"';

				makeLink("<button $class>IMDB</button>", imdbMovieURL . $imdb_id, 1, 1);
				if(isset($csfd_id))
					makeLink("<button $class>CSFD</button>", csfdMovieURL . $csfd_id, 1, 1);
				if(isset($movie_id) && is_login()){
					makeLink(wrapToTag("upraviť", "button", 0, $class), movieEditURL . $movie_id, 1, 0);
					wrapToTag("Vymazať", "button", 1, "$class onclick=\"window.location='/movies/movies/delete/$movie_id/'+prompt('Zadaj id filmu')\"");
				}
			?>
		</div>
	</div>
<?php if(!isset($hideFooter))$this -> load -> view('footer_view.html'); ?>