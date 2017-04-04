<?php $this -> load -> view('header_view.html'); ?>
	<h3><?= $title ?></h3>	
	<table class="table table-striped sortable">
		<thead>
			<tr>
				<?php
					foreach($columns as $key => $value):
						if($value)
							wrapToTag($value, "th", 1);
					endforeach; 
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($data as $values):
				echo "<tr>";
				foreach($columns as $key => $value):
					if($value):
						if($key == "name")
							wrapToTag(makeLink($values[$key], $path . $values[$key]), "td", 1);
						else if($key == "year")
							wrapToTag(makeLink($values[$key], yearURL . $values[$key]), "td", 1);
						else if($key == "edit" && isset($values["name"]) && is_login())
							wrapToTag(makeLink(word("edit"), othersEditURL . "$type/" . $values[$type . "_id"]), "td", 1);
						else
							wrapToTag($values[$key], "td", 1);
					endif;
				endforeach;
				echo "</tr>";
			endforeach; 
		?>
		</tbody>
	</table>
<?php $this -> load -> view('footer_view.html'); ?>