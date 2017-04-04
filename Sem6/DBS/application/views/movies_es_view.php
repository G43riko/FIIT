<?php if(!isset($hideHeader))$this -> load -> view('header_view.html'); ?>
	<script>
		$(function() {
			$("#slider-range-year").slider({
				range: true,
				min: <?= $min_year?>,
				max: <?= $max_year?>,
				step: 1,
				values: [ <?= $min_year?>, <?= $max_year?> ],
				change: updateResults,
				slide: updateResults,
				slide: function( event, ui ) {
					$("#yearAmount").text( ui.values[ 0 ] + " - " + ui.values[ 1 ] );	
				}
			});

			$("#slider-range-length").slider({
				range: true,
				min: <?= $min_length?>,
				max: <?= $max_length?>,
				step: 1,
				change: updateResults,
				slide: updateResults,
				values: [ <?= $min_length?>, <?= $max_length?> ],
				slide: function( event, ui ) {
					$("#lengthAmount").text( ui.values[ 0 ] + " - " + ui.values[ 1 ] );	
				}
			});

			$("#slider-range-rating").slider({
				range: true,
				min: <?= $min_rating?>,
				max: <?= $max_rating?>,
				step: 0.1,
				change: updateResults,
				slide: updateResults,
				values: [ <?= $min_rating?>, <?= $max_rating?> ],
				slide: function( event, ui ) {
					$("#ratingAmount").text( ui.values[ 0 ] + " - " + ui.values[ 1 ] );	
				}
			});
		updateResults();
		});
	</script>
	<h3>Pokročilé hladanie</h3>

	<label for="input-title">Klúč: </label>
	<input id="input-title" type="text" onkeydown="if(event.keyCode == 13)updateResults()"> 
	 | hladať v:
	<label class="checkbox-inline">
		<input type="checkbox" checked id="es_title" onchange="updateResults()">názvoch
	</label>
	<label class="checkbox-inline">
		<input type="checkbox" id="es_tag" onchange="updateResults()">tagoch
	</label>
	<label class="checkbox-inline">
		<input type="checkbox" id="es_actor" onchange="updateResults()">hercoch
	</label>
	<label class="checkbox-inline">
		<input type="checkbox" id="es_country" onchange="updateResults()">krajnách
	</label>
	<label class="checkbox-inline">
		<input type="checkbox" id="es_genre" onchange="updateResults()">zanroch
	</label>
	 | 
	<label class="checkbox-inline">
		<input type="checkbox" id="es_prefix" checked onchange="updateResults()">iba prefix
	</label>
	<div style="padding: 10px;">
		<div id="yearSelection">
			<p>
				<b>Years range:</b>
				<span id="yearAmount" style="border:0; color:#f6931f; font-weight:bold;">
				<?= $min_year . " - " . $max_year?>
				</span>
			</p>
			<div id="slider-range-year"></div>
		</div>
		<div id="ratingSelection">
			<p>
				<b>Rating range:</b>
				<span id="ratingAmount" style="border:0; color:#f6931f; font-weight:bold;">
				<?= $min_rating . " - " . $max_rating?>
				</span>
			</p>
			<div id="slider-range-rating"></div>
		</div>
		<div id="lengthSelection">
			<p>
				<b>Length range:</b>
				<span id="lengthAmount" style="border:0; color:#f6931f; font-weight:bold;">
				<?= $min_length . " - " . $max_length?>
				</span>
			</p>
			<div id="slider-range-length"></div>
		</div>
		<div id="search_results" style="margin-top: 20px">
			<span id="results_num"></span>
			<table class="table table-striped sortable">
			</table>
		</div>
	</div>
<?php if(!isset($hideFooter))$this -> load -> view('footer_view.html'); ?>