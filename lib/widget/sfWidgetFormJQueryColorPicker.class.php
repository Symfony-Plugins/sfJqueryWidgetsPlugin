<?php

/**
 * sfWidgetFormColorPicker uses jquery colorpicker plugin and attach
 * color picker to an input field
 *
 * @package symfony
 * @subpackage widget
 * @author Yanko Simeonoff
 *
 */
class sfWidgetFormJQueryColorPicker extends sfWidgetFormInput
{

    /**
     * Constructor
     *
     * @param array $options
     * @param array $attributes
     */
    public function configure($options = array(), $attributes = array())
    {
        parent::configure($options, $attributes);
    }

    /**
     * @param  string $name        The element name
     * @param  string $value       The value selected in this widget
     * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
     * @param  array  $errors      An array of errors for the field
     *
     * @return string An HTML select tag string plus jQuery ajax script
     *
     * @see sfWidgetForm
     */
    public function render($name, $value = null, $attributes = array(), $errors = array())
    {
        $html = parent::render($name, $value, $attributes, $errors);
        
        $html .= sprintf(<<<EOF
        
<script type="text/javascript" charset="utf-8">
    jQuery('#%s').ColorPicker({
        onSubmit: function(hsb, hex, rgb) {
            jQuery('#%s').val(hex);
        },
        onBeforeShow: function () {
            jQuery(this).ColorPickerSetColor(this.value);
        }
    })
    .bind('keyup', function() {
        jQuery(this).ColorPickerSetColor(this.value);
    });
</script>
EOF
        ,
        $this->generateId($name),
        $this->generateId($name)
        );
        
        return $html;
    }

    /**
     * Retrieve javascripts needed for the widget
     *
     * @return array
     */
    public function getJavascripts()
    {
        return array('/sfJqueryWidgetsPlugin/js/jquery.colorpicker.js');
    }
    
    /**
     * Retrieve stylesheets needed for the widget
     *
     * @return array
     */
    public function getStylesheets()
    {
        return array('/sfJqueryWidgetsPlugin/css/jquery.colorpicker.css' => 'all');
    }
    
}
