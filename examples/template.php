<!-- Example form template for 300form -->

<form action="" method="post">

	<!-- Input text field -->
	<p>
		<label for="form_name">Name *:</label>
		<input type="text" name="form_name" placeholder="Your Name" value="<?php $form->data( 'form_name' ); ?>" />
		&nbsp;
		<span style="color:red"><?php $form->hint( 'form_name' ); ?></span>
	</p>
	<p>
		<label for="form_street">Street *:</label>
		<input type="text" name="form_street" placeholder="Your street" value="<?php $form->data( 'form_street' ); ?>" />
		&nbsp;
		<span style="color:red"><?php $form->hint( 'form_street' ); ?></span>
	</p>
	
	<!-- Multiple select field -->
	<p>
		<label for="form_place">Place *:</label>
		<select name="form_place[]" multiple="multiple" >
			<option value="berlin" <?php selected( 'berlin', $form->get_data( 'berlin' ) ); ?> >Berlin</option>
			<option value="madrid" <?php selected( 'madrid', $form->get_data( 'madrid' ) ); ?> >Madrid</option>
		</select>

		<span style="color:red"><?php $form->hint( 'form_place' ); ?></span>
	</p>
	
	<!-- Checkboxes -->
	<p>
		<input type="checkbox" name="form_pets[]" value="dog" <?php checked( 'dog', $form->get_data( 'dog' ) ); ?> /> Dog
		<input type="checkbox" name="form_pets[]" value="cat" <?php checked( 'cat', $form->get_data( 'cat' ) ); ?> /> Cat
		<span style="color:red"><?php $form->hint( 'form_pets' ); ?></span>
	</p>
		
	<!-- Radio buttons -->
	<p>
		<input type="radio" name="form_yesno" value="yes" <?php checked( 'yes', $form->get_data( 'form_yesno' ) ); ?> /> Yes
		<input type="radio" name="form_yesno" value="no" <?php checked( 'no', $form->get_data( 'form_yesno' ) ); ?> /> No
		<span style="color:red"><?php $form->hint( 'form_yesno' ); ?></span>
	</p>
	
	<!-- Dynamicaly generated form fields. ** Important: The field name must start with 'dyn-' ** -->
	<?php 
	for ( $i = 0; $i <= 2; $i++ ) {
	?>

	<p>
		<label for="dyn-form_name-<?php echo $i; ?>">Name-<?php echo $i; ?> *:</label>
		<input type="text" name="dyn-form_name-<?php echo $i; ?>" placeholder="Dynamic field <?php echo $i; ?>" value="<?php $form->data( 'dyn-form_name-' . $i ); ?>" />
		&nbsp;
		<span style="color:red"><?php $form->hint( 'dyn-form_name-' . $i ); ?></span>
	</p>
	<?php
	}
	?>
	
	<!-- Submit button :P -->
	<p>
		<input type="submit" value="send">
	</p>

</form>
