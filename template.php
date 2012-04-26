<!-- Example template for 300form -->

<form action="" method="post">

	<!-- Input text field -->
	<p>
		<label for="form_name">Name *:</label>
		<input type="text" name="form_name" placeholder="Dein Name" value="<?php $form->data( 'form_name' ); ?>" />
		&nbsp;
		<span style="color:red"><?php $form->hint( 'form_name' ); ?></span>
	</p>
	<p>
		<label for="form_strasse">Strasse *:</label>
		<input type="text" name="form_strasse" placeholder="Deine Strasse" value="<?php $form->data( 'form_strasse' ); ?>" />
		&nbsp;
		<span style="color:red"><?php $form->hint( 'form_strasse' ); ?></span>
	</p>
	
	<!-- Multiple select field -->
	<p>
		<label for="form_wohnort">Wohnort *:</label>
		<select name="form_wohnort[]" multiple="multiple" >
			<option value="biel" <?php selected( 'biel', $form->get_data( 'biel' ) ); ?> >Biel</option>
			<option value="zurich18" <?php selected( 'zurich18', $form->data( 'zurich18' ) ); ?> >Zürich</option>
		</select>

		<span style="color:red"><?php $form->hint( 'form_wohnort' ); ?></span>
	</p>
	
	<!-- Checkboxes -->
	<p>
		<input type="checkbox" name="form_haustiere[]" value="hund" <?php checked( 'hund', $form->get_data( 'hund' ) ); ?> /> Hund
		<input type="checkbox" name="form_haustiere[]" value="katze" <?php checked( 'katze', $form->get_data( 'katze' ) ); ?> /> Katze
		<span style="color:red"><?php $form->hint( 'form_haustiere' ); ?></span>
	</p>
		
	<!-- Radio buttons -->
	<p>
		<input type="radio" name="form_janein" value="ja" <?php checked( 'ja', $form->get_data( 'form_janein' ) ); ?> /> Ja
		<input type="radio" name="form_janein" value="nein" <?php checked( 'nein', $form->get_data( 'form_janein' ) ); ?> /> Nein
		<span style="color:red"><?php $form->hint( 'form_janein' ); ?></span>
	</p>
	
	<!-- Submit button :P -->
	<p>
		<input type="submit" value="send">
	</p>

</form>
