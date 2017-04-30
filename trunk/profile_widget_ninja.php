<?php
/*
Plugin Name: Profile Widget Ninja
Plugin URI: http://pankajgurudeb.blogspot.com/2016/01/profile-widget-ninja-wordpress-plugin.html
Description: Profile Widget Ninja is a full featured profile display widget plugin for WordPress. It is user-friendly and easily customizable.
Text Domain: profile-widget-ninja
Author: Pankaj Kumar Mondal
Author URI: http://pankajgurudeb.blogspot.com
Tags: profile, widget, widgets, about, shortcode, me, user, social, about me, about me widget, aboutme, my profile, details, quick view, sidebar, simple, link, users, ninja, custom, color, colour, customize
Version: 3.1
License: GPLv2 or later.
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Profile_Widget_Ninja')) {
    class Profile_Widget_Ninja extends WP_Widget {

        /**
         * Constructor function
         *
         * @param none
         */
        public function __construct() {
            parent::__construct(
                'profile_widget_ninja', 
                __('Profile Widget Ninja', 'profile-widget-ninja'),
                array('description' => __('Display your profile.', 'profile-widget-ninja'))
            );
            add_shortcode('profile_widget_ninja', array($this, 'pwn_shortcode_handler'));
            add_action('in_widget_form', array($this, 'show_shortcode_for_widget'), 10, 3);
        }

        /**
         * Shortcode handler function
         *
         * @param array $atts Shortcode attributes
         * @param null $content
         */
        public function pwn_shortcode_handler( $attrs, $content = null ) {
            $attrs['echo'] = false;
            return $this->parse_shortcode( $attrs );
        }

        /**
         * Get all widgets as key value map
         *
         * @param null
         */
        function get_all_widgets_mapping() {
            $sidebars_widgets = wp_get_sidebars_widgets();
            $all_widgets_map = array();
            if (!empty($sidebars_widgets)) {
                foreach($sidebars_widgets as $position => $widgets) {
                    if(!empty($widgets)) {
                        foreach($widgets as $widget) {
                            $all_widgets_map[$widget] = $position;
                        }
                    }
                }
            }
            return $all_widgets_map;
        }

        /**
         * Displays Ninja Profile using shortcode
         *
         * @param atts $args
         */
        function parse_shortcode($args) {
            if (is_admin()) {
                return '';
            }

            global $wp_registered_sidebars, $wp_registered_widgets;

            extract(shortcode_atts(array(
                'id' => '',
                'title' => true,
                'container_tag' => 'div',
                'container_class' => 'widget %2$s',
                'container_id' => '%1$s',
                'title_tag' => 'h2',
                'title_class' => 'widgettitle',
                'echo' => true
            ), $args));

            $widget_args = shortcode_atts( array(
                'before_widget' => '<' . $container_tag . ' id="' . $container_id . '" class="' . $container_class . '">',
                'before_title' => '<' . $title_tag . ' class="' . $title_class . '">',
                'after_title' => '</' . $title_tag . '>',
                'after_widget' => '</' . $container_tag . '>',
            ), $args );
            // extract values
            extract( $widget_args );

            // If $id is not present, return
            if( empty( $id ) || ! isset( $wp_registered_widgets[$id] ) ) {
                return;
            }

            // get the widget instance options
            preg_match( '/(\d+)/', $id, $number );
            $instance_options = ( ! empty( $wp_registered_widgets ) && ! empty( $wp_registered_widgets[$id] ) ) ? get_option( $wp_registered_widgets[$id]['callback'][0]->option_name ) : array();
            $instance = isset( $instance_options[$number[0]] ) ? $instance_options[$number[0]] : array();
            $class = get_class( $wp_registered_widgets[$id]['callback'][0] );

            // Stop operation if the widget is removed or de-registered
            if( ! $class ) {
                return;
            }

            // build the widget args that needs to be filtered through dynamic_sidebar_paramsd
            $params = array(
                0 => array(
                    'name' => '',
                    'id' => '',
                    'description' => '',
                    'before_widget' => $before_widget,
                    'before_title' => $before_title,
                    'after_title' => $after_title,
                    'after_widget' => $after_widget,
                    'widget_id' => $id,
                    'widget_name' => $wp_registered_widgets[$id]['name']
                ),
                1 => array(
                    'number' => $number[0]
                )
            );

            // Use sidebar's parameters
            $widgets_map = $this->get_all_widgets_mapping();
            if( isset( $widgets_map[$id] ) ) {
                $params[0]['name'] = $wp_registered_sidebars[ $widgets_map[$id] ]['name'];
                $params[0]['id'] = $wp_registered_sidebars[ $widgets_map[$id] ]['id'];
                $params[0]['description'] = $wp_registered_sidebars[ $widgets_map[$id] ]['description'];
            }

            $params = apply_filters( 'dynamic_sidebar_params', $params );

            $show_title = ( '0' === $title || 'no' === $title || false === $title ) ? false : true;
            if ( ! $show_title ) {
                $params[0]['before_title'] = '<!-- pwn_shortcode_before_title -->';
                $params[0]['after_title'] = '<!-- pwn_shortcode_after_title -->';
            } elseif ( is_string( $title ) && strlen( $title ) > 0 ) {
                $instance['title'] = $title;
            }

            $substitute_classname = '';
            foreach ( (array) $wp_registered_widgets[$id]['classname'] as $classname ) {
                if ( is_string( $classname ) ) {
                    $substitute_classname .= '_' . $classname;
                } elseif ( is_object($classname) ) {
                    $substitute_classname .= '_' . get_class( $classname );
                }
            }
            $substitute_classname = ltrim( $substitute_classname, '_' );
            $params[0]['before_widget'] = sprintf( $params[0]['before_widget'], $id, $substitute_classname );

            ob_start();
            echo '<!-- Profile Widget Ninja -->';
            the_widget( $class, $instance, $params[0] );
            echo '<!-- /Profile Widget Ninja -->';
            $content = ob_get_clean();

            if (!$show_title) {
                $content = preg_replace('/<!-- pwn_shortcode_before_title -->(.*?)<!-- pwn_shortcode_after_title -->/', '', $content);
            }

            if ($echo !== true) {
                return $content;
            }

            echo $content;
        }
        /**
         * Admin widget options
         *
         * @param array $instance The widget options
         */
        public function form($instance) {
            $selected = 'selected = "selected"';
            $checked = 'checked = "checked"';
            ?>
            <div class="profile-widget-ninja-form">
                <!-- Title -->
                <p>
                    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'profile-widget-ninja') ?></label>
                    <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" class="widefat" value="<?php if (isset ($instance['title'])) { echo esc_attr($instance['title']); } ?>" />
                </p>

                <!-- Layout alignment -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_alignment'); ?>"><?php _e('Layout alignment:', 'profile-widget-ninja'); ?></label>
                    <select id="<?php echo $this->get_field_id('pwn_alignment'); ?>" name="<?php echo $this->get_field_name('pwn_alignment'); ?>">
                        <option value='center' <?php if (isset($instance['pwn_alignment']) && $instance['pwn_alignment'] == 'center') { echo $selected; } ?> ><?php _e('Center', 'profile-widget-ninja');?></option>
                        <option value='left' <?php if (isset($instance['pwn_alignment']) && $instance['pwn_alignment'] == 'left') { echo $selected; } ?> ><?php _e('Left', 'profile-widget-ninja');?></option>
                        <option value='right' <?php if (isset($instance['pwn_alignment']) && $instance['pwn_alignment'] == 'right') { echo $selected; } ?> ><?php _e('Right', 'profile-widget-ninja');?></option>
                    </select>
                </p>

                <!-- Layout style -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_layout_style'); ?>"><?php _e('Layout style:', 'profile-widget-ninja'); ?></label>
                    <select id="<?php echo $this->get_field_id('pwn_layout_style'); ?>" name="<?php echo $this->get_field_name('pwn_layout_style'); ?>">
                        <option value='style1' <?php if (isset($instance['pwn_layout_style']) && $instance['pwn_layout_style'] == 'style1') { echo $selected; } ?> ><?php _e('Style 1 (Desert Bluebells)', 'profile-widget-ninja');?></option>
                        <option value='style2' <?php if ( isset($instance['pwn_layout_style']) && $instance['pwn_layout_style'] == 'style2') { echo $selected; } ?> ><?php _e('Style 2 (White Amaryllis)', 'profile-widget-ninja');?></option>
                        <option value='style3' <?php if ( isset($instance['pwn_layout_style']) && $instance['pwn_layout_style'] == 'style3') { echo $selected; } ?> ><?php _e('Style 3 (Silky Salvia)', 'profile-widget-ninja');?></option>
                        <option value='style4' <?php if ( isset($instance['pwn_layout_style']) && $instance['pwn_layout_style'] == 'style4') { echo $selected; } ?> ><?php _e('Style 4 (Gerbera Daisy)', 'profile-widget-ninja');?></option>
                        <option value='style5' <?php if ( isset($instance['pwn_layout_style']) && $instance['pwn_layout_style'] == 'style5') { echo $selected; } ?> ><?php _e('Style 5 (Green Carnations)', 'profile-widget-ninja');?></option>
                    </select>
                </p>

                <!-- Icon style -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_iconset_style'); ?>"><?php _e('Icon style:', 'profile-widget-ninja'); ?></label>
                    <select id="<?php echo $this->get_field_id('pwn_iconset_style'); ?>" name="<?php echo $this->get_field_name('pwn_iconset_style'); ?>">
                        <option value='iconset1' <?php if (isset($instance['pwn_iconset_style']) && $instance['pwn_iconset_style'] == 'iconset1') { echo $selected; } ?> ><?php _e('Set 1 (Metro)', 'profile-widget-ninja');?></option>
                        <option value='iconset2' <?php if ( isset($instance['pwn_iconset_style']) && $instance['pwn_iconset_style'] == 'iconset2') { echo $selected; } ?> ><?php _e('Set 2 (Whirl)', 'profile-widget-ninja');?></option>
                    </select>
                </p>

                <!-- Widget custom background color -->
                <p>
                    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('pwn_use_custom_background_color'); ?>" name="<?php echo $this->get_field_name('pwn_use_custom_background_color'); ?>" value="1" <?php checked(isset($instance['pwn_use_custom_background_color']) && $instance['pwn_use_custom_background_color'] === '1', 1); ?>>
                    <label for="<?php echo $this->get_field_id('pwn_use_custom_background_color'); ?>"><?php _e('Use widget custom background color:', 'profile-widget-ninja'); ?></label><br/>
                    <input type="text"id="<?php echo $this->get_field_id('pwn_background_color'); ?>" name="<?php echo $this->get_field_name('pwn_background_color'); ?>"  value="<?php if (isset($instance['pwn_background_color'])) { echo esc_attr($instance['pwn_background_color']); } ?>" class="pwn-background-color" data-default-color="#3a5795" />
                </p>

                <!-- Widget custom font color -->
                <p>
                    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('pwn_use_custom_font_color'); ?>" name="<?php echo $this->get_field_name('pwn_use_custom_font_color'); ?>" value="1" <?php checked(isset($instance['pwn_use_custom_font_color']) && $instance['pwn_use_custom_font_color'] === '1', 1); ?>>
                    <label for="<?php echo $this->get_field_id('pwn_use_custom_font_color'); ?>"><?php _e('Use custom font color:', 'profile-widget-ninja'); ?></label><br/>
                    <input type="text"id="<?php echo $this->get_field_id('pwn_font_color'); ?>" name="<?php echo $this->get_field_name('pwn_font_color'); ?>"  value="<?php if (isset($instance['pwn_font_color'])) { echo esc_attr($instance['pwn_font_color']); } ?>" class="pwn-background-color" data-default-color="#ffffff" />
                </p>

                <!-- Name -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_profile_name'); ?>"><?php _e('Name:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_profile_name'); ?>" name="<?php echo $this->get_field_name('pwn_profile_name'); ?>" value="<?php if (isset($instance['pwn_profile_name'])) { echo esc_attr($instance['pwn_profile_name']); } ?>" />
                </p>

                <!-- Tagline -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_profile_designation'); ?>"><?php _e('Tagline:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_profile_designation'); ?>" name="<?php echo $this->get_field_name('pwn_profile_designation'); ?>" value="<?php if (isset($instance['pwn_profile_designation'])) { echo esc_attr($instance['pwn_profile_designation']); } ?>" />
                </p>

                <!-- Cover image URL -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_cover_image_url'); ?>"><?php _e('Cover image URL (e.g. http://example.com/image-name.jpg):', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_cover_image_url'); ?>" name="<?php echo $this->get_field_name('pwn_cover_image_url'); ?>" value="<?php if (isset($instance['pwn_cover_image_url'])) { echo esc_attr($instance['pwn_cover_image_url']); } ?>" />
                </p>

                <!-- Profile image URL -->
                <p>
                    <label for="<?php echo $this->get_field_id( 'pwn_profile_image_url' ); ?>"><?php _e('Profile image URL (e.g. http://example.com/image-name.jpg):', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_profile_image_url'); ?>" name="<?php echo $this->get_field_name('pwn_profile_image_url'); ?>" value="<?php if (isset($instance['pwn_profile_image_url'])) { echo esc_attr($instance['pwn_profile_image_url']); } ?>" />
                </p>

                <!-- Profile image thumbnail preview -->
                <?php if (isset($instance['pwn_profile_image_url']) && ($instance['pwn_profile_image_url'] != '')) { 
                    $profile_image_alt = $instance['pwn_profile_name'];
                    $profile_image_dimension = intval($instance['pwn_profile_image_dimension']);
                    if ($profile_image_dimension > 200) {
                        $profile_image_dimension = 200;
                    } else if ($profile_image_dimension < 50) {
                        $profile_image_dimension = 90;
                    }
                    $profile_image_shape = $instance['pwn_profile_image_shape'];
                    if ($profile_image_shape === '') {
                        $profile_image_shape = 'round';
                    }
                    $profile_image_style = '';
                    if ($profile_image_shape == 'round') {
                        $profile_image_style = ' style="-webkit-border-radius: 50%; -moz-border-radius: 50%; -ms-border-radius: 50%; -o-border-radius: 50%; border-radius: 50%; border-width: 2px; border-style: solid; border-color: #FFFFFF;"';
                    }
                ?>
                    <p>
                        <?php _e('Profile image preview:', 'profile-widget-ninja') ?><br/><img src="<?php echo $instance['pwn_profile_image_url']; ?>" alt="<?php echo $profile_image_alt; ?>" width="<?php echo $profile_image_dimension; ?>" height="<?php echo $profile_image_dimension; ?>" <?php echo $profile_image_style; ?> />
                    </p>
                <?php } ?>

                <!-- Profile image dimension -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_profile_image_dimension'); ?>"><?php _e('Profile image dimension:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="small-text" id="<?php echo $this->get_field_id('pwn_profile_image_dimension'); ?>" name="<?php echo $this->get_field_name('pwn_profile_image_dimension'); ?>" value="<?php if (isset ($instance['pwn_profile_image_dimension'])) { echo esc_attr($instance['pwn_profile_image_dimension']); } else { _e('90', 'profile-widget-ninja'); } ?>" />px<br/>
                    <?php _e('(Min. 50 to Max. 200. Default value. 90)', 'profile-widget-ninja') ?>
                </p>

                <!-- Profile image shape -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_profile_image_shape'); ?>"><?php _e('Profile image shape:', 'profile-widget-ninja'); ?></label><br/>
                    <!-- Rounded shape - Default -->
                    <input  type="radio" 
                            id="<?php echo $this->get_field_id('pwn_profile_image_shape'); ?>-round" 
                            name="<?php echo $this->get_field_name('pwn_profile_image_shape'); ?>" 
                            value="round" <?php if ( isset ( $instance['pwn_profile_image_shape'] ) && ($instance['pwn_profile_image_shape'] == 'round' || $instance['pwn_profile_image_shape'] == '') ) { echo $checked; } ?> />
                    <label for="<?php echo $this->get_field_id( 'pwn_profile_image_shape' ); ?>-round"><?php _e('Round', 'profile-widget-ninja');?></label><br />

                    <!-- Square shape -->
                    <input  type="radio" 
                            id="<?php echo $this->get_field_id('pwn_profile_image_shape'); ?>-square" 
                            name="<?php echo $this->get_field_name('pwn_profile_image_shape'); ?>" 
                            value="square" <?php if (isset ($instance['pwn_profile_image_shape']) && $instance['pwn_profile_image_shape'] == 'square') { echo $checked; } ?> />
                    <label for="<?php echo $this->get_field_id('pwn_profile_image_shape'); ?>-square"><?php _e('Square', 'profile-widget-ninja');?></label><br />
                </p>

                <!-- Profile description -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_description'); ?>"><?php _e('Profile Description:', 'profile-widget-ninja'); ?></label><br />
                    <textarea id="<?php echo $this->get_field_id('pwn_description'); ?>" name="<?php echo $this->get_field_name('pwn_description'); ?>" class="widefat" rows="5" cols="5" ><?php if (isset ($instance['pwn_description'])) { echo esc_attr($instance['pwn_description']); } ?></textarea>
                </p>

                <!-- Widget custom anchor link color -->
                <p>
                    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('pwn_use_custom_anchor_link_color'); ?>" name="<?php echo $this->get_field_name('pwn_use_custom_anchor_link_color'); ?>" value="1" <?php checked(isset($instance['pwn_use_custom_anchor_link_color']) && $instance['pwn_use_custom_anchor_link_color'] === '1', 1); ?>>
                    <label for="<?php echo $this->get_field_id('pwn_use_custom_anchor_link_color'); ?>"><?php _e('Use custom anchor link color:', 'profile-widget-ninja'); ?></label><br/>
                    <input type="text"id="<?php echo $this->get_field_id('pwn_anchor_link_color'); ?>" name="<?php echo $this->get_field_name('pwn_anchor_link_color'); ?>"  value="<?php if (isset($instance['pwn_anchor_link_color'])) { echo esc_attr($instance['pwn_anchor_link_color']); } ?>" class="pwn-background-color" data-default-color="#55e6c6" />
                </p>
                <!-- Widget custom anchor link hover color -->
                <p>
                    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('pwn_use_custom_anchor_link_hover_color'); ?>" name="<?php echo $this->get_field_name('pwn_use_custom_anchor_link_hover_color'); ?>" value="1" <?php checked(isset($instance['pwn_use_custom_anchor_link_hover_color']) && $instance['pwn_use_custom_anchor_link_hover_color'] === '1', 1); ?>>
                    <label for="<?php echo $this->get_field_id('pwn_use_custom_anchor_link_hover_color'); ?>"><?php _e('Use custom anchor link hover color:', 'profile-widget-ninja'); ?></label><br/>
                    <input type="text"id="<?php echo $this->get_field_id('pwn_anchor_link_hover_color'); ?>" name="<?php echo $this->get_field_name('pwn_anchor_link_hover_color'); ?>"  value="<?php if (isset($instance['pwn_anchor_link_hover_color'])) { echo esc_attr($instance['pwn_anchor_link_hover_color']); } ?>" class="pwn-background-color" data-default-color="#ffffff" />
                </p>

                <!-- Read more page URL -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_read_more_page'); ?>"><?php _e('Read More page URL (e.g. http://example.com/read-more):', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_read_more_page'); ?>" name="<?php echo $this->get_field_name('pwn_read_more_page'); ?>" value="<?php if (isset($instance['pwn_read_more_page'])) { echo esc_attr($instance['pwn_read_more_page']); } ?>" /><br/>
                    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('pwn_read_more_page_new_tab'); ?>" name="<?php echo $this->get_field_name('pwn_read_more_page_new_tab'); ?>" value="1" <?php checked(isset($instance['pwn_read_more_page_new_tab']) && $instance['pwn_read_more_page_new_tab'] === '1', 1); ?>>
                    <label for="<?php echo $this->get_field_id('pwn_read_more_page_new_tab'); ?>"><?php _e('Open in new browser tab', 'profile-widget-ninja'); ?></label>
                </p>

                <!-- Read more button text -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_read_more_link_text'); ?>"><?php _e('Read More link text:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'pwn_read_more_link_text' ); ?>" name="<?php echo $this->get_field_name( 'pwn_read_more_link_text' ); ?>" value="<?php if (isset($instance['pwn_read_more_link_text']) && !empty($instance['pwn_read_more_link_text'])) { echo esc_attr( $instance['pwn_read_more_link_text'] ); } else { _e( 'Read more...', 'profile-widget-ninja' ); } ?>" />
                </p>

                <!-- Social links area custom background color -->
                <p>
                    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('pwn_use_custom_social_background_color'); ?>" name="<?php echo $this->get_field_name('pwn_use_custom_social_background_color'); ?>" value="1" <?php checked(isset($instance['pwn_use_custom_social_background_color']) && $instance['pwn_use_custom_social_background_color'] === '1', 1); ?>>
                    <label for="<?php echo $this->get_field_id('pwn_use_custom_social_background_color'); ?>"><?php _e('Use custom social link area background color:', 'profile-widget-ninja'); ?></label><br/>
                    <input type="text" id="<?php echo $this->get_field_id('pwn_social_background_color'); ?>" name="<?php echo $this->get_field_name('pwn_social_background_color'); ?>"  value="<?php if (isset($instance['pwn_social_background_color'])) { echo esc_attr($instance['pwn_social_background_color']); } ?>" class="pwn-background-color" data-default-color="#29395a" />
                </p>

                <!-- Email ID -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_email_id'); ?>"><?php _e('Email ID:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_email_id'); ?>" name="<?php echo $this->get_field_name('pwn_email_id'); ?>" value="<?php if (isset($instance['pwn_email_id']) && !empty($instance['pwn_email_id'])) { echo sanitize_email($instance['pwn_email_id']); } ?>" />
                </p>

                <!-- Facebook link -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_facebook_url'); ?>"><?php _e('Facebook URL:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_facebook_url'); ?>" name="<?php echo $this->get_field_name('pwn_facebook_url'); ?>" value="<?php if (isset($instance['pwn_facebook_url']) && !empty($instance['pwn_facebook_url'])) { echo esc_attr($instance['pwn_facebook_url']); } ?>" />
                </p>

                <!-- Twitter link -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_twitter_url'); ?>"><?php _e('Twitter URL:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_twitter_url'); ?>" name="<?php echo $this->get_field_name('pwn_twitter_url'); ?>" value="<?php if (isset($instance['pwn_twitter_url']) && !empty($instance['pwn_twitter_url'])) { echo esc_attr($instance['pwn_twitter_url']); } ?>" />
                </p>

                <!-- LinkedIn link -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_linkedin_url'); ?>"><?php _e('LinkedIn URL:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_linkedin_url'); ?>" name="<?php echo $this->get_field_name('pwn_linkedin_url'); ?>" value="<?php if (isset($instance['pwn_linkedin_url']) && !empty($instance['pwn_linkedin_url'])) { echo esc_attr($instance['pwn_linkedin_url']); } ?>" />
                </p>

                <!-- Google+ link -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_googleplus_url'); ?>"><?php _e('Google+ URL:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_googleplus_url'); ?>" name="<?php echo $this->get_field_name('pwn_googleplus_url'); ?>" value="<?php if (isset($instance['pwn_googleplus_url']) && !empty($instance['pwn_googleplus_url'])) { echo esc_attr($instance['pwn_googleplus_url']); } ?>" />
                </p>

                <!-- YouTube link -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_youtube_url'); ?>"><?php _e('YouTube URL:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_youtube_url'); ?>" name="<?php echo $this->get_field_name('pwn_youtube_url'); ?>" value="<?php if (isset($instance['pwn_youtube_url']) && !empty($instance['pwn_youtube_url'])) { echo esc_attr($instance['pwn_youtube_url']); } ?>" />
                </p>

                <!-- Vimeo link -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_vimeo_url'); ?>"><?php _e('Vimeo URL:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_vimeo_url'); ?>" name="<?php echo $this->get_field_name('pwn_vimeo_url'); ?>" value="<?php if (isset($instance['pwn_vimeo_url']) && !empty($instance['pwn_vimeo_url'])) { echo esc_attr($instance['pwn_vimeo_url']); } ?>" />
                </p>

                <!-- GitHub link -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_github_url'); ?>"><?php _e('GitHub URL:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_github_url'); ?>" name="<?php echo $this->get_field_name('pwn_github_url'); ?>" value="<?php if (isset($instance['pwn_github_url']) && !empty($instance['pwn_github_url'])) { echo esc_attr($instance['pwn_github_url']); } ?>" />
                </p>

                <!-- Blogger link -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_blogger_url'); ?>"><?php _e('Blogger URL:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_blogger_url'); ?>" name="<?php echo $this->get_field_name('pwn_blogger_url'); ?>" value="<?php if (isset($instance['pwn_blogger_url']) && !empty($instance['pwn_blogger_url'])) { echo esc_attr($instance['pwn_blogger_url']); } ?>" />
                </p>

                <!-- Skype link -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_skype_url'); ?>"><?php _e('Skype Username:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_skype_url'); ?>" name="<?php echo $this->get_field_name('pwn_skype_url'); ?>" value="<?php if (isset($instance['pwn_skype_url']) && !empty($instance['pwn_skype_url'])) { echo esc_attr($instance['pwn_skype_url']); } ?>" />
                </p>

                <!-- Instagram link -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_instagram_url'); ?>"><?php _e('Instagram URL:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_instagram_url'); ?>" name="<?php echo $this->get_field_name('pwn_instagram_url'); ?>" value="<?php if (isset($instance['pwn_instagram_url']) && !empty($instance['pwn_instagram_url'])) { echo esc_attr($instance['pwn_instagram_url']); } ?>" />
                </p>

                <!-- Pinterest link -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_pinterest_url'); ?>"><?php _e('Pinterest URL:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_pinterest_url'); ?>" name="<?php echo $this->get_field_name('pwn_pinterest_url'); ?>" value="<?php if (isset($instance['pwn_pinterest_url']) && !empty($instance['pwn_pinterest_url'])) { echo esc_attr($instance['pwn_pinterest_url']); } ?>" />
                </p>
            </div>
            <?php
        }

        /**
         * Displays shortcode for the widget
         *
         * @param WP_Widget $widget
         * @param null $return
         * @param array $instance
         */
        function show_shortcode_for_widget( $widget_ref, $return, $instance ) {
            echo '<p><strong>'.__( 'Ninja Shortcode:', 'profile-widget-ninja' ).'</strong> '. (( $widget_ref->number == '__i__') ? __( 'Please save the widget before shortcode can be generated.', 'profile-widget-ninja' ) : '<input type="text" value="' . esc_attr( '[profile_widget_ninja id="'. $widget_ref->id .'"]' ) .'" readonly="readonly" class="widefat" onclick="this.select()" />').'<br/>';
            echo __( 'You can use this shortcode in any page or post to display this profile widget.', 'profile-widget-ninja' ).'</p>';
            ?>
            <p>
                <img src="<?php echo plugins_url('icons/donate.png', __FILE__) ?>" alt="Donate" /> <strong><?php _e(' Show some Love', 'profile-widget-ninja') ?></strong> <img src="<?php echo plugins_url('icons/donate.png', __FILE__) ?>" alt="Donate" />
            </p>
            <!-- Rating link -->
            <p>
                <a href="https://wordpress.org/plugins/profile-widget-ninja/" target="_blank"><img src="<?php echo plugins_url('icons/star.png', __FILE__) ?>" alt="" /><img src="<?php echo plugins_url('icons/star.png', __FILE__) ?>" alt="" /><img src="<?php echo plugins_url('icons/star.png', __FILE__) ?>" alt="" /><img src="<?php echo plugins_url('icons/star.png', __FILE__) ?>" alt="" /><img src="<?php echo plugins_url('icons/star.png', __FILE__) ?>" alt="" /> <?php _e('Rate this plugin', 'profile-widget-ninja') ?></a>
            </p>
            <!-- Donation link -->
            <p>
                <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8RU2P54KQFT56" target="_blank"><img src="<?php echo plugins_url('icons/paypal.png', __FILE__) ?>" alt="Donate" /> <?php _e('Support Development of this plugin', 'profile-widget-ninja') ?></a>
            </p>
            <?php
        }

        /**
         * Outputs widget contents
         *
         * @param array $args
         * @param array $instance
         */
        public function widget($args, $instance) {
            extract($args);
            ob_start();

            $title = apply_filters('widget_title', $instance['title']);
            echo $before_widget;
            if ($title) {
                echo $before_title . $title . $after_title;
            }
            $widget_custom_background_color = '';
            $social_area_custom_background_color = '';
            $custom_font_color = '';
            $custom_anchor_color_style = '';
            if (isset($instance['pwn_use_custom_background_color']) && $instance['pwn_use_custom_background_color'] === '1') {
                if (isset($instance['pwn_background_color']) && !empty($instance['pwn_background_color'])) {
                    $widget_custom_background_color .= 'background-color:' . $instance['pwn_background_color'] . ';';
                }
            }
            if (isset($instance['pwn_use_custom_social_background_color']) && $instance['pwn_use_custom_social_background_color'] === '1') {
                if (isset($instance['pwn_social_background_color']) && !empty($instance['pwn_social_background_color'])) {
                    $social_area_custom_background_color .= 'background-color:' . $instance['pwn_social_background_color'] . ';';
                }
            }
            if (isset($instance['pwn_use_custom_font_color']) && $instance['pwn_use_custom_font_color'] === '1') {
                if (isset($instance['pwn_font_color']) && !empty($instance['pwn_font_color'])) {
                    $custom_font_color = 'color:' . $instance['pwn_font_color'] . ';';
                }
            }
            if (isset($instance['pwn_use_custom_anchor_link_color']) && $instance['pwn_use_custom_anchor_link_color'] === '1') {
                if (isset($instance['pwn_anchor_link_color']) && !empty($instance['pwn_anchor_link_color'])) {
                    $custom_anchor_color_style .= ' style="color:' . $instance['pwn_anchor_link_color'] . ';" onMouseOut="this.style.color=\'' . $instance['pwn_anchor_link_color'] .'\'"';
                }
            }
            if (isset($instance['pwn_use_custom_anchor_link_hover_color']) && $instance['pwn_use_custom_anchor_link_hover_color'] === '1') {
                if (isset($instance['pwn_anchor_link_hover_color']) && !empty($instance['pwn_anchor_link_hover_color'])) {
                    $custom_anchor_color_style .= ' onMouseOver="this.style.color=\'' . $instance['pwn_anchor_link_hover_color'] .'\'"';
                }
            }
            ?>
            <div class="pwn-profile-widget-ninja-wrapper pwn-profile-widget-ninja-layout-<?php echo (isset( $instance['pwn_alignment'])) ? $instance['pwn_alignment'] : 'center'; ?> pwn-layout-style-<?php echo (isset( $instance['pwn_layout_style'])) ? $instance['pwn_layout_style'] : 'style1'; ?> pwn-profile-widget-ninja-avatar-<?php echo (isset($instance['pwn_profile_image_shape'])) ? $instance['pwn_profile_image_shape'] : ''; ?>"<?php if (!empty($widget_custom_background_color)) { echo ' style="' . $widget_custom_background_color . '"'; } ?>>
                <?php 
                do_action('before_profile_widget_ninja', $instance);
                if (isset($instance['pwn_cover_image_url']) && !empty($instance['pwn_cover_image_url'])) {
                    echo '<div class="pwn-profile-widget-ninja-cover-pic" style="background-image: url('.esc_url($instance['pwn_cover_image_url']).'); background-position: top center;"></div>';
                }
                echo '<div class="pwn-container-bottom-wrapper" style="' . $custom_font_color . '">';
                    // Profile avatar
                    do_action('before_profile_widget_ninja_avatar', $instance);
                    if (isset($instance['pwn_profile_image_url']) && !empty($instance['pwn_profile_image_url'])) {
                        $profile_image_alt = $instance['pwn_profile_name'];
                        $profile_image_dimension = intval($instance['pwn_profile_image_dimension']);
                        if ($profile_image_dimension > 200) {
                            // restrict max width/height
                            $profile_image_dimension = 200;
                        } else if ($profile_image_dimension < 50) {
                            // set default dimension
                            $profile_image_dimension = 90;
                        }
                        
                        // calculate margin top for profile picture
                        if (isset($instance['pwn_cover_image_url']) && !empty($instance['pwn_cover_image_url'])) {
                            $profile_image_margin_top = (($profile_image_dimension / 2) + 20);
                        } else {
                            $profile_image_margin_top = 0;
                        }

                        $profile_avatar = '<img src="'.esc_url($instance['pwn_profile_image_url']).'" alt="'.$profile_image_alt.'" width="'.$profile_image_dimension.'" height="'.$profile_image_dimension.'" class="profile-avatar" style="margin-top: -'.$profile_image_margin_top.'px;" />';
                        echo apply_filters('profile_widget_ninja_avatar', $profile_avatar, $instance);
                    }
                    do_action('after_profile_widget_ninja_avatar', $instance);

                    // Profile name
                    $profile_name = strip_tags(stripslashes($instance['pwn_profile_name']));
                    if (!empty($profile_name)) {
                        echo apply_filters('profile_widget_ninja_name', '<h4>'. $profile_name .'</h4>', $instance);
                    }

                    // Profile designation
                    $profile_designation = strip_tags(stripslashes($instance['pwn_profile_designation']));
                    if (!empty($profile_designation)) {
                        echo apply_filters('profile_widget_ninja_designation', '<div class="pwn-description">'. $profile_designation .'</div>', $instance);
                    }

                    if ((isset($instance['pwn_description']) && !empty($instance['pwn_description'])) || (isset($instance['pwn_read_more_page']) && !empty($instance['pwn_read_more_page']))) {
                        // Profile description
                        do_action('before_profile_widget_ninja_description', $instance);
                        echo '<div class="pwn-description-container">';
                        if (isset($instance['pwn_description']) && !empty($instance['pwn_description'])) {
                            $profile_description = $instance['pwn_description'] ;
                            echo apply_filters( 'profile_widget_ninja_description', $profile_description, $instance );
                        }

                        // Read more link
                        if (isset($instance['pwn_read_more_page']) && !empty($instance['pwn_read_more_page'])) {
                            $read_more_button = (isset($instance['pwn_read_more_link_text']) && !empty($instance['pwn_read_more_link_text'])) ? strip_tags(stripslashes($instance['pwn_read_more_link_text'])) : 'Read more...';
                            $open_target = ($instance['pwn_read_more_page_new_tab'] === '1') ? '_blank' : '_self';
                            echo '<a href="'.esc_url($instance['pwn_read_more_page']).'" target="'. $open_target .'" class="pwn-read-more"'. $custom_anchor_color_style .'>'. $read_more_button .'</a>';
                        }
                        echo '</div>';
                        do_action('after_profile_widget_ninja_description', $instance);
                    }
                echo '</div>';
                $social_media_links_exist = 'n';
                if ((isset($instance['pwn_email_id']) && !empty($instance['pwn_email_id'])) ||
                    (isset($instance['pwn_facebook_url']) && !empty($instance['pwn_facebook_url'])) ||
                    (isset($instance['pwn_twitter_url']) && !empty($instance['pwn_twitter_url'])) ||
                    (isset($instance['pwn_linkedin_url']) && !empty($instance['pwn_linkedin_url'])) ||
                    (isset($instance['pwn_googleplus_url']) && !empty($instance['pwn_googleplus_url'])) ||
                    (isset($instance['pwn_youtube_url']) && !empty($instance['pwn_youtube_url'])) ||
                    (isset($instance['pwn_vimeo_url']) && !empty($instance['pwn_vimeo_url'])) ||
                    (isset($instance['pwn_github_url']) && !empty($instance['pwn_github_url'])) ||
                    (isset($instance['pwn_blogger_url']) && !empty($instance['pwn_blogger_url'])) ||
                    (isset($instance['pwn_skype_url']) && !empty($instance['pwn_skype_url'])) ||
                    (isset($instance['pwn_instagram_url']) && !empty($instance['pwn_instagram_url'])) ||
                    (isset($instance['pwn_pinterest_url']) && !empty($instance['pwn_pinterest_url']))) {
                    $social_media_links_exist = 'y';
                }
                $iconset = "1";
                if (isset( $instance['pwn_iconset_style'] ) && $instance['pwn_iconset_style'] == "iconset2") {
                    $iconset = "2";
                }
                if ($social_media_links_exist === 'y') {
                    echo '<div class="pwn-container-social-links" style="' . $social_area_custom_background_color . '">';
                    // Email ID
                    if (isset($instance['pwn_email_id']) && !empty($instance['pwn_email_id'])) {
                        echo '<a href="mailto:'. $instance['pwn_email_id'] .'" target="_blank"><img src="'. plugins_url('icons/email-'.$iconset.'-32x32.png', __FILE__) .'" alt="Email" title="Email" width="32" height="32" /></a>';
                    }
                    // Facebook URL
                    if (isset($instance['pwn_facebook_url']) && !empty($instance['pwn_facebook_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_facebook_url']) .'" target="_blank"><img src="'. plugins_url('icons/facebook-'.$iconset.'-32x32.png', __FILE__) .'" alt="Facebook" title="Facebook" width="32" height="32" /></a>';
                    }
                    // Twitter URL
                    if (isset($instance['pwn_twitter_url']) && !empty($instance['pwn_twitter_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_twitter_url']) .'" target="_blank"><img src="'. plugins_url('icons/twitter-'.$iconset.'-32x32.png', __FILE__) .'" alt="Twitter" title="Twitter" width="32" height="32" /></a>';
                    }
                    // LinkedIn URL
                    if (isset($instance['pwn_linkedin_url']) && !empty($instance['pwn_linkedin_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_linkedin_url']) .'" target="_blank"><img src="'. plugins_url('icons/linkedin-'.$iconset.'-32x32.png', __FILE__) .'" alt="LinkedIn" title="LinkedIn" width="32" height="32" /></a>';
                    }
                    // Google+ URL
                    if (isset($instance['pwn_googleplus_url']) && !empty($instance['pwn_googleplus_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_googleplus_url']) .'" target="_blank"><img src="'. plugins_url('icons/googleplus-'.$iconset.'-32x32.png', __FILE__) .'" alt="Google+" title="Google+" width="32" height="32" /></a>';
                    }
                    // YouTube URL
                    if (isset($instance['pwn_youtube_url']) && !empty($instance['pwn_youtube_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_youtube_url']) .'" target="_blank"><img src="'. plugins_url('icons/youtube-'.$iconset.'-32x32.png', __FILE__) .'" alt="YouTube" title="YouTube" width="32" height="32" /></a>';
                    }
                    // Vimeo URL
                    if (isset($instance['pwn_vimeo_url']) && !empty($instance['pwn_vimeo_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_vimeo_url']) .'" target="_blank"><img src="'. plugins_url('icons/vimeo-'.$iconset.'-32x32.png', __FILE__) .'" alt="Vimeo" title="Vimeo" width="32" height="32" /></a>';
                    }
                    // GitHub URL
                    if (isset($instance['pwn_github_url']) && !empty($instance['pwn_github_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_github_url']) .'" target="_blank"><img src="'. plugins_url('icons/github-'.$iconset.'-32x32.png', __FILE__) .'" alt="GitHub" title="GitHub" width="32" height="32" /></a>';
                    }
                    // Blogger URL
                    if (isset($instance['pwn_blogger_url']) && !empty($instance['pwn_blogger_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_blogger_url']) .'" target="_blank"><img src="'. plugins_url('icons/blogger-'.$iconset.'-32x32.png', __FILE__) .'" alt="Blogger" title="Blogger" width="32" height="32" /></a>';
                    }
                    // Skype URL
                    if (isset($instance['pwn_skype_url']) && !empty($instance['pwn_skype_url'])) {
                        echo '<a href="skype:'. $instance['pwn_skype_url'] .'"><img src="'. plugins_url('icons/skype-'.$iconset.'-32x32.png', __FILE__) .'" alt="Skype" title="Skype" width="32" height="32" /></a>';
                    }
                    // Instagram URL
                    if (isset($instance['pwn_instagram_url']) && !empty($instance['pwn_instagram_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_instagram_url']) .'" target="_blank"><img src="'. plugins_url('icons/instagram-'.$iconset.'-32x32.png', __FILE__) .'" alt="Instagram" title="Instagram" width="32" height="32" /></a>';
                    }
                    // Pinterest URL
                    if (isset($instance['pwn_pinterest_url']) && !empty($instance['pwn_pinterest_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_pinterest_url']) .'" target="_blank"><img src="'. plugins_url('icons/pinterest-'.$iconset.'-32x32.png', __FILE__) .'" alt="Pinterest" title="Pinterest" width="32" height="32" /></a>';
                    }
                    echo '</div>';
                }
                do_action('after_profile_widget_ninja', $instance);
                ?>
            </div>
            <?php
            echo $after_widget;
            $html = ob_get_clean();
            echo apply_filters('do_profile_widget_ninja', $html, $args, $instance);
        }

        /**
         * Update widget options
         *
         * @param array $new_instance The new options
         * @param array $old_instance The previous options
         */
        public function update($new_instance, $old_instance) {
            if (is_admin()) {

                $allowedtags = array(
                    'a' => array(
                        'href' => true,
                        'title' => true,
                        'class' => true
                    ),
                    'abbr' => array(
                        'title' => true
                    ),
                    'img' => array(
                        'src' => true,
                        'width' => true,
                        'height' => true,
                        'class' => true
                    ),
                    'acronym' => array(
                        'title' => true
                    ),
                    'b' => array(),
                    'blockquote' => array(
                        'cite' => true
                    ),
                    'cite' => array(),
                    'code' => array(),
                    'del' => array(
                        'datetime' => true
                    ),
                    'em' => array(),
                    'i' => array(),
                    'p' => array(),
                    'q' => array(
                        'cite' => true
                    ),
                    'strike' => array(),
                    'strong' => array(),
                    'br' => array()
                );

                $old_instance['title'] = isset ($new_instance['title']) ? strip_tags ($new_instance['title']) : '';
                $old_instance['pwn_background_color'] = isset ($new_instance['pwn_background_color']) ? $new_instance['pwn_background_color'] : '#3a5795';
                $old_instance['pwn_use_custom_background_color'] = isset ($new_instance['pwn_use_custom_background_color']) ? strip_tags ($new_instance['pwn_use_custom_background_color']) : '';
                $old_instance['pwn_font_color'] = isset ($new_instance['pwn_font_color']) ? $new_instance['pwn_font_color'] : '#ffffff';
                $old_instance['pwn_use_custom_font_color'] = isset ($new_instance['pwn_use_custom_font_color']) ? strip_tags ($new_instance['pwn_use_custom_font_color']) : '';
                $old_instance['pwn_anchor_link_color'] = isset ($new_instance['pwn_anchor_link_color']) ? $new_instance['pwn_anchor_link_color'] : '#55e6c6';
                $old_instance['pwn_use_custom_anchor_link_color'] = isset ($new_instance['pwn_use_custom_anchor_link_color']) ? strip_tags ($new_instance['pwn_use_custom_anchor_link_color']) : '';
                $old_instance['pwn_anchor_link_hover_color'] = isset ($new_instance['pwn_anchor_link_hover_color']) ? $new_instance['pwn_anchor_link_hover_color'] : '#ffffff';
                $old_instance['pwn_use_custom_anchor_link_hover_color'] = isset ($new_instance['pwn_use_custom_anchor_link_hover_color']) ? strip_tags ($new_instance['pwn_use_custom_anchor_link_hover_color']) : '';
                $old_instance['pwn_social_background_color'] = isset ($new_instance['pwn_social_background_color']) ? $new_instance['pwn_social_background_color'] : '#29395a';
                $old_instance['pwn_use_custom_social_background_color'] = isset ($new_instance['pwn_use_custom_social_background_color']) ? strip_tags ($new_instance['pwn_use_custom_social_background_color']) : '';
                $old_instance['pwn_profile_name'] = isset ($new_instance['pwn_profile_name']) ? strip_tags ($new_instance['pwn_profile_name']) : '';
                $old_instance['pwn_profile_designation'] = isset ($new_instance['pwn_profile_designation']) ? strip_tags ($new_instance['pwn_profile_designation']) : '';
                $old_instance['pwn_alignment'] = isset ($new_instance['pwn_alignment']) ? strip_tags ($new_instance['pwn_alignment']) : 'center';
                $old_instance['pwn_layout_style'] = isset ($new_instance['pwn_layout_style']) ? strip_tags ($new_instance['pwn_layout_style']) : 'style1';
                $old_instance['pwn_iconset_style'] = isset ($new_instance['pwn_iconset_style']) ? strip_tags ($new_instance['pwn_iconset_style']) : 'iconset1';
                $old_instance['pwn_read_more_page'] = isset ($new_instance['pwn_read_more_page']) ? strip_tags ($new_instance['pwn_read_more_page']) : '';
                $old_instance['pwn_read_more_page_new_tab'] = isset ($new_instance['pwn_read_more_page_new_tab']) ? strip_tags ($new_instance['pwn_read_more_page_new_tab']) : '';
                $old_instance['pwn_read_more_link_text'] = isset ($new_instance['pwn_read_more_link_text']) ? strip_tags ($new_instance['pwn_read_more_link_text']) : 'Read more...';
                $old_instance['pwn_cover_image_url'] = isset ($new_instance['pwn_cover_image_url']) ? strip_tags ($new_instance['pwn_cover_image_url']) : '';
                $old_instance['pwn_profile_image_url'] = isset ($new_instance['pwn_profile_image_url']) ? strip_tags ($new_instance['pwn_profile_image_url']) : '';
                $old_instance['pwn_profile_image_dimension'] = (isset ($new_instance['pwn_profile_image_dimension']) && (intval ($new_instance['pwn_profile_image_dimension']) >= 50 && intval($old_instance['pwn_profile_image_dimension']) <= 200)) ? strip_tags($new_instance['pwn_profile_image_dimension']) : 90;
                $old_instance['pwn_profile_image_shape'] = isset ($new_instance['pwn_profile_image_shape']) ? strip_tags ($new_instance['pwn_profile_image_shape']) : 'round';
                $old_instance['pwn_description'] = isset ($new_instance['pwn_description']) ? wp_kses($new_instance['pwn_description'], $allowedtags) : '';
                $old_instance['pwn_email_id'] = (isset ($new_instance['pwn_email_id']) && (!empty ($new_instance['pwn_email_id']))) ? sanitize_email($new_instance['pwn_email_id']) : '';
                $old_instance['pwn_facebook_url'] = (isset ($new_instance['pwn_facebook_url']) && (!empty ($new_instance['pwn_facebook_url']))) ? strip_tags ($new_instance['pwn_facebook_url']) : '';
                $old_instance['pwn_twitter_url'] = (isset ($new_instance['pwn_twitter_url']) && (!empty ($new_instance['pwn_twitter_url']))) ? strip_tags ($new_instance['pwn_twitter_url']) : '';
                $old_instance['pwn_linkedin_url'] = (isset ($new_instance['pwn_linkedin_url']) && (!empty ($new_instance['pwn_linkedin_url']))) ? strip_tags ($new_instance['pwn_linkedin_url']) : '';
                $old_instance['pwn_googleplus_url'] = (isset ($new_instance['pwn_googleplus_url']) && (!empty ($new_instance['pwn_googleplus_url']))) ? strip_tags ($new_instance['pwn_googleplus_url']) : '';
                $old_instance['pwn_youtube_url'] = (isset ($new_instance['pwn_youtube_url']) && (!empty ($new_instance['pwn_youtube_url']))) ? strip_tags ($new_instance['pwn_youtube_url']) : '';
                $old_instance['pwn_vimeo_url'] = (isset ($new_instance['pwn_vimeo_url']) && (!empty ($new_instance['pwn_vimeo_url']))) ? strip_tags ($new_instance['pwn_vimeo_url']) : '';
                $old_instance['pwn_github_url'] = (isset ($new_instance['pwn_github_url']) && (!empty ($new_instance['pwn_github_url']))) ? strip_tags ($new_instance['pwn_github_url']) : '';
                $old_instance['pwn_blogger_url'] = (isset ($new_instance['pwn_blogger_url']) && (!empty ($new_instance['pwn_blogger_url']))) ? strip_tags ($new_instance['pwn_blogger_url']) : '';
                $old_instance['pwn_skype_url'] = (isset ($new_instance['pwn_skype_url']) && (!empty ($new_instance['pwn_skype_url']))) ? strip_tags ($new_instance['pwn_skype_url']) : '';
                $old_instance['pwn_instagram_url'] = (isset ($new_instance['pwn_instagram_url']) && (!empty ($new_instance['pwn_instagram_url']))) ? strip_tags ($new_instance['pwn_instagram_url']) : '';
                $old_instance['pwn_pinterest_url'] = (isset ($new_instance['pwn_pinterest_url']) && (!empty ($new_instance['pwn_pinterest_url']))) ? strip_tags ($new_instance['pwn_pinterest_url']) : '';

                $instance = apply_filters('save_profile_widget_ninja_instance', $old_instance, $new_instance);
                return $instance;
            }
        }
    }

    /**
     * Register widget
     *
     * @param none
     */
    function register_widget_profile_widget_ninja() {
        register_widget('Profile_Widget_Ninja');
        // Register a hidden sidebar that is used for creating widgets for shortcode generation
        register_sidebar( array(
            'name' => __( 'Hidden Widget Area', 'profile-widget-ninja' ),
            'description' => __( 'This widget area is not shown in the frontend and can be used for [profile_widget_ninja] shortcode creation.', 'profile-widget-ninja' ),
            'id' => 'pwn_hidden_sidebar',
            'before_widget' => '',
            'after_widget' => '',
        ) );
    }
    add_action('widgets_init', 'register_widget_profile_widget_ninja');
}

/**
 * Add frontend stylesheet
 *
 * @param none
 */
function enqueue_style_profile_widget_ninja() {
    wp_enqueue_style('profile-widget-ninja-frontend', plugins_url('css/profile_widget_ninja.css', __FILE__), array(), null );
}
add_action('wp_enqueue_scripts', 'enqueue_style_profile_widget_ninja');

/**
 * Add admin stylesheet and JS
 *
 * @param none
 */
function enqueue_pwn_color_picker() {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('pwn-admin-script', plugins_url('js/profile_widget_ninja_admin.js', __FILE__), array('wp-color-picker'), false, true );
}
add_action( 'admin_enqueue_scripts', 'enqueue_pwn_color_picker' );

add_action('plugins_loaded', 'pwn_load_textdomain');
function pwn_load_textdomain() {
    load_plugin_textdomain( 'profile-widget-ninja', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}
