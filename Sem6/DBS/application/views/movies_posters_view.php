<?php $this -> load -> view('header_view.html'); ?>
<div>
	<table style="width: 100%">
		<?php
			$num = 0;
			for($i=0 ; $i<$numY ; $i++):
				echo "<tr>";
				for($j=0 ; $j<$numX ; $j++):
					if(isset($movies[$num]["poster"])):
						if(strlen($movies[$num]["title"]) > 18)
							$movies[$num]["title"] = substr($movies[$num]["title"], 0, 15) . "...";
				?>
					<td>
						<a href="http://127.0.0.1/movies/movies/detail/<?=$movies[$num]["movie_id"]?>">
							<div style="position: relative; overflow: hidden;" class="show">
								<div class="image-text-top">
									<?=$movies[$num]["title"]?>
								</div>
								<div class="image-text" style="bottom: 0;">
									<?=$movies[$num]["year"]?>
								</div>

								<img src="<?=getImage($movies[$num]["poster"])?>" class="img-thumbnail">
							</div>
						</a>
					</td>
				<?php
					endif;
					$num++;
				endfor;
				echo "</tr>";
			endfor;
		?>
	</table>
	<a href="<?= movieURL . "posters/" . ($numY + 4)?>">
		<button type="button" class="btn btn-default">Načítať daľšie</button>
	</a>
</div>
<?php $this -> load -> view('footer_view.html'); ?>