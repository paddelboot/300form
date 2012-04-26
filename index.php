<?php

global $form;

$form = new form();

define( 'FORM_DEBUG', true );

// Objekt konfigurieren
///////////////////////

$form->required = array(
	'form_name', 
	'form_strasse',
	'form_wohnort',
	'form_haustiere',
	'form_janein'
);

$form->pattern = array(
	'form_name' => '!^[a-zA-Z]+$!',
	'form_strasse' => '!^([a-zA-Z])+ (\d{1,4})$!',
	'form_wohnort' => '!^[a-zA-Z]+$!'
);

// Form send?
if ( !empty( $_POST ) )
	$form->process( $_POST );

// Get the processed data
echo 'Processed Data: <pre>' . print_r( $form->processed_data, TRUE ) . '</pre>';

include ( 'template.php' );
?>