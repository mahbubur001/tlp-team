<?php

if ( ! class_exists( 'TPLTeamShortCode' ) ):

	/**
	 *
	 */
	class TPLTeamShortCode {

		function __construct() {
			add_shortcode( 'tlpteam', array( $this, 'team_shortcode' ) );
			add_action( 'wp_ajax_tlpTeamPreviewAjaxCall', array( $this, 'team_shortcode' ) );

		}

		function team_shortcode( $atts ) {

			$error = true;
			$html  = $msg = null;

			$preview       = isset( $_REQUEST['sc_id'] ) ? absint( $_REQUEST['sc_id'] ) : 0;
			$scID          = isset( $atts['id'] ) ? absint( $atts['id'] ) : 0;
			$new_shortCode = false;
			if ( $scID ) {
				$post          = get_post( $scID );
				$new_shortCode = $post->post_type === TLPTeam()->getScPostType();
			}

			if ( $new_shortCode || $preview ) {
				$args       = $arg = array();
				$query_args = array(
					'post_type'   => TLPTeam()->post_type,
					'post_status' => 'publish',
				);

				if ( ( ! $preview && $new_shortCode ) || ( $preview && TLPTeam()->verifyNonce() ) ) {
					$rand     = mt_rand();
					$layoutID = "tlp-team-" . $rand;
					if ( $preview ) {
						$error  = false;
						$scMeta = $_REQUEST;
						$layout = isset( $scMeta['layout'] ) ? $scMeta['layout'] : 'layout1';
						$allCol = isset( $scMeta['ttp_column'] ) && ! empty( $scMeta['ttp_column'] ) ? $scMeta['ttp_column'] : array();
						/* LIMIT */
						$query_args['posts_per_page'] = $limit = ( ( empty( $scMeta['ttp_limit'] ) || $scMeta['ttp_limit'] === '-1' ) ? 10000000 : absint( $scMeta['ttp_limit'] ) );
						$pagination                   = ! empty( $scMeta['ttp_pagination'] ) ? true : false;
						if ( $pagination ) {
							$query_args['posts_per_page'] = $posts_per_page = isset( $scMeta['ttp_posts_per_page'] ) ? absint( $scMeta['ttp_posts_per_page'] ) : $limit;
						} else {
							$query_args['posts_per_page'] = $posts_per_page = $limit;
						}

						$order_by = isset( $scMeta['order_by'] ) ? $scMeta['order_by'] : null;
						$order    = isset( $scMeta['order'] ) ? $scMeta['order'] : null;

						$fImg                            = isset( $scMeta['ttp_image'] ) && ! empty( $scMeta['ttp_image'] ) ? true : false;
						$parent_class                    = isset( $scMeta['ttp_parent_class'] ) && ! empty( $scMeta['ttp_parent_class'] ) ? sanitize_text_field( $scMeta['ttp_parent_class'] ) : '';
						$fImgSize                        = isset( $scMeta['ttp_image_size'] ) ? $scMeta['ttp_image_size'] : "medium";
						$character_limit                 = isset( $scMeta['character_limit'] ) && ! empty( $scMeta['character_limit'] ) ? absint( $scMeta['character_limit'] ) : 0;
						$customImgSize                   = ! empty( $scMeta['ttp_custom_image_size'] ) ? $scMeta['ttp_custom_image_size'] : array();
						$arg['disable_detail_page_link'] = isset( $scMeta['disable_detail_page_link'] ) && ! empty( $scMeta['disable_detail_page_link'] );

					} else {
						$scMeta          = get_post_meta( $scID );
						$scMeta['sc_id'] = $scID;
						$layout          = isset( $scMeta['layout'][0] ) ? $scMeta['layout'][0] : 'layout1';
						$allCol          = isset( $scMeta['ttp_column'] ) && ! empty( $scMeta['ttp_column'] ) ? unserialize( $scMeta['ttp_column'][0] ) : array();


						/* LIMIT */
						$query_args['posts_per_page'] = $limit = ( ( empty( $scMeta['ttp_limit'][0] ) || $scMeta['ttp_limit'][0] === '-1' ) ? 10000000 : absint( $scMeta['ttp_limit'][0] ) );
						$pagination                   = ! empty( $scMeta['ttp_pagination'][0] ) ? true : false;

						if ( $pagination ) {
							$query_args['posts_per_page'] = $posts_per_page = isset( $scMeta['ttp_posts_per_page'][0] ) ? absint( $scMeta['ttp_posts_per_page'][0] ) : $limit;
						} else {
							$query_args['posts_per_page'] = $posts_per_page = $limit;
						}

						$order_by = isset( $scMeta['order_by'][0] ) ? $scMeta['order_by'][0] : null;
						$order    = isset( $scMeta['order'][0] ) ? $scMeta['order'][0] : null;

						$fImg                            = ! empty( $scMeta['ttp_image'][0] ) ? true : false;
						$fImgSize                        = isset( $scMeta['ttp_image_size'][0] ) ? $scMeta['ttp_image_size'][0] : "medium";
						$character_limit                 = isset( $scMeta['character_limit'][0] ) && ! empty( $scMeta['character_limit'][0] ) ? absint( $scMeta['character_limit'][0] ) : 0;
						$customImgSize                   = ! empty( $scMeta['ttp_custom_image_size'][0] ) ? unserialize( $scMeta['ttp_custom_image_size'][0] ) : array();
						$arg['disable_detail_page_link'] = isset( $scMeta['disable_detail_page_link'][0] ) && ! empty( $scMeta['disable_detail_page_link'][0] );
						$parent_class                    = isset( $scMeta['ttp_parent_class'][0] ) && ! empty( $scMeta['ttp_parent_class'][0] ) ? sanitize_text_field( $scMeta['ttp_parent_class'][0] ) : '';
					}
					$arg['fImg']            = $fImg;
					$arg['character_limit'] = $character_limit;

					/* post__in */
					if ( ! empty( $scMeta['ttp_post__in'] ) && is_array( $scMeta['ttp_post__in'] ) ) {
						$query_args['post__in'] = $scMeta['ttp_post__in'];
					}
					/* post__not_in */
					if ( ! empty( $scMeta['ttp_post__not_in'] ) && is_array( $scMeta['ttp_post__not_in'] ) ) {
						$query_args['post__not_in'] = $scMeta['ttp_post__not_in'];
					}

					if ( ! in_array( $layout, array_keys( TLPTeam()->scLayouts() ) ) ) {
						$layout = 'layout1';
					}

					$dCol = ( ! empty( $allCol['desktop'] ) ? absint( $allCol['desktop'] ) : 4 );
					$tCol = ( ! empty( $allCol['tab'] ) ? absint( $allCol['tab'] ) : 2 );
					$mCol = ( ! empty( $allCol['mobile'] ) ? absint( $allCol['mobile'] ) : 1 );
					if ( ! in_array( $dCol, array_keys( TLPTeam()->scColumns() ) ) ) {
						$dCol = 3;
					}
					if ( ! in_array( $tCol, array_keys( TLPTeam()->scColumns() ) ) ) {
						$tCol = 2;
					}
					if ( ! in_array( $dCol, array_keys( TLPTeam()->scColumns() ) ) ) {
						$mCol = 1;
					}

					$isIsotope  = preg_match( '/isotope/', $layout );
					$isCarousel = preg_match( '/carousel/', $layout );
					$isGrid     = preg_match( '/layout/', $layout );

					if ( $pagination && ! $isCarousel ) {
						if ( $posts_per_page > $limit ) {
							$posts_per_page = $limit;
						}
						// Set 'posts_per_page' parameter
						$query_args['posts_per_page'] = $posts_per_page;

						$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

						$offset              = $posts_per_page * ( (int) $paged - 1 );
						$query_args['paged'] = $paged;

						// Update posts_per_page
						if ( absint( $query_args['posts_per_page'] ) > $limit - $offset ) {
							$query_args['posts_per_page'] = $limit - $offset;
						}

					}

					if ( $order ) {
						$query_args['order'] = $order;
					}
					if ( $order_by ) {
						$query_args['orderby'] = $order_by;
					}

					$containerDataAttr = " data-layout='{$layout}' data-desktop-col='{$dCol}'  data-tab-col='{$tCol}'  data-mobile-col='{$mCol}'";
					$dCol              = $dCol == 5 ? '24' : round( 12 / $dCol );
					$tCol              = $dCol == 5 ? '24' : round( 12 / $tCol );
					$mCol              = $dCol == 5 ? '24' : round( 12 / $mCol );
					if ( $isCarousel ) {
						$dCol                         = $tCol = $mCol = 12;
						$query_args['posts_per_page'] = $limit;
					}
					$arg['grid'] = "tlp-col-md-{$dCol} tlp-col-sm-{$tCol} tlp-col-xs-{$mCol}";

					if ( $dCol == 2 ) {
						$arg['image_area']   = "tlp-col-md-5 tlp-col-sm-6 tlp-col-xs-12 ";
						$arg['content_area'] = "tlp-col-md-7 tlp-col-sm-6 tlp-col-xs-12 ";
					} else {
						$arg['image_area']   = "tlp-col-md-3 tlp-col-sm-6 tlp-col-xs-12 ";
						$arg['content_area'] = "tlp-col-md-9 tlp-col-sm-6 tlp-col-xs-12 ";
					}

					$teamQuery = new WP_Query( apply_filters( 'tlp_team_query_ars', $query_args ) );
					if ( $teamQuery->have_posts() ) {
						$class = array(
							'tlp-team-container',
							'tlp-team'
						);
						if ( ! empty( $atts['class'] ) ) {
							$class[] = $atts['class'];
						}
						if ( $parent_class ) {
							$class[] = $parent_class;
						}
						$class           = implode( ' ', $class );
						$html            .= $this->layoutStyle( $layoutID, $scMeta, $preview );
						$html            .= sprintf( '<div class="%s" id="%s" %s>', $class, $layoutID, $containerDataAttr );
						$inner_class     = $layout;
						$carouselOptions = '';
						if ( $isCarousel ) {
							$cOpt = ! empty( $scMeta['ttp_carousel_options'] ) ? $scMeta['ttp_carousel_options'] : array();
							if ( $preview ) {
								$autoPlayTimeOut = ! empty( $scMeta['ttp_carousel_autoplay_timeout'] ) ? $scMeta['ttp_carousel_autoplay_timeout'] : 5000;
								$speed           = ! empty( $scMeta['ttp_carousel_speed'][0] ) ? $scMeta['ttp_carousel_speed'][0] : 2000;
							} else {
								$autoPlayTimeOut = ! empty( $scMeta['ttp_carousel_autoplay_timeout'][0] ) ? $scMeta['ttp_carousel_autoplay_timeout'][0] : 5000;
								$speed           = ! empty( $scMeta['ttp_carousel_speed'][0] ) ? $scMeta['ttp_carousel_speed'][0] : 2000;
							}

							$carouselOptions = apply_filters( 'tlp_team_slider_js_options', array(
								"speed"              => $speed,
								"autoPlayTimeOut"    => $autoPlayTimeOut,
								"autoPlay"           => in_array( 'autoplay', $cOpt ) ? true : false,
								"autoplayHoverPause" => in_array( 'autoplayHoverPause', $cOpt ) ? true : false,
								"nav"                => in_array( 'nav', $cOpt ) ? true : false,
								"dots"               => in_array( 'dots', $cOpt ) ? true : false,
								"stopOnHover"        => in_array( 'stop_hover', $cOpt ) ? true : false,
								"loop"               => in_array( 'loop', $cOpt ) ? true : false,
								"lazyLoad"           => in_array( 'lazy_load', $cOpt ) ? true : false,
								"autoHeight"         => in_array( 'auto_height', $cOpt ) ? true : false,
								"rtl"                => in_array( 'rtl', $cOpt ) ? true : false
							), $scMeta );

							$carouselOptions = htmlspecialchars( wp_json_encode( $carouselOptions ) );
							$inner_class     .= " tlp-team-carousel";
						}
						if ( $isIsotope ) {
							$html        .= sprintf( '<div class="tlp-isotope-buttons button-group sort-by-button-group">
									<button data-sort-by="original-order" class="selected">%s</button>
									<button data-sort-by="name">%s</button>
									  <button data-sort-by="designation">%s</button>
								  </div>',
								esc_html__( "Default", "tlp-team" ),
								esc_html__( "Name", "tlp-team" ),
								esc_html__( "Designation", "tlp-team" )
							);
							$inner_class .= ' tlp-team-isotope';
						}


						$html .= "<div class='tlp-row {$inner_class}' data-owl-options='{$carouselOptions}'>";
						while ( $teamQuery->have_posts() ) : $teamQuery->the_post();
							$arg['pID']         = $pID = get_the_ID();
							$arg['title']       = get_the_title();
							$arg['pLink']       = get_permalink();
							$short_bio          = get_post_meta( $pID, 'short_bio', true );
							$arg['short_bio']   = TLPTeam()->get_ttp_short_description( $short_bio, $character_limit );
							$arg['designation'] = get_post_meta( get_the_ID(), 'designation', true );
							$arg['imgSrc']      = ! $fImg && has_post_thumbnail() ? TLPTeam()->getFeatureImageSrc( $pID, $fImgSize, $customImgSize ) : null;
							if ( ! $arg['imgSrc'] ) {
								$arg['content_area'] = "rt-col-md-12";
							}
							$arg['sLink'] = unserialize( get_post_meta( get_the_ID(), 'social', true ) );
							$html         .= TLPTeam()->render( 'layouts/' . $layout, $arg, true );

						endwhile;
						// end row
						$html .= '</div>';
						if ( $pagination && ! $isCarousel ) {
							$html .= TLPTeam()->custom_pagination( $teamQuery, $query_args, $scMeta );

						}
						wp_reset_postdata();
					}

				} else {
					if ( $preview ) {
						$msg = __( 'Session Error !!', 'tlp-team' );
					} else {
						$html .= "<p>" . __( "No shortCode found", 'tlp-team' ) . "</p>";
					}
				}

				if ( $preview ) {
					wp_send_json( array(
						'error' => $error,
						'msg'   => $msg,
						'data'  => $html
					) );
				} else {
					return $html;
				}
			} else {
				$atts = shortcode_atts( array(
					'layout'             => 1,
					'member'             => null,
					'image'              => 'true',
					'col'                => 3,
					'orderby'            => 'date',
					'order'              => 'DESC',
					'name-color'         => null,
					'designation-color'  => null,
					'sd-color'           => null,
					'class'              => null,
					'id'                 => 0,
					'loop'               => 1,
					'autoplay'           => 1,
					'autoplayHoverPause' => 1,
					'nav'                => 1,
					'dots'               => 1,
					'autoHeight'         => 1,
					'lazyLoad'           => 1,
					'rtl'                => 0,
				), $atts, 'tlpteam' );

				return $this->get_team_old_layout( $atts );
			}
		}

		function layoutOne( $title, $pLink, $imgSrc, $designation, $short_bio, $sLink ) {

			$settings = get_option( TLPTeam()->options['settings'] );
			$html     = null;
			$html     .= '<div class="single-team-area">';
			if ( $imgSrc ) {
				if ( $settings['link_detail_page'] == 'no' ) {
					$html .= '<img class="img-responsive" src="' . $imgSrc . '" alt="' . $title . '"/>';
				} else {
					$html .= '<a title="' . $title . '" href="' . $pLink . '"><img class="img-responsive" src="' . $imgSrc . '" alt="' . $title . '"/></a>';
				}
			}
			$html .= '<div class="tlp-content">';
			if ( $settings['link_detail_page'] == 'no' ) {
				$html .= '<h3 class="name">' . $title . '</h3>';
			} else {
				$html .= '<h3 class="name"><a title="' . $title . '" href="' . $pLink . '">' . $title . '</a></h3>';
			}
			if ( $designation ) {
				$html .= '<div class="designation">' . $designation . '</div>';
			}
			$html .= '</div>';
			$html .= '<div class="short-bio">';
			if ( $short_bio ) {
				$html .= '<p>' . $short_bio . '</p>';
			}
			$html .= '</div>';
			$html .= '<div class="tpl-social">';
			if ( $sLink ) {
				foreach ( $sLink as $id => $link ) {
					$html .= "<a href='{$sLink[$id]}' title='$id' target='_blank'><i class='fa fa-$id'></i></a>";
				}
			}
			$html .= '</div>';
			$html .= '</div>';

			return $html;
		}

		function layoutTwo( $title, $pLink, $imgSrc, $designation, $short_bio, $sLink, $image_area, $content_area ) {

			$settings = get_option( TLPTeam()->options['settings'] );
			$html     = null;
			$html     .= '<div class="single-team-area tlp-row">';
			if ( $imgSrc ) {
				$html .= '<div class="' . $image_area . '">';
				if ( $settings['link_detail_page'] == 'no' ) {
					$html .= '<img class="img-responsive" src="' . $imgSrc . '" alt="' . $title . '"/>';
				} else {
					$html .= '<a title="' . $title . '" href="' . $pLink . '"><img class="img-responsive" src="' . $imgSrc . '" alt="' . $title . '"/></a>';
				}
				$html .= '</div>';
			}

			$html .= '<div class="' . $content_area . '">';
			if ( $settings['link_detail_page'] == 'no' ) {
				$html .= '<h3 class="tlp-title">' . $title . '</h3>';
			} else {
				$html .= '<h3 class="tlp-title"><a title="' . $title . '" href="' . $pLink . '">' . $title . '</a></h3>';
			}
			$html .= '<div class="designation">' . $designation . '</div>';
			$html .= '<div class="short-bio">
							    	<p>' . $short_bio . '</p>
							    </div>';
			$html .= '<div class="tpl-social">';
			if ( $sLink ) {
				foreach ( $sLink as $id => $link ) {
					$html .= "<a href='{$sLink[$id]}' title='$id' target='_blank'><i class='fa fa-$id'></i></a>";
				}
			}
			$html .= '</div>';

			$html .= '</div>';
			$html .= '</div>';

			return $html;
		}

		function layoutThree( $title, $pLink, $imgSrc, $designation, $short_bio, $sLink, $image_area, $content_area ) {

			$settings = get_option( TLPTeam()->options['settings'] );
			$html     = null;
			$html     .= '<div class="single-team-area tlp-row">';
			if ( $imgSrc ) {
				$html .= '<div class="' . $image_area . ' round-img">';
				if ( $settings['link_detail_page'] == 'no' ) {
					$html .= '<img class="img-responsive" src="' . $imgSrc . '" alt="' . $title . '"/>';
				} else {
					$html .= '<a title="' . $title . '" href="' . $pLink . '"><img class="img-responsive" src="' . $imgSrc . '" alt="' . $title . '"/></a>';
				}
				$html .= '</div>';
			}
			$html .= '<div class="' . $content_area . '">';
			if ( $settings['link_detail_page'] == 'no' ) {
				$html .= '<h3 class="tlp-title">' . $title . '</h3>';
			} else {
				$html .= '<h3 class="tlp-title"><a title="' . $title . '" href="' . $pLink . '">' . $title . '</a></h3>';
			}
			$html .= '<div class="designation">' . $designation . '</div>';
			$html .= '<div class="short-bio">
						    	<p>' . $short_bio . '</p>
						    </div>';
			$html .= '<div class="tpl-social">';
			if ( $sLink ) {
				foreach ( $sLink as $id => $link ) {
					$html .= "<a href='{$sLink[$id]}' title='$id' target='_blank'><i class='fa fa-$id'></i></a>";
				}
			}
			$html .= '</div>';

			$html .= '</div>';
			$html .= '</div>';

			return $html;
		}

		function layoutFour( $title, $pLink, $imgSrc, $designation, $short_bio, $sLink, $image_area, $content_area ) {

			$settings = get_option( TLPTeam()->options['settings'] );
			$html     = null;
			$html     .= '<div class="single-team-area">';
			if ( $imgSrc ) {
				$html .= '<div class="round-img">';
				if ( $settings['link_detail_page'] == 'no' ) {
					$html .= '<img class="img-responsive" src="' . $imgSrc . '" alt="' . $title . '"/>';
				} else {
					$html .= '<a title="' . $title . '" href="' . $pLink . '"><img class="img-responsive" src="' . $imgSrc . '" alt="' . $title . '"/></a>';
				}
				$html .= '</div>';
			}

			$html .= '<div class="tlp-team-content">';
			if ( $settings['link_detail_page'] == 'no' ) {
				$html .= '<h3 class="tlp-title">' . $title . '</h3>';
			} else {
				$html .= '<h3 class="tlp-title"><a title="' . $title . '" href="' . $pLink . '">' . $title . '</a></h3>';
			}
			$html .= '<div class="designation">' . $designation . '</div>';
			$html .= '<div class="short-bio">
						        <p>' . $short_bio . '</p>
						    </div>';
			$html .= '<div class="tpl-social">';
			if ( $sLink ) {
				foreach ( $sLink as $id => $link ) {
					$html .= "<a href='{$sLink[$id]}' title='$id' target='_blank'><i class='fa fa-$id'></i></a>";
				}
			}
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';

			return $html;
		}

		function layoutCarousel( $title, $pLink, $imgSrc, $designation, $short_bio, $sLink, $image_area, $content_area ) {

			$settings = get_option( TLPTeam()->options['settings'] );
			$html     = null;
			$html     .= '<div class="single-team-area">';
			if ( $imgSrc ) {
				$html .= '<div class="round-img">';
				if ( $settings['link_detail_page'] == 'no' ) {
					$html .= '<img class="img-responsive" src="' . $imgSrc . '" alt="' . $title . '"/>';
				} else {
					$html .= '<a title="' . $title . '" href="' . $pLink . '"><img class="img-responsive" src="' . $imgSrc . '" alt="' . $title . '"/></a>';
				}
				$html .= '</div>';
			}

			$html .= '<div class="tlp-team-content">';
			if ( $settings['link_detail_page'] == 'no' ) {
				$html .= '<h3 class="tlp-title">' . $title . '</h3>';
			} else {
				$html .= '<h3 class="tlp-title"><a title="' . $title . '" href="' . $pLink . '">' . $title . '</a></h3>';
			}
			$html .= '<div class="designation">' . $designation . '</div>';
			$html .= '<div class="short-bio">
						        <p>' . $short_bio . '</p>
						    </div>';
			$html .= '<div class="tpl-social">';
			if ( $sLink ) {
				foreach ( $sLink as $id => $link ) {
					$html .= "<a href='{$sLink[$id]}' title='$id' target='_blank'><i class='fa fa-$id'></i></a>";
				}
			}
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';

			return $html;
		}

		function layoutIsotope( $title, $pLink, $imgSrc, $designation, $grid ) {

			$settings = get_option( TLPTeam()->options['settings'] );
			$html     = null;
			$html     .= '<div class="single-team-area">';
			if ( $imgSrc ) {
				if ( $settings['link_detail_page'] == 'no' ) {
					$html .= '<img class="img-responsive" src="' . $imgSrc . '" alt="' . $title . '"/>';
				} else {
					$html .= '<a title="' . $title . '" href="' . $pLink . '"><img class="img-responsive" src="' . $imgSrc . '" alt="' . $title . '"/></a>';
				}
			}
			$html .= '<div class="tlp-content">';
			if ( $settings['link_detail_page'] == 'no' ) {
				$html .= '<h3 class="name">' . $title . '</h3>';
			} else {
				$html .= '<h3 class="name"><a title="' . $title . '" href="' . $pLink . '">' . $title . '</a></h3>';
			}
			if ( $designation ) {
				$html .= '<div class="designation">' . $designation . '</div>';
			}
			$html .= '</div>';
			$html .= '</div>';

			return $html;
		}

		private function layoutStyle( $layoutID, $scMeta, $preview = false, $old = false ) {
			$css = null;
			if ( $old ) {
				$name        = isset( $scMeta['name-color'] ) && ! empty( $scMeta['name-color'] ) ? $scMeta['name-color'] : null;
				$designation = isset( $scMeta['designation-color'] ) && ! empty( $scMeta['designation-color'] ) ? $scMeta['designation-color'] : null;
				$sd          = isset( $scMeta['sd-color'] ) && ! empty( $scMeta['sd-color'] ) ? $scMeta['sd-color'] : null;
				if ( $name ) {
					$css .= "#{$layoutID} .single-team-area h3,
							#{$layoutID} .single-team-area h3 a{ color: {$name};}";
				}
				if ( $designation ) {
					$css .= "#{$layoutID} .single-team-area .designation{ color: {$designation};}";
				}
				if ( $sd ) {
					$css .= "#{$layoutID} .single-team-area .short-bio{ color: {$sd};}";
				}

			} else {

				// Variable
				if ( $preview ) {
					$primaryColor = isset( $scMeta['primary_color'] ) && ! empty( $scMeta['primary_color'] ) ? $scMeta['primary_color'] : null;
					$button       = isset( $scMeta['ttp_button_style'] ) && ! empty( $scMeta['ttp_button_style'] ) ? $scMeta['ttp_button_style'] : null;
					$name         = isset( $scMeta['name'] ) && ! empty( $scMeta['name'] ) ? $scMeta['name'] : null;
					$designation  = isset( $scMeta['designation'] ) && ! empty( $scMeta['designation'] ) ? $scMeta['designation'] : null;
					$short_bio    = isset( $scMeta['short_bio'] ) && ! empty( $scMeta['short_bio'] ) ? $scMeta['short_bio'] : null;
				} else {

					$primaryColor = isset( $scMeta['primary_color'][0] ) && ! empty( $scMeta['primary_color'][0] ) ? $scMeta['primary_color'][0] : null;
					$button       = isset( $scMeta['ttp_button_style'][0] ) && ! empty( $scMeta['ttp_button_style'][0] ) ? unserialize( $scMeta['ttp_button_style'][0] ) : null;
					$name         = isset( $scMeta['name'][0] ) && ! empty( $scMeta['name'][0] ) ? unserialize( $scMeta['name'][0] ) : null;
					$designation  = isset( $scMeta['designation'][0] ) && ! empty( $scMeta['designation'][0] ) ? unserialize( $scMeta['designation'][0] ) : null;
					$short_bio    = isset( $scMeta['short_bio'][0] ) && ! empty( $scMeta['short_bio'][0] ) ? unserialize( $scMeta['short_bio'][0] ) : null;
				}

				if ( $primaryColor ) {
					$css .= "#{$layoutID} .tlp-content, 
						#{$layoutID} .layout1 .tlp-content{background:{$primaryColor };}";
					$css .= "#{$layoutID} .short-desc,
							#{$layoutID} .tlp-team-isotope .tlp-content, 
							#{$layoutID} .tpl-social a, 
							#{$layoutID} .tpl-social li a.fa{background: $primaryColor;}";
				}

				/* button */
				if ( ! empty( $button ) ) {
					if ( isset( $button['bg'] ) && ! empty( $button['bg'] ) ) {
						$css .= "#{$layoutID} .tlp-pagination-wrap .tlp-pagination > li > a, 
							#{$layoutID} .tlp-pagination-wrap .tlp-pagination > li > span,
							#{$layoutID} .tlp-isotope-buttons.button-group button,
							#{$layoutID} .owl-theme .owl-nav [class*=owl-],
							#{$layoutID} .owl-theme .owl-dots .owl-dot span{";
						$css .= "background-color: {$button['bg']};";
						$css .= "}";
					}
					if ( ! empty( $button['hover_bg'] ) ) {
						$css .= "#{$layoutID} .tlp-pagination-wrap .tlp-pagination > li > a:hover, 
							#{$layoutID} .tlp-pagination-wrap .tlp-pagination > li > span:hover,
							#{$layoutID} .owl-theme .owl-dots .owl-dot span:hover,
							#{$layoutID} .owl-theme .owl-nav [class*=owl-]:hover,
							#{$layoutID} .tlp-isotope-buttons.button-group button:hover{";
						$css .= "background-color: {$button['hover_bg']};";
						$css .= "}";
					}
					if ( ! empty( $button['active_bg'] ) ) {
						$css .= "#{$layoutID} .tlp-isotope-buttons.button-group button.selected,
					#{$layoutID} .owl-theme .owl-dots .owl-dot.active span,
					#{$layoutID} .tlp-pagination-wrap .tlp-pagination > .active > span{";
						$css .= "background-color: {$button['active_bg']};";
						$css .= "}";
					}
					if ( ! empty( $button['text'] ) ) {
						$css .= "#{$layoutID} .tlp-pagination-wrap .tlp-pagination > li > a, 
							#{$layoutID} .tlp-pagination-wrap .tlp-pagination > li > span,
							#{$layoutID} .tlp-isotope-buttons.button-group button,
							#{$layoutID} .owl-theme .owl-nav [class*=owl-]{";
						$css .= "color: {$button['text']};";
						$css .= "}";
					}
					if ( ! empty( $button['hover_text'] ) ) {
						$css .= "#{$layoutID} .tlp-pagination-wrap .tlp-pagination > li > a:hover, 
							#{$layoutID} .tlp-pagination-wrap .tlp-pagination > li > span:hover,
							#{$layoutID} .tlp-isotope-buttons.button-group button:hover,
							#{$layoutID} .owl-theme .owl-nav [class*=owl-]:hover{";
						$css .= "color: {$button['hover_text']};";
						$css .= "}";
					}
				}

				// Name
				if ( ! empty( $name ) ) {
					$cCss = null;
					$cCss .= isset( $name['color'] ) && ! empty( $name['color'] ) ? "color:" . $name['color'] . ";" : null;
					$cCss .= isset( $name['align'] ) && ! empty( $name['align'] ) ? "text-align:" . $name['align'] . ";" : null;
					$cCss .= isset( $name['size'] ) && ! empty( $name['size'] ) ? "font-size:" . $name['size'] . "px;" : null;
					$cCss .= isset( $name['weight'] ) && ! empty( $name['weight'] ) ? "font-weight:" . $name['weight'] . ";" : null;
					if ( $cCss ) {
						$css .= "#{$layoutID} .single-team-area h3,
							#{$layoutID} .single-team-area h3 a{ {$cCss} }";
					}
					if ( isset( $name['hover_color'] ) && ! empty( $name['hover_color'] ) ) {
						$css .= "#{$layoutID} .single-team-area h3:hover,
							#{$layoutID} .single-team-area h3 a:hover{ color: {$name['hover_color']}; }";
					}

				}
				// Designation
				if ( ! empty( $designation ) ) {
					$cCss = null;
					$cCss .= isset( $designation['color'] ) && ! empty( $designation['color'] ) ? "color:" . $designation['color'] . ";" : null;
					$cCss .= isset( $designation['align'] ) && ! empty( $designation['align'] ) ? "text-align:" . $designation['align'] . ";" : null;
					$cCss .= isset( $designation['size'] ) && ! empty( $designation['size'] ) ? "font-size:" . $designation['size'] . "px;" : null;
					$cCss .= isset( $designation['weight'] ) && ! empty( $designation['weight'] ) ? "font-weight:" . $designation['weight'] . ";" : null;

					$css .= "#{$layoutID} .designation,
						#{$layoutID} .designation a{ {$cCss} }";

					if ( isset( $designation['hover_color'] ) && ! empty( $designation['hover_color'] ) ) {
						$css .= "#{$layoutID} .designation:hover,
							#{$layoutID} .designation a:hover{ color: {$designation['hover_color']}; }";
					}
				}

				// Short biography
				if ( ! empty( $short_bio ) ) {
					$cCss = null;
					$cCss .= isset( $short_bio['color'] ) && ! empty( $short_bio['color'] ) ? "color:" . $short_bio['color'] . ";" : null;
					$cCss .= isset( $short_bio['align'] ) && ! empty( $short_bio['align'] ) ? "text-align:" . $short_bio['align'] . ";" : null;
					$cCss .= isset( $short_bio['size'] ) && ! empty( $short_bio['size'] ) ? "font-size:" . $short_bio['size'] . "px;" : null;
					$cCss .= isset( $short_bio['weight'] ) && ! empty( $short_bio['weight'] ) ? "font-weight:" . $short_bio['weight'] . ";" : null;
					$css  .= "#{$layoutID} .short-bio p,#{$layoutID} .short-bio p a{{$cCss}}";
					if ( isset( $short_bio['hover_color'] ) && ! empty( $short_bio['hover_color'] ) ) {
						$css .= "#{$layoutID} .designation:hover,
							#{$layoutID} .designation a:hover{ color: {$short_bio['hover_color']}; }";
					}
				}
			}

			if ( ! empty( $css ) ) {
				$css = "<style>{$css}</style>";
			}


			return $css;

		}

		/**
		 * @param $atts
		 *
		 * @return string|null
		 */
		public function get_team_old_layout( $atts ) {
			$atts['image'] = 'true' === $atts['image'];

			if ( ! in_array( $atts['col'], array_keys( TLPTeam()->scColumns() ) ) ) {
				$atts['col'] = 3;
			}
			if ( ! in_array( $atts['layout'], array_keys( TLPTeam()->oldScLayouts() ) ) ) {
				$atts['layout'] = 1;
			}
			$posts_per_page = $atts['member'] ? absint( $atts['member'] ) : '-1';

			$html = null;

			$args = array(
				'post_type'      => 'team',
				'post_status'    => 'publish',
				'posts_per_page' => $posts_per_page,
				'orderby'        => $atts['orderby'],
				'order'          => $atts['order']
			);
			if ( is_user_logged_in() && is_super_admin() ) {
				$args['post_status'] = array( 'publish', 'private' );
			}
			/* post__in */
			$post__in = $atts['id'] ? trim( $atts['id'] ) : '';
			if ( $post__in ) {
				$post__in         = explode( ',', $post__in );
				$args['post__in'] = $post__in;
			}

			$settings      = get_option( TLPTeam()->options['settings'] );
			$fImgSize      = ! empty( $settings['feature_img_size'] ) ? $settings['feature_img_size'] : TLPTeam()->options['feature_img_size'];
			$customImgSize = ! empty( $settings['rt_custom_img_size'] ) ? $settings['rt_custom_img_size'] : array();

			$teamQuery = new WP_Query( $args );
			$layoutID  = "tlp-team-" . mt_rand();
			$grid      = $atts['col'] == 5 ? '24' : 12 / $atts['col'];
			if ( $teamQuery->have_posts() ) {
				$class = array(
					'tlp-team-container',
					'tlp-team'
				);
				if ( ! empty( $atts['class'] ) ) {
					$class[] = $atts['class'];
				}
				$class = implode( ' ', $class );
				$html  .= "<div class='" . esc_attr( $class ) . "' id='{$layoutID}' data-desktop='{$grid}'>";
				$html  .= $this->layoutStyle( $layoutID, $atts, false, true );
				$class = 'layout' . $atts['layout'];
				$attr  = '';
				if ( $atts['layout'] == 'carousel' ) {
					$loop               = $atts['loop'] == 1 ? 1 : 0;
					$autoplay           = $atts['autoplay'] == 1 ? 1 : 0;
					$items              = isset( $atts['col'] ) ? absint( $atts['col'] ) : 3;
					$nav                = $atts['nav'] == 1 ? 1 : 0;
					$dots               = $atts['dots'] == 1 ? 1 : 0;
					$autoplayHoverPause = $atts['autoplayHoverPause'] == 1 ? 1 : 0;
					$autoHeight         = $atts['autoHeight'] == 1 ? 1 : 0;
					$lazyLoad           = $atts['lazyLoad'] == 1 ? 1 : 0;
					$rtl                = $atts['rtl'] == 1 ? 1 : 0;
					$attr               .= " data-owl-options='{\"items\": {$items},\"loop\": {$loop},\"autoplay\": {$autoplay}, \"nav\": {$nav}, \"dots\": {$dots}, \"autoplayHoverPause\": {$autoplayHoverPause}, \"autoHeight\": {$autoHeight}, \"lazyLoad\": {$lazyLoad}, \"rtl\": {$rtl} }'";
				}
				if ( $atts['layout'] == 'isotope' ) {
					$html  .= '<div class="button-group sort-by-button-group">
									<button data-sort-by="original-order" class="selected">Default</button>
									<button data-sort-by="name">Name</button>
									  <button data-sort-by="designation">Designation</button>
								  </div>';
					$class .= ' tlp-team-isotope';
				}


				$html .= "<div class='tlp-row {$class}' {$attr}>";
				while ( $teamQuery->have_posts() ) : $teamQuery->the_post();
					$pID         = get_the_ID();
					$title       = get_the_title();
					$pLink       = get_permalink();
					$short_bio   = get_post_meta( get_the_ID(), 'short_bio', true );
					$designation = get_post_meta( get_the_ID(), 'designation', true );

					if ( has_post_thumbnail() ) {
						$imgSrc = TLPTeam()->getFeatureImageSrc( $pID, $fImgSize, $customImgSize );
					} else {
						$imgSrc = TLPTeam()->assetsUrl . 'images/demo.jpg';
					}

					if ( $atts['col'] == 2 ) {
						$image_area   = "tlp-col-md-5 tlp-col-sm-6 tlp-col-xs-12 ";
						$content_area = "tlp-col-md-7 tlp-col-sm-6 tlp-col-xs-12 ";
					} else {
						$image_area   = "tlp-col-md-3 tlp-col-sm-6 tlp-col-xs-12 ";
						$content_area = "tlp-col-md-9 tlp-col-sm-6 tlp-col-xs-12 ";
					}
					if ( ! $atts['image'] ) {
						$content_area = "tlp-col-md-12";
						$imgSrc       = null;
					}

					$sLink = unserialize( get_post_meta( get_the_ID(), 'social', true ) );
					$html  .= "<div class='team-member tlp-col-md-{$grid} tlp-col-sm-{$grid} tlp-col-xs-12 tlp-equal-height'>";
//						$html  .= TLPTeam()->render( 'layouts/' . $layout, $arg, true );
					switch ( $atts['layout'] ) {
						case 1:
							$html .= $this->layoutOne( $title, $pLink, $imgSrc, $designation, $short_bio, $sLink );
							break;

						case 2:
							$html .= $this->layoutTwo( $title, $pLink, $imgSrc, $designation, $short_bio, $sLink,
								$image_area, $content_area );
							break;

						case 3:
							$html .= $this->layoutThree( $title, $pLink, $imgSrc, $designation, $short_bio, $sLink,
								$image_area, $content_area );
							break;

						case 4:
							$html .= $this->layoutFour( $title, $pLink, $imgSrc, $designation, $short_bio, $sLink,
								$image_area, $content_area );
							break;

						case 'isotope':
							$html .= $this->layoutIsotope( $title, $pLink, $imgSrc, $designation, $grid );
							break;

						case 'carousel':
							$html .= $this->layoutCarousel( $title, $pLink, $imgSrc, $designation, $short_bio, $sLink,
								$image_area, $content_area );
							break;

						default:
							# code...
							break;
					}
					$html .= "</div>";

				endwhile;
				wp_reset_postdata();
				// end row
				$html .= '</div>';
				$html .= '</div>'; // end container
			} else {
				$html .= "<p>" . __( 'No member found', "tlp-team" ) . "</p>";
			}

			return $html;
		}


	}

endif;
