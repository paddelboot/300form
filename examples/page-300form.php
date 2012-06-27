<?php

/**
 * Example page for the 300form validation class.
 * 
 * Copy this to your theme's main folder and select
 * it as page template in your page editor.
 * 
 * 
 * What are dynamic forms?
 * 
 * By "dynamic forms" I mean forms
 * that can contain a different number of form fields by each form call. 
 * Let's say you have a cart page that includes some products, and
 * these products are displayed within an order form. Since you
 * do not know the number of products in advance (and therefore don't know 
 * the exact number of fields your form will have), you will
 * need to set the "required" and "pattern" rules in the way described below.
 * 
 * ** IMPORTANT** 
 * All dynamicaly generated fields names must start with 'dyn-', i.e. 'dyn-name-13'
 * 
 * 
 * Patterns:
 * 
 * These require the use of regular expressions. If you don't know
 * how to write these, you can still help yourself with a 
 * regex library, such as http://regexlib.com
 * 
 * 
 * Ask questions: Michael Schröder <ms@ts-webdesign.net> 
 * 
 * Template Name: 300form-example
 */

get_header();

?>
<div id="content">
<?php

// Get instance of form object
$form = new form();


/* Step 1: Set field rules */

// Set required fields
$form->required = array(
	'form_name', 
	'form_street',
	'form_place',
	'form_pets',
	'form_yesno'
);

// Set regex patterns to match fields against
$form->pattern = array(
	'form_name' => '!^[a-zA-Z]+$!',
	'form_street' => '!^([a-zA-Z])+( )?(\d{1,4}[a-zA-Z])?$!',
	'form_place' => '!^[a-zA-Z]+$!'
);



/* Step 2 (optional): Create some dynamic fields (i.e. shopping cart) */

// Set dynamicaly generated fields to be required
for ( $i = 0; $i <= 2; $i++ ) {
	array_push( $form->required, 'dyn-form_name-' . $i );
}

// Set patterns for dynamicaly generated fields
$pattern_dyn = array();
for ( $i = 0; $i <= 2; $i++ ) {
	$pattern_dyn[ 'dyn-form_name-' . $i ] = '!^[a-zA-Z]+$!';
}
$form->pattern = array_merge( $form->pattern, $pattern_dyn );



/* Step 3: Process user input */

// Form sent?
if ( ! empty( $_POST ) )
	$form->process( $_POST );

// Get the processed data
if ( isset( $form->processed_data ) )
	echo 'Processed Data: <pre>' . print_r( $form->processed_data, TRUE ) . '</pre>';
// Include your form template or your form building function here.
else
	include ( PLUGINDIR . '/300form/examples/template.php' );
 
?>
</div>
<?php

get_footer();
?>