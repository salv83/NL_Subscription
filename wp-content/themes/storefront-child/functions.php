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