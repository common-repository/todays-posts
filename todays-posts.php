<?php
/**
 * Plugin Name: Todays Posts
 * Plugin URI: http://www.masud-rana.com/plugins/todays-posts/
 * Description: Displays posts but only from the current day.
 * Version: 1.0
 * Author: Masud Rana
 * Author URI: http://www.masud-rana.com
 * License: GPLv2 or later
 */

/*  Copyright 2014  Md Masud Rana  (email : masud.rana.ah@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('TP_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );

$tpphorver = my_get_option( 'tppverhor', 'tpp_settings', 'vertically' );

if ($tpphorver == 'vertically') {
       wp_enqueue_style('tp_css', TP_PATH.'css/ver-styles.css');      
} else {
       wp_enqueue_style('tpp_css', TP_PATH.'css/hor-styles.css');
} 

function todays_posts_function ($content) {

global $more;    

$tpptitle = my_get_option( 'title', 'tpp_settings', 'TODAY\'S POSTS' );
$tppnumposts = my_get_option( 'numposts', 'tpp_settings', '' );
$tppthmb = my_get_option( 'tdppthmb', 'tpp_settings', 'enable' );
$tppdup = my_get_option( 'tppdup', 'tpp_settings', 'yes' );
$tppexcer = my_get_option( 'tdppexcer', 'tpp_settings', 'yes' );
$tppcat = my_get_option( 'cat', 'tpp_settings', '' );
$tpptitsiz = my_get_option( 'titsiz', 'tpp_settings', '' );
$tpptitcol = my_get_option( 'titcol', 'tpp_settings', '' );
$tppptitsiz = my_get_option( 'ptitsiz', 'tpp_settings', '' );
$tppptitcol = my_get_option( 'ptitcol', 'tpp_settings', '' );
$tpppexsiz = my_get_option( 'pexsiz', 'tpp_settings', '' );
$tpppexcol = my_get_option( 'pexcol', 'tpp_settings', '' );
$tppalttext = my_get_option( 'alttext', 'tpp_settings', 'No any post published yet today' );

$currentId = get_the_ID();
$today = getdate(); 
$loop = new WP_Query(array('year' => $today["year"], 'monthnum' => $today["mon"], 'day' => $today["mday"], 'post__not_in' => array($currentId), 'posts_per_page' => $tppnumposts, 'category_name'=> $tppcat ));

if ( is_single() && $tppdup == 'yes' ) {
 $content .= '<div class=tpdv><h3 style="font-size:'.$tpptitsiz.'; color:'.$tpptitcol.';">'.$tpptitle.'</h3><ul>';  
   if($loop->have_posts()) : 
      while($loop->have_posts()) : 
            $loop->the_post(); 
   $more = 0;

if ($tppthmb == 'enable' && $tppexcer == 'yes') {
   $content .= '<li><div class="tdppThumb"><a href="'.get_permalink().'">'.get_the_post_thumbnail($post_id, array(110,110) ).'</a></div><div class="tdppCont"><a style="color:'.$tppptitcol.';font-size:'. $tppptitsiz.';" href="'.get_permalink().'">'.get_the_title().'</a><br /><div style="color:'.$tpppexcol.';font-size:'.$tpppexsiz.';">'.get_the_content('...read more →').'</div></div></li>';
} elseif ($tppthmb == 'enable' && $tppexcer == 'no') {
   $content .= '<li><div class="tdppThumb"><a href="'.get_permalink().'">'.get_the_post_thumbnail($post_id, array(110,110) ).'</a></div><div class="tdppCont"><a style="color:'.$tppptitcol.';font-size:'.$tppptitsiz.';" href="'.get_permalink().'">'.get_the_title().'</a></div></li>';
} elseif ($tppthmb == 'disable' && $tppexcer == 'yes') {
   $content .= '<li ><div class="tdppCont"><a style="color:'.$tppptitcol.';font-size:'.$tppptitsiz.';" href="'.get_permalink().'">'.get_the_title().'</a><br /><div style="color:'.$tpppexcol.';font-size:'.$tpppexsiz.';">'.get_the_content('...read more →').'</div></div></li>';
} elseif ($tppthmb == 'disable' && $tppexcer == 'no') {
   $content .= '<li><div class="tdppCont"><a style="color:'.$tppptitcol.';font-size:'.$tppptitsiz.';" href="'.get_permalink().'">'.get_the_title().'</a></div></li>';
} else {
   $content .= '<li><div class="tdppThumb"><a href="'.get_permalink().'">'.get_the_post_thumbnail($post_id, array(110,110) ).'</a></div><div class="tdppCont"><a style="color:'.$tppptitcol.';font-size:'. $tppptitsiz.';" href="'.get_permalink().'">'.get_the_title().'</a><br /><div style="color:'.$tpppexcol.';font-size:'.$tpppexsiz.';">'.get_the_content('...read more →').'</div></div></li>';
}

endwhile; 

else:

$content .= '<p>'.$tppalttext.'</p>';

endif;

 wp_reset_query(); 

$content .= '</ul></div>';

}

return $content;

}
add_filter( 'the_content', 'todays_posts_function' );


//Removing read more link scroll
function remove_more_link_scroll( $link ) {
    $link = preg_replace( '|#more-[0-9]+|', '', $link );
    return $link;
}
add_filter( 'the_content_more_link', 'remove_more_link_scroll' );



//shortcode

function todays_post_shortcode ($atts) {
   extract(shortcode_atts(array(
      'categories' => '',
      'posts' => null,
      'horizontal' =>'',
      'excerpt' => '',
      'thumbnail' =>''
   ), $atts));

wp_enqueue_style('tppp_css', TP_PATH.'css/stcde-styles.css');   
global $more;  
$today = getdate(); 
$loopst = new WP_Query(array('year' => $today["year"], 'monthnum' => $today["mon"], 'day' => $today["mday"], 'posts_per_page' => $posts, 'category_name'=> $categories ));
 
 if ($horizontal) { 
 $tpscontent = '<div class="tppsch"><ul>'; 
} else {
 $tpscontent = '<div class="tppsc"><ul>';
}

   if($loopst->have_posts()) : 
      while($loopst->have_posts()) : 
            $loopst->the_post(); 
   $more = 0; 

if ($horizontal && $excerpt && $thumbnail) {
      $tpscontent .= '<li><a href="'.get_permalink().'">'.get_the_post_thumbnail($post_id, array(110,110) ).'</a>
<a href="'.get_permalink().'">'.get_the_title().'</a><br />'.get_the_content('...read more →').'</li>';
  } elseif ($horizontal && $excerpt) {
      $tpscontent .= '<li><a href="'.get_permalink().'">'.get_the_title().'</a><br />'.get_the_content('...read more →').'</li>';
  } elseif ($horizontal && $thumbnail) {
  $tpscontent .= '<li><a href="'.get_permalink().'">'.get_the_post_thumbnail($post_id, array(110,110) ).'</a>
<a href="'.get_permalink().'">'.get_the_title().'</a></li>';
  } elseif ($horizontal) {
      $tpscontent .= '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
  } elseif ($excerpt && $thumbnail) {
  $tpscontent .= '<li><a href="'.get_permalink().'">'.get_the_post_thumbnail($post_id, array(110,110) ).'</a>
<a href="'.get_permalink().'">'.get_the_title().'</a><br />'.get_the_content('...read more →').'</li>';
  } elseif ($excerpt) {
  $tpscontent .= '<li><a href="'.get_permalink().'">'.get_the_title().'</a><br />'.get_the_content('...read more →').'</li>';
  } elseif ($thumbnail) {
  $tpscontent .= '<li><a href="'.get_permalink().'">'.get_the_post_thumbnail($post_id, array(110,110) ).'</a>
<a href="'.get_permalink().'">'.get_the_title().'</a></li>';
  } else {
    $tpscontent .= '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
}
 
endwhile; 

endif;

wp_reset_query(); 

$tpscontent .= '</ul></div>';

return $tpscontent;
}

add_shortcode ('todaysposts', 'todays_post_shortcode');

add_filter('widget_text', 'do_shortcode');


// Settings

require_once dirname( __FILE__ ) . '/class.settings-api.php';

if ( !class_exists('TP_Settings_API' ) ):
class TP_Settings_API {

    private $settings_api;

    function __construct() {
        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page( 'Todays Posts', 'Todays Posts', 'manage_options', 'todays_posts', array($this, 'plugin_page') );
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'tpp_settings',
                'title' => __( 'Settings', 'tpp' )
            ),
            array(
                'id' => 'tpp_support',
                'title' => __( 'Support', 'tpp' )
            )
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'tpp_settings' => array(
                array(
                    'name' => 'title',
                    'label' => __( 'Title', 'tpp' ),
                    'type' => 'text',
                    'default' => "TODAY'S POSTS"
                ),
                array(
                    'name' => 'tppverhor',
                    'label' => __( 'Display', 'tpp' ),
                    'default' => 'vertically',
                    'type' => 'radio',
                    'options' => array(
                        'vertically' => __('Vertically', 'tpp'),
                        'horizontally' => __('Horizontally', 'tpp')
                    )
                ),
                array(
                    'name' => 'tppdup',
                    'label' => __( 'Display under posts', 'tpp' ),
                    'default' => 'yes',
                    'type' => 'radio',
                    'options' => array(
                        'yes' => __('Yes', 'tpp'),
                        'no' => __('No', 'tpp')
                    )
                ),
                array(
                    'name' => 'numposts',
                    'label' => __( 'Number of posts to display', 'tpp' ),
                    'desc' => __( 'If you want to display all the todays posts, leave this field blank.', 'tpp' ),
                    'type' => 'text'
                ),
                array(
                    'name' => 'tdppthmb',
                    'label' => __( 'Thumbnail', 'tpp' ),
                    'default' => 'enable',
                    'type' => 'radio',
                    'options' => array(
                        'enable' => __('Enable', 'tpp'),
                        'disable' => __('Disable', 'tpp')
                    )
                ),
                array(
                    'name' => 'tdppexcer',
                    'label' => __( 'Display post excerpt', 'tpp' ),
                    'default' => 'yes',
                    'type' => 'radio',
                    'options' => array(
                        'yes' => __('Yes', 'tpp'),
                        'no' => __('No', 'tpp')
                    )
                ),
                array(
                    'name' => 'cat',
                    'label' => __( 'Categories to display', 'tpp' ),
                    'desc' => __( 'Ex: tree,bird,fish (If you want to display todays posts from all the categories, leave this field blank) ', 'tpp' ),
                    'type' => 'text'
                ),
                array(
                    'name' => 'titsiz',
                    'label' => __( 'Title font size', 'tpp' ),
                    'desc' => __( 'Ex: 22px', 'tpp' ),
                    'type' => 'text'
                ),
                array(
                    'name' => 'titcol',
                    'label' => __( 'Title text color', 'tpp' ),
                    'type' => 'color'
                ),
                array(
                    'name' => 'ptitsiz',
                    'label' => __( 'Post title font size', 'tpp' ),
                    'desc' => __( 'Ex: 16px', 'tpp' ),
                    'type' => 'text'
                ),
                array(
                    'name' => 'ptitcol',
                    'label' => __( 'Post title text color', 'tpp' ),
                    'type' => 'color'
                ),
                array(
                    'name' => 'pexsiz',
                    'label' => __( 'Post excerpt font size', 'tpp' ),
                    'desc' => __( 'Ex: 16px', 'tpp' ),
                    'type' => 'text'
                ),
                array(
                    'name' => 'pexcol',
                    'label' => __( 'Post excerpt text color', 'tpp' ),
                    'type' => 'color'
                ),
                array(
                    'name' => 'alttext',
                    'label' => __( 'Alternative text', 'tpp' ),
                    'desc' => __( 'Text to display when no any post has been posted today', 'tpp' ),
                    'type' => 'text',
                    'default' => 'No any post published yet today'
                )
            ),
            'tpp_support' => array(
                array(
                    'name' => 'file',
                    'type' => 'html',
                    'desc' => '
                         <h2>Usage</h2>
                         <p>Click <a href="http://www.masud-rana.com/plugins/todays-posts/" " target="_blank">here</a> to know how to use the plugin.</p><br /><br />

                         <h2> Support Forum</h2>
                         <p>If you need any helps, please use this <a href="http://www.masud-rana.com/forums/" " target="_blank">Support Forum</a>.</p><br /><br />

                         <h2> Further help</h2>
                         <p>You may need to customize or add new feature(s) in the plugin. You can hire me at a
                                very reasonable rate. Please use this <a href="http://www.masud-rana.com/hire-me/" " target="_blank">form</a> to contact me.</p>
                                '
                )
            ),
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}
endif;

$settings = new TP_Settings_API();


//Setting page link
function add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=todays_posts">Settings</a>';
    array_push( $links, $settings_link );
    return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'add_settings_link' );


//Retrieving the values
function my_get_option( $option, $section, $default = '' ) {
 
    $options = get_option( $section );
 
    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }
 
    return $default;
}

?>