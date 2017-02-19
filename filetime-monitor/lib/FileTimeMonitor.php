<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class FileTimeMonitor {
    private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;
	private $settings_base;
	private $settings;

	public function __construct($file) {
		
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url(trailingslashit(plugins_url('/assets/',$this->file)));
		$this->settings_base = 'fmt_';

		// Initialise settings
		add_action('admin_init',array($this,'init'));

		// Register plugin settings
		add_action('admin_init',array($this,'register_settings'));

		// Add settings page to menu
		add_action('admin_menu',array($this,'add_menu_item'));

		// Add the dashboard widget
		add_action('wp_dashboard_setup', array($this,'setup_dash_widget'));
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item() {
		$page = 
			add_options_page( 
				__( 'File Time Monitor Settings',FTM_TEXT_DOMAIN),
				__( 'File Time Monitor',FTM_TEXT_DOMAIN), 
				'manage_options',
				'ftm-settings',
				array($this,'settings_page')
			);
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {
		
		// Build the base fields list
		$fields = array();
		
		$fields[] =
			array(
				'id' 			=> 'hour_alert',
				'label'			=> __('Visual Alert Time',FTM_TEXT_DOMAIN ),
				'description'	=> __('<br />How long before the warning icon should show, after the file has been updated?',FTM_TEXT_DOMAIN ),
				'type'			=> 'select',
				'options'		=> 
					array( 
						3600 => '1 Hour',		// No point having PHP
						7200 => '2 Hours',		// calculate these out
						10800 => '3 Hours',		// every time. Just do
						21600 => '6 Hours',		// it manually
						43200 => '12 Hours',
						86400 => '1 Day',
						172800 => '2 Days',
						259200 => '3 Days',
						345600 => '4 Days',
						432000 => '5 Days',
						518400 => '6 Days',
						604800 => '1 Week',
					),
				'default'		=> 86400
			);
			
		// Iterate through the fields
		for($i=0;$i<20;$i++) {
			$fields[] = 
				array(
					'id' 			=> 'file_path_'.($i+1),
					'label'			=> __( 'Path to File '.($i+1) , FTM_TEXT_DOMAIN ),
					'description'	=> __( '', FTM_TEXT_DOMAIN ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'Full path to file', FTM_TEXT_DOMAIN )
				);
		}

		// And the settings group
		$settings['standard'] = array(
			'title'					=> __( 'Files to Monitor', FTM_TEXT_DOMAIN ),
			'description'			=> __( 'List the files you would like to add to the Dashboard Widget.</p><ul><li>&bull; You may monitor a maximum of 20 files.</li><li>&bull; You must add the <strong>full path</strong> of the file (eg: <i>'.__FILE__.'</i>)</li></ul><p>', FTM_TEXT_DOMAIN ),
			'fields'				=> $fields
		);

		$settings = apply_filters( 'ftm-settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings() {
		if( is_array( $this->settings ) ) {
			foreach( $this->settings as $section => $data ) {

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'ftm-settings' );

				foreach( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->settings_base . $field['id'];
					register_setting( 'ftm-settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), 'ftm-settings', $section, array( 'field' => $field ) );
				}
			}
		}
	}

	public function settings_section($section) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Generate HTML for displaying fields
	 * @param  array $args Field data
	 * @return void
	 */
	public function display_field( $args ) {

		$field = $args['field'];

		$html = '';

		$option_name = $this->settings_base . $field['id'];
		$option = get_option( $option_name );

		$data = '';
		if( isset( $field['default'] ) ) {
			$data = $field['default'];
			if( $option ) {
				$data = $option;
			}
		}

		switch( $field['type'] ) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
			break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
			break;
		}

		switch( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;

			default:
				$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
			break;
		}

		echo $html;
	}

	/**
	 * Validate individual settings field
	 * @param  string $data Inputted value
	 * @return string       Validated value
	 */
	public function validate_field( $data ) {
		if( $data && strlen( $data ) > 0 && $data != '' ) {
			$data = urlencode( strtolower( str_replace( ' ' , '-' , $data ) ) );
		}
		return $data;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML
		$html = '<div class="wrap" id="ftm-settings">' . "\n";
			$html .= '<h2>' . __( 'File Time Monitor Settings' , FTM_TEXT_DOMAIN ) . '</h2>' . "\n";
			$html .= '<form method="post" action="options.php" enctype="multipart/form-data" class="ftm-form">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( 'ftm-settings' );
				do_settings_sections( 'ftm-settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , FTM_TEXT_DOMAIN ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}
	
	/**
	 * Set's up the dashboard widget
	 */
	public function setup_dash_widget() {
		global $wp_meta_boxes;
		wp_add_dashboard_widget('ftm_widget', 'File Time Monitoring', array($this,'display_dash_widget'));
	}
	
	/**
	 * Display's the files being monitored
	 */
	public function display_dash_widget() {
		$timeline = intval(get_option('fmt_hour_alert'));
		$alert = time() - $timeline;
		?>
		<table id="ftm-info" cellpadding="0" cellspacing="0">
			<tbody>
				<?php 
				for($i=1;$i<=20;$i++) {
					$filepath = get_option('fmt_file_path_'.$i);
					$fileinfo = '';
					
					if (strlen(trim($filepath))) {
						$filename = substr($filepath, strrpos($filepath, '/')+1);
						
						$fileinfo .= 
							'<tr>'.
								'<td>';
								
						if (file_exists($filepath)) {
							$filetime = filemtime($filepath);
							$icon = ($filetime > $alert ? 'yes' : 'warning');
							$fileinfo .=
									'<span class="dashicons dashicons-'.$icon.'"></span>'.
								'</td>'.
								'<td>'.
									$filename.
								'</td>'.
								'<td>'.
									date('l j M Y H:i',$filetime).'<span class="light-grey">'.date(':s',$filetime).'</span>';
						} else {
							$fileinfo .=
									'<span class="dashicons dashicons-dismiss"></span>'.
								'</td>'.
								'<td>'.
									$filename.
								'</td>'.
								'<td>'.
									'&nbsp;';
						}
						
						$fileinfo .=
								'</td>'.
							'</tr>';
					}				
					
					if (strlen($fileinfo)) {
						echo $fileinfo;
					}
				}
				?>
			</tbody>
		</table>
		<p>
			<strong><i>Server Time:</i></strong>
			<i><?php echo date('l j M Y H:i').'<span class="light-grey">'.date(':s').'</span>' ?></i>
		</p>
		<?php
	}
}