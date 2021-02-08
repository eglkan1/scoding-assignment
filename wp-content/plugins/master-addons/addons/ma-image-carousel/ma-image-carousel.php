<?php
namespace Elementor;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 10/26/19
 */

// Elementor Classes
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use MasterAddons\Inc\Helper\Master_Addons_Helper;


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }


if ( ! defined( 'ABSPATH' ) ) exit; // If this file is called directly, abort.

class Master_Addons_Image_Carousel extends Widget_Base {

	public function get_name() {
		return 'ma-image-carousel';
	}

	public function get_title() {
		return __( 'MA Image Carousel', MELA_TD);
	}

	public function get_icon() {
		return 'ma-el-icon eicon-media-carousel';
	}

	public function get_categories() {
		return [ 'master-addons' ];
	}

	public function get_script_depends() {
		return [ 'jquery-slick', 'master-addons-scripts' ];
	}

	public function get_style_depends() {
		return [ 'master-addons-main-style' ];
	}

	public function get_keywords() {
		return [ 'image', 'image carousel', 'image slider', 'carousel text', 'Image Carousel with Text' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'ma_el_image_carousel',
			[
				'label' => __( 'Images', MELA_TD ),
			]
		);

		$this->add_control(
			'jltma_image_carousel_items',
			[
				'label'             => __( 'Carousel Items', MELA_TD ),
				'type'              => Controls_Manager::REPEATER,
				'default'           => [

					[
						'title'                                  => __( 'Item #1', MELA_TD ),
						'subtitle'                               => __( '', MELA_TD )
					],
					[
						'title'                                  => __( 'Item #2', MELA_TD ),
						'subtitle'                               => __( '', MELA_TD ),
						'jltma_image_carousel_button_one_text'   => __( 'Details', MELA_TD ),
						'jltma_image_carousel_link_one_url'      => "#",
						'jltma_image_carousel_button_two_text'   => __( 'Demo', MELA_TD ),
						'jltma_image_carousel_link_two_url'      => "#"
					],
					[
						'title'                                  => __( 'Item #3', MELA_TD ),
						'subtitle'                               => __( '', MELA_TD ),
						'jltma_image_carousel_button_one_text'   => __( 'Details', MELA_TD ),
						'jltma_image_carousel_link_one_url'      => "#",
						'jltma_image_carousel_button_two_text'   => __( 'Demo', MELA_TD ),
						'jltma_image_carousel_link_two_url'      => "#"
					],
					[
						'title'                                  => __( 'Item #4', MELA_TD ),
						'subtitle'                               => __( '', MELA_TD ),
						'jltma_image_carousel_button_one_text'   => __( 'Details', MELA_TD ),
						'jltma_image_carousel_link_one_url'      => "#",
						'jltma_image_carousel_button_two_text'   => __( 'Demo', MELA_TD ),
						'jltma_image_carousel_link_two_url'      => "#"
					],
					[
						'title'                                  => __( 'Item #5', MELA_TD ),
						'subtitle'                               => __( '', MELA_TD ),
						'jltma_image_carousel_button_one_text'   => __( 'Details', MELA_TD ),
						'jltma_image_carousel_link_one_url'      => "#",
						'jltma_image_carousel_button_two_text'   => __( 'Demo', MELA_TD ),
						'jltma_image_carousel_link_two_url'      => "#"
					]
				],
				'fields'          => [
					[
						'name'    => 'jltma_image_carousel_img',
						'label'   => __( 'Image', MELA_TD ),
						'type'    => Controls_Manager::MEDIA,
						'default' => [
							'url' => Utils::get_placeholder_image_src(),
						]
					],
					[
						'type'          => Controls_Manager::TEXT,
						'name'          => 'title',
						'label_block'   => true,
						'label'         => __( 'Title', MELA_TD ),
						'default'       => __( 'Item Title', MELA_TD )
					],
					[
						'type'          => Controls_Manager::TEXTAREA,
						'name'          => 'subtitle',
						'label_block'   => true,
						'label'         => __( 'Subtitle', MELA_TD ),
						'default'       => __( 'item sub-title', MELA_TD )
					],

					[
						'name'          => 'jltma_image_carousel_buttons',
						'label'        => __( 'Popup or Links ?', MELA_TD ),
						'type'         => Controls_Manager::CHOOSE,
						'options' => [
							'popup' => [
								'title' => __( 'Popup', MELA_TD ),
								'icon' => 'eicon-search',
							],
							'links' => [
								'title' => __( 'Links', MELA_TD ),
								'icon' => 'eicon-editor-external-link',
							],
						],
						'default' => 'popup',
					],

					[
						'name'			=> 'image_link_to_type',
						'label'   		=> esc_html__( 'Link to', MELA_TD ),
						'type'    		=> Controls_Manager::SELECT,
						'options' 		=> [
							''       		=> esc_html__( 'None', MELA_TD ),
							'file'   		=> esc_html__( 'Media File', MELA_TD ),
							'custom' 		=> esc_html__( 'Custom URL', MELA_TD ),
						],
						'condition' 		=> [
							'jltma_image_carousel_link_popup' => 'links'
						],
					],

					[
						'name'          => 'jltma_image_carousel_url',
						'label'        => __( 'Link', MELA_TD ),
						'type'         => Controls_Manager::URL,
						'default'     => [
							'url' => '#',
							'is_external' => true,
							'nofollow' => true,
						],
						'show_external' => true,
						'condition'     => [
							'jltma_image_carousel_link_popup' 	=> 'links',
							'image_link_to_type' 				=> 'custom',
						]
					],


				],
				'title_field' => '{{title}}'
			]
		);



		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'          => 'jltma_image_carousel_image',
				'default'       => 'medium',
				'separator'     => 'before'
			]
		);

		$this->add_control(
			'jltma_image_carousel_image_fit',
			[
				'label'   => esc_html__( 'Image Fit', MELA_TD ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''        => esc_html__( 'Cover', MELA_TD ),
					'contain' => esc_html__( 'Contain', MELA_TD ),
					'auto'    => esc_html__( 'Auto', MELA_TD ),
				],
				'selectors' => [
					// '{{WRAPPER}} .swiper-container .bdt-custom-carousel-thumbnail' => 'background-size: {{VALUE}}',
				]
			]
		);



        $this->add_control(
            'jltma_image_carousel_enable_lightbox',
            [
                'type' 				=> Controls_Manager::SWITCHER,
                'label_off' 		=> esc_html__('No', MELA_TD),
                'label_on' 			=> esc_html__('Yes', MELA_TD),
                'return_value' 		=> 'yes',
                'default' 			=> 'yes',
                'label' 			=> esc_html__('Enable Lightbox Gallery?', MELA_TD),
            ]
        );

        $this->add_control(
            'jltma_image_carousel_lightbox_library',
            [
                'type' 				=> Controls_Manager::SELECT,
                'label' 			=> esc_html__('Lightbox Library', MELA_TD),
                'description' 		=> esc_html__('Choose the preferred library for the lightbox', MELA_TD),
                'options' 			=> array(
	                    'fancybox' 		=> esc_html__('Fancybox', MELA_TD),
	                    'elementor' 	=> esc_html__('Elementor', MELA_TD),
                ),
                'default' 			=> 'fancybox',
                'condition' 		=> [
                    'jltma_image_carousel_enable_lightbox' => 'yes',
                ],
            ]
        );

		$this->end_controls_section();




		/* Carousel Navigation */
		$this->start_controls_section(
			'section_carousel_nav_settings',
			[
				'label' => esc_html__( 'Carousel Navigation', MELA_TD ),
			]
		);


			$this->add_control(
				'jltma_image_carousel_nav',
				[
					'label' 		=> esc_html__( 'Navigation Style', MELA_TD ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'arrows',
					'separator' 	=> 'before',
					'options' 		=> [
						'both'   		=> esc_html__( 'Arrows and Dots', MELA_TD ),
						'arrows' 		=> esc_html__( 'Arrows', MELA_TD ),
						'dots' 			=> esc_html__( 'Dots', MELA_TD ),
						'none'   		=> esc_html__( 'None', MELA_TD )

					],
					'prefix_class' => 'jltma-navigation-type-',
					'render_type'  => 'template',
				]
			);

            $this->add_control(
                'jltma_image_carousel_nav_both_position',
                [
                    'label'     => esc_html__( 'Arrows and Dots Position', MELA_TD ),
                    'type'      => Controls_Manager::SELECT,
                    'default'   => 'center',
                    'options'   => Master_Addons_Helper::jltma_carousel_navigation_position(),
                    'condition' => [
                        'jltma_image_carousel_nav' => 'both',
                    ],
                ]
            );


            $this->add_control(
                'jltma_image_carousel_nav_arrows_position',
                [
                    'label'     => esc_html__( 'Arrows Position', MELA_TD ),
                    'type'      => Controls_Manager::SELECT,
                    'default'   => 'center',
                    'options'   => Master_Addons_Helper::jltma_carousel_navigation_position(),
                    'condition' => [
                        'jltma_image_carousel_nav' => 'arrows',
                    ],              
                ]
            );

            $this->add_control(
                'jltma_image_carousel_nav_dots_position',
                [
                    'label'     => esc_html__( 'Dots Position', MELA_TD ),
                    'type'      => Controls_Manager::SELECT,
                    'default'   => 'bottom-center',
                    'options'   => Master_Addons_Helper::jltma_carousel_pagination_position(),
                    'condition' => [
                        'jltma_image_carousel_nav' => 'dots',
                    ],              
                ]
            );  


			$this->add_control(
				'jltma_image_carousel_hide_arrow_on_mobile',
				[
					'label'     => __( 'Hide Arrow on Mobile ?', MELA_TD ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'condition' => [
						'navigation' => [ 'arrows-fraction', 'arrows', 'both' ],
					],				
				]
			);

			$this->start_controls_tabs( 'jltma_image_carousel_navigation_tabs' );

			$this->start_controls_tab( 'jltma_image_carousel_navigation_control', [ 'label' => __( 'Normal', MELA_TD
			) ] );

			$this->add_control(
				'jltma_image_carousel_arrow_color',
				[
					'label' => __( 'Arrow Background', MELA_TD ),
					'type' => Controls_Manager::COLOR,
					'default' => '#b8bfc7',
					'selectors' => [
						'{{WRAPPER}} .ma-el-team-carousel-prev, {{WRAPPER}} .ma-el-team-carousel-next' => 'background: {{VALUE}};',
					],
					'condition' => [
						'jltma_image_carousel_nav' => 'arrows',
					],
				]
			);

			$this->add_control(
				'jltma_image_carousel_dot_color',
				[
					'label' => __( 'Dot Color', MELA_TD ),
					'type' => Controls_Manager::COLOR,
					'default' => '#8a8d91',
					'selectors' => [
						'{{WRAPPER}} .ma-el-image-carousel-wrapper .slick-dots li button' => 'background-color: {{VALUE}};',
					],
					'condition' => [
						'jltma_image_carousel_nav' => 'dots',
					],
				]
			);


			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'        => 'jltma_image_carousel_border',
					'placeholder' => '1px',
					'default'     => '0px',
					'selector'    => '{{WRAPPER}} .ma-el-team-carousel-prev, {{WRAPPER}} .ma-el-team-carousel-next'
				]
			);


			$this->end_controls_tab();

			$this->start_controls_tab( 'jltma_image_carousel_social_icon_hover', [ 'label' => __( 'Hover', MELA_TD )
			] );

			$this->add_control(
				'jltma_image_carousel_arrow_hover_color',
				[
					'label' => __( 'Arrow Hover', MELA_TD ),
					'type' => Controls_Manager::COLOR,
					'default' => '#917cff',
					'selectors' => [
						'{{WRAPPER}} .ma-el-team-carousel-prev:hover, {{WRAPPER}} .ma-el-team-carousel-next:hover' =>
							'background: {{VALUE}};',
					],
					'condition' => [
						'jltma_image_carousel_nav' => 'arrows',
					],
				]
			);

			$this->add_control(
				'jltma_image_carousel_dot_hover_color',
				[
					'label' => __( 'Dot Hover', MELA_TD ),
					'type' => Controls_Manager::COLOR,
					'default' => '#8a8d91',
					'selectors' => [
						'{{WRAPPER}} .ma-el-image-carousel-wrapper .slick-dots li.slick-active button, {{WRAPPER}} .ma-el-image-carousel-wrapper .slick-dots li button:hover' => 'background: {{VALUE}};',
					],
					'condition' => [
						'jltma_image_carousel_nav' => 'dots',
					],
				]
			);


			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'        => 'jltma_image_carousel_hover_border',
					'placeholder' => '1px',
					'default'     => '0px',
					'selector'    => '{{WRAPPER}} .ma-el-team-carousel-prev:hover, {{WRAPPER}} .ma-el-team-carousel-next:hover'
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();




		/* Carousel Settings */
		$this->start_controls_section(
			'section_carousel_settings',
			[
				'label' => esc_html__( 'Carousel Settings', MELA_TD ),
			]
		);

			$slides_per_view = range( 1, 10 );
			$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

			$this->add_responsive_control(
				'jltma_image_carousel_slides_to_show',
				[
					'type'           => Controls_Manager::SELECT,
					'label'          => esc_html__( 'Slides to Show', MELA_TD ),
					'options'        => $slides_per_view,
					'default'        => '3',
					'tablet_default' => '2',
					'mobile_default' => '1',
				]
			);

			$this->add_control(
				'jltma_image_carousel_slides_to_scroll',
				[
					'type'      	=> Controls_Manager::SELECT,
					'label'     	=> __( 'Items to Scroll', MELA_TD ),
					'options'   	=> $slides_per_view,
					'default'   	=> '1',
				]
			);

			$this->add_control(
				'jltma_image_carousel_transition_duration',
				[
					'label'   => __( 'Transition Duration', MELA_TD ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 1000,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'jltma_image_carousel_autoplay',
				[
					'label'     => __( 'Autoplay', MELA_TD ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'no',
				]
			);

			$this->add_control(
				'jltma_image_carousel_autoplay_speed',
				[
					'label'     => __( 'Autoplay Speed', MELA_TD ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 5000,
					'condition' => [
						'jltma_image_carousel_autoplay' => 'yes',
					],
				]
			);

			$this->add_control(
				'jltma_image_carousel_loop',
				[
					'label'   => __( 'Infinite Loop', MELA_TD ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'jltma_image_carousel_pause',
				[
					'label'     => __( 'Pause on Hover', MELA_TD ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'condition' => [
						'jltma_image_carousel_autoplay' => 'yes',
					],
				]
			);

		$this->end_controls_section();



		if ( ma_el_fs()->is_not_paying() ) {

			$this->start_controls_section(
				'maad_el_section_pro',
				[
					'label' => __( 'Upgrade to Pro Version for More Features', MELA_TD )
				]
			);

			$this->add_control(
				'maad_el_control_get_pro',
				[
					'label' => __( 'Unlock more possibilities', MELA_TD ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'1' => [
							'title' => __( '', MELA_TD ),
							'icon' => 'fa fa-unlock-alt',
						],
					],
					'default' => '1',
					'description' => '<span class="pro-feature"> Upgrade to  <a href="' . ma_el_fs()->get_upgrade_url() . '" target="_blank">Pro Version</a> for more Elements with Customization Options.</span>'
				]
			);

			$this->end_controls_section();
		}



	}


	// Render Function
	protected function render(){
		$settings       = $this->get_settings_for_display();

        $this->jltma_render_image_carousel_header($settings);
        $this->jltma_render_image_carousel_loop_item($settings);
        $this->jltma_render_image_carousel_footer($settings);
	}


	// Render Header
	private function jltma_render_image_carousel_header( $settings ) {
        $settings = $this->get_settings_for_display();
        $id       = $this->get_id();


        $this->add_render_attribute( 'jltma-img-carousel-wrapper', 'id', 'jltma-image-carousel-' . esc_attr($id) );
        $this->add_render_attribute( 'jltma-img-carousel-wrapper', 'class', ['jltma-image-carousel-wrapper'] );
        $this->add_render_attribute( 'jltma-img-carousel-wrapper', 'class', [ 
                'slider-items',
                ( 'both' == $settings['jltma_image_carousel_nav'] ) ? 'jltma-arrows-dots-align-' . $settings['jltma_image_carousel_nav_both_position'] : '',
                ( 'arrows' == $settings['jltma_image_carousel_nav'] ) ? 'jltma-arrows-align-' . $settings['jltma_image_carousel_nav_arrows_position'] : '',
                ( 'dots' == $settings['jltma_image_carousel_nav'] ) ? 'jltma-dots-align-'. $settings['jltma_image_carousel_nav_dots_position'] : '',
            ] );
        

        $this->add_render_attribute(
            [
                'jltma-image-carousel' => [
                    'class' => [
                        'jltma-image-carousel'
                    ]
                ]
            ]
        );

        ?>
            
            <div <?php echo $this->get_render_attribute_string( 'jltma-img-carousel-wrapper' ); ?>>
                <div <?php echo $this->get_render_attribute_string( 'jltma-image-carousel' ); ?>>

        <?php
	}



	// Render Header
	private function jltma_render_image_carousel_loop_item( $settings ) {
        $settings = $this->get_settings_for_display();

        if ( empty($settings['jltma_image_carousel_items'] ) ) {
            return;
        } 

        foreach ( $settings['jltma_image_carousel_items'] as $index => $item ) {
            $slider_image = wp_get_attachment_image_url( $item['jltma_image_carousel_img']['id'], $item['jltma_image_carousel_image_size'] );

            $repeater_key = 'carousel_item' . $index;
            $tag = 'div';
            $image_alt = esc_html($item['title']) . ' : ' . esc_html($item['subtitle']);
            $this->add_render_attribute( $repeater_key, 'class', 'jltma-logo-slider-item' );
			
			?>
            
                <div <?php $this->print_render_attribute_string( $repeater_key ); ?>>
                    <figure class="jltma-logo-slider-figure">
                        
                        <?php 
                            if ( $slider_image ) {
                                echo wp_get_attachment_image(
                                    $item['jltma_image_carousel_img']['id'],
                                    $item['jltma_image_carousel_image_size'],
                                    false,
                                    [
                                        'class' => 'jltma-logo-slider-img elementor-animation-' . esc_attr( $settings['jltma_logo_slider_carousel_hover_animation'] ),
                                        'alt'=> esc_attr( $image_alt ),
                                    ]
                                );
                            }
                        ?>

                    </figure>

                </div>

            <?php 

            if($item['jltma_logo_slider_item_logo_tooltip'] == "yes"){
                echo '<div class="ma-el-tooltip-text">' . esc_html( $item['subtitle'] ) .'</div></div></div>';
            }

        }  // end of foreach

	}



	// Render Header
	private function jltma_render_image_carousel_footer( $settings ) {

        $settings = $this->get_settings_for_display(); ?>

        </div>

        <?php
            if ('both' == $settings['jltma_logo_slider_nav']){
            
                $this->jltma_render_logo_slider_navigation($settings);

                if ( 'center' === $settings['jltma_logo_slider_nav_both_position'] ){
                    $this->jltma_logo_slider_render_dots_navigation($settings);
                }
            
            } elseif ('arrows' == $settings['jltma_logo_slider_nav']){

                $this->jltma_logo_slider_render_arrow_navigation($settings);

            } elseif ('dots' == $settings['jltma_logo_slider_nav']){

                $this->jltma_logo_slider_render_dots_navigation($settings);

            }?>
            
        </div><!--/.jltma-logo-slider-->

    <?php 
	}


	private function render_image( $image_id, $settings ) {
		$jltma_image_carousel_image = $settings['jltma_image_carousel_image_size'];
		if ( 'custom' === $jltma_image_carousel_image ) {
			$image_src = Group_Control_Image_Size::get_attachment_image_src( $image_id, 'jltma_image_carousel_image', $settings );
		} else {
			$image_src = wp_get_attachment_image_src( $image_id, $jltma_image_carousel_image );
			$image_src = $image_src[0];
		}

		return sprintf( '<img src="%s" alt="%s" />', esc_url($image_src), esc_html( get_post_meta( $image_id, '_wp_attachment_image_alt', true) ) );
	}


	protected function _content_template() {}

}

Plugin::instance()->widgets_manager->register_widget_type( new Master_Addons_Image_Carousel());
