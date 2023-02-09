<?php
/**
 * The template for displaying enquiry details data
 *
 * Override this template by copying it to yourtheme/woocommerce-catalog-enquiry-pro/vendor-dashboard/woocommerce-catalog-enquiry-pro-vendor-catalog-settings.php
 *
 * @author    WC Marketplace
 * @package   woocommerce-catalog-enquiry-pro/Templates
 * @version   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $MVX, $Woocommerce_Catalog_Enquiry_Pro, $woocommerce, $Woocommerce_Catalog_Enquiry_Pro_Cart;
$settings = get_woocommerce_catalog_catalog_settings();
$vendor = get_current_vendor();
?>
<div class="col-md-12 settings">
    <form method="post" name="catalog_enquiry_menu_content" class="mvx_vendor_catalog_enquiry">
        <div class="vendor-catalog-enquiry-settings">
            <ul class="nav nav-tabs">
                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#general"><?php _e( 'General Settings', 'woocommerce-catalog-enquiry-pro' );?></a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#templates"><?php _e('Email Template', 'woocommerce-catalog-enquiry-pro');?></a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#exclusion"><?php _e('Exclusion', 'woocommerce-catalog-enquiry-pro');?></a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#enq_button"><?php _e('Enquiry Button', 'woocommerce-catalog-enquiry-pro');?></a></li>
            </ul>
            <?php wp_nonce_field( 'mvx_vendor_catalog_settings_nonce', 'vendor_catalog_settings_nonce' ); ?>
            <div class="tab-content panel panel-body">
                <!-- General Settings -->
                <div id="general" class="tab-pane fade in active">
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Enable Multiple Catalog Enquiry for your Store', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <div class="switch">
                                <input id="is_enable_multiple_product_enquiry" name="is_enable_multiple_product_enquiry" class="" type="checkbox" value="Enable" <?php if(get_mvx_vendor_data('is_enable_multiple_product_enquiry') == 'Enable') echo 'checked';?>>
                                <label for="is_enable_multiple_product_enquiry"></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Catalog Enquiry Popup Disable', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <div class="switch">
                                <input id="is_disable_popup" name="is_disable_popup" class="" type="checkbox" value="Enable" <?php if(get_mvx_vendor_data('is_disable_popup') == 'Enable') echo 'checked';?>>
                                <label for="is_disable_popup"></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Override Enquiry Form Heading', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <div class="switch">
                                <input id="is-override-form-heading" name="is_override_form_heading" class="" type="checkbox" value="Enable" <?php if(get_mvx_vendor_data('is_override_form_heading') == 'Enable') echo 'checked';?>>
                                <label for="is_override_form_heading"></label>
                            </div>
                        </div>
                    </div>
                    <div id="custom-form-heading" class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Custom Form Heading', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control regular-text" type="text" id="custom_static_heading" name="custom_static_heading" value="<?php echo get_mvx_vendor_data('custom_static_heading');?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Enable Add to cart with Catalog mode', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <div class="switch">
                                <input id="is_enable_add_to_cart" name="is_enable_add_to_cart" class="" type="checkbox" value="Enable" <?php if(get_mvx_vendor_data('is_enable_add_to_cart') == 'Enable') echo 'checked';?>>
                                <label for="is_enable_add_to_cart"></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Remove Product Price', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <div class="switch">
                                <input id="is-remove-price" name="is_remove_price" class="" type="checkbox" value="Enable" <?php if(get_mvx_vendor_data('is_remove_price') == 'Enable') echo 'checked';?>>
                                <label for="is_remove_price"></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Replace Price with Text', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <div class="switch">
                                <input id="is-replace-price-with-txt" name="is_replace_price_with_txt" class="" type="checkbox" value="Enable" <?php if(get_mvx_vendor_data('is_replace_price_with_txt') == 'Enable') echo 'checked';?>>
                                <label for="is_replace_price_with_txt"></label>
                            </div>
                        </div>
                    </div>
                    <div id="alternate-price" class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Alternative Text at Price', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control regular-text" type="text" id="replace-text-in-price" name="replace_text_in_price" value="<?php echo get_mvx_vendor_data('replace_text_in_price');?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Remove your email from Enquiry Mail Receiver', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <div class="switch">
                                <input id="is_other_admin_mail" name="is_other_admin_mail" class="" type="checkbox" value="Enable" <?php if(get_mvx_vendor_data('is_other_admin_mail') == 'Enable') echo 'checked';?>>
                                <label for="is_other_admin_mail"></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Other Email that receive Enquiry Mail', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control regular-text" type="text" id="other_emails" name="other_emails" value="<?php echo get_mvx_vendor_data('other_emails');?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Redirect other page after Enquiry Success', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <div class="switch">
                                <input id="is_page_redirect" name="is_page_redirect" class="" type="checkbox" value="Enable" <?php if(get_mvx_vendor_data('is_page_redirect') == 'Enable') echo 'checked';?>>
                                <label for="is_page_redirect"></label>
                            </div>
                        </div>
                    </div>
                    <div id="redirect-other-page" class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Select Redirect page', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <select id="redirect_page_id" name="redirect_page_id" class="form-control regular-select">
                                <?php 
                                    $options = '';
                                    $redirect_page_id = get_mvx_vendor_data('redirect_page_id');
                                    foreach ( get_all_pages_for_vendor() as $key => $page ) {
                                      $options .= '<option value="' . esc_attr( $key ) . '" ';
                                      if(esc_attr( $key ) == $redirect_page_id ) $options .= 'selected'; 
                                      $options .= '>' . esc_html( $page ) . '</option>';
                                    }
                                    echo $options;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- End General Settings -->
                <!-- Enquiry Template Settings -->
                <div id="templates" class="tab-pane fade">
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Choose a Email Template', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-9 col-sm-9">
                            <div class="template">
                                <?php 
                                $html = '';
                                if(!empty($Woocommerce_Catalog_Enquiry_Pro->template->email_template)){
                                    $selected =''; 
                                    if(!empty(get_mvx_vendor_data('selected_email_tpl')))
                                            $selected = get_mvx_vendor_data('selected_email_tpl');
                                    else
                                            $selected = $Woocommerce_Catalog_Enquiry_Pro->email_tpl['selected_email_tpl'];
                                    $html .= '<ul id="woocommerce-catalog-eml-tpl">';
                                    foreach($Woocommerce_Catalog_Enquiry_Pro->template->email_template as $key => $value){
                                    $class = ''; if($key == $selected) $class = 'selected';
                                    if($key== 0 && $value == 'template1'){
                                      $html .= '<li>
                                                  <div class="woocommerce-catalog-eml-tpl-cell '.$class.'"  data-tpl="'.$value.'">
                                                      <span class="woocommerce-catalog-eml-tpl-img" style="background-position:0 0;background-image:url('.$Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/templates/default_wc_tpl.png'.');"></span>
                                                      <span class="woocommerce-catalog-eml-tpl-overlay"></span>
                                                  </div>
                                              </li>';
                                    }else{
                                      $html .= '<li>
                                                  <div class="woocommerce-catalog-eml-tpl-cell '.$class.'"  data-tpl="'.$value.'">
                                                      <span class="woocommerce-catalog-eml-tpl-img" style="background-position:0 0;background-image:url('.$Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/templates/woocommerce_catalog_send_email_tpl_'.$key.'.png'.');"></span>
                                                      <span class="woocommerce-catalog-eml-tpl-overlay"></span>
                                                  </div>
                                              </li>';
                                    }

                                  }
                                  $html .= '</ul>';
                                }
                                echo $html;
                                ?>
                                <input type="hidden" name="selected_email_tpl" id="selected_email_tpl" value="<?php echo get_mvx_vendor_data('selected_email_tpl');?>">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Enquiry Template Settings -->
                <!-- Enquiry Exclusion Settings -->
                <div id="exclusion" class="tab-pane fade">
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'User List Excluded from Catalog Enquiry', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <select multiple id="woocommerce-user-vendor-list" name="woocommerce_user_vendor_list[]" class="regular-select">
                                <?php 
                                    $options = '';
                                    $excluded_user_by_vendor = array();
                                    if(get_mvx_vendor_data('woocommerce_user_vendor_list')){
                                            $excluded_user_by_vendor = get_mvx_vendor_data('woocommerce_user_vendor_list');
                                    }

                                    foreach ( get_userList_for_vendor() as $key => $value ) {
                                      $options .= '<option value="' . esc_attr( $key ) . '" ';
                                      if(in_array( esc_attr( $key ), $excluded_user_by_vendor )) $options .= 'selected'; 
                                      $options .= '>' . esc_html( $value ) . '</option>';
                                    }
                                    echo $options;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Product List Excluded from Catalog Enquiry', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <select multiple id="woocommerce-product-vendor-list" name="woocommerce_product_vendor_list[]" class="regular-select">
                                <?php 
                                    $options = '';
                                    $excluded_product_by_vendor = array();
                                    if(get_mvx_vendor_data('woocommerce_product_vendor_list')){
                                            $excluded_product_by_vendor = get_mvx_vendor_data('woocommerce_product_vendor_list');
                                    }

                                    foreach ( get_productList_for_vendor() as $key => $value ) {
                                      $options .= '<option value="' . esc_attr( $key ) . '" ';
                                      if(in_array( esc_attr( $key ), $excluded_product_by_vendor )) $options .= 'selected'; 
                                      $options .= '>' . esc_html( $value ) . '</option>';
                                    }
                                    echo $options;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Product Catagory List Excluded from Catalog Enquiry', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <select multiple id="woocommerce-category-vendor-list" name="woocommerce_category_vendor_list[]" class="regular-select">
                                <?php 
                                    $options = '';
                                    $excluded_product_cat_by_vendor = array();
                                    if(get_mvx_vendor_data('woocommerce_category_vendor_list')){
                                            $excluded_product_cat_by_vendor = get_mvx_vendor_data('woocommerce_category_vendor_list');
                                    }

                                    foreach ( get_productCatagoryList_for_vendor() as $key => $value ) {
                                      $options .= '<option value="' . esc_attr( $key ) . '" ';
                                      if(in_array( esc_attr( $key ), $excluded_product_cat_by_vendor )) $options .= 'selected'; 
                                      $options .= '>' . esc_html( $value ) . '</option>';
                                    }
                                    echo $options;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- End Enquiry Exclusion Settings -->
                <!-- Enquiry Button Settings -->
                <div id="enq_button" class="tab-pane fade">
                    <?php if(isset($settings['can_vendor_customize_btn_style']) && mvx_catalog_get_settings_value($settings['can_vendor_customize_btn_style'], 'checkbox') == 'Enable') : ?>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Enable Custom Button style', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <div class="switch">
                                <input id="is_button" name="is_button" class="" type="checkbox" value="Enable" <?php if(get_mvx_vendor_data('is_button') == 'Enable') echo 'checked';?>>
                                <label for="is_button"></label>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Enquiry Button Text', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control regular-text" type="text" id="enquiry_button_text" name="enquiry_button_text" value="<?php echo get_mvx_vendor_data('enquiry_button_text');?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'Enquiry Cart Button Text', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control regular-text" type="text" id="enquiry_cart_button_text" name="enquiry_cart_button_text" value="<?php echo get_mvx_vendor_data('enquiry_cart_button_text');?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e( 'View Enquiry Cart Button Text', 'woocommerce-catalog-enquiry-pro' );?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control regular-text" type="text" id="view_enquiry_cart_button_text" name="view_enquiry_cart_button_text" value="<?php echo get_mvx_vendor_data('view_enquiry_cart_button_text');?>">
                        </div>
                    </div>
                    <?php if(isset($settings['can_vendor_customize_btn_style']) && mvx_catalog_get_settings_value($settings['can_vendor_customize_btn_style'], 'checkbox') == 'Enable') : ?>
                    <div class="form-group enq-button-form-holder">
                        <div class="custom_change" id="mkUrBtn" style="display:<?php if(get_mvx_vendor_data('is_button') == 'Enable') echo 'block'; else echo 'none'; ?>;">
                            <input type ="hidden" name="custom_enquiry_buttons_css" id="custom_enquiry_buttons_css" value="<?php echo get_mvx_vendor_data('custom_enquiry_buttons_css');?>" /> 
                            <input type ="hidden" name="custom_enquiry_buttons_cssStuff" id="custom_enquiry_buttons_cssStuff" value='<?php echo get_mvx_vendor_data('custom_enquiry_buttons_cssStuff');?>' /> 
                            <input type ="hidden" name="custom_enquiry_buttons_cssValues" id="custom_enquiry_buttons_cssValues" value='<?php echo get_mvx_vendor_data('custom_enquiry_buttons_cssValues');?>' /> 
                            <div id="Enquiry-Btn-wrapper">
                                <div class="col-md-5 col-sm-12">
                                    <div class="button-box">
                                        <a href="#" class="custom_enquiry_buttons_css previewbutton"><?php _e("Example Enquiry", "woocommerce-catalog-enquiry-pro"); ?></a>
                                    </div>
                                </div>
                                <div class="col-md-7 col-sm-12">
                                    <div class="controls">
                                        <div>
                                            <label>Button Size:</label>
                                            <input type="text" id="sizer-amount" readonly class="size-label"> 
                                            <div class="sliderBar" id="sizer"><div id="sizer-handle" class="ui-slider-handle"></div></div>
                                        </div>
                                        <div>
                                            <label>Font Size:</label>
                                            <input type="text" id="font-sizer-amount" readonly class="size-label"> 
                                            <div class="sliderBar" id="font-sizer"><div id="font-sizer-handle" class="ui-slider-handle"></div></div>
                                        </div>
                                        <div>
                                            <label>Border Radius:</label>
                                            <input type="text" id="border-rounder-amount" readonly class="size-label"> 
                                            <div class="sliderBar" id="border-rounder"><div id="border-rounder-handle" class="ui-slider-handle"></div></div>
                                        </div>
                                        <div>
                                            <label>Border Size:</label>
                                            <input type="text" id="border-sizer-amount" readonly class="size-label"> 
                                            <div class="sliderBar" id="border-sizer"><div id="border-sizer-handle" class="ui-slider-handle"></div></div>
                                        </div>
                                        <div id="colors">
                                            <!--div class="background-color-control">
                                                <label>
                                                    Solid Background color: <input type="radio" name="backgroundColor" checked>
                                                </label>
                                                <label>
                                                    Gradient Background color: <input type="radio" name="backgroundColor">
                                                </label>
                                            </div-->
                                            <div>
                                                <label for="topGradientValue">Top Gradient Color</label>
                                                <input type="text" maxlength="6" size="6" id="topGradientValue" class="pickable backgroundTop" rel="backgroundTop" value="3e779d" style="background: #3e779d;" />
                                            </div>
                                            <div>
                                                <label for="bottomGradientValue">Bottom Gradient Color</label>
                                                <input type="text" maxlength="6" size="6" id="bottomGradientValue" class="pickable backgroundBottom" rel="backgroundBottom" value="65a9d7" style="background: #65a9d7;" />
                                            </div>
                                            <div>
                                                <label for="borderTopColorValue">Border Color</label>
                                                <input type="text" maxlength="6" size="6" id="borderTopColorValue" class="pickable borderColor" rel="borderColor" value="96d1f8" style="background: #96d1f8;" />
                                            </div>
                                            <div>
                                                <label for="hoverBackgroundColorValue">Hover Background Color</label>
                                                <input type="text" maxlength="6" size="6" id="hoverBackgroundColorValue" class="pickable hoverBackground" rel="hoverBackground" value="28597a" style="background: #28597a;" />
                                            </div>
                                            <div>
                                                <label for="textColor">Text Color</label>
                                                <input type="text" maxlength="6" size="6" id="textColor" class="pickable textColor" rel="textColor" value="white" style="background: white;" />
                                            </div>
                                            <div>
                                                <label for="hoverTextColorValue">Hover Text Color</label>
                                                <input type="text" maxlength="6" size="6" id="hoverTextColorValue" class="pickable hoverColor" rel="hoverColor" value="cccccc" style="background: #cccccc;" />
                                            </div>
                                            <div>
                                                <label for="activeBackgroundColor">Active Background Color</label>
                                                <input type="text" maxlength="6" size="6" id="activeBackgroundColor" class="pickable activeBackground" rel="activeBackground" value="1b435e" style="background: #1b435e;" />
                                            </div>
                                            <div>
                                                <label for="fontSelector">Select Font</label>
                                                <select id="fontSelector" class="form-control inline-select">
                                                    <option value="">Default</option>
                                                    <option value="Helvetica, Arial, Sans-Serif">Helvetica</option>
                                                    <option value="Georgia, Serif">Georgia</option>
                                                    <option value="Lucida Grande, Helvetica, Arial, Sans-Serif">Lucida Grande</option>
                                                    <?php 
                                                        $extra_fonts = apply_filters('woocommerce_catalog_catalog_enquiry_extra_button_style_fonts',array());
                                                        $extra_fonts_options = '';
                                                        if(!empty($extra_fonts) && is_array($extra_fonts)){
                                                            foreach ($extra_fonts as $key => $value) {
                                                                $extra_fonts_options .= '<option value="'.$value.', Helvetica, Arial, Sans-Serif">'.$value.'</option>';
                                                            }
                                                        }
                                                        echo $extra_fonts_options;
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <!-- End Enquiry Button Settings -->
            </div>
        </div>
        <div class="mvx-action-container">
            <button class="btn btn-default" name="vendor_catalog_settings"><?php _e("Save Options", "woocommerce-catalog-enquiry-pro"); ?></button>
            <div class="clear"></div>
        </div>
    </form>
</div>