<?php
/*
Plugin Name: My Cool Widget Plugin
Plugin URI: http://www.wpexplorer.com/create-widget-plugin-wordpress/
Description: This plugin adds a custom widget, creates a settings page to control global settings of our widgets.
Version: 1.0
Author: Abhay Sharma
Author URI: https://www.linkedin.com/in/abhaynanak/
License: GPL2
Text Domain: mcwp
*/

/**
* Building our custom Widget
*/
// The widget class
class My_Cool_Widget extends WP_Widget
{

  // Main constructor
  public function __construct()
  {
    parent::__construct(
      'my_cool_widget'
      ,__( 'My Cool Widget', 'mcwp' )
      ,array(
        'customize_selective_refresh' => true
      )
    );
  }

  // The widget form (for the backend )
  public function form( $instance )
  {
    // Set widget defaults
    $defaults = array(
      'title'     => ''
      ,'btnText'  => ''
      ,'btnLink'  => ''
      ,'newWindow'=> ''
    );

    // Parse current settings with defaults
    extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

    <?php // Widget Title ?>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'mcwp' ); ?></label>
      <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>

    <?php // Text Field ?>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'btnText' ) ); ?>"><?php _e( 'Button Text:', 'mcwp' ); ?></label>
      <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'btnText' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'btnText' ) ); ?>" type="text" value="<?php echo esc_attr( $btnText ); ?>" />
    </p>

    <?php // Text Field ?>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'btnLink' ) ); ?>"><?php _e( 'Button Link:', 'mcwp' ); ?></label>
      <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'btnLink' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'btnLink' ) ); ?>" type="url" value="<?php echo esc_attr( $btnLink ); ?>" />
    </p>

    <?php // Checkbox ?>
    <p>
      <input id="<?php echo esc_attr( $this->get_field_id( 'newWindow' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'checkbox' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $newWindow ); ?> />
      <label for="<?php echo esc_attr( $this->get_field_id( 'newWindow' ) ); ?>"><?php _e( 'Open link in new window.', 'mcwp' ); ?></label>
    </p>
  <?php
  }

  // Update widget settings
  public function update( $new_instance, $old_instance )
  {
    $instance = $old_instance;
    $instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
    $instance['btnText']     = isset( $new_instance['btnText'] ) ? wp_strip_all_tags( $new_instance['btnText'] ) : '';
    $instance['btnLink'] = isset( $new_instance['btnLink'] ) ? esc_url_raw( $new_instance['btnLink'] ) : '';
    $instance['newWindow'] = isset( $new_instance['checkbox'] ) ? 1 : false;
    return $instance;
  }

  // Display the widget
  public function widget( $args, $instance )
  {
    extract( $args );

    // Check the widget options
    $title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
    $btnText  = isset( $instance['btnText'] ) ? $instance['btnText'] : '';
    $btnLink  = isset( $instance['btnLink'] ) ?$instance['btnLink'] : '';
    $newWindow= ! empty( $instance['newWindow'] ) ? $instance['newWindow'] : false;

    // WordPress core before_widget hook (always include )
    echo $before_widget;

    // Display the widget
    echo '<div class="widget-text wp_widget_plugin_box">';

      // Display widget title if defined
      if ( $title )
      {
        echo $before_title . $title . $after_title;
      }

      // Display the button
      if ( $btnText )
      {
        $button = '<a class="widget-button '.get_option('widget_button_theme').'" href="'.$btnLink.'"';
        if($newWindow)
        {
          $button .= ' target="_blank" ';
        }
        $button .= '>' . $btnText . '</a>';
        echo $button;
      }

    echo '</div>';

    // WordPress core after_widget hook (always include )
    echo $after_widget;
  }

}

// Register the widget
function register_my_cool_widget()
{
	register_widget( 'My_Cool_Widget' );
}
add_action( 'widgets_init', 'register_my_cool_widget' );

/**
*  Let's add styles to our plugin
*/
function add_my_stylesheet()
{
    wp_enqueue_style( 'my-cool-plugin-styles', plugins_url( '/css/my-cool-plugin-styles.css', __FILE__ ) );
}

add_action('wp_enqueue_scripts', 'add_my_stylesheet');

/**
*  Let's create a settings page for this plugin
*/

// create custom plugin settings menu
add_action('admin_menu', 'my_cool_plugin_create_menu');

function my_cool_plugin_create_menu() {

	//create new top-level menu
	add_menu_page('My Cool Plugin Settings', 'Cool Settings', 'administrator', __FILE__, 'my_cool_plugin_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_my_cool_plugin_settings' );
}


function register_my_cool_plugin_settings() {
	//register our settings
	register_setting( 'my-cool-plugin-settings-group', 'widget_button_theme' );
}

function my_cool_plugin_settings_page() {
?>
<div class="wrap">
<h1>Your Plugin Name</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'my-cool-plugin-settings-group' ); ?>
    <?php do_settings_sections( 'my-cool-plugin-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
          <th scope="row">Widget Theme</th>
          <td>
            <label for="dark">
              <input type="radio" name="widget_button_theme" value="dark" id="dark" <?php if(esc_attr( get_option('widget_button_theme') ) == "dark"){echo "checked"; } ?> />Dark
            </label>
            <label for="light">
              <input type="radio" name="widget_button_theme" value="light" id="light" <?php if(esc_attr( get_option('widget_button_theme') ) == "light"){ echo "checked"; } ?> />Light
            </label>
          </td>
        </tr>
    </table>

    <?php submit_button(); ?>

</form>
</div>
<?php }
