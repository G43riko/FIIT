<?php if(!isset($hideHeader))$this -> load -> view('header_view.html'); ?>
	<div class="row">
		<div class="modal-header">
			<h1>
				<?= $first_name . " " . $second_name . (is_null($d_birthday) ? "" : " (" . $d_birthday . ")")?>
			</h1>
		</div>
		<div class="modal-body">
			<table>
				<tr>
					<td><h3>Požičané dňa: </h3></td>
					<td>
						<h4 style="margin-top:24px; margin-left: 10px">
							<?= $d_created ?>
						</h4>
					</td>
				</tr>
				<tr>
					<td><h3>Vrátené dňa: </h3></td>
					<td>
						<h4 style="margin-top:24px; margin-left: 10px">
							<?= is_null($d_returned) ? "no" : $d_returned ?>
						</h4>
					</td>
				</tr>
				<tr>
					<td><h3>Filmy: </h3></td>
				</tr>
				<tr>
					<td></td><td><?= prepareData($movies, movieDetailURL, NULL, "</td></tr><tr><td></td><td>")?></td>
				</tr>
			</table>
			
		</div>
		<div class="modal-footer">
			<form method="get" action="<?= loanURL . "finish/$loan_id" ?> ">
			<?php
				$class = 'class="btn btn-default" onclick="window.location=/"';
				wrapToTag("Dokončiť", "button", 1, $class);
			?>
			</form>
		</div>
	</div>
<?php if(!isset($hideFooter))$this -> load -> view('footer_view.html'); ?>