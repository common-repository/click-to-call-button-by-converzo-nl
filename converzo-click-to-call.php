<?php
/*
Plugin Name: Click-to-Call Button by Converzo.nl
Plugin URI:  https://wordpress.org/plugins/converzo-click-to-call/
Description: With this plugin you add a click-to-call button to your responsive website in no-time. 
Version:     1.0
Author:      Converzo
Author URI:  https://converzo.nl/wordpress-programmeur/
License:     GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class WP_CTA_ACTION
{
	 
	public $menu_id;
	
	/**
	 * Plugin initialization
	 *
	 * @since 1.0
	 */
	public function __construct() {
		  
		// admin
		add_action( 'admin_menu', array( $this, 'wp_cta_add_admin_menu' ));
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_cta_admin_scripts' ));
		
		// create needed initialization
		add_action('admin_init', array( $this, 'wp_cta_register_options_settings') );
		
		// create custom footer
		add_action('wp_footer', array( $this, 'wp_cta_add_buttons'), 10);
		
		// grab the options, use for entire object
		$this->wp_cta_options = $this->wp_cta_options();
	}

	/**
	 * Add Menu Page
	 */
	public function wp_cta_add_admin_menu() {
    	add_options_page('Settings page for Click-to-Call by Converzo.nl', 'Click-to-Call options', 'publish_posts', 'wp_cta_quick_call_button', array($this,'wp_cta_options_page'),''); 
	}
	
	/**
	 * Add Resources
	 */
	function wp_cta_admin_scripts() {

		if (get_current_screen()->base == 'settings_page_wp_cta_quick_call_button') {
	        wp_register_script( 'wp_cta_js', plugins_url('assets/js/wp-cta-js.js', __FILE__), array('jquery'), '1.0', true );
	        wp_enqueue_script( 'wp_cta_js' );
			 
		    wp_enqueue_style('wp-color-picker');
		    wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), false,1);
	    }
	}

	/**
	 * Whitelist Options
	 *
	 * @since 1.0
	 */
	function wp_cta_register_options_settings() { 
	    register_setting( 'wp_cta_custom_options-group', 'wp_cta_options' );
	}  
	    
	/**
	 * Options Page
	 *
	 * @since 1.0.1
	 */
	function wp_cta_options_page() {
		global $_wp_admin_css_colors, $wp_version;
		
		// access control
	    if ( !(isset($_GET['page']) && $_GET['page'] == 'wp_cta_quick_call_button' )) 
	    	return;
		?>
	
		<div class='wrap'>
			<h2>Click-to-Call by Converzo.nl</h2>
			<div class="brand">
            	<p>Thank you for using the click-to-call button by <a href="https://converzo.nl/?utm_source=Click-to-call%20plugin&utm_medium=Click-to-call%20plugin&utm_campaign=Click-to-call%20plugin">Converzo</a>. </p>
				<ul>
				<li> • The click-to-call button won't show on screens larger than 767 pixels.</li>
				<li> • You can customize the position of the button and the color of both the icon and background.</li>
				<li> • We added 2 effects: Shake and Wave. You can easily disable them on this page</li>
				<li> • And.. don't forget to change the default phone number ;) </li>
				</ul></p>
            </div>
            
            <form method="post" action="options.php" class="form-table">
				<?php
				wp_nonce_field('wp_cta_options');
				settings_fields('wp_cta_custom_options-group');
				?>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="wp_cta_options" />
				<h2 class='title'>Setting</h2>
			 
				
				<table border=0 cellpadding=2 cellspacing="2">
				<tr>
					<th><label>Phone number</label></th>
					<td>
						<input name="wp_cta_options[phone_number]" placeholder="+1234567890" value='<?php echo $this->wp_cta_sanitize_phone($this->wp_cta_options['phone_number']); ?>' /><br />
					</td>
				</tr>
				</table>
				
				<table border=0 cellpadding=2 cellspacing="2">
				
                <tr>
				   <th><label>Select button position</label></th>
						<td>
							<input type="radio" id="wp-position-button-left" name="wp_cta_options[button_position]" value="left" <?php checked('left', $this->wp_cta_options['button_position']) ?>  />
							<label for="wp-position-button-left">Left Bottom</label><br />
						    
                            <input type="radio" id="wp-position-button-right" name="wp_cta_options[button_position]" value="right" <?php checked('right', $this->wp_cta_options['button_position']) ?>  />
							<label for="wp-position-button-right">Right Bottom</label><br />
						</td>
				</tr>
                 
				<tr>
				    <th><label>Circle color</label></th>
					    <td>
						    <input type="text" class="colourme" name="wp_cta_options[circle_color]" value="<?php echo $this->wp_cta_options['circle_color']; ?>">
					    </td>
				</tr>
				      
				<tr>
					<th><label>Icon color</label></th>
					    <td>
						    <input type="text" class="colourme" name="wp_cta_options[button_color]" value="<?php echo $this->wp_cta_options['button_color']; ?>">
					    </td>
				</tr>
				
				<tr>
					<th><label>Button effect</label></th>
						<td>
							<input type="checkbox" id="wp-call-button-effect" name="wp_cta_options[call_wave_effect]" value="inactive" <?php checked('inactive', $this->wp_cta_options['call_wave_effect']) ?>  />
							<label for="wp-call-button-effect">Disable Wave Effect</label><br />
                            
						    <input type="checkbox" id="wp-call-button-shake-effect" name="wp_cta_options[call_shake_effect]" value="notshake" <?php checked('notshake', $this->wp_cta_options['call_shake_effect']) ?>  />
							<label for="wp-call-button-shake-effect">Disable Shake Effect</label><br />
						</td>
				</tr>
               
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="Save Changes" />
					<a class="button button-primary" href="http://converzo.nl/contact/" target="_blank">Need help? Contact us.</a>
				</p>
			</form>
		 	
		</div>
		
	  	<?php
	}
	
	// Adding Custom Quick Call Buttons.
	function wp_cta_add_buttons() {
		
		$return =  "";

		// Setup valuable settings.
		if ($this->wp_cta_mandatory_have_info()) {
			
			// adding the enque here will setup the style.
			wp_register_style( 'wp_cta_css', plugins_url('/assets/css/wp-cta-css.css', __FILE__) , false, '1.1.0' );
			wp_enqueue_style( 'wp_cta_css');
			
			
			// code button
			$return .=  "
			<div class='wp-cta-button ".$this->wp_cta_options['button_position']."'>";	
		 
			if ( !empty($this->wp_cta_options['phone_number']) ) { 
				
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="22pt" height="22pt" viewBox="0 0 22 22" version="1.1"><g id="surface1"><path style="stroke:none;fill-rule:nonzero; fill:'.$this->wp_cta_options['button_color'].'; fill-opacity:1;" d="M 21.511719 17.390625 L 18.113281 13.992188 C 17.4375 13.316406 16.316406 13.339844 15.613281 14.039062 L 13.902344 15.75 C 13.792969 15.691406 13.683594 15.628906 13.566406 15.5625 C 12.484375 14.964844 11.003906 14.140625 9.445312 12.585938 C 7.886719 11.023438 7.0625 9.539062 6.460938 8.457031 C 6.398438 8.34375 6.339844 8.234375 6.277344 8.128906 L 7.992188 6.414062 C 8.695312 5.714844 8.714844 4.59375 8.039062 3.917969 L 4.640625 0.519531 C 3.964844 -0.15625 2.84375 -0.136719 2.140625 0.566406 L 1.183594 1.527344 L 1.210938 1.554688 C 0.886719 1.964844 0.621094 2.4375 0.421875 2.945312 C 0.238281 3.429688 0.121094 3.894531 0.0703125 4.359375 C -0.378906 8.078125 1.320312 11.476562 5.933594 16.089844 C 12.3125 22.46875 17.449219 21.988281 17.671875 21.964844 C 18.15625 21.90625 18.617188 21.789062 19.089844 21.605469 C 19.59375 21.410156 20.066406 21.140625 20.476562 20.820312 L 20.496094 20.839844 L 21.464844 19.890625 C 22.167969 19.1875 22.1875 18.066406 21.511719 17.390625 Z M 21.511719 17.390625 "/></g></svg>';
			
				$return .= "
				<div>
					<a href='tel:".$this->wp_cta_sanitize_phone($this->wp_cta_options['phone_number'])."'>
					<div class='wp-cta-ph-circle {$this->wp_cta_options['call_wave_effect']}'></div>
                    <div class='wp-cta-ph-circle-fill {$this->wp_cta_options['call_wave_effect']}'></div>
                    <div class='wp-cta-phone-circle {$this->wp_cta_options['call_shake_effect']}'>".$svg."</div>
					</a>
				</div>"; 
			}
			$return .= "
			</div>
			<style> 
				@media screen and (max-width: 767px) { 
					.wp-cta-button { display: flex !important; background: {$this->wp_cta_options['circle_color']}; }  
					.wp-call-button { display: block !important; } 
				}
				.wp-cta-phone-circle svg, .wp-cta-phone-circle path { fill: {$this->wp_cta_options['circle_color']}; }
				.wp-cta-button { background: {$this->wp_cta_options['circle_color']}; }
				.wp-cta-button div a .quick-alo-ph-img-circle, .wp-cta-button div a .wp-cta-phone-circle { background-color: {$this->wp_cta_options['circle_color']}; }
				";
			$return .= "
			</style>";
		} 
		$return .= "";
			
		echo apply_filters('wp_cta_output',$return);
	}
	
	// Checking and setting the default options.
	function wp_cta_options() { 
	   
		$defaults = array(
		    'button_position' 	 => 'right',
			'circle_color' 	 => '#c40f1a',
			'button_color'=> '#fff',
			'phone_number' 	 => '+1234567890',
			'call_wave_effect' => 'active',
			'call_shake_effect' => 'shake',
		);

		// Get user options
		$wp_cta_options = get_option('wp_cta_options');		
		
		// if the user hasn't made settings yet, default
		if (is_array($wp_cta_options)) {
			// Lets make sure we have a value for each as some might be new.
			foreach ($defaults as $k => $v)
				if (!isset($wp_cta_options[$k]) || empty($wp_cta_options[$k]))
					$wp_cta_options[$k] = $v;
		} 
		// Must be first, lets use defaults
		else {
			$wp_cta_options = $defaults;
		}
		
		return $wp_cta_options;
	}
	
	 
	function wp_cta_mandatory_have_info() {
		return ((isset($this->wp_cta_options['phone_number']) && !empty($this->wp_cta_options['phone_number']))) ? true : false;
	}

	/* clean phone */
	function wp_cta_sanitize_phone($number) {
		return str_replace( array(' ','(',')','.'), array('','','-','-'), $number);
	}
	 
	
}
new WP_CTA_ACTION();