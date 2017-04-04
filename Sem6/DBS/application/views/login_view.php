<?php if(!isset($hideHeader))$this -> load -> view('header_view.html'); ?>
		<h1>Prihlásenie</h1>
		<?php
			echo validation_errors();
			echo form_open("auth/login/" . $page , array('role' => 'form', "class" => "form"));

			drawInputField(strtolower(word("email")), "email_id",  word("email"));
			drawInputField(strtolower(word("pass")), "heslo_id", word("pass"), "password");
			echo form_submit("submit", word("login"), array("class" => "btn btn-default"));

			
			$attr = 'type="button" class="btn btn-default" onclick="window.location=\'' . regURL . '\' "';
			wrapToTag(word("register"), "button", TRUE, $attr);
			
			echo form_close();
		?>
<?php if(!isset($hideFooter))$this -> load -> view('footer_view.html'); ?>