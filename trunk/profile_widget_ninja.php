<?php
/*
Plugin Name: Profile Widget Ninja
Plugin URI: http://pankajgurudeb.blogspot.com/2016/01/profile-widget-ninja-wordpress-plugin.html
Description: Profile Widget Ninja is a full featured profile display widget plugin for WordPress. It is user-friendly and easily customizable.
Text Domain: profile-widget-ninja
Author: Pankaj Kumar Mondal
Author URI: http://pankajgurudeb.blogspot.com
Tags: profile, widget, widgets, about, me, user, social, about me, about me widget, aboutme, my profile, details, quick view, sidebar, simple, link, users, ninja, custom, color, colour, customize
Version: 2.1
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
                        <option value='style1' <?php if (isset($instance['pwn_layout_style']) && $instance['pwn_layout_style'] == 'style1') { echo $selected; } ?> ><?php _e('Style 1', 'profile-widget-ninja');?></option>
                        <option value='style2' <?php if ( isset($instance['pwn_layout_style']) && $instance['pwn_layout_style'] == 'style2') { echo $selected; } ?> ><?php _e('Style 2', 'profile-widget-ninja');?></option>
                        <option value='style3' <?php if ( isset($instance['pwn_layout_style']) && $instance['pwn_layout_style'] == 'style3') { echo $selected; } ?> ><?php _e('Style 3', 'profile-widget-ninja');?></option>
                    </select>
                </p>

                <!-- Widget custom background color -->
                <p>
                    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('pwn_use_custom_background_color'); ?>" name="<?php echo $this->get_field_name('pwn_use_custom_background_color'); ?>" value="1" <?php checked($instance['pwn_use_custom_background_color'], 1); ?>>
                    <label for="<?php echo $this->get_field_id('pwn_use_custom_background_color'); ?>"><?php _e('Use widget custom background color:', 'profile-widget-ninja'); ?></label><br/>
                    <input type="text"id="<?php echo $this->get_field_id('pwn_background_color'); ?>" name="<?php echo $this->get_field_name('pwn_background_color'); ?>"  value="<?php if (isset($instance['pwn_background_color'])) { echo esc_attr($instance['pwn_background_color']); } ?>" class="pwn-background-color" data-default-color="#3a5795" />
                </p>

                <!-- Widget custom font color -->
                <p>
                    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('pwn_use_custom_font_color'); ?>" name="<?php echo $this->get_field_name('pwn_use_custom_font_color'); ?>" value="1" <?php checked($instance['pwn_use_custom_font_color'], 1); ?>>
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
                    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('pwn_use_custom_anchor_link_color'); ?>" name="<?php echo $this->get_field_name('pwn_use_custom_anchor_link_color'); ?>" value="1" <?php checked($instance['pwn_use_custom_anchor_link_color'], 1); ?>>
                    <label for="<?php echo $this->get_field_id('pwn_use_custom_anchor_link_color'); ?>"><?php _e('Use custom anchor link color:', 'profile-widget-ninja'); ?></label><br/>
                    <input type="text"id="<?php echo $this->get_field_id('pwn_anchor_link_color'); ?>" name="<?php echo $this->get_field_name('pwn_anchor_link_color'); ?>"  value="<?php if (isset($instance['pwn_anchor_link_color'])) { echo esc_attr($instance['pwn_anchor_link_color']); } ?>" class="pwn-background-color" data-default-color="#55e6c6" />
                </p>
                <!-- Widget custom anchor link hover color -->
                <p>
                    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('pwn_use_custom_anchor_link_hover_color'); ?>" name="<?php echo $this->get_field_name('pwn_use_custom_anchor_link_hover_color'); ?>" value="1" <?php checked($instance['pwn_use_custom_anchor_link_hover_color'], 1); ?>>
                    <label for="<?php echo $this->get_field_id('pwn_use_custom_anchor_link_hover_color'); ?>"><?php _e('Use custom anchor link hover color:', 'profile-widget-ninja'); ?></label><br/>
                    <input type="text"id="<?php echo $this->get_field_id('pwn_anchor_link_hover_color'); ?>" name="<?php echo $this->get_field_name('pwn_anchor_link_hover_color'); ?>"  value="<?php if (isset($instance['pwn_anchor_link_hover_color'])) { echo esc_attr($instance['pwn_anchor_link_hover_color']); } ?>" class="pwn-background-color" data-default-color="#ffffff" />
                </p>

                <!-- Read more page URL -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_read_more_page'); ?>"><?php _e('Read More page URL (e.g. http://example.com/read-more):', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id('pwn_read_more_page'); ?>" name="<?php echo $this->get_field_name('pwn_read_more_page'); ?>" value="<?php if (isset($instance['pwn_read_more_page'])) { echo esc_attr($instance['pwn_read_more_page']); } ?>" /><br/>
                    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('pwn_read_more_page_new_tab'); ?>" name="<?php echo $this->get_field_name('pwn_read_more_page_new_tab'); ?>" value="1" <?php checked($instance['pwn_read_more_page_new_tab'], 1); ?>>
                    <label for="<?php echo $this->get_field_id('pwn_read_more_page_new_tab'); ?>"><?php _e('Open in new browser tab', 'profile-widget-ninja'); ?></label>
                </p>

                <!-- Read more button text -->
                <p>
                    <label for="<?php echo $this->get_field_id('pwn_read_more_link_text'); ?>"><?php _e('Read More link text:', 'profile-widget-ninja') ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'pwn_read_more_link_text' ); ?>" name="<?php echo $this->get_field_name( 'pwn_read_more_link_text' ); ?>" value="<?php if (isset($instance['pwn_read_more_link_text']) && !empty($instance['pwn_read_more_link_text'])) { echo esc_attr( $instance['pwn_read_more_link_text'] ); } else { _e( 'Read more...', 'profile-widget-ninja' ); } ?>" />
                </p>

                <!-- Social links area custom background color -->
                <p>
                    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('pwn_use_custom_social_background_color'); ?>" name="<?php echo $this->get_field_name('pwn_use_custom_social_background_color'); ?>" value="1" <?php checked($instance['pwn_use_custom_social_background_color'], 1); ?>>
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

                <!-- Donation link -->
                <p>
                    <a href="http://pankajgurudeb.blogspot.com/2016/01/profile-widget-ninja-wordpress-plugin.html" target="_blank"><?php _e('Donate to this plugin', 'profile-widget-ninja') ?> <img src="<?php echo plugins_url('icons/donate.png', __FILE__) ?>" alt="Donate" /></a>
                </p>
            </div>
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
            if ($instance['pwn_use_custom_background_color'] === '1') {
                if (isset($instance['pwn_background_color']) && !empty($instance['pwn_background_color'])) {
                    $widget_custom_background_color .= 'background-color:' . $instance['pwn_background_color'] . ';';
                }
            }
            if ($instance['pwn_use_custom_social_background_color'] === '1') {
                if (isset($instance['pwn_social_background_color']) && !empty($instance['pwn_social_background_color'])) {
                    $social_area_custom_background_color .= 'background-color:' . $instance['pwn_social_background_color'] . ';';
                }
            }
            if ($instance['pwn_use_custom_font_color'] === '1') {
                if (isset($instance['pwn_font_color']) && !empty($instance['pwn_font_color'])) {
                    $custom_font_color = 'color:' . $instance['pwn_font_color'] . ';';
                }
            }
            if ($instance['pwn_use_custom_anchor_link_color'] === '1') {
                if (isset($instance['pwn_anchor_link_color']) && !empty($instance['pwn_anchor_link_color'])) {
                    $custom_anchor_color_style .= ' style="color:' . $instance['pwn_anchor_link_color'] . ';" onMouseOut="this.style.color=\'' . $instance['pwn_anchor_link_color'] .'\'"';
                }
            }
            if ($instance['pwn_use_custom_anchor_link_hover_color'] === '1') {
                if (isset($instance['pwn_anchor_link_hover_color']) && !empty($instance['pwn_anchor_link_hover_color'])) {
                    $custom_anchor_color_style .= ' onMouseOver="this.style.color=\'' . $instance['pwn_anchor_link_hover_color'] .'\'"';
                }
            }
            ?>
            <div class="pwn-profile-widget-ninja-wrapper pwn-profile-widget-ninja-layout-<?php echo (isset( $instance['pwn_alignment'])) ? $instance['pwn_alignment'] : 'center'; ?> pwn-layout-style-<?php echo (isset( $instance['pwn_layout_style'])) ? $instance['pwn_layout_style'] : 'style1'; ?> pwn-profile-widget-ninja-avatar-<?php echo (isset($instance['pwn_profile_image_shape'])) ? $instance['pwn_profile_image_shape'] : ''; ?>"<?php if (!empty($widget_custom_background_color)) { echo ' style="' . $widget_custom_background_color . '"'; } ?>>
                <?php 
                do_action('before_profile_widget_ninja', $instance);
                if (isset($instance['pwn_cover_image_url']) && !empty($instance['pwn_cover_image_url'])) {
                    echo '<div class="pwn-profile-widget-ninja-cover-pic" style="background-image: url('.esc_url($instance['pwn_cover_image_url']).');"></div>';
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
                    (isset($instance['pwn_instagram_url']) && !empty($instance['pwn_instagram_url'])) ||
                    (isset($instance['pwn_pinterest_url']) && !empty($instance['pwn_pinterest_url']))) {
                    $social_media_links_exist = 'y';
                }
                if ($social_media_links_exist === 'y') {
                    echo '<div class="pwn-container-social-links" style="' . $social_area_custom_background_color . '">';
                    // Email ID
                    if (isset($instance['pwn_email_id']) && !empty($instance['pwn_email_id'])) {
                        echo '<a href="mailto:'. $instance['pwn_email_id'] .'" target="_blank"><img src="'. plugins_url('icons/email-1-32x32.png', __FILE__) .'" alt="Email" width="32" height="32" /></a>';
                    }
                    // Facebook URL
                    if (isset($instance['pwn_facebook_url']) && !empty($instance['pwn_facebook_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_facebook_url']) .'" target="_blank"><img src="'. plugins_url('icons/facebook-1-32x32.png', __FILE__) .'" alt="Facebook" width="32" height="32" /></a>';
                    }
                    // Twitter URL
                    if (isset($instance['pwn_twitter_url']) && !empty($instance['pwn_twitter_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_twitter_url']) .'" target="_blank"><img src="'. plugins_url('icons/twitter-1-32x32.png', __FILE__) .'" alt="Twitter" width="32" height="32" /></a>';
                    }
                    // LinkedIn URL
                    if (isset($instance['pwn_linkedin_url']) && !empty($instance['pwn_linkedin_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_linkedin_url']) .'" target="_blank"><img src="'. plugins_url('icons/linkedin-1-32x32.png', __FILE__) .'" alt="LinkedIn" width="32" height="32" /></a>';
                    }
                    // Instagram URL
                    if (isset($instance['pwn_instagram_url']) && !empty($instance['pwn_instagram_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_instagram_url']) .'" target="_blank"><img src="'. plugins_url('icons/instagram-1-32x32.png', __FILE__) .'" alt="Instagram" width="32" height="32" /></a>';
                    }
                    // Pinterest URL
                    if (isset($instance['pwn_pinterest_url']) && !empty($instance['pwn_pinterest_url'])) {
                        echo '<a href="'. esc_url($instance['pwn_pinterest_url']) .'" target="_blank"><img src="'. plugins_url('icons/pinterest-1-32x32.png', __FILE__) .'" alt="Pinterest" width="32" height="32" /></a>';
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
