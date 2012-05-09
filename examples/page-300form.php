<?php

/**
 * Example page for the 300form validation class.
 * 
 * 
 * What are dynamicaly generated fields?
 * 
 * By "dynamicaly generated fields" I mean form fields
 * that can vary in number by each form call. Let's say you
 * have a cart page that includes some products, and
 * these products are displayed within an order form. Since you
 * do not know the number of products in advance (and therefore don't know 
 * the exact number of field your form will have), you will
 * need to set the "required" and "pattern" rules in the way described below.
 * 
 * 
 * ask questions: Michael Schröder <ms@ts-webdesign.net> 
 * 
 * Template Name: 300form-example
 */

get_header();

// Get instance of form object
$form = new form();

// Set required fields
$form->required = array(
	'form_name', 
	'form_street',
	'form_place',
	'form_pets',
	'form_yesno'
);

// Set required dynamicaly generated fields
for ( $i = 0; $i <= 2; $i++ ) {
	array_push( $form->required, 'dyn-form_name-' . $i );
}

// Set patterns to match against
$form->pattern = array(
	'form_name' => '!^[a-zA-Z]+$!',
	'form_street' => '!^([a-zA-Z])+ (\d){1,4}$!',
	'form_place' => '!^[a-zA-Z]+$!'
);

// Set patterns for dynamicaly generated fields
$pattern_dyn = array();
for ( $i = 0; $i <= 2; $i++ ) {
	$pattern_dyn[ 'dyn-form_name-' . $i ] = '!^[a-zA-Z]+$!';
}
$form->pattern = array_merge( $form->pattern, $pattern_dyn );

// Form sent?
if ( ! empty( $_POST ) )
	$form->process( $_POST );

// Get the processed data
if ( isset( $form->processed_data ) )
	echo 'Processed Data: <pre>' . print_r( $form->processed_data, TRUE ) . '</pre>';

// Include your form template or your form building function here. 
// You could skip this if the processed data was set.
include ( PLUGINDIR . '/300form/examples/template.php' );

get_footer();
?>