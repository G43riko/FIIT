<?php $this -> load -> view('header_view.html'); ?>
		<h1>Registrácia</h1>
		<?php
			echo validation_errors();
			echo form_open("auth/register/" . $page, array('role' => 'form', "class" => "form"));

			drawInputField("meno"		, "meno_id"			, "Meno");
			drawInputField("priezvisko"	, "priezvisko_id"	, "Priezvisko");
			drawInputField("email"		, "email_id"		, "Email");
			//drawInputField("date"		, "date_id"			, "Date", "date");
			drawInputField("heslo"		, "heslo_id"		, "Heslo", "password");

			echo form_submit("submit", "pridaj", array("class" => "btn btn-default"));

			$attr = 'type="button" class="btn btn-default" onclick="window.location=\'' . loginURL . '\' "';
			wrapToTag("Prihlásiť sa", "button", TRUE, $attr);
			echo form_close();
		?>
<?php $this -> load -> view('footer_view.html'); ?>