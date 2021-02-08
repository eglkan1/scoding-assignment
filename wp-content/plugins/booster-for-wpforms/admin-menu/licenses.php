<?php

class Bfwpf_License_Page{

	function __construct(){
		add_action('admin_menu',array($this,'register_menu') );
		add_action( 'admin_init', array( $this, 'setting_fields' ) );
	}

	public function register_menu(){
		add_menu_page(  'Booster for WPForms', 'Boost WPForms', 'manage_options', 'bfwpf_licenses' );
		add_submenu_page( 'bfwpf_licenses', 'Licenses', 'Licenses', 'manage_options', 'bfwpf_licenses', array( $this, 'license_settings' ) );
	}

	public function license_settings(){

		?>
			<!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">

        <!-- Make a call to the WordPress function for rendering errors when settings are saved. -->
        <?php settings_errors(); ?>
        <!-- Create the form that will be used to render our options -->
        <form method="post" action="options.php">
            <?php settings_fields( 'bfwpf_licenses' ); ?>
            <?php do_settings_sections( 'bfwpf_licenses' ); ?>
            <?php submit_button(); ?>
        </form>

    </div><!-- /.wrap -->
	<?php
	}


	function setting_fields(){
		// If settings don't exist, create them.
		if ( false == get_option( 'bfwpf_licenses' ) ) {
			add_option( 'bfwpf_licenses' );
		}


		add_settings_section(
			'bfwpf_licenses_section',
			'Add-On Licenses',
			array( $this, 'section_callback' ),
			'bfwpf_licenses'
		);

		do_action('bfwpf_license_fields',$this);

		//register settings
		register_setting( 'bfwpf_licenses', 'bfwpf_licenses' );

	}

	public function section_callback() {

		echo '<h4> Licence Fields will automatically appear once you install addons for \'Booster for WPForms\'. You can check all the available addons <a href="https://wpmonks.com/wpforms-booster-addons/?utm_source=dashboard&utm_medium=licence&utm_campaign=wpforms-booster-plugin">here</a></h4>';
	}


}

new Bfwpf_License_Page();