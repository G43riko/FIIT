<?php $this -> load -> view('header_view.html'); ?>
	<h3><?= "Úprava: $title" ?></h3>
	<form method="post" action=<?="\"" . otherURL . "save\""?> role="form">
		<div class="form-group">
			<label for="id"><?=word("id")?>:</label>
			<input 	type="text" 
					class="form-control" 
					id="id" 
					name="id"
					disabled
					value=<?="\"" . $edit . "\""?>>
		</div>
		<div class="form-group">
			<label for="id"><?=word("created")?>:</label>
			<input 	type="text" 
					class="form-control" 
					id="id" 
					name="id"
					disabled
					value=<?="\"" . $data["d_created"] . "\""?>>
		</div>
		<div class="form-group">
			<label for="name"><?=word("title")?>:</label>
			<input 	type="text" 
					class="form-control" 
					id="name" 
					name="name" 
					value=<?="\"" . $data["name"] . "\""?>>
		</div>
		<div class="form-group">
			<label for="nameSK"><?=word("titleSK")?>:</label>
			<input 	type="text"
					class="form-control"
					id="nameSK"
					name="nameSK"
					value=<?="\"" . $data["name_sk"] . "\""?>>
		</div>
		<input type="hidden" name="type" value=<?="\"" . $title . "\""?>>
		<input type="hidden" name="id" value=<?="\"" . $edit . "\""?>>
		<button type="submit" class="btn btn-default"><?= word("save")?></button>
	</form>
	
<?php $this -> load -> view('footer_view.html'); ?>