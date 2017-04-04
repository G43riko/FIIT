<?php if(!isset($hideHeader))$this -> load -> view('header_view.html'); ?>
	<h3><?= word("overview") ?></h3>
	<div class="row">
		<div class="col-sm-3" >
			<h5><?= word("counts") ?></h5>
			<div class="well gScrollable">
				<table class="table table-striped">
					<?php
						foreach($data["number"]["body"] as $key => $val)
							wrapToTag(wrapToTag($key, "td") . wrapToTag($val, "td"), "tr", 1);
					?>
				</table>
			</div>
		</div>
		<div class="col-sm-3">
			<h5><?= word("best") ?></h5>
			<div class="well gScrollable">
				<table class="table table-striped">
			<?php
				showSimpleMovies($data["movies"]["best"], "rating", movieDetailURL);
			?>
				</table>
			</div>
		</div>
		<div class="col-sm-3">
			<h5><?= word("longest") ?></h5>
			<div class="well gScrollable">
				<table class="table table-striped">
			<?php
				showSimpleMovies($data["movies"]["longest"], "length", movieDetailURL, " min");
			?>
				</table>
			</div>
		</div>
		<div class="col-sm-3">
			<h5><?= word("newest") ?></h5>
			<div class="well gScrollable">
				<table class="table table-striped">
			<?php
				showSimpleMovies($data["movies"]["newest"], "d_created", movieDetailURL);
			?>
				</table>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-3">
			<h5><?= word("genres") ?></h5>
			<div class="well gScrollable">
				<table class="table table-striped">
					<thead>
						<tr>
							<?php
								foreach($data["genres"]["head"] as $genre)
									wrapToTag($genre, "td", 1);
							?>
						</tr>
					</thead>
					<tbody>
			<?php
				foreach($data["genres"]["body"] as $genre):
					$d = makeLink($genre["name"], genreURL . $genre["name"]);
					wrapToTag(wrapToTag($d, "td") . wrapToTag($genre["num"], "td"), "tr", 1);
				endforeach;
			?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-sm-3">
			<h5><?= word("years") ?></h5>
			<div class="well gScrollable">
				<table class="table table-striped">
					<thead>
						<tr>
							<?php
								foreach($data["years"]["head"] as $year)
									wrapToTag($year, "td", 1);
							?>
						</tr>
					</thead>
					<tbody>
				<?php
					foreach($data["years"]["body"] as $year):
						$d = makeLink($year["name"], yearURL . $year["name"]);
						wrapToTag(wrapToTag($d, "td") . wrapToTag($year["num"], "td"), "tr", 1);
					endforeach;
				?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-sm-3">
			<h5><?= word("countries") ?></h5>
			<div class="well gScrollable">
				<table class="table table-striped">
					<thead>
						<tr>
							<?php
								foreach($data["countries"]["head"] as $country)
									wrapToTag($country, "td", 1);
							?>
						</tr>
					</thead>
					<tbody>
				<?php
					foreach($data["countries"]["body"] as $country):
						$d = makeLink($country["name"], countryURL . $country["name"]);
						wrapToTag(wrapToTag($d, "td") . wrapToTag($country["num"], "td"), "tr", 1);
					endforeach;
				?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-sm-3">
			<h5><?= word("tags") ?></h5>
			<div class="well gScrollable">
				<table class="table table-striped">
					<thead>
						<tr>
							<?php
								foreach($data["tags"]["head"] as $tag)
									wrapToTag($tag, "td", 1);
							?>
						</tr>
					</thead>
					<tbody>
				<?php
					foreach($data["tags"]["body"] as $tag):
						$d = makeLink($tag["name"], tagURL . $tag["name"]);
						wrapToTag(wrapToTag($d, "td") . wrapToTag($tag["num"], "td"), "tr", 1);
					endforeach;
				?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div id="row">	
		<div class="col-sm-4">
			<h5><?= word("actors") ?></h5>
			<div class="well gScrollable">
				<table class="table table-striped">
					<thead>
						<tr>
							<?php
								foreach($data["actors"]["head"] as $maker)
									wrapToTag($maker, "td", 1);
							?>
						</tr>
					</thead>
					<tbody>
				<?php
					foreach($data["actors"]["body"] as $maker){
						$url = makerDetailURL . $maker["maker_id"];
						$link = makeLink(checkStringLength($maker["name"], 20), $url);
						wrapToTag(wrapToTag($link, "td") . wrapToTag($maker["num"], "td"), "tr", 1);
					}
				?>
					</tbody>
				</table>

			</div>
		</div>
		<div class="col-sm-4">
			<h5><?= word("directors") ?></h5>
			<div class="well gScrollable">
				<table class="table table-striped">
					<thead>
						<tr>
							<?php
								foreach($data["directors"]["head"] as $maker)
									wrapToTag($maker, "td", 1);
							?>
						</tr>
					</thead>
					<tbody>
				<?php
					foreach($data["directors"]["body"] as $maker){
						$url = makerDetailURL . $maker["maker_id"];
						$link = makeLink(checkStringLength($maker["name"], 20), $url);
						wrapToTag(wrapToTag($link, "td") . wrapToTag($maker["num"], "td"), "tr", 1);
					}
				?>
					</tbody>
				</table>

			</div>
		</div>
	</div>
<?php if(!isset($hideFooter))$this -> load -> view('footer_view.html'); ?>