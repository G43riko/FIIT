<?php if(!isset($hideHeader))$this -> load -> view('header_view.html'); ?>
	<div class="row">
		<div class="modal-header">
			<h1>
				<?= $name . " (" . (!isset($d_birthday) ? "undefined" : $d_birthday) . ")" ?>
			</h1>
		</div>
		<div class="modal-body">
			<blockquote>
				<table style="width: 100%">
					<?php
						$movies = explode(", ", $movies);
					?>
					<tr>
<?php if(isset($avatar)): ?>
	<td width="25%">
		<img style="width:90%" class="img-thumbnail" usemap="#s" src="<?= getImage($avatar) ?>" id="makerAvatar">
	</td>
<?php endif; ?>
						<td valign="top">
						<?php if(isset($hideHeader)): ?>
							<ul class="list-group" id="moviesList">
							<?php
								foreach($movies as $val):
									$tmp = explode(":::", $val);
									$d = wrapToTag($tmp[2], "span", 0, "class='badge'");
									$d .= wrapToTag($tmp[0], "a", 0, " href='" . movieDetailURL . $tmp[1] . "'");
									wrapToTag($d, "li", 1, "class='list-group-item'");
								endforeach;
							?>
							</ul>
						<?php else: ?>
							<div class="list-group" id="moviesList">
								<?php
								foreach($movies as $val):
									$tmp = explode(":::", $val);
									if(count($tmp) < 3)
										continue;
									$d = wrapToTag($tmp[2], "span", 0, "class='badge'") . $tmp[0];
									makeLink($d, movieDetailURL . $tmp[1], 1, 0, "class='list-group-item'");
								endforeach;
							?>
							</div>
						<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td colspan="2"><footer>Tvorca bol pridaný: <?= $d_created ?></footer></td>
					</tr>
				</table>
			</blockquote>
		</div>
		<div class="modal-footer">
			<?php
				$class = 'class="btn btn-default"';
				
				makeLink("<button $class>IMDB</button>", imdbMakerURL . $imdb_id, 1, 1);
				if(isset($csfd_id))
					makeLink("<button $class>CSFD</button>", csfdMakerURL . $csfd_id, 1, 1);
				if(is_login())
					wrapToTag("upraviť", "button", 1, $class);
			?>
		</div>
	</div>
	<script type="text/javascript">
		$("#moviesList").css("maxHeight", $("#makerAvatar").css("height"));
	</script>
<?php if(!isset($hideFooter))$this -> load -> view('footer_view.html'); ?>