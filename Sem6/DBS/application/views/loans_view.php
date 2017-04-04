<?php $this -> load -> view('header_view.html'); ?>
	<h3><?= word("loans") ?></h3>
	<a href="<?=loanURL?>add"><?= word("addLoan")?></a> |
		<label class="checkbox-inline">
			<input type="checkbox" onchange="$('.returned').toggleClass('hidden')">zobraziť všetky pôžičky
		</label>
	<table class="table table-striped sortable">
		<thead>
			<tr>
				<?php
					foreach($data as $key => $value)
						if($value)
							wrapToTag($value, "th", 1);
				?>
				<th>Detail</th>
			</tr>
		</thead>
		</tbody>
			<?php
				if($loans)
					foreach($loans as $loan):
						echo "<tr " . ($loan["d_returned"] ? "class='returned hidden'" : "") .">";
						foreach($data as $key => $value):
							if($value)
								if($key == "d_returned")
									wrapToTag($loan[$key] ? $loan[$key] : "No", "td", 1);
								else
									wrapToTag($loan[$key], "td", 1);
						endforeach;
						$attr  = "onclick=loadLoan('" . $loan["loan_id"] . "')";
						$attr .= " data-toggle='modal' data-target='#detailModal' class='btn btn-default'";
						wrapToTag(wrapToTag("detail", "button", 0,  $attr), "td", 1);
						echo "</tr>";
					endforeach;
				else
					wrapToTag(wrapToTag(word("noResults"), "td", 0, "colspan='" . count($data) . "'"), "tr", 1);
			?>
		</tbody>
	</table>
<?php $this -> load -> view('footer_view.html'); ?>