<?php
add_action( 'wp_enqueue_scripts', 'storefront_child_enqueue_styles' );

function storefront_child_enqueue_styles() {
    $parent_style = 'parent-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}

/* Add the webpack script  - bundle.js - */
add_action('wp_enqueue_scripts', 'storefront_child_scripts');

function storefront_child_scripts() {
    wp_enqueue_script( 'theme_js', get_stylesheet_directory_uri() . '/public/js/bundle.js', array('jquery'), '', true );
}


function newsletter_subscription($atts = array(), $content = null, $tag) {
    
    /* I will use output buffering in order to avoid positioning problems of the shortcode output 
     * that could occur for some Wordpress themes 
     */
    ob_start();
    
    
    /* The shorcode will be used with a parameter called layout, it accept a string that
     * could have the value dark or light, if it is set to dark the newsletter box will show
     * the button with bg-color #333, this can be used in sections with white background like
     * the content of the current page. If layout it's set to light it will display the submit
     * button with white background and all the text of the form will be in black, in this way
     * we can use this shortcode in the footer that has bg-color #333. If nothing is set or
     * it is set something else will be displayed the deafult layout and the submit button
     * will have bg-color #333  
     * */
    $newsletter_subscription_parameters = shortcode_atts(array(
        'layout'=> false,
    ), $atts);
    
    $newsletter_subscription_layout = $newsletter_subscription_parameters['layout'];
    $newsletter_subscription_layout_class = "";
    
    if(isset($newsletter_subscription_layout)&&!empty($newsletter_subscription_layout)){
        switch ($newsletter_subscription_layout) {
            case "dark":
                $newsletter_subscription_layout_class = "newsletter__form_dark_layout";
                break;
            case "light":
                $newsletter_subscription_layout_class = "newsletter__form_light_layout";
                break;
            default:
                $newsletter_subscription_layout_class = "newsletter__form_dark_layout";
        }
    }
    
    /* Since this shortcode will be used at least two times in a page (content + footer), i will create
     * a unique identifier based on the current timstamp plus a random number, and I will assign this identifier
     * to the input for the email address, when I click the submit button the function
     * subscribe() will use this identifier to read the value of the input field inside the form I have clicked on 
     * in order to avoid the problem that it could read the input value somewhere else in the page.
     * I will stop the submit event of the form with onsubmit="return false;" in order to call the subscribe()
     * function that will simulate a "fake" subscription, this function will check if the value inside the input field
     * respect the format of an email address, if yes it will display a successfull subscription messagge,
     * if not it will display an error with the email format.  
     */
    
    $date = new DateTime();
    $identifier = $date->getTimestamp().rand ( 100000, 999999 );
    $formInputId = "form-id" .$identifier;
    $responseId = "response-id" .$identifier;
    ?>
    <div class="newsletter__form_container">
        <form class="newsletter__form <?php echo $newsletter_subscription_layout_class;?>" onsubmit="return false;">
          <label>E-mail:</label><br>
          <input id="<?php echo $formInputId;?>" class="newsletter__textfield" type="text" name="email"><br>
    	  <input class="newsletter__submit" type="submit" value="Submit" onclick="subscribe('<?php echo $identifier; ?>')"><br>
    	  <div id="<?php echo $responseId;?>" class="newsletter__check"></div><br>
    	</form>
	</div>
    <?php
    
    $output_string = ob_get_contents();
    ob_end_clean();
    return $output_string;
}
add_shortcode( 'newsletter-subscription', 'newsletter_subscription' );


/*
 * The action add_birthday_field_to_checkout will create a new text field in the checkout form
 * of woocommerce, I will this field to get the user's date of Birthday 
 */
add_action('woocommerce_before_checkout_billing_form', 'add_birthday_field_to_checkout');

function add_birthday_field_to_checkout($checkout) {
    woocommerce_form_field( 'billing_customer_birthday', array(
        'type'     => 'text',
        'label'    => __( 'Birthday (DD/MM/YYYY)' ),
        'required' => true
    ), $checkout->get_value('billing_customer_birthday') );
    ?>
    <script type="text/javascript">
    
    /* While the user is typing something inside the Birthday fields this script will check if
    the value inserted in the field corresponds to the date format DD/MM/YYYY, it checks if the
    value match with the regular expression of the date, if not it will display an error message,
    if yes it extract the year inserted by the user from the value, it get the current year and
    calculate the age of the user, if this age is < 18 it will display an error to the user, if
    the age is >= 18 it will display a success message */

    jQuery( "<p class='birthday_check'></p>" ).insertAfter( "#billing_customer_birthday" );
    jQuery('#billing_customer_birthday').keyup(function(){	
        value = jQuery('#billing_customer_birthday').val();
        var re = /([0-9]{2})\/([0-9]{2})\/([0-9]{4})/;
        if(!re.test(value)){
        	jQuery('.birthday_check').addClass("birthday_check_error");
        	jQuery('.birthday_check').removeClass("birthday_check_success");
        	jQuery('.birthday_check').text("Wrong date format");
        }else{
        	var data = value.split("/");
        	var year = data[2];
        	var currentyear = new Date().getFullYear();
        	if(currentyear - year<18){
            	jQuery('.birthday_check').addClass("birthday_check_error");
            	jQuery('.birthday_check').removeClass("birthday_check_success");
        		jQuery('.birthday_check').text("You are not allowed to continue the purchase, you must be older than 18 years");
        	} else{
            	jQuery('.birthday_check').removeClass("birthday_check_error");
            	jQuery('.birthday_check').addClass("birthday_check_success");
        		jQuery('.birthday_check').text("Date Format OK");
        	}
        } 
    });
    </script>
    <?php
}


/*
 * The action add_gender_field_to_checkout will create a new text field in the checkout form
 * of woocommerce, I will this field to get the user's gender
 */
add_action('woocommerce_before_checkout_billing_form', 'add_gender_field_to_checkout');

function add_gender_field_to_checkout($checkout) {
    woocommerce_form_field( 'billing_customer_gender', array(
        'type'     => 'text',
        'label'    => __( 'Gender (m/f/x)' ),
        'required' => true
    ), $checkout->get_value('billing_customer_gender') );
    ?>    
    <script type="text/javascript">

    /* The method simply check if the value inserted by the user corresponds to one of the characters: m,f,x
     * if yes it display a success message, if not it display an error
     */
     
    jQuery( "<p class='gender_check'></p>" ).insertAfter( "#billing_customer_gender" );
    jQuery('#billing_customer_gender').keyup(function(){	
        value = jQuery('#billing_customer_gender').val();
        if ( (value=="m")||(value=="f")||(value=="x")){
        	jQuery('.gender_check').removeClass("gender_check_error");
        	jQuery('.gender_check').addClass("gender_check_success");
        	jQuery('.gender_check').text("Gender Field OK");
        	
        }else{
        	jQuery('.gender_check').removeClass("gender_check_success");
        	jQuery('.gender_check').addClass("gender_check_error");
        	jQuery('.gender_check').text("Wrong value for the Gender Field");
        }   
    });
    </script>
    <?php
}

/*
 * Now we have to disable the possibility to proceed with the checkout for users who don't have
 * inserted the right data inside the fields and also for users who put the correct data format
 * but are younger than 18 years. In order to do this we use these two actions gender_field_validation
 * and birthday_field_validation.
 * The action birthday_field_validation will do the same check done by the jQuery function above
 * in case of errors it will display woocommerce NoticeGroup and will not allow to the user to 
 * proceed with the checkout. The same behaviour will be applied by the gender_field_validation action. 
 */
add_action( 'woocommerce_checkout_process', 'birthday_field_validation' );

function birthday_field_validation() {
    if ( isset( $_POST['billing_customer_birthday'] ) && empty( $_POST['billing_customer_birthday'] ) ){
        wc_add_notice( __( 'Please insert your birthday', 'woocommerce' ), 'error' );
    }else{
        $regex="/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/";
        $check = preg_match($regex,$_POST['billing_customer_birthday']);
        if (!$check) {
            wc_add_notice( __( 'Wrong date format: '.$_POST['billing_customer_birthday'], 'woocommerce' ), 'error' );
        }else{
            $customerdata = explode("/", $_POST['billing_customer_birthday']);
            $customeryear = intval($customerdata[2]);
            $currentyear = intval(date("Y"));
            if($currentyear-$customeryear<18){
                wc_add_notice( __( 'You are not allowed to continue the purchase, you must be older than 18 years', 'woocommerce' ), 'error' );
            }
        }
    }
}

add_action( 'woocommerce_checkout_process', 'gender_field_validation' );

function gender_field_validation() {
    $gender=$_POST['billing_customer_gender'];
    $test1 = strcmp($gender,"m");
    $test2 = strcmp($gender,"f");
    $test3 = strcmp($gender,"x");
    if ( ($test1==0)||($test2==0)||($test3==0)){
        /* The field is fine */
    }else{
        wc_add_notice( __( 'Please insert a correct value for the Gender field', 'woocommerce' ), 'error' );
    }
}