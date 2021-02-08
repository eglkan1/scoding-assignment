<?php

/*
 * Plugin Name: Booster for WPForms
 * Plugin URI: https://wpmonks.com/downloads/custom-themes
 * Description: Add missing features to WPForms
 * Author: Sushil Kumar
 * Author URI: https://wpmonks.com
 * Version: 1.1.1
 * License: GPLv2
 */


 // don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );

//set constants for plugin directory and plugin url
define( "BFWPF_DIR", WP_PLUGIN_DIR . "/" . basename( dirname( __FILE__ ) ) );
define( "BFWPF_URL", plugins_url() . "/" . basename( dirname( __FILE__ ) ) );
define( 'BFWPF_STORE_URL', 'https://wpmonks.com' );

if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	include_once BFWPF_DIR . '/admin-menu/EDD_SL_Plugin_Updater.php';
}

require_once BFWPF_DIR . '/admin-menu/licenses.php';


class Booster_For_Wpforms{

	function __construct() {
		
		add_filter( 'wpforms_settings_tabs', array( $this, 'wpforms_settings_tabs')  );

		add_filter( 'wpforms_settings_defaults', array( $this, 'wpforms_settings_defaults')  );

		add_action( "wpforms_field_options_bottom_advanced-options", array( $this, 'field_options' ), 100, 2 );

		add_filter( 'wpforms_field_properties', array( $this, 'wpforms_field_properties'), 100, 3);

		add_filter( 'wpforms_address_schemes', array( $this, 'wpforms_address_schemes' ) );

		// Enable show values option for checkbox and radio
		add_filter( 'wpforms_fields_show_options_setting', array( $this, 'wpforms_fields_show_options_setting' ) );

		// Show remaining entries
		add_action( 'wpforms_frontend_output_before', array( $this, 'show_remaining_entries') );

		// Add new settings under General section.
		add_action( 'wpforms_form_settings_general', array( $this, 'wpforms_form_settings_general' ) );

		// Disable autocomplete
		add_filter( 'wpforms_frontend_form_atts', array( $this, 'disable_form_autocomplete' ), 10, 2 );

		// Add Validatin Content
		add_action( 'wpforms_builder_after_panel_content', array( $this, 'wpforms_builder_after_panel_content'), 20, 2 );

		// Add validation section to array
		add_filter( 'wpforms_builder_settings_sections', array( $this, 'wpforms_builder_settings_sections'), 10, 2 );


		// Customize validation per form
		add_filter( 'wpforms_frontend_strings', array( $this, 'wpforms_frontend_strings' ) );

		// Enqueue Scripts
		add_action( 'wpforms_frontend_js', array( $this, 'wpforms_frontend_js') );

	}

	function wpforms_frontend_js() {
		wp_enqueue_style( 'bfwpf_public', BFWPF_URL.'/css/public.css' );
	}


	/**
	 * Return supported field types for different boosts
	 *
	 * @param [string] $type
	 * @return array
	 */
	function supported_fields( $type ) {
		switch( $type ) {
			case 'bfwpf_readonly': 
				return array( 'text', 'textarea', 'number', 'email' );
				break;
			
			case 'bfwpf_maxchars':
				return array( 'text', 'textarea' );
				break;
			
			case 'bfwpf_minmax':
				return array( 'number' );
				break;

			case 'bfwpf_autocomplete_off':
				return array( 'number', 'email', 'text', 'phone', 'url' );
				break;
		}
	}

	/**
	 * Adds settings under 'General' sidebar pannel of Settings menu in form
	 *
	 * @param [object] $base_object
	 * @return void
	 */
	function wpforms_form_settings_general( $base_object ) {

		$settings = get_option( 'wpforms_settings' );
		
		if ( ! empty( $settings['bfwpf-autocomplete-off'] ) ) { // show option to turn on/off autocomplete
			wpforms_panel_field(
				'checkbox',
				'settings',
				'bfwpf_autocomplete_off',
				$base_object->form_data,
				esc_html__( 'Turn off browser autocomplete for form', 'wpforms-lite' ),
				array(
					'tooltip' => 'Removes the autofill options that appear when user types in the form',
				)
			);
		}

		if ( ! empty( $settings['bfwpf-entry-count'] ) ) { // show option to turn on/off autocomplete
			wpforms_panel_field(
				'checkbox',
				'settings',
				'bfwpf_entry_count',
				$base_object->form_data,
				esc_html__( 'Show Remaining entries ( Form Locker addon ) ', 'wpforms-lite' ),
				array(
					'tooltip' => 'This will show the count of entries. It will only work if you have enabled Form Locker addon. ',
				)
			);
		}
	}

	/**
	 * Adds validation section in sidebar panel of Settings per form
	 *
	 * @param [array] $sections
	 * @param [array] $form_data
	 * @return sections
	 */
	function wpforms_builder_settings_sections( $sections , $form_data ) {
		$settings = get_option( 'wpforms_settings' );
		
		if ( ! empty( $settings['bfwpf-performval'] ) ) {
			$sections['validation'] = 'Validation';
		}
		
		return $sections;
	}

	/**
	 * Add the various validation fields under form settings
	 *
	 * @param [array] $form
	 * @param [string] $slug
	 * @return void 
	 */
	function wpforms_builder_after_panel_content( $form, $slug ) {
		if ( 'settings' !== $slug ) {
			return;
		}
		$settings = get_option( 'wpforms_settings' );

		$form_data = $form ? wpforms_decode( $form->post_content ) : false;

		if( false === $form_data || empty( $settings['bfwpf-performval'] ) ) { // if validation is not checked or false under settings
			return;
		}
	

		echo '<div class="wpforms-panel-content-section wpforms-panel-content-section-validation">';
			echo '<div class="wpforms-panel-content-section-title">';
				esc_html_e( 'Validation Messages', 'wpforms-lite' );
				
			echo '<p>These messages are displayed to the users as they fill out a form in real-time.</p>
			</div>';

			wpforms_panel_field(
				'text',
				'settings',
				'required_validation',
				$form_data,
				esc_html__( 'Required' , 'wpforms-lite' ),
				array(
					'default' => 'This field is required.',
				)
			);

			wpforms_panel_field(
				'text',
				'settings',
				'url_validation',
				$form_data,
				esc_html__( 'Website URL' , 'wpforms-lite' ),
				array(
					'default' => 'Please enter a valid URL.',
				)
			);

			wpforms_panel_field(
				'text',
				'settings',
				'email_validation',
				$form_data,
				esc_html__( 'Email' , 'wpforms-lite' ),
				array(
					'default' => 'Please enter a valid email address.',
				)
			);

			wpforms_panel_field(
				'text',
				'settings',
				'email_suggestion_validation',
				$form_data,
				esc_html__( 'Email Suggestion' , 'wpforms-lite' ),
				array(
					'default' => 'Did you mean {suggestion}?',
				)
			);

			wpforms_panel_field(
				'text',
				'settings',
				'number_validation',
				$form_data,
				esc_html__( 'Number' , 'wpforms-lite' ),
				array(
					'default' => 'Please enter a valid number.',
				)
			);

			wpforms_panel_field(
				'text',
				'settings',
				'confirm_validation',
				$form_data,
				esc_html__( 'Confirm Value' , 'wpforms-lite' ),
				array(
					'default' => 'Field values do not match.',
				)
			);

			wpforms_panel_field(
				'text',
				'settings',
				'checkbox_limit_validation',
				$form_data,
				esc_html__( 'Checkbox Selection Limit' , 'wpforms-lite' ),
				array(
					'default' => 'You have exceeded the number of allowed selections: {#}.',
				)
			);

			wpforms_panel_field(
				'text',
				'settings',
				'file_ext_validation',
				$form_data,
				esc_html__( 'File Extension' , 'wpforms-lite' ),
				array(
					'default' => 'File type is not allowed.',
				)
			);

			wpforms_panel_field(
				'text',
				'settings',
				'file_size_validation',
				$form_data,
				esc_html__( 'File Size' , 'wpforms-lite' ),
				array(
					'default' => 'File exceeds max size allowed.',
				)
			);

			wpforms_panel_field(
				'text',
				'settings',
				'time_12_validation',
				$form_data,
				esc_html__( 'Time (12 hour)' , 'wpforms-lite' ),
				array(
					'default' => 'Please enter time in 12-hour AM/PM format (eg 8:45 AM).',
				)
			);

			wpforms_panel_field(
				'text',
				'settings',
				'time_24_validation',
				$form_data,
				esc_html__( 'Time (24 hour)' , 'wpforms-lite' ),
				array(
					'default' => 'Please enter time in 24-hour format (eg 22:45).',
				)
			);

			wpforms_panel_field(
				'text',
				'settings',
				'payment_validation',
				$form_data,
				esc_html__( 'Payment Required' , 'wpforms-lite' ),
				array(
					'default' => 'Payment is required.',
				)
			);

			wpforms_panel_field(
				'text',
				'settings',
				'card_validation',
				$form_data,
				esc_html__( 'Credit Card' , 'wpforms-lite' ),
				array(
					'default' => 'Please enter a valid credit card number.',
				)
			);

			wpforms_panel_field(
				'text',
				'settings',
				'total_size_validation',
				$form_data,
				esc_html__( 'File Upload Total Size' , 'wpforms-lite' ),
				array(
					'default' => 'The total size of the selected files {totalSize} Mb exceeds the allowed limit {maxSize} Mb.',
				)
			);
			
		echo '</div>';
	}

	/**
	 * Shows the user added validation messages in frontend.
	 *
	 * @param [array] $strings
	 * @return $strings
	 */
	function wpforms_frontend_strings( $strings ) {

		global $post;
		$content = $post->post_content;
		$settings = get_option( 'wpforms_settings' );
		if ( ! isset( $content) || empty( $settings['bfwpf-performval'] ) ) {
			return $strings;
		}

		if( has_block( 'wpforms/form-selector', $content ) ){
			$blocks = parse_blocks( $content );

			foreach( $blocks as $block ) {
				if( $block['blockname'] = 'wpforms/form-selector' ){
					$form_id = $block['attrs']['formId'];
				}
			}

		}
		else{

			preg_match( '~\wpforms id=\"(\d+)\"~', $post->post_content, $matches );
			if ( ! is_array( $matches ) || ! isset( $matches[1] ) ) { 
				return $strings;
			}
			$form_id = $matches[1];
		}

		// echo '<pre>';
		$form  = wpforms()->form->get( $form_id );
		$form_data = $form ? wpforms_decode( $form->post_content ) : false;
		// print_r( $form_data );
		if( $form_data !== false ) {
			$form_settings = $form_data['settings'];
			// Change to your form's ID
			// Below are default validation messages - change as needed.
			$strings['val_required'] = $form_settings['required_validation'];
			$strings['val_url'] = $form_settings['url_validation'];
			$strings['val_email'] = $form_settings['email_validation'];
			$strings['val_email_suggestion'] = $form_settings['email_suggestion_validation'];
			$strings['val_number'] = $form_settings['number_validation'];
			$strings['val_confirm'] = $form_settings['confirm_validation'];
			$strings['val_checklimit'] = $form_settings['checkbox_limit_validation'];
			$strings['val_fileextension'] = $form_settings['file_ext_validation'];
			$strings['val_filesize'] = $form_settings['file_size_validation'];
			$strings['val_time12h'] = $form_settings['time_12_validation'];
			$strings['val_time24h'] = $form_settings['time_24_validation'];
			$strings['val_requiredpayment'] = $form_settings['payment_validation'];
			$strings['val_creditcard'] = $form_settings['card_validation'];
			$strings['val_post_max_size'] = $form_settings['total_size_validation'];
		}
		return $strings;
	}

	/**
	 * Adds autocomplete off attribute in frontend to form
	 *
	 * @param [array] $atts
	 * @param [array] $form_data
	 * @return void
	 */
	function disable_form_autocomplete( $atts, $form_data ) {

		$settings = get_option( 'wpforms_settings' );
		$form_settings = $form_data['settings'];

		if ( ! empty( $settings['bfwpf-autocomplete-off'] ) && ! empty( $form_settings['bfwpf_autocomplete_off'] ) ) {
			$atts['atts']['autocomplete'] = 'off';
		}

		return $atts;
	}

	/**
	 * Shows the remaining entries at top of form ( Form Locker addon)
	 *
	 * @param [array] $form_data
	 * @return void
	 */
	function show_remaining_entries( $form_data ) {

		$settings = get_option( 'wpforms_settings' );
		$form_settings = $form_data['settings'];

        if ( empty( $form_settings['form_locker_entry_limit_enable'] ) ||  empty( $form_settings['bfwpf_entry_count'] ) ||  empty( $settings['bfwpf-entry-count'] ) ) {
            return;
        }

		$reference     = ! empty( $form_settings['form_locker_entry_limit'] ) ? (int) $form_settings['form_locker_entry_limit'] : 0;

        $entries_count = wpforms()->entry->get_entries( array( 'form_id' => $form_data['id'] ), true );
        $result        = absint( $reference - $entries_count );

        echo '<p class="bfwpf-remaining-entries-count">' . esc_html( $result ) . ' entries left.</p>';
	}

	/**
	 * Gives the option to set values for checkboxes, radio and dropdowns from backend
	 *
	 * @param [boolean] $value
	 * @return $value
	 */
	function wpforms_fields_show_options_setting( $value ) {
		$settings = get_option( 'wpforms_settings' );

		if( ! empty( $settings['bfwpf-field-values'] ) ) {
			$value = true;
		}
		return $value;
	}

	/**
	 * Add more schemes for Address fields
	 *
	 * @param [array] $schemes
	 * @return $schemes
	 */
	function wpforms_address_schemes( $schemes ) {
		

		$settings = get_option( 'wpforms_settings' );

		if( empty( $settings['bfwpf-schemes'] ) ) { 
			return $schemes;
		}

		$schemes['canada'] = array(
			'label'          => 'Canada',
			'address1_label' => 'Address Line 1',
			'address2_label' => 'Address Line 2',
			'city_label'     => 'City',
			'postal_label'   => 'Code Postal',
			'state_label'    => 'Province',
			'states'         => array(
				'AB' => 'Alberta',
				'BC' => 'British Columbia',
				'MB' => 'Manitoba',
				'NB' => 'New Brunswick',
				'NL' => 'Newfoundland and Labrador',
				'NS' => 'Nova Scotia',
				'ON' => 'Ontario',
				'PE' => 'Prince Edward Island',
				'WQ' => 'Quebec',
				'SK' => 'Saskatchewan',
			),
		);

		$schemes['australia'] = array(
			'label'          => 'Australia',
			'address1_label' => 'Address Line 1',
			'address2_label' => 'Address Line 2',
			'city_label'     => 'City',
			'postal_label'   => 'Postal',
			'state_label'    => 'State / Territory',
			'states'         => array(
				'NSW' => 'New South Wales',
				'VIC' => 'Victoria',
				'QLD' => 'Queensland',
				'WA'  => 'Western Australia',
				'SA'  => 'South Australia',
				'TAS' => 'Tasmania',
				'ACT' => 'Australia Capital Territory',
				'NT'  => 'Northern Territory',
			),
		);

		return $schemes;
	}

	/**
	 * Add Booster tab under Global Forms settings
	 *
	 * @param [array] $tabs
	 * @return void
	 */
	function wpforms_settings_tabs( $tabs ) {
		$tabs['booster'] = array(
			'name'   => esc_html__( 'Booster', 'wpforms-lite' ),
			'form'   => true,
			'submit' => esc_html__( 'Save Settings', 'wpforms-lite' ),
		);

		return $tabs;
	}

	/**
	 * Adds settings under Booster Tab
	 *
	 * @param [array] $defaults
	 * @return $default
	 */
	function wpforms_settings_defaults( $defaults ) {
		
		$advanced_boosts = array(

			// Advanced Boosts
			'bfwpf-advanced-heading' => array(
				'id'       => 'bfwpf-advanced-booster-heading',
				'content'  => '<h4>' . esc_html__( 'Advanced Boosts', 'wpforms-lite' ) . '</h4>',
				'type'     => 'content',
				'no_label' => true,
				'class'    => array( 'section-heading', 'no-desc' ),
			),

			// 'bfwpf-auto-complete' => array(
			// 	'id'   => 'bfwpf-auto-complete',
			// 	'name' => esc_html__( 'Enable Suggestions in Fields', 'wpforms-lite' ),
			// 	'desc' => __( 'Add list of suggestion which are automatically shown when user starts typing. Setting is present in Form editor under Fields -> Field Options -> Advanced Options. <a href="https://wpmonks.com/downloads/booster-for-wpforms-pro" target="_blank" >Buy Pro</a>', 'wpforms-lite' ),
			// 	'type' => 'checkbox',
			// 	'callback' => 'bfwpf_settings_disabled_checkbox_callback'
			// ),

			'bfwpf-range-slider' => array(
				'id'   => 'bfwpf-range-slider',
				'name' => esc_html__( 'Range Slider Field', 'wpforms-lite' ),
				'desc' => __( 'Adds a Range Slider field in WPForms. <a href="https://wpmonks.com/downloads/range-slider-for-wpforms/" target="_blank" >Enable this Boost</a>', 'wpforms-lite' ),
				'type' => 'checkbox',
				'callback' => 'bfwpf_settings_disabled_checkbox_callback'
			),

			'bfwpf-confirmation' => array(
				'id'   => 'bfwpf-confirmation',
				'name' => esc_html__( 'Show Confirtmation Page', 'wpforms-lite' ),
				'desc' => __( 'Add a confirmation ( Submission Preview ) step which shows all the values filled by user.  <a href="https://wpmonks.com/downloads/confirmation-step-for-wpforms/" target="_blank" >Enable this Boost</a>', 'wpforms-lite' ),
				'type' => 'checkbox',
				'callback' => 'bfwpf_settings_disabled_checkbox_callback'
			 ),

			'bfwpf-popup' => array(
				'id'   => 'bfwpf-popup',
				'name' => esc_html__( 'Show Form in Popup', 'wpforms-lite' ),
				'desc' => __( 'Show your form in Popup window. Plenty of options to customize design of the popup to fit your site\'s design. <a href="https://wpmonks.com/downloads/popup-for-wpforms/" target="_blank" >Enable this Boost</a>', 'wpforms-lite' ),
				'type' => 'checkbox',
				'callback' => 'bfwpf_settings_disabled_checkbox_callback'
			),

			'bfwpf-places-api' => array(
				'id'   => 'bfwpf-places-api',
				'name' => esc_html__( 'Address Autocomplete', 'wpforms-lite' ),
				'desc' => __( 'Auto suggest address when user starts typing in an Address or text field. <a href="https://wpmonks.com/downloads/address-autocomplete-for-wpforms/" target="_blank" >Enable this Boost</a>', 'wpforms-lite' ),
				'type' => 'checkbox',
				'callback' => 'bfwpf_settings_disabled_checkbox_callback'
			),
		);

		// var_dump( $advanced_boosts );
		// die();

		$advanced_boosts = apply_filters( 'bfwpf_global_settings_advanced', $advanced_boosts );

			// Basic Boosts
		$basic_boosts = array( 
			'bfwpf-heading' => array(
				'id'       => 'bfwpf-heading',
				'content'  => '<h4>' . esc_html__( 'Basic Boosts', 'wpforms-lite' ) . '</h4>',
				'type'     => 'content',
				'no_label' => true,
				'class'    => array( 'section-heading', 'no-desc' ),
			),

			'bfwpf-readonly-fields' => array(
				'id'   => 'bfwpf-readonly-fields',
				'name' => esc_html__( 'Enable Read only fields', 'wpforms-lite' ),
				'desc' => esc_html__( 'Check this option if you want to enable read only option for your fields. Setting is present in Form editor under Fields -> Field Options -> Advanced Options', 'wpforms-lite' ),
				'type' => 'checkbox',
			),

			'bfwpf-autocomplete-off' => array(
				'id'   => 'bfwpf-autocomplete-off',
			'name' => esc_html__( 'Browser Auto Complete', 'wpforms-lite' ),
				'desc' => esc_html__( 'Show the option to turn of browser autocomplete on each form. Setting is present in Form editor under Settings -> General', 'wpforms-lite' ),
				'type' => 'checkbox',
			),

			'bfwpf-entry-count' => array(
				'id'   => 'bfwpf-entry-count',
			'name' => esc_html__( 'Show entries remaining count', 'wpforms-lite' ),
				'desc' => esc_html__( 'This will show if the number of entries remaining at top of form if you have set the limit using Form Locker addon. Setting is present in Form editor under Settings -> General', 'wpforms-lite' ),
				'type' => 'checkbox',
			),

			'bfwpf-maxchars' => array(
				'id'   => 'bfwpf-maxchars',
			'name' => esc_html__( 'Max Characters', 'wpforms-lite' ),
				'desc' => esc_html__( 'Limit the number of characters that can be submitted in text and textarea fields. Setting is present in Form editor under Fields -> Field Options -> Advanced Options', 'wpforms-lite' ),
				'type' => 'checkbox',
			),

			'bfwpf-field-values' => array(
				'id'   => 'bfwpf-field-values',
			'name' => esc_html__( 'Checbox, Radio, Dropdown field values', 'wpforms-lite' ),
				'desc' => esc_html__( 'Show the option to set field values which is different from field name for checkboxes, radios and dropdowns. Setting is present in Form editor under Fields -> Field Options -> Advanced Options', 'wpforms-lite' ),
				'type' => 'checkbox',
			),

			// 'bfwpf-places-api' => array(
			// 	'id'   => 'bfwpf-places-api',
			// 	'name' => esc_html__( 'Google Places API', 'wpforms-lite' ),
			// 	'desc' => esc_html__( 'Add Google Clould API with Places and Maps Javascript API enabled.', 'wpforms-lite' ),
			// 	'type' => 'text',
			// ),

			'bfwpf-minmax' => array(
			'id'   => 'bfwpf-minmax',
			'name' => esc_html__( 'Min, Max range for Numbers', 'wpforms-lite' ),
				'desc' => esc_html__( 'Limit the minimum and maximun number which can be added into numbers field. Setting is present in Form editor under Fields -> Field Options -> Advanced Options', 'wpforms-lite' ),
				'type' => 'checkbox',
			),

			'bfwpf-performval' => array(
				'id'   => 'bfwpf-performval',
				'name' => esc_html__( 'Show Validation Message per form', 'wpforms-lite' ),
					'desc' => esc_html__( 'Enable seperate validation messages per form. You can set validation messages in the form under Settings -> Validation', 'wpforms-lite' ),
					'type' => 'checkbox',
			),

			'bfwpf-schemes' => array(
				'id'   => 'bfwpf-schemes',
				'name' => esc_html__( 'More countries in Address', 'wpforms-lite' ),
					'desc' => esc_html__( 'Add more countries in Address field schema. Setting is present in Form editor under Fields -> Field Options ', 'wpforms-lite' ),
					'type' => 'checkbox',
				)

		);


		$basic_boosts = apply_filters( 'bfwpf_global_settings', $basic_boosts );

		$defaults['booster'] = array_merge( $advanced_boosts, $basic_boosts );



		return $defaults;
	}

	/**
	 * Set attributes for fields in frontend
	 *
	 * @param [array] $properties
	 * @param [array] $field
	 * @param [array] $form_data
	 * 
	 * @return $properties
	 */
	function wpforms_field_properties( $properties, $field, $form_data  ) {

		$settings = get_option( 'wpforms_settings' );
		$form_settings = $form_data['settings'];
	
		// Make field bfwpf_readonly
		if( ! empty( $settings['bfwpf-readonly-fields'] ) ) { // ennabled from global settings

			$supported_fields = $this->supported_fields('bfwpf_readonly');

			if( in_array( $field['type'], $supported_fields ) && ! empty( $field['bfwpf_readonly'] ) ) {
				$properties['inputs']['primary']['attr']['readonly'] = 'readonly';
			}

		}

		// Max length of chars in field
		if( ! empty( $settings['bfwpf-maxchars'] ) ) { // ennabled from global settings

			$supported_fields = $this->supported_fields('bfwpf_maxchars');

			if( in_array( $field['type'], $supported_fields ) && ! empty( $field['bfwpf_maxchars'] ) ) {
				$properties['inputs']['primary']['attr']['maxlength'] =  $field['bfwpf_maxchars'];
			}
		}

		// Set Minimum and maximum range for numbers
		if( ! empty( $settings['bfwpf-minmax'] ) ) { // ennabled from global settings

			$supported_fields = $this->supported_fields('bfwpf_minmax');

			// for minimum
			if( in_array( $field['type'], $supported_fields ) && ! empty( $field['bfwpf_minnumber'] ) ) {
				$properties['inputs']['primary']['attr']['min'] =  $field['bfwpf_minnumber'];
			}

			// for maximum
			if( in_array( $field['type'], $supported_fields ) && ! empty( $field['bfwpf_maxnumber'] ) ) {
				$properties['inputs']['primary']['attr']['max'] =  $field['bfwpf_maxnumber'];
			}
		}

		// $properties['inputs']['primary']['attr']['autocomplete'] =  'false';
		// var_dump( $properties['inputs']['primary']['attr'] );
		// die();

		// Set Autofill attribute to false
		if( ! empty( $settings['bfwpf-autocomplete-off'] )  ) { 
			$supported_fields = $this->supported_fields('bfwpf_autocomplete_off');

            if (in_array($field['type'], $supported_fields) && ! empty($form_settings['bfwpf_autocomplete_off']) ) {
                //  https://bugs.chromium.org/p/chromium/issues/detail?id=468153#c164, this is why nope
				$properties['inputs']['primary']['attr']['autocomplete'] =  'nope';

            }
			
		}
		
		return $properties;
			
	}

	/**
	 * Includes necessary files for adding settings under Advanced tab of field.
	 *
	 * @param [array] $field
	 * @param [object] $base_object
	 * @return void
	 */
	function field_options( $field, $base_object ) {

		$settings = get_option( 'wpforms_settings' );

		foreach( $settings as $setting_name => $setting_value ) {
	

			// Enabled from global settings
			if ( true === $setting_value && file_exists( BFWPF_DIR . '/includes/admin/' . $setting_name . '.php' ) ) { 
				include BFWPF_DIR . '/includes/admin/' . $setting_name . '.php';
			}
		}
		do_action( 'bfwpf_field_options', $settings, $field, $base_object );
	}

	


} // Class ends here

add_action( 'wpforms_loaded', 'booster_for_wpforms', 1000 );

function booster_for_wpforms( ) {
	new Booster_For_Wpforms();

}

/**
 * Settings uncheced and disabled checkbox field callback.
 *
 * @since 1.3.9
 *
 * @param array $args
 *
 * @return string
 */
function bfwpf_settings_disabled_checkbox_callback( $args ) {
	// var_dump( $args );
	// die();

	$value   = wpforms_setting( $args['id'] );
	$id      = wpforms_sanitize_key( $args['id'] );
	$checked = ! empty( $value ) ? checked( 1, $value, false ) : '';

	$output = '<input type="checkbox" disabled id="wpforms-setting-' . $id . '" name="' . $id . '" >';

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	return $output;
}

/**
 * Settings checked but disabled checkbox field callback.
 *
 * @since 1.3.9
 *
 * @param array $args
 *
 * @return string
 */
function bfwpf_settings_disabled_checked_checkbox_callback( $args ) {

	$value   = wpforms_setting( $args['id'] );
	$id      = wpforms_sanitize_key( $args['id'] );
	$checked = ! empty( $value ) ? checked( 1, $value, false ) : '';

	$output = '<input type="checkbox" checked disabled id="wpforms-setting-' . $id . '" name="' . $id . '" >';

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	return $output;
}