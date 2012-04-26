<?php

/**
 * Plugin Name: 300form
 * Description: Simple form validation class
 * Version: 0.1a
 * Author: Michael Schröder <ms@ts-webdesign.net>
 * TextDomain: 300form
 * DomainPath: /l10n
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
	 * Contains obligatory fields.
	 * @var array 
	 */
	public $needed;

	/**
	 * Contains regular expressions to match user input against.
	 * @var array
	 */
	public $pattern;

	/**
	 * Form field alerts.
	 * 
	 * @var array
	 */
	public $hint = array( );

	/**
	 * The processed data.
	 * 
	 * @var array
	 */
	public $processed_data;

	/**
	 * The textdomain string
	 * 
	 * @var string 
	 */
	private $textdomain;

	/**
	 * Get a value of the plugin header
	 *
	 * @access private
	 * @uses get_plugin_data, ABSPATH
	 * @param string $value
	 * @return string The plugin header value
	 * @since 0.1a
	 */
	private function get_plugin_header ( $value = 'TextDomain' ) {
		
		if ( !function_exists( 'get_plugin_data' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		$plugin_data = get_plugin_data( __FILE__ );
		$plugin_value = $plugin_data[ $value ];

		return $plugin_value;
	}

	/**
	 * Get the Textdomain
	 *
	 * @access private
	 * @return string | The plugins' textdomain
	 * @since 0.1a
	 */
	private function get_textdomain () {

		if ( empty( $this->textdomain ) )
			$this->textdomain = $this->get_plugin_header( 'TextDomain' );
		
		return $this->textdomain;
	}

	/**
	 * Process form
	 * 
	 * @access public
	 * @param array $data | Form data to validate
	 * @return TRUE | All checks passed
	 * @return FALSE | checks not passed
	 * @since 0.1a
	 */
	public function process ( $data ) {

		// Nothing to do?
		if ( FALSE == $data )
			return FALSE;

		$this->debug( 'REQUEST-Daten: ' . $this->p( $data ) );

		// Validate user input
		// 1. obligatory fields
		// 2. match patterns
		if ( TRUE !== $this->obligatory( $data ) && TRUE !== $this->pattern( $data ) )
			return FALSE;

		
		// If all checks are passed successfully,
		// we will make the data accessible:
		$this->processed_data = $data;
		
		return TRUE;
	}

	/**
	 * Check input for obligatory fields
	 * 
	 * @access private
	 * @param array $data | the REQUEST data
	 * @return TRUE | all required fields are filled out
	 * @return FALSE | a required field is not filled out
	 * @since 0.1a
	 */
	private function obligatory ( $data ) {

		$this->debug( 'Checking obligatory fields...' );

		if ( ! ISSET( $this->required ) || FALSE == $data )
			return TRUE;

		foreach ( $this->required AS $input_name ) {

			if ( empty( $data[ $input_name ] ) ) {

				$this->hint[ $input_name ] = __( 'This field is required!', $this->textdomain );
				$incomplete = TRUE;
			}
		}

		if ( ISSET( $incomplete ) ) {

			$this->debug( "Form not completed!" );

			$this->request_to_form( $data );
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Check user input
	 * 
	 * @access private
	 * @param array $data | the REQUEST data
	 * @return TRUE | all fields match their pattern
	 * @return FALSE | a field does not match it's pattern
	 * @since 0.1a
	 */
	private function pattern ( $data ) {

		if ( empty( $this->pattern ) || FALSE == $data )
			return TRUE;

		$this->debug( 'Matching patterns...' );

		foreach ( $this->pattern AS $input_name => $regex ) {

			// REQUEST array
			if ( !empty( $data[ $input_name ] ) && is_array( $data[ $input_name ] ) ) {

				foreach ( $data[ $input_name ] as $key => $value ) {
					// Obligatory with non-matching pattern
					if ( array_key_exists( $input_name, $data ) && // is there a pattern?
							! preg_match( $regex, stripslashes( $value ) ) && // is the pattern matching?
							TRUE == in_array( $input_name, $this->required ) ) { // is this obligatory?
						
						if ( empty( $this->hint[ $input_name ] ) ) : $this->hint[ $input_name ] = __( 'Check your input!', $this->textdomain );
						endif;
					}

					// Non-obligatory, but an input must always match it's pattern
					if ( ! in_array( $input_name, $this->required ) && // non-obligatory?
							! empty( $data[ $input_name ] ) && // but there is an input?
							! preg_match( $regex, stripslashes( $value ) ) ) { // not matching?
						
						if ( empty( $this->hint[ $input_name ] ) ) : $this->hint[ $input_name ] = __( 'Check your input!', $this->textdomain );
						endif;
					}
				}
			}
			else {

				// Obligatory with non-matching pattern
				if ( array_key_exists( $input_name, $data ) && // is there a pattern?
						FALSE == preg_match( $regex, stripslashes( $data[ $input_name ] ) ) && // is the pattern matching?
						TRUE == in_array( $input_name, $this->required ) ) { // is this obligatory?
					
					if ( empty( $this->hint[ $input_name ] ) ) : $this->hint[ $input_name ] = __( 'Check your input!', $this->textdomain );
					endif;
				}

				// Non-obligatory, but an input must always match it's pattern
				if ( ! in_array( $input_name, $this->required ) && // non-obligatory?
						! empty( $data[ $input_name ] ) && // but there is an input?
						! preg_match( $regex, stripslashes( $data[ $input_name ] ) ) ) {  // not matching?
					
					if ( empty( $this->hint[ $input_name ] ) ) : $this->hint[ $input_name ] = __( 'Check your input!', $this->textdomain );
					endif;
				}
			}
		}

		// Any alerts?
		if ( ! empty( $this->hint ) ) {
			
			// Load data into class var
			$this->request_to_form( $data );
			
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Load REQUEST data into class var for
	 * displaying form field values.
	 * 
	 * @access private
	 * @param $data | REQUEST data
	 * @since 0.1a
	 */
	private function request_to_form ( $data ) {

		foreach ( $data AS $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $key => $val )
					$this->form_data[ $val ] = $val;
				continue;
			}
			$this->form_data[ $key ] = $value;
		}

		return TRUE;
	}

	/**
	 * Return a form fields value
	 *
	 * @access public
	 * @param string $field | the field name
	 * @param string $default | the optional default field value
	 * @return string $output | field value
	 * @since 0.1a
	 */
	public function get_data ( $field, $default = FALSE ) {

		$output = '';

		if ( ! is_string( $field ) )
			return $output;

		if ( ! empty( $this->form_data[ $field ] ) )
			$output = $this->form_data[ $field ];
		else
			$output = ( FALSE == $default ) ? '' : $default;

		return $output;
	}

	/**
	 * Print field value
	 * 
	 * @access public
	 * @param string $field | the field name
	 * @param string $default | the optional default field value
	 * @since 0.1a
	 */
	public function data ( $field, $default = FALSE ) {

		echo $this->get_data( $field, $default );
	}

	/**
	 * Get this form element's hint. Displays
	 * a default hint if none is provided.
	 * 
	 * @access public
	 * @param string $field | the field name
	 * @param string $hint | optional field hint
 	 * @return string | the field hint, i.e. "Please fill out this field!"
	 * @since 0.1a
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
	 * @access public
	 * @param string $field | the field name
	 * @param string $hint | optional field hint
	 * @since 0.1a
	 */
	public function hint ( $field, $hint = FALSE ) {

		echo $this->get_hint( $field, $hint );
	}

	/**
	 * Show some debug info
	 * 
	 * @access private
	 * @param string $nachricht | the message to display
	 * @since 0.1a
	 */
	private function debug ( $nachricht ) {
		if ( TRUE == FORM_DEBUG ) {
			echo '<p style="color:orange">' . $nachricht . '</p>';
		}
	}

	/**
	 * Debug function
	 * 
	 * @access private
	 * @param mixed $array | the var to display
	 * @param string $text | title text 
	 */
	private function p ( $array, $text = FALSE ) {

		echo "<span style='color:black'><b>{$text}</b></span>" . "<pre>" . print_r( $array, TRUE ) . "</pre>";
	}

	// class Ende
}

?>