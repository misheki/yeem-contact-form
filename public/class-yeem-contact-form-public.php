<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       www.michelleanneyee.com
 * @since      1.0.0
 *
 * @package    Yeem_Contact_Form
 * @subpackage Yeem_Contact_Form/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Yeem_Contact_Form
 * @subpackage Yeem_Contact_Form/public
 * @author     Michelle Anne Yee <mishi@michelleanneyee.com>
 */
class Yeem_Contact_Form_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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
        //wp_enqueue_style('bootstrap-style','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css');
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/yeem-contact-form-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
				wp_register_script( 'jquery-validate-script', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js' , array( 'jquery'));
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/yeem-contact-form-public.js', array('jquery-validate-script' , 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery'), $this->version, false );
        wp_localize_script( $this->plugin_name, 'yeemScriptObj', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'pluginsUrl' => plugins_url('', dirname(__FILE__)) ) );

	}


    //YEEM

		public function yeem_register_session(){
			if(!session_id()) session_start();
		}

    private function yeem_display_form() {

        //Captcha
        //session_start();
        $addend1 = mt_rand(1,10);
        $addend2 = mt_rand(1,10);
        $equation = "$addend1 + $addend2";
        $_SESSION['sum'] = $addend1 + $addend2;
        $bSourceExists = false;

        //Source
        if(isset($_POST['page_title']) && $_POST['page_title'] != "")
        {
            $pt = $_POST['page_title'];
            $bSourceExists = true;
        }

        ?>
        <form id="mycontactform" class="contactform" novalidate>

            <?php echo ($bSourceExists) ?
                "<p><input type='text' name='visible_source_page' value='Source: $pt' readonly><input type='hidden' name='source_page' value='$pt' readonly></p>" : "";
            ?>

            <div id="formarea">


            </div>

            <div class="captchaSection">
							<span>Answer if you are not a robot.</span><br><br>
							<?php echo $equation; ?> = <input id="captchaAns" name="sum" type="text" size="2"/>
            </div>

            <p>
                <input type="submit" id="mycontactsubmit" name="submitted" class="contact-submit" value="Send">
            </p>
        </form>
				<div id="ConfirmationMsg" class="hide"></div>

    <?php
    }

    public function yeem_wp_mail_from( $original_email_address ) {
        return esc_attr( get_option('yeem_sender_email_address'));
    }

    public function yeem_wp_mail_from_name( $original_email_from ) {
        return esc_attr( get_option('yeem_your_name'));
    }

    public function yeem_set_content_type(){
        return "text/html";
    }

    public function yeem_sendmail() {

        $subject = esc_attr( get_option('yeem_email_subject'));
        $to   = get_option(yeem_sender_email_address) . ", " . sanitize_email( $_POST["email_to"] );
        $source_page = sanitize_text_field( $_POST["source_page"] );
        $sum = sanitize_text_field( $_POST['captcha_sum'] );
        //captcha
        //session_start();

        if ($_SESSION['sum'] == $sum ) {

            $message = get_option('yeem_email_msg') .
                " <br><br><br>" .
                    "<table style='border: 2px solid #000000;'><th colspan='2' style='padding:5px; border: 2px solid #000000;'>Customer Data</th>";

            /*$message = get_option('yeem_email_msg') .
                " <br><br><br>" .
                    "<table style='border: 2px solid #000000;'><th colspan='2' style='padding:5px; border: 2px solid #000000;'>Requirements</th>" .
                    "<tr><td style='padding:5px; border: 2px solid #000000; width:30%;'><b>SOURCE</b></td><td style='padding:5px; border: 2px solid #000000; width:60%; word-wrap:break-word;'> " . $source_page . "</td></tr>"; */

            $fields = $_POST['fields'];

            foreach($fields as $field)
            {
                $message .= "<tr><td style='padding:5px; border: 2px solid #000000; width:30%;'><b>" . sanitize_text_field($field['name']) .
                    " </b></td><td style='padding:5px; border: 2px solid #000000; width:60%; word-wrap:break-word;'> " . sanitize_text_field($field['value']) . "</td></tr>";
            }

            $message .= "</table>";

            //$headers = "From: $name <$email>" . "\r\n";

            // If email has been process for sending, display a success message
            if ( wp_mail( $to, $subject, $message, $headers ) ) {
                echo '<div>';
                echo esc_attr( get_option('yeem_confirmation_msg'));
                echo '</div>';
            } else {
                echo 'An unexpected error occurred';
            }
						exit();
        }
        else {
            //captcha is incorrect
						echo "Verification value is incorrect.";
            exit();
        }
    }

    public function yeem_my_shortcode() {
        ob_start();
        $this->yeem_display_form();
        return ob_get_clean();
    }

    public function yeem_get_formelements() {
        echo stripslashes_deep(get_option('yeem_form_elements'));
        die();
    }

}
