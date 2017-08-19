<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.michelleanneyee.com
 * @since      1.0.0
 *
 * @package    Yeem_Contact_Form
 * @subpackage Yeem_Contact_Form/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Yeem_Contact_Form
 * @subpackage Yeem_Contact_Form/admin
 * @author     Michelle Anne Yee <mishi@michelleanneyee.com>
 */
class Yeem_Contact_Form_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function yeem_enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Yeem_Contact_Form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Yeem_Contact_Form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        //YEEM
        wp_enqueue_style('jquery-style','https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/themes/smoothness/jquery-ui.css');
        wp_enqueue_style('bootstrap-style','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css');
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/yeem-contact-form-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function yeem_enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Yeem_Contact_Form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Yeem_Contact_Form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        //YEEM

        //wp_enqueue_script( $this->plugin_name, 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array(), $this->version, false );
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/yeem-contact-form-admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), $this->version, false );
        wp_localize_script( $this->plugin_name, 'yeemScriptObj', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'pluginsUrl' => plugins_url('', dirname(__FILE__)) ) );


	}


    //YEEM
    public function yeem_add_menu(){

        /**
         * Add a top-level menu page.
         *
         *  Parameters:
         *      string      required    Page Title  The text to be displayed in the title tags of the page when the menu is selected.
         *      string      required    Menu Title  The text to be used for the menu.
         *      string      required    Capability  The capability required for this menu to be displayed to the user.
         *      string      required    Menu Slug   The slug name to refer to this menu by (should be unique for this menu).
         *      callable    optional    Function    The function to be called to output the content for this page.
         *      string      required    Icon URL    The URL to the icon to be used for this menu.
         *      int         optional    Position    The position in the menu order this one should appear.
         *
         * @since    1.0.0
         */
        add_menu_page(
            'Yeem Contact Form',
            'Yeem Contact Form',
            'manage_options',
            'yeem_contact_form_builder',
            '','none'
        );

        add_submenu_page(
            'yeem_contact_form_builder',
            'Design Form - Yeem Contact Form',
            'Design Form',
            'manage_options',
            'yeem_contact_form_builder',
            array ( $this, 'yeem_display_form_builder_page')
        );

        add_submenu_page(
            'yeem_contact_form_builder',
            'Settings - Yeem Contact Form',
            'Settings',
            'manage_options',
            'yeem_contact_form_settings',
            array ( $this, 'yeem_display_form_settings_page' )
        );
    }


    public function yeem_add_settings_link( $links ) {
        $settings_link = '<a href="admin.php?page=yeem_contact_form_builder">' . __( 'Settings' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    public function yeem_contactform_settings() {
        register_setting( 'yeem_contact_form_settings', 'yeem_your_name', array($this, 'yeem_text_setting_sanitize') );
        register_setting( 'yeem_contact_form_settings', 'yeem_sender_email_address', array($this, 'yeem_email_setting_sanitize') );
        register_setting( 'yeem_contact_form_settings', 'yeem_email_subject', array($this, 'yeem_text_setting_sanitize') );
        register_setting( 'yeem_contact_form_settings', 'yeem_confirmation_msg', array($this, 'yeem_text_setting_sanitize') );
        register_setting( 'yeem_contact_form_settings', 'yeem_email_msg', array($this, 'yeem_text_setting_sanitize'));
    }

    public function yeem_text_setting_sanitize($input){
        $input = sanitize_text_field($input);
        return $input;
    }

    public function yeem_email_setting_sanitize($input){
        $input = sanitize_email($input);
        if(!is_email($input))
            add_settings_error( 'invalid-email', '', 'Please enter a valid email address.', 'error' );
        return $input;
    }

    public function yeem_display_form_settings_page(){
    ?>
        <div class="wrap">
            <h1>Settings - Yeem Contact Form</h1>
            <br>
            <?php settings_errors(); ?>
            <br>

            <form method="post" action="options.php">

                <?php settings_fields( 'yeem_contact_form_settings' ); ?>
                <?php do_settings_sections( 'yeem_contact_form_settings' ); ?>

                <table class="table table-striped">
                    <tr>
                        <th data-toggle="tooltip" title="This is name that will appear as the sender in your email to your customers.">Your Name<span class="glyphicon glyphicon-info-sign text-info infoicon"></span></th>
                        <td><input type="text" name="yeem_your_name" value="<?php echo esc_attr( get_option('yeem_your_name') ); ?>" /></td>
                    </tr>

                    <tr>
                        <th data-toggle="tooltip" title="This is your contact email address.">Email Address<span class="glyphicon glyphicon-info-sign text-info infoicon"></span></th>
                        <td><input type="text" name="yeem_sender_email_address" value="<?php echo esc_attr( get_option('yeem_sender_email_address') ); ?>" /></td>
                    </tr>

                    <tr>
                        <th data-toggle="tooltip" title="This is the subject line in your emails to your customers.">Email Subject<span class="glyphicon glyphicon-info-sign text-info infoicon"></span></th>
                        <td><input type="text" name="yeem_email_subject" value="<?php echo esc_attr( get_option('yeem_email_subject') ); ?>" /></td>
                    </tr>

                    <tr>
                        <th data-toggle="tooltip" title="This is the message that will appear on the customer screen after they submit their message.">Confirmation Message<span class="glyphicon glyphicon-info-sign text-info infoicon"></span></th>
                        <td><textarea rows="10" cols="50" name="yeem_confirmation_msg"><?php echo esc_textarea( get_option('yeem_confirmation_msg') ); ?></textarea></td>
                    </tr>

                    <tr>
                        <th data-toggle="tooltip" title="This is the body of the acknowledgement email that will be sent to the customer after they submit their message.  HTML tags can be used to format this text.">Email Message<span class="glyphicon glyphicon-info-sign text-info infoicon"></span></th>
                        <td><textarea rows="10" cols="50" name="yeem_email_msg"><?php echo esc_textarea( get_option('yeem_email_msg') ); ?></textarea></td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
    <?php
    }

    public function yeem_display_form_builder_page(){
    ?>

        <div class="wrap">
            <h1>Form Designer - Yeem Contact Form</h1>
            <p><em>How to use: </em> Type the shortcode <strong>[yeem_contact_form]</strong> on the page to display the form.</p>
            <?php
                settings_errors();
                //$ajax_nonce = wp_create_nonce( "yeem-nonce-string" );
            ?>
            <hr />
            <div class="alert alert-success hide">Success! Form saved.</div>
            <div class="holder">
                <div class="left">
                    <h4>Click button to add a field of that type.</h4>
                    <ul id="fieldSelection">
                        <li><a id="add-text" data-type="text" href="#">Text Field</a></li>
                        <li><a id="add-email" data-type="email" href="#">Email Field</a></li>
                        <li><a id="add-date" data-type="date" href="#">Date Field</a></li>
                        <li><a id="add-textarea" data-type="textarea" href="#">Comment Box</a></li>
                        <li><a id="add-select" data-type="select" href="#">Drop Down Selection</a></li>
                        <li><a id="add-radio" data-type="radio" href="#">Radio Buttons</a></li>
                        <li><a id="add-checkbox" data-type="checkbox" href="#">Checkboxes</a></li>
                        <!--<li><a id="add-agree" data-type="agree" href="#">Agree Box</a></li>-->
                    </ul>
                    <span class="clearfix"></span>
                </div>
                <div class="right">
                    <div class="intro">
                        <h3>FORM LAYOUT</h3>
                        <em>We have provided the basic fields by default.</em>
                    </div>

                    <form id="yeemcf" novalidate>
                        <div id="fieldSection" class="ui-sortable">
                        </div>
                        <button type="submit" class="submit">Save Form</button>
                    </form>
                </div>
            </div>
    </div>
    <?php
    }

    public function yeem_save_form() {
        if(update_option( 'yeem_form_elements', $_POST['formfields'])){   echo "success";}
        else {  echo "failed";}
        die();
    }

    public function yeem_get_formelements() {
        echo stripslashes_deep(get_option('yeem_form_elements'));
        die();
    }

}
