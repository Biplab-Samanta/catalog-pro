<?php
/**
 * The Template for displaying enquiry form fields.
 *
 * Override this template by copying it to yourtheme/woocommerce-catalog-enquiry-pro/woocommerce-catalog-enquiry-pro-form-fileds.php
 *
 * @author    WC Marketplace
 * @package   woocommerce-catalog-enquiry-pro/Templates
 * @version   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $Woocommerce_Catalog_Enquiry_Pro;
if (!empty($wcce_enquiry_form_data) && is_array($wcce_enquiry_form_data)) {
    $sep_count = 0;
    foreach ($wcce_enquiry_form_data as $key => $value) {
        switch ($value['type']) {
            case 'section':
                ?>
                <div class="clearboth"></div>
                </div>
                <div class="woocommerce_catalog_separator">
                <h3 class="enq_header2"><?php echo __($value['label'],'woocommerce-catalog-enquiry-pro'); ?></h3>
                <?php
                break;
            case 'textbox':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcce-enq-12'; } ?>">
                    <label><?php echo __($value['label'],'woocommerce-catalog-enquiry-pro'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="text" value="<?php if (!empty($_POST['woocommerce_catalog_enquiry_fields'][$key]["value"])) echo esc_attr($_POST['woocommerce_catalog_enquiry_fields'][$key]["value"]); ?>" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][type]" value="textbox" />
                </div>
                <?php
                break;
            case 'email':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcce-enq-12'; } ?>">
                    <label><?php echo __($value['label'],'woocommerce-catalog-enquiry-pro'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="email" value="<?php if (!empty($_POST['woocommerce_catalog_enquiry_fields'][$key]["value"])) echo esc_attr($_POST['woocommerce_catalog_enquiry_fields'][$key]["value"]); ?>" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][type]" value="email" />
                </div>
                <?php
                break;
            case 'textarea':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcce-enq-12'; } ?>">
                    <label><?php echo __($value['label'],'woocommerce-catalog-enquiry-pro'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <textarea <?php if(!empty($value['limit'])){ echo 'maxlength="'.$value['limit'].'"'; } ?> name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['defaultValue']; ?>"><?php if (!empty($_POST['woocommerce_catalog_enquiry_fields'][$key]["value"])){ echo esc_attr($_POST['woocommerce_catalog_enquiry_fields'][$key]["value"]); } ?></textarea>
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][type]" value="textarea" />
                </div>
                <?php
                break;
            case 'url': 
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcce-enq-12'; } ?>">
                    <label><?php echo __($value['label'],'woocommerce-catalog-enquiry-pro'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="url" value="<?php if (!empty($_POST['woocommerce_catalog_enquiry_fields'][$key]["value"])) echo esc_attr($_POST['woocommerce_catalog_enquiry_fields'][$key]["value"]); ?>" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][type]" value="url" />
                </div>
                <?php
                break;
            case 'selectbox':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcce-enq-12'; } ?>">
                    <label><?php echo __($value['label'],'woocommerce-catalog-enquiry-pro'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][type]" value="selectbox" />
                    <?php
                     switch ($value['selecttype']){
                         case 'dropdown':
                            ?>
                            <select class="select_box" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][value]" <?php if($value['required']){ echo 'required="required"'; }?>>
                            <?php
                            if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <option value="<?php echo $option_value['value']; ?>" <?php if($option_value['selected']){ echo 'selected="selected"'; } ?>><?php echo $option_value['label']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                            </select>
                             <?php
                             break;
                         case 'radio':
                             if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <p class="wcce-enq-radio-wrap"><label><input type="radio" <?php if($option_value['selected']){ echo 'checked="checked"'; } ?> name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][value]" value="<?php echo $option_value['value']; ?>"> <?php echo $option_value['label']; ?></label></p>
                                    <?php
                                }
                            }
                             break;
                         case 'checkboxes':
                            if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <p> <input type="checkbox" <?php if($option_value['selected']){ echo 'checked="checked"'; } ?> name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][value][]" value="<?php echo $option_value['value']; ?>"> <?php echo $option_value['label']; ?></p>
                                    <?php
                                }
                            }
                             break;
                         case 'multi-select':
                             ?>
                            <select class="select_box" style="min-height: 59px;" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][value][]" <?php if($value['required']){ echo 'required="required"'; }?> multiple="">
                            <?php
                            if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <option value="<?php echo $option_value['value']; ?>" <?php if($option_value['selected']){ echo 'selected="selected"'; } ?>><?php echo $option_value['label']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                            </select>
                            <?php
                            break;
                     }
                    ?>
                </div>
                <?php
                break;

            case 'checkbox':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcce-enq-12'; } ?>">
                    <input type="checkbox" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][value]" <?php if($value['defaultValue'] == 'checked'){ echo 'checked="checked"';} ?>  <?php if($value['required']){ echo 'required="required"'; }?> />
                    <label><?php echo __($value['label'],'woocommerce-catalog-enquiry-pro'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][type]" value="checkbox" />
                </div>
                <?php
                break;

            case 'datepicker':
                ?>
                <script>
                    jQuery( function() {
                        jQuery( "#woocommerce_catalog_enquiry_datepicker_<?php echo $key; ?>" ).datepicker({
                           <?php if(!empty($value['dateformat'])){ ?> dateFormat: "<?php echo $value['dateformat']; ?>" <?php } ?>
                        });
                    });
                </script>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcce-enq-12'; } ?>">
                    <label><?php echo __($value['label'],'woocommerce-catalog-enquiry-pro'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="text" id="woocommerce_catalog_enquiry_datepicker_<?php echo $key; ?>" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][value]" class="date-picker" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][type]" value="datepicker" />
                </div>
                <?php
                break;

            case 'timepicker': 
                ?>
                <script>
                    jQuery( function() {
                        jQuery( "#woocommerce_catalog_enquiry_timepicker_<?php echo $key; ?>" ).dctimepicker({
                            <?php if($value['twentyFour']){ ?>twentyFour: <?php echo $value['twentyFour'] ?>, <?php }else{ ?>twentyFour: false,<?php } ?> 
                            'title': '', 
                            <?php if($value['showSeconds']){ ?>showSeconds: <?php echo $value['showSeconds'] ?> <?php }else{ ?>showSeconds: false,<?php } ?>  
                        });
                    });
                </script>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcce-enq-12'; } ?>">
                    <label><?php echo __($value['label'],'woocommerce-catalog-enquiry-pro'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="text" id="woocommerce_catalog_enquiry_timepicker_<?php echo $key; ?>" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][value]" class="timepicker" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][type]" value="timepicker" />
                </div>
                <?php
                break;

            case 'captcha':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcce-enq-12'; } ?>">
                    <label><?php echo __($value['label'],'woocommerce-catalog-enquiry-pro'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <?php //echo $value['script']; ?>
                    <span class="noselect" style="background:#000; color:#fff; border:1px solid #333; padding:5px; letter-spacing: 5px; font-size:18px;" ><i><?php echo get_transient('woocaptcha'); ?></i></span>
                    <input type="text" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][value]" value="" style="margin-top: 12px;"/>
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][type]" value="recaptcha" />
                </div>
                <?php
                break;

            case 'recaptcha':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcce-enq-12'; } ?>">
                    <label><?php echo __($value['label'],'woocommerce-catalog-enquiry-pro'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <div class="g-recaptcha" data-sitekey="<?php echo $value['site_key']; ?>"></div>
                    <input type="hidden" name="woocommerce_catalog_enquiry_grecaptcha_secret" value="<?php echo $value['secret_key']; ?>" />
                
                </div>
                <?php
                break;

            case 'file':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcce-enq-12'; } ?>">
                    <label><?php echo __($value['label'],'woocommerce-catalog-enquiry-pro'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="file" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][]" <?php if($value['required']){ echo 'required="required"'; }?> <?php if($value['muliple']){ echo 'multiple="true"'; }?> />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="woocommerce_catalog_enquiry_fields[<?php echo $key; ?>][type]" value="file" />
                </div>
                <?php
                break;
        }
    }
}