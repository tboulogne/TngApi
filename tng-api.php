<?php
/*
 * Plugin Name: tng api 1.3
 * Description: This plugin allows access to the TNG database. For access to TNG pages, tng-wordpress-plugin must be installed and activated 
 *
 * Plugin URI: https://github.com/upavadi/TngApi
 * Version: 1.3
 *         
 * Author: Neel Upadhyaya & Mahesh Upadhyaya
 * Author URI: http://www.upavadi.net/
 * License: MIT Licence http://opensource.org/licenses/MIT
 *
 * URL to the plugin Directory 	
 * <?php echo plugins_url('subdirectory/file', dirname(__FILE__)); ?>
 *
 */
require_once __DIR__ . '/autoload.php';
include_once __DIR__. '/tabs.php';

$content = Upavadi_TngContent::instance();

$content->addShortcode(new Upavadi_Shortcode_FamilySearch);
$content->addShortcode(new Upavadi_Shortcode_PersonNotes);
$content->addShortcode(new Upavadi_Shortcode_FamilyUser);
$content->addShortcode(new Upavadi_Shortcode_FamilyForm);
$content->addShortcode(new Upavadi_Shortcode_AddFamilyForm);
$content->addShortcode(new Upavadi_Shortcode_Birthdays());
$content->addShortcode(new Upavadi_Shortcode_Danniversaries());
$content->addShortcode(new Upavadi_Shortcode_Manniversaries());
$content->addShortcode(new Upavadi_Shortcode_Manniversariesplusone());
//$content->addShortcode(new Upavadi_Shortcode_TngProxy());
$content->addShortcode(new Upavadi_Shortcode_SubmitImage());
$familySearch = new Upavadi_Widget_FamilySearch;

add_action('init', array($content, 'initPlugin'), 1);
add_action('widgets_init', array($familySearch, 'init'));
add_action( 'admin_menu', array($content, 'adminMenu') );
add_action( 'admin_init', array($content, 'initAdmin') );
//add_filter('the_posts', array($content, 'proxyFilter'));

$dir = dirname(__FILE__);
$customDir = $dir . "/../tng-api-custom";
if (is_dir($customDir)) {
    $customContent = new TngApiCustom_TngCustom($content);
}

//ADDTB
add_filter('wp_die_handler', 'swap_die_handlers');
 
function my_custom_die_handler($message, $title='', $args=array())
{
  // Get the path to the custom die template
  $template = plugin_dir_path(__FILE__).'templates/custom_die.php';

  // If not an admin page, and if the custom die template exists
  if( ! is_admin() && file_exists( $template ) )
  {
    // The default response code is 500
    $defaults = array( 'response' => 500 );
 
    // Combine the args and defaults
    $r = wp_parse_args( $args, $defaults );
 
    // Determine what type of error we are dealing with
    if( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) 
    {
      // If no title, try to make one
      if ( empty( $title ) ) 
      {
        $error_data = $message->get_error_data();
 
        if ( is_array( $error_data ) && isset( $error_data['title'] ) )
          $title = $error_data['title'];
      }
 
      // Get the actual errors
      $errors = $message->get_error_messages();
 
      // Depending on how many errors there are, do something
      switch ( count( $errors ) )
      {
        case 0 :
          $message = '';
          break;
        case 1 :
          $message = '<li>' . $errors[0] . '</li>';
          break;
        default :
          $message = '
            <li>' . join( "</li><li>", $errors ) . '</li>
          ';
          break;
      }
 
    }
    else if( is_string( $message ) )
    {
      $message = '<li>' . $message . '</li>';
    }
 
    /**
     * Since wp-comments-post.php does not allow error 
     * messages to be changed, the error message string 
     * is run through str_replace to customize.
     */
    $lame_messages = array(
      'Sorry, comments are closed for this item.',
      'please fill the required fields (name, email).',
      'please enter a valid email address.',
      'please type a comment.'
    );
 
    $replacements = array(
      'Sorry, comments are closed.',
      'All comment form fields are required. Please try again.',
      'Please enter a valid email address.',
      'No comment in your submission. Please try again.'
    );
 
    // Save the original error message for later comparison
    $pre_replacements = $message;
 
    // Do the comment form error message replacements
    $message = str_replace( $lame_messages, $replacements, $message );
 
    if(
      // If the back link was set in the args
      ( isset( $r['back_link'] ) && $r['back_link'] ) OR
 
      // Or if message identified as comment error
      $message !== $pre_replacements
    )
    {
      // Show the back button
      $back_link = '
        <p>
          <a href="javascript:history.back()" class="back">Back</a>
        </p>
      ';
    }
 
    // If the title is empty, set it as "Error"
    if( empty( $title ) )
      $title = 'Error';
 
    require_once( $template );
 
    die();
 
  }
  else
  {
    _default_wp_die_handler( $message, $title, $args );
  }
}
 
// Intermediate function is necessary to customize wp_die
function swap_die_handlers()
{
  return 'my_custom_die_handler';
}

