<?php global $TLPteam;

?>
<div class="wrap">
	<div id="upf-icon-edit-pages" class="icon32 icon32-posts-page"><br/></div>
	<h2><?php _e( 'Shortcode generator', TLP_TEAM_SLUG ); ?></h2>
	<div class="tlp-content-holder">
		<div class="tch-left">
			<div class="postbox" id="scg-wrapper">
				<h3 class="hndle ui-sortable-handle"><span>Shortcode</span></h3>
				<div class="inside">
					<h4>Layout and filter</h4>
					<div class="scg-wrapper">
						<div class="scg-item-wrap">
							<div class="scg-label"><label>Layout</label></div>
							<div class="scg-field">
								<select name="layout" id="sc-layout">
									<?php
									$layouts = $TLPteam->scLayouts();
									foreach ( $layouts as $key => $layout ) {
										echo "<option value={$key}>$layout</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="scg-item-wrap">
							<div class="scg-label"><label>Column / Number to Display at slider</label></div>
							<div class="scg-field">
								<select name="col">
									<option value="">Default</option>
									<?php
									$cols = $TLPteam->scColumns();
									foreach ( $cols as $key => $col ) {
										echo "<option value={$key}>$col</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="scg-item-wrap">
							<div class="scg-label"><label>Order by</label></div>
							<div class="scg-field">
								<select name="orderby">
									<option value="">Default</option>
									<?php
									$obs = $TLPteam->scOrderBy();
									foreach ( $obs as $key => $ob ) {
										echo "<option value={$key}>$ob</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="scg-item-wrap">
							<div class="scg-label"><label>Order</label></div>
							<div class="scg-field">
								<select name="order">
									<option value="">Default</option>
									<?php
									$orders = $TLPteam->scOrder();
									foreach ( $orders as $key => $order ) {
										echo "<option value={$key}>$order</option>";
									}
									?>
								</select>
							</div>
						</div>
                        <div class="scg-item-wrap">
                            <div class="scg-label"><label><?php esc_html_e("Include Member Only", "tlp-team"); ?></label></div>
                            <div class="scg-field">
                                <input type="text" name="id">
                                <p class="description"><?php esc_html_e("List of member IDs (comma-separated values, for example: 1,2,3)", "tlp-team") ?></p>
                            </div>
                        </div>
						<div class="scg-item-wrap">
							<div class="scg-label"><label>Total Member</label></div>
							<div class="scg-field"><input type="number" name="member">
								<p class="description">Leave it blank to display all. (Only number is allowed)</p>
							</div>
						</div>
						<div class="scg-item-wrap" id="carousel-sc" style="display: none;">
							<div class="scg-label"><label>Carousel Options</label></div>
							<div class="scg-field">
                                <?php
                                    $options = $TLPteam->owlProperty();
                                    foreach ($options as $optId => $optName){
                                        echo "<label for='csc-{$optId}'><input id='csc-{$optId}' value='1' type='checkbox' name='{$optId}'> {$optName}</label>";
                                    }
                                ?>
							</div>
						</div>
                        <div class="scg-item-wrap">
                            <div class="scg-label"><label>Wrapper Class</label></div>
                            <div class="scg-field"><input type="text" name="class"/>
                            </div>
                        </div>
					</div>
					<h4>Style</h4>
					<div class="scg-wrapper">
						<div class="scg-item-wrap">
							<div class="scg-label"><label>Name color</label></div>
							<div class="scg-field"><input type="text" class="tlp-color" name="name-color"></div>
						</div>
						<div class="scg-item-wrap">
							<div class="scg-label"><label>Designation color</label></div>
							<div class="scg-field"><input type="text" class="tlp-color" name="designation-color"></div>
						</div>
						<div class="scg-item-wrap">
							<div class="scg-label"><label>Short description color</label></div>
							<div class="scg-field"><input type="text" class="tlp-color" name="sd-color"></div>
						</div>
					</div>

					<div id="sc-output">
						<textarea></textarea>
						<p class="description">Click to copy the shortcode.</p>
					</div>

				</div>
			</div>
		</div>
		<div class="tch-right">
			<div id="pro-feature" class="postbox">
				<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="hndle ui-sortable-handle"><span>TLP Team Pro</span></h3>
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
					<p></p><a href="https://radiustheme.com/tlp-team-pro-for-wordpress/" class="button button-primary"
					          target="_blank">Get Pro Version</a></p>
				</div>
			</div>
		</div>
	</div>

	<div class="tlp-help">
		<p style="font-weight: bold"><?php _e( 'Short Code', TLP_TEAM_SLUG ); ?> :</p>
		<code>[tlpteam col="2" member="4" orderby="title" order="ASC" layout="1"]</code><br>
		<p><?php _e( 'col = The number of column you want to create (1,2,3,4)', TLP_TEAM_SLUG ); ?></p>
		<p><?php _e( 'member = The number of the member, you want to display', TLP_TEAM_SLUG ); ?></p>
		<p><?php _e( 'orderby = Orderby (title , date, menu_order)', TLP_TEAM_SLUG ); ?></p>
		<p><?php _e( 'ordr = ASC, DESC', TLP_TEAM_SLUG ); ?></p>
		<p><?php _e( 'layout = 1,2,3,isotope', TLP_TEAM_SLUG ); ?></p>
		<p class="tlp-help-link"><a class="button-primary"
		                            href="http://demo.radiustheme.com/wordpress/plugins/tlp-team/"
		                            target="_blank"><?php _e( 'Demo', TLP_TEAM_SLUG ); ?></a> <a class="button-primary"
		                                                                                         href="https://radiustheme.com/how-to-setup-configure-tlp-team-free-version-for-wordpress/"
		                                                                                         target="_blank"><?php _e( 'Documentation',
					TLP_TEAM_SLUG ); ?></a></p>
	</div>

</div>
