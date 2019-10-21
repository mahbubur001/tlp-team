<?php $settings = get_option(TLPTeam()->options['settings']); ?>
<div class="wrap">
    <div id="upf-icon-edit-pages" class="icon32 icon32-posts-page"><br /></div>
    <h2><?php _e('TLP Team Settings', TLP_TEAM_SLUG);?></h2>
    <div class="tlp-content-holder">
        <div class="tch-left">
            <form id="tlp-team-settings" onsubmit="tlpTeamSettings(this); return false;">

                <h3><?php _e('General settings',TLP_TEAM_SLUG);?></h3>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="primary-color"><?php _e('Primary Color',TLP_TEAM_SLUG);?></label></th>
                        <td class="">
                            <input name="primary_color" id="primary_color" type="text" value="<?php echo (isset($settings['primary_color']) ? ($settings['primary_color'] ? $settings['primary_color'] : '#0367bf') : '#0367bf'); ?>" class="tlp-color">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="imgWidth"><?php _e('Image Size',TLP_TEAM_SLUG);?></label></th>
                        <td>
                            <select id="rt-feature-img-size" name="feature_img_size">
                                <?php
                                $fSize = !empty($settings['feature_img_size']) ? $settings['feature_img_size'] : TLPTeam()->options['feature_img_size'];
                                $imgSizes = TLPTeam()->get_image_sizes();
                                foreach ($imgSizes as $key => $size){
                                    $slt = $key == $fSize ? "selected" : null;
                                    echo "<option value='{$key}' {$slt}>{$size}</option>";
                                }
                                $fw = !empty($settings['rt_custom_img_size']['width']) ? absint($settings['rt_custom_img_size']['width']) : null;
                                $fh = !empty($settings['rt_custom_img_size']['height']) ? absint($settings['rt_custom_img_size']['height']) : null;
                                $fc = !empty($settings['rt_custom_img_size']['crop']) ? $settings['rt_custom_img_size']['crop'] : 'soft';
                                ?>
                            </select>
                            <div class="rt-custom-image-size-wrap rt-hidden">
                                <div class="item">
                                    <label>Width</label>
                                    <input type="number" name="rt_custom_img_size[width]" class="small-text" value="<?php echo $fw; ?>">
                                </div>
                                <div class="item">
                                    <label>Height</label>
                                    <input type="number" name="rt_custom_img_size[height]" class="small-text" value="<?php echo $fh; ?>">
                                </div>
                                <div class="item">
                                    <label>Crop</label>
                                    <select name="rt_custom_img_size[crop]">
                                        <option value="soft" <?php echo $fc == "soft" ? "selected" : null; ?>>Soft crop</option>
                                        <option value="hard" <?php echo $fc == "hard" ? "selected" : null; ?>>Hard crop</option>
                                    </select>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="slug"><?php _e('Slug',TLP_TEAM_SLUG);?></label></th>
                        <td class="">
                            <input name="slug" id="slug" type="text" value="<?php echo (isset($settings['slug']) ? ($settings['slug'] ? sanitize_title_with_dashes($settings['slug']) : 'team' ) : 'team'); ?>" size="15" class="">
                            <p class="description"><?php _e('Slug configuration',TLP_TEAM_SLUG);?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="link_detail_page"><?php _e('Link To Detail Page',TLP_TEAM_SLUG);?></label></th>
                        <td class="">
                            <fieldset>
                                <legend class="screen-reader-text"><span>Link To Detail Page</span></legend>
                                <?php
                                $opt = array('yes'=>"Yes", 'no'=>"No");
                                $i = 0;
                                $pds = (isset($settings['link_detail_page']) ? ($settings['link_detail_page'] ? $settings['link_detail_page'] : 'yes') : 'yes');
                                foreach ($opt as $key => $value) {
                                    $select = (($pds == $key) ? 'checked="checked"' : null);
                                    echo "<label title='$value'><input type='radio' $select name='link_detail_page' value='$key' > $value</label>";
                                    if($i == 0) echo "<br>";
                                    $i++;
                                }
                                ?>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="css"><?php _e('Custom Css ',TLP_TEAM_SLUG);?></label></th>
                        <td>
                            <textarea name="custom_css" cols="40" rows="6"><?php echo (isset($settings['custom_css']) ? ($settings['custom_css'] ? $settings['custom_css'] : null) : null); ?></textarea>
                        </td>
                    </tr>

                </table>
                <p class="submit"><input type="submit" name="submit" id="tlpSaveButton" class="button button-primary" value="<?php _e('Save Changes', TLP_TEAM_SLUG); ?>"></p>

                <?php wp_nonce_field( TLPTeam()->nonceText(), 'tlp_nonce' ); ?>
            </form>
            <div id="response" class="updated"></div>
        </div>
        <div class="tch-right">
            <div id="pro-feature" class="postbox">
                <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle ui-sortable-handle"><span>TLP Team Pro</span></h3>
                <div class="inside">
                    <p><strong>Pro Feature</strong></p>
                    <ol>
                        <li>Total 15 Layouts (Grid, Table, Isotope & Carousel).</li>
                        <li>40+ Layout variation.</li>
                        <li>Unlimited Shortcode Generator.</li>
                        <li>Visual Composer compatibility.</li>
                        <li>Drag & Drop ordering.</li>
                        <li>Unlimited color.</li>
                        <li>All fields control show/hide.</li>
                        <li>All text size, color and text align control.</li>
                        <li>Square / Rounded Image Style.</li>
                        <li>Grid with Margin or No Margin.</li>
                        <li>Social icon, color size and background color control.</li>
                        <li>Detail page with Popup and Next Preview button.</li>
                        <li>Skill fields with progress bar.</li>
                    </ol>
                    <p></p><a href="https://radiustheme.com/tlp-team-pro-for-wordpress/" class="button button-primary" target="_blank">Get Pro Version</a></p>
                </div>
            </div>
        </div>
    </div>

    <div class="tlp-help">
        <p style="font-weight: bold"><?php _e('Short Code', TLP_TEAM_SLUG );?> :</p>
        <code>[tlpteam col="2" member="4" image="false" orderby="title" order="ASC" layout="1"]</code><br>
        <p><?php _e('col = The number of column you want to create (1,2,3,4)', TLP_TEAM_SLUG );?></p>
        <p><?php _e('member = The number of the member, you want to display', TLP_TEAM_SLUG );?></p>
        <p><?php _e('orderby = Orderby (title , date, menu_order)', TLP_TEAM_SLUG );?></p>
        <p><?php _e('ordr = ASC, DESC', TLP_TEAM_SLUG );?></p>
        <p><?php _e('image = true/false (default false)', TLP_TEAM_SLUG );?></p>
        <p><?php _e('layout = 1,2,3,4,isotope', TLP_TEAM_SLUG );?></p>
        <p class="tlp-help-link"><a class="button-primary" href="http://demo.radiustheme.com/wordpress/plugins/tlp-team/" target="_blank"><?php _e('Demo', TLP_TEAM_SLUG );?></a> <a class="button-primary" href="https://radiustheme.com/how-to-setup-configure-tlp-team-free-version-for-wordpress/" target="_blank"><?php _e('Documentation', TLP_TEAM_SLUG );?></a> </p>
    </div>

</div>
