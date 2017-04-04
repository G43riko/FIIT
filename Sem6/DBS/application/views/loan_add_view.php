<?php 
	$this -> load -> view('header_view.html');
	if(!is_login())
		redirect("login/loans_add");
?>

	<script type="text/javascript">

	window.onload = function(){
		loadMoviesFromBasket();
	}

	</script>
	<h3><?= "Nová pôžička" ?></h3>
	<?= form_open("", array('role' => 'form', "class" => "form", "autocomplete" => "off"))?>
	<div>
		<div class="form-group">
			<label for="frst_name"><?= word("firstName") ?>:</label>
			<input type="text" class="form-control" disabled id="frst_name" value="<?= getSession('first_name')?>">
		</div>
		<div class="form-group">
			<label for="scnd_name"><?= word("secondName") ?>:</label>
			<input type="text" class="form-control" disabled id="scnd_name" value="<?= getSession('second_name')?>">
		</div>
		<div class="form-group">
			<label for="email"><?= word("email") ?>:</label>
			<input type="text" class="form-control" disabled id="email" value="<?= getSession('email')?>">
		</div>
		<div class="form-group">
			<label for="date">Požičať od:</label>
			<input type="date" class="form-control" id="date" value="<?= date("Y-m-d") ?>">
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6 well" id="movies_list">
			<ul class='list-group'>
				<li class='list-group-item'>
					<span style="margin-left: 40px;"><?= word("totalPrice")?>: <span id="price">0</span>€<span>
					<span style="margin-left: 40px;"><?= word("numOfMovies")?>: <span id="number">0</span><span>
					<input type="button" class="btn btn-default" value="<?= word('clearBasket')?>" onclick="clearMovies($(this).parent().parent().parent().parent().parent())">
				</li>
			</ul>
		</div>
		<div class="col-sm-6">
			<input type="text" name="movies" class="form-control" id="movies_id" placeholder="<?= word('enterMovies')?>" onkeyup="getMovies(this.value)">
			<div id="moviesHints"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<input type="button" onclick="makeLoan()" value="<?= word("finishLoan") ?>" class="btn btn-default">
		</div>
	</div>
	<?php
			echo form_close();
		
	?>	
<?php $this -> load -> view('footer_view.html'); ?>