<?php

echo $this->session->flashdata('saved');

echo form_open('profile/save', array('class' => 'cssform', 'id' => 'profile_edit'));

?>


<fieldset>

	<legend accesskey="U" tabindex="<?php tab_index() ?>"><?= lang('user_details') ?></legend>

	<p>
	  <label for="email" class="required">Email</label>
	  <?php
		$email = set_value('email', $user->email, FALSE);
		echo form_input(array(
			'name' => 'email',
			'id' => 'email',
			'size' => '35',
			'maxlength' => '255',
			'tabindex' =>tab_index(),
			'value' => $email,
		));
		?>
	</p>
	<?php echo form_error('email'); ?>


	<p>
	  <label for="password1"><?= lang('password') ?></label>
	  <?php
		echo form_password(array(
			'name' => 'password1',
			'id' => 'password1',
			'size' => '20',
			'tabindex' => tab_index(),
			'value' => '',
		));
		?>
	</p>
	<?php echo form_error('password1'); ?>


	<p>
	  <label for="password2"><?= lang('password_again') ?></label>
	  <?php
		echo form_password(array(
			'name' => 'password2',
			'id' => 'password2',
			'size' => '20',
			'tabindex' => tab_index(),
			'value' => '',
		));
		?>
	</p>
	<?php echo form_error('password2'); ?>


</fieldset>


<fieldset>


	<p>
	  <label for="firstname"><?= lang('first_name') ?></label>
	  <?php
		$firstname = set_value('firstname', $user->firstname, FALSE);
		echo form_input(array(
			'name' => 'firstname',
			'id' => 'firstname',
			'size' => '20',
			'maxlength' => '100',
			'tabindex' => tab_index(),
			'value' => $firstname,
		));
		?>
	</p>
	<?php echo form_error('firstname'); ?>


	<p>
	  <label for="lastname"><?= lang('last_name') ?></label>
	  <?php
		$lastname = set_value('lastname', $user->lastname, FALSE);
		echo form_input(array(
			'name' => 'lastname',
			'id' => 'lastname',
			'size' => '20',
			'maxlength' => '100',
			'tabindex' => tab_index(),
			'value' => $lastname,
		));
		?>
	</p>
	<?php echo form_error('lastname'); ?>


	<p>
	  <label for="displayname"><?= lang('display_name') ?></label>
	  <?php
		$displayname = set_value('displayname', $user->displayname, FALSE);
		echo form_input(array(
			'name' => 'displayname',
			'id' => 'displayname',
			'size' => '20',
			'maxlength' => '100',
			'tabindex' => tab_index(),
			'value' => $displayname,
		));
		?>
	</p>
	<?php echo form_error('displayname'); ?>


	<p>
	  <label for="ext"><?= lang('extension') ?></label>
	  <?php
		$ext = set_value('ext', $user->ext, FALSE);
		echo form_input(array(
			'name' => 'ext',
			'id' => 'ext',
			'size' => '10',
			'maxlength' => '10',
			'tabindex' => tab_index(),
			'value' => $ext,
		));
		?>
	</p>
	<?php echo form_error('ext'); ?>


</fieldset>


<?php
$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
));

echo form_close();
