<?php
/**
 * Plugin Name: 300form
 * Description: Simple form validation class
 * Version: 0.1a
 * Author: Michael Schröder <ms@ts-webdesign.net>
 * TextDomain: 300form
 * DomainPath: /l10n
 *
 * @author Michael Schröder
 * 
 * 
 * @TODO:
 * 
 * 
 * Changelog:
 * 
 */
class form {

	/**
	 * Contains obligatory fields
	 * @var array 
	 */
	public $obligatory;

	/**
	 * Contains regular expressions to match user input against
	 * @var type 
	 */
	public $pattern;

	/**
	 * Form field alert
	 * 
	 */
	public $hint = array( );

	/**
	 * The processed data
	 * 
	 * @var type 
	 */
	public $processed_data;

	/**
	 * Process form
	 * 
	 * @param type $data
	 * @return boolean 
	 */
	function process ( $data ) {

		// Nothing to do?
		if ( $data == FALSE )
			return FALSE;

		$this->debug( "REQUEST-Daten: <pre>" . print_r( $data, TRUE ) . "</pre>" );

		// Daten pruefen 
		// 1. obligatory fields
		// 2. Bestimmte pattern anwenden
		if ( TRUE !== $this->obligatory( $data ) && TRUE !== $this->pattern( $data ) )
			return FALSE;

		//Besondere Operationen (fuer
		//Checkboxen, Radiobuttons etc.
		# muss noch programmiert werden :/ #

		$this->processed_data = $data;
		return TRUE;
	}

	/**
	 * Check input for obligatory fields
	 * 
	 * @param type $data
	 * @return boolean 
	 */
	function obligatory( $data ) {

		$this->debug( "Checking obligatory fields" );

		if ( !ISSET( $this->needed ) || $data == FALSE )
			return TRUE;

		foreach ( $this->needed AS $input_name ) {

			if ( empty( $data[ $input_name ] ) ) {

				$this->hint[ $input_name ] = "Bitte ausfüllen!";
				$incomplete = TRUE;
			}
		}

		if ( ISSET( $incomplete ) ) {

			$this->debug( "Formular nicht komplett ausgefüllt!" );

			$this->request_to_form( $data );
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Check user input
	 * 
	 * @param type $data
	 * @return boolean 
	 */
	function pattern ( $data ) {

		if ( !ISSET( $this->pattern ) || $data == FALSE )
			return TRUE;

		$this->debug( "Matching patterns" );

		foreach ( $this->pattern AS $input_name => $regex ) {

			// REQUEST array
			if ( !empty( $data[ $input_name ] ) && is_array( $data[ $input_name ] ) ) {

				foreach ( $data[ $input_name ] as $key => $value ) {
					// Obligatory with non-matching pattern
					if ( array_key_exists( $input_name, $data ) && // is there a pattern?
							! preg_match( $regex, stripslashes( $value ) ) && // is the pattern matching?
							TRUE == in_array( $input_name, $this->needed ) ) { // is this obligatory?
						if ( empty( $this->hint[ $input_name ] ) ) : $this->hint[ $input_name ] = "Bitte Eingaben überprüfen!";
						endif;
					}

					// Non-obligatory, but an input must always match it's pattern
					if ( ! in_array( $input_name, $this->needed ) && // non-obligatory?
							! empty( $data[ $input_name ] ) && // but there is an input?
							! preg_match( $regex, stripslashes( $value ) ) ) { // not matching?
						if ( empty( $this->hint[ $input_name ] ) ) : $this->hint[ $input_name ] = "Bitte Eingaben überprüfen!";
						endif;
					}
				}
			}
			else {

				// Obligatory with non-matching pattern
				if ( array_key_exists( $input_name, $data ) && // existiert ein Pr�fmuster?
						FALSE == preg_match( $regex, stripslashes( $data[ $input_name ] ) ) && // passt das Pr�fmuster?
						TRUE == in_array( $input_name, $this->needed ) ) { // ist die Eingabe oblig.?
					if ( empty( $this->hint[ $input_name ] ) ) : $this->hint[ $input_name ] = "Bitte Eingaben überprüfen!";
					endif;
				}

				// Non-obligatory, but an input must always match it's pattern
				if ( in_array( $input_name, $this->needed ) == FALSE && // Eingabe nicht oblig. ?
						$data[ $input_name ] != '' && // Wurde dennoch etwas eingegeben?
						preg_match( $regex, stripslashes( $data[ $input_name ] ) ) == FALSE ) {// und passt das Pr�fmuster nicht?
					if ( empty( $this->hint[ $input_name ] ) ) : $this->hint[ $input_name ] = "Bitte Eingaben überprüfen!";
					endif;
				}
			}
		}

		// Any alerts?
		if ( !empty( $this->hint ) ) {
			$this->request_to_form( $data );
			return FALSE;
		}

		return TRUE;
	}
	
	private function run_check( ) {
		
		
	}

	/**
	 * Load REQUEST data into class var
	 * 
	 * @since 0.1a
	 */
	function request_to_form ( $data ) {

		foreach ( $data AS $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $key => $val )
					$this->form_data[ $val ] = $val;
				continue;
			}
			$this->form_data[ $key ] = $value;
		}

		//$this->debug( "Form Data: <pre>" . print_r( $this->form_data, TRUE ) . "</pre>" );

		return TRUE;
	}

	public function get_data ( $string, $default = FALSE ) {

		$output = '';

		if ( !is_string( $string ) )
			return $output;

		if ( !empty( $this->form_data[ $string ] ) )
			$output = $this->form_data[ $string ];
		else
			$output = ( FALSE == $default ) ? '' : $default;

		return $output;
	}

	public function data ( $string, $default = FALSE ) {

		echo $this->get_data( $string, $default );
	}

	/**
	 * Get this form element's hint. Displays
	 * a default hint if none is provided.
	 * 
	 * @param type $field
	 * @param type $hint
	 * @return string 
	 */
	public function get_hint ( $field, $hint = FALSE ) {

		$field = ( string ) $field;

		if ( empty( $this->hint[ $field ] ) )
			return '';
		else
			return ( FALSE == $hint ) ? $this->hint[ $field ] : $hint;
	}

	/**
	 * Display form element's hint.
	 * 
	 * @param type $field
	 * @param type $hint 
	 */
	public function hint ( $field, $hint = FALSE ) {

		echo $this->get_hint( $field, $hint );
	}

	function debug ( $nachricht ) {
		if ( FORM_DEBUG == TRUE ) {
			echo "<p style='color:orange'>" . $nachricht . "</p>";
		}
	}

	function hsc ( $daten ) {
		// htmlspecialchars anwenden
		return strip_tags( htmlspecialchars( stripslashes( $daten ), $quote_style = ENT_QUOTES ) );
	}

	function p ( $array, $text = FALSE ) {

		echo "<span style='color:black'><b>{$text}</b></span>" . "<pre>" . print_r( $array, TRUE ) . "</pre>";
	}

	// class Ende
}

?>