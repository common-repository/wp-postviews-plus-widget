<?php
/*
Plugin Name: WP-PostViews-Plus Widget
Plugin URI: http://www.gofunnow.com/wordpress/plugins/wp-postviews-plus-widget.htm
Description: This is a widget based on WP-PostViews Plus plugin by Richer Yang (http://wordpress.org/extend/plugins/wp-postviews-plus). Adds a WP-PostViews Plus widget to display most viewed posts and/or pages By User Or Bot on your sidebar.
Version: 1.0
Author: flyaga li
Author URI: http://www.gofunnow.com/
*/

/*
  * WP-PostViews-Plus-Widget
  * Copyright (C) 2007-2009 flyaga li
  * 
  * This program is free software; you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation; either version 2 of the License, or
  * (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License along
  * with this program; if not, write to the Free Software Foundation, Inc.,
  * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
  */

// We're putting the plugin's functions in one big function we then
// call at 'plugins_loaded' (add_action() at bottom) to ensure the
// required Sidebar Widget functions are available.
function widget_postview_plus_mostpopular_init() {

    // Check to see required Widget API functions are defined...
    if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
        return; // ...and if not, exit gracefully from the script.

    // This function prints the sidebar widget--the cool stuff!
    function widget_postview_plus_mostpopular($args) {

        // $args is an array of strings which help your widget
        // conform to the active theme: before_widget, before_title,
        // after_widget, and after_title are the array keys.
        extract($args);
		
		// Collect our widget's options, or define their defaults.
		$options = get_option('widget_postview_plus_mostpopular');
		$title = empty($options['title']) ? __('Popular Posts') : $options['title'];
		if ( !$number = (int) $options['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;

         // It's important to use the $before_widget, $before_title,
         // $after_title and $after_widget variables in your output.
        echo $before_widget;
        echo $before_title . $title . $after_title;
		echo '<ul>';
        if(is_single())
        {global $wp_query;
          $thiscategories = get_the_category($wp_query->queried_object->ID);
          if ( empty( $thiscategories ) )
            get_most_viewed('post', $number, 0, true, false);
          else
          {
            foreach ( $thiscategories as $thiscategory )
            {
              get_most_viewed_category($thiscategory->term_id,'post', $number, 0, true, false);
              break;
            }
          }
        }
        else
          get_most_viewed('post', $number, 0, true, false);
		echo '</ul>';
        echo '<a style="font-size: 9px;" href="http://www.gofunnow.com" target=_blank>By gofunnow</a>';
        echo $after_widget;
    }
	
	// This is the function that outputs the form to let users edit
    // the widget's title and so on. It's an optional feature, but
    // we'll use it because we can!
    function widget_postview_plus_mostpopular_control() {
		
		// Collect our widget options.
		$options = $newoptions = get_option('widget_postview_plus_mostpopular');
		
		// This is for handing the control form submission.
		if ( $_POST['postview_plus_mostpopular-submit'] ) {
		// Clean up control form submission options
		$newoptions['title'] = strip_tags(stripslashes($_POST['postview_plus_mostpopular-title']));
		$newoptions['number'] = strip_tags(stripslashes($_POST['postview_plus_mostpopular-number']));
		}
		
		// If original widget options do not match control form
        // submission options, update them.
        if ( $options != $newoptions ) {
            $options = $newoptions;
            update_option('widget_postview_plus_mostpopular', $options);
		}
		
		$title = attribute_escape($options['title']);
		if ( !$number = (int) $options['number'] )
			$number = 10;
?>
		<p><label for="postview_plus_mostpopular-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="postview_plus_mostpopular-title" name="postview_plus_mostpopular-title" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="postview_plus_mostpopular-number"><?php _e('Number of posts to show:'); ?> <input style="width: 25px; text-align: center;" id="postview_plus_mostpopular-number" name="postview_plus_mostpopular-number" type="text" value="<?php echo $number; ?>" /></label> <?php _e('(at most 15)'); ?></p>
		<input type="hidden" id="postview_plus_mostpopular-submit" name="postview_plus_mostpopular-submit" value="1" />
    <?php
    // end of widget_mywidget_control()
    }
	
    // This registers the widget.
    register_sidebar_widget('postview_plus Most Popular Post', 'widget_postview_plus_mostpopular');
	
	// This registers the (optional!) widget control form.
    register_widget_control('postview_plus Most Popular Post', 'widget_postview_plus_mostpopular_control');
}

// Delays plugin execution until Dynamic Sidebar has loaded first.
add_action('plugins_loaded', 'widget_postview_plus_mostpopular_init'); 
?>