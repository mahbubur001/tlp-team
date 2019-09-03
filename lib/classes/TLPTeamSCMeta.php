<?php

if (!class_exists('TLPTeamSCMeta')):
    /**
     *
     */
    class TLPTeamSCMeta
    {

        function __construct() {
            add_action('add_meta_boxes', array($this, 'team_sc_meta_boxes'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            add_action('save_post', array($this, 'save_team_sc_meta_data'), 10, 3);
            add_action('edit_form_after_title', array($this, 'team_sc_after_title'));
            add_action('admin_init', array($this, 'tlp_team_pro_remove_all_meta_box'));
        }

        function team_sc_after_title($post) {
            if (TLPTeam()->getScPostType() !== $post->post_type) {
                return;
            }

            $html = null;
            $html .= '<div class="postbox" style="margin-bottom: 0;"><div class="inside">';
            $html .= '<p><input type="text" onfocus="this.select();" readonly="readonly" value="[tlpteam id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]" class="large-text code tlp-code-sc">
            <input type="text" onfocus="this.select();" readonly="readonly" value="&#60;&#63;php echo do_shortcode( &#39;[tlpteam id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]&#39; ) &#63;&#62;" class="large-text code tlp-code-sc">
            </p>';
            $html .= '</div></div>';
            echo $html;
        }

        function tlp_team_pro_remove_all_meta_box() {
            if (is_admin()) {
                add_filter("get_user_option_meta-box-order_{TLPTeam()->getScPostType()}",
                    array($this, 'remove_all_meta_boxes_team_sc'));
            }
        }

        function remove_all_meta_boxes_team_sc() {
            global $wp_meta_boxes;
            $publishBox = $wp_meta_boxes[TLPTeam()->getScPostType()]['side']['core']['submitdiv'];
            $scBox = $wp_meta_boxes[TLPTeam()->getScPostType()]['normal']['high']['tlp_team_sc_settings_meta'];
            $scPreviewBox = $wp_meta_boxes[TLPTeam()->getScPostType()]['normal']['high']['tlp_team_sc_preview_meta'];
            $wp_meta_boxes[TLPTeam()->getScPostType()] = array(
                'side'   => array('core' => array('submitdiv' => $publishBox)),
                'normal' => array(
                    'high' => array(
                        'tlp_team_sc_settings_meta' => $scBox,
                        'tlp_team_sc_preview_meta'  => $scPreviewBox
                    )
                )
            );

            return array();
        }

        function admin_enqueue_scripts() {

            global $pagenow, $typenow;
            // validate page
            if (!in_array($pagenow, array('post.php', 'post-new.php', 'edit.php'))) {
                return;
            }
            if ($typenow != TLPTeam()->getScPostType()) {
                return;
            }
            wp_dequeue_script('autosave');
            wp_enqueue_media();

            // scripts
            wp_enqueue_script(array(
                'jquery',
                'wp-color-picker-alpha',
                'tlp-isotope',
                'tlp-owl-carousel',
                TLPTeam()->getSelect2JsId(),
                'tlp-team',
                'tlp-team-admin',
            ));

            // styles
            wp_enqueue_style(array(
                'wp-color-picker',
                'tlp-select2',
                'tlp-owl-carousel',
                'tlp-owl-carousel-theme',
                'tlp-fontawsome',
                'tlp-team',
                'tlp-team-admin',
            ));

            wp_localize_script('tlp-team-admin', 'ttp',
                array(
                    'nonceID' => TLPTeam()->nonceID(),
                    'nonce'   => wp_create_nonce(TLPTeam()->nonceText()),
                    'ajaxurl' => admin_url('admin-ajax.php')
                ));
        }

        function team_sc_meta_boxes() {

            add_meta_box(
                'tlp_team_sc_settings_meta',
                __('Short Code Generator', 'tlp-team'),
                array($this, 'tlp_team_sc_settings_selection'),
                TLPTeam()->getScPostType(),
                'normal',
                'high');
            add_meta_box(
                'tlp_team_sc_preview_meta',
                __('Layout Preview', 'tlp-team'),
                array($this, 'tlp_team_sc_preview_selection'),
                TLPTeam()->getScPostType(),
                'normal',
                'high');
        }

        function tlp_team_sc_preview_selection() {
            $html = null;
            $html .= "<div id='tlp-team-response'><span class='spinner'></span></div>";
            $html .= "<div id='tlp-team-preview-container'></div>";
            echo $html;
        }

        function tlp_team_sc_settings_selection($post) {
            wp_nonce_field(TLPTeam()->nonceText(), TLPTeam()->nonceID());
            $html = null;
            $html .= '<div id="sc-tabs" class="rt-tab-container">';
            $html .= '<ul class="rt-tab-nav">
                            <li class="active"><a href="#sc-layout-settings">' . __('Layout Settings', 'tlp-team') . '</a></li>
                            <li><a href="#sc-filtering">' . __('Filtering', 'tlp-team') . '</a></li>
                            <li><a href="#sc-styling">' . __('Styling', 'tlp-team') . '</a></li>
                          </ul>';

            $html .= '<div id="sc-layout-settings" class="rt-tab-content" style="display: block">';
            $html .= TLPTeam()->rtFieldGenerator(TLPTeam()->get_sc_layout_settings_meta_fields());
            $html .= '</div>';

            $html .= '<div id="sc-filtering" class="rt-tab-content">';
            $html .= TLPTeam()->rtFieldGenerator(TLPTeam()->get_sc_query_filter_meta_fields());
            $html .= '</div>';

            $html .= '<div id="sc-styling" class="rt-tab-content">';
            $html .= TLPTeam()->rtFieldGenerator(TLPTeam()->get_sc_field_style_meta());
            $html .= '</div>';
            $html .= '</div>';

            echo $html;
        }

        function save_team_sc_meta_data($post_id, $post, $update) {

            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            if (!TLPTeam()->verifyNonce()) {
                return $post_id;
            }

            if (TLPTeam()->getScPostType() != $post->post_type) {
                return $post_id;
            }

            $request = $_REQUEST;
            $mates = TLPTeam()->getScTeamMetaFields();
            TLPTeam()->updateMetaFields($post_id, $mates, $request);

        }
    }
endif;