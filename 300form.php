<?php
/**
 * Plugin Name: 300form
 * Description: Simple form validation class
 * Version: 0.2a
 * Author: Michael Schröder <ms@ts-webdesign.net>
 * TextDomain: 300form
 * DomainPath: /l10n
 * 
 */
class form {

	/**
	 * Contains obligatory fields.
	 * @var array 
	 */
	public $required;

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
	public $hint;

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
		return $plugin_data[ $value ];
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

		// Validate user input
		// 1. obligatory fields
		// 2. match patterns
		$oblig = $this->obligatory( $data );
		
		$pattern = $this->pattern( $data );
		
		if ( FALSE === $oblig || FALSE === $pattern ) {
			
			$this->request_to_form( $data );
			return FALSE;
		}

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

		if ( ! isset( $this->required ) || FALSE == $data )
			return TRUE;

		foreach ( $this->required AS $input_name ) {
			if ( empty( $data[ $input_name ] ) ) {
				$this->hint[ $input_name ] = __( 'This field is required!', $this->textdomain );
				$incomplete = TRUE;
			}
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

		foreach ( $this->pattern AS $input_name => $regex ) {
			// REQUEST array
			if ( !empty( $data[ $input_name ] ) && is_array( $data[ $input_name ] ) ) {
				foreach ( $data[ $input_name ] as $key => $value ) {
					// Obligatory with non-matching pattern
					if ( ( array_key_exists( $input_name, $data ) ) && // is there a pattern?
							! preg_match( $regex, stripslashes( $value ) ) && // is the pattern matching?
							in_array( $input_name, $this->required ) ) { // is this obligatory?
						
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
						! preg_match( $regex, stripslashes( $data[ $input_name ] ) ) && // is the pattern matching?
						in_array( $input_name, $this->required ) ) { // is this obligatory?

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

		if ( ! empty( $this->hint ) )
			return FALSE;

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

		foreach ( $data AS $field_key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $key => $val ) {
					// Is this a dynamic field?
					if ( 0 === strpos( $field_key, "dyn-" ) )
						$this->form_data[ $key ] = $val;
					else
						$this->form_data[ $val ] = $val;
				}
			}
			else 
				$this->form_data[ $field_key ] = $value;
		}
		
		return TRUE;
	}

	/**
	 * Return a form field value
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
	 * Print  form element's hint.
	 * 
	 * @access public
	 * @param string $field | the field name
	 * @param string $hint | optional field hint
	 * @since 0.1a
	 */
	public function hint ( $field, $hint = FALSE ) {

		echo $this->get_hint( $field, $hint );
	}
}
?>