<?php
/**
 * sfWidgetFormPropelSelect represents a select HTML tag with Ajax support for a model.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Ahmed El.Hussaini <eng.ahmed.elhussaini@sandbox-ws.com>
 */
class sfWidgetFormPropelSelectAjax extends sfWidgetFormPropelSelect
{
  /**
   *  Constructor.
   *
   *  Available options:
   *
   *  - <b>url:</b>                The url of the action(routing, or module/action) to be called using Ajax (required)
   *  - <b>update:</b>             The ID of the HTML element to be updated (model_name_update by default)
   *  - <b>with:</b>               List of parameters to be sent with the Ajax request (value of the selected select option is added by default as a parameter called id)
   *  - <b>complete:</b>           A function to be called when the request finishes (after success and error callbacks are executed, null by default)
   *  - <b>error:</b>              A function to be called if the request fails (null by default)
   *  - <b>beforeSend:</b>         A pre-callback to modify the XMLHttpRequest object before it is sent
   *  - <b>event:</b>              The DOM event of the select tag that will trigger the Ajax call (onchange by default)
   *  - <b>update_element:</b>     The HTML element rendered that will be updated with the Ajax response (div by default)
   *
   *  <b>Usage:</b>
   *  <br />
   *  
   *    The following example creates a drop down list that sends an Ajax request to the action "test/ajax" on change of the select menu.
   *    By default if you don't specify the <b>update</b> option, the widget renders an empty <b>div</b> element with an id in the format
   *    <b>model_name_update</b>.
   *
   *  <code>
   *    $this->widgetSchema['category'] = new sfWidgetFormPropelSelectAjax(array(
   *	    'model' => 'Category',
   *	    'method' => 'getName',
   *	    'url' => 'test/ajax'
   *    ));
   *  </code>
   *
   *  The above code will render the following HTML code
   *
   *  <code>
   *    <label for="category_category">Category</label>
   *    <select id="category_category" name="category[category]">
   *      <option value="2">Fun</option>
   *      <option value="3">Stuff</option>
   *    </select>
   *    <div id="category_update"><div>
   *    <script charset="utf-8" type="text/javascript">
   *      jQuery('#category_category').change(function(event){
   *        jQuery.ajax({
   *          type:        'POST',
   *          dataType:    'html',
   *          url:         '/sandbox/web/backend_dev.php/test/ajax',
   *          data:        "id="+jQuery('#'+event.target.id).val(),
   *          success:
   *            function(data, textStatu)
   *            {
   *              jQuery('#category_update').html(data);
   *            }
   *          
   *        })
   *      });
   *    </script>
   *  </code>
   *
   *  If you want to change the id of the generated <b>div</b> or change the <b>div</b> to lets say a <b>span</b> use the following
   *  <code>
   *    'update' => 'foobar'
   *  </code>
   *
   *  <code>
   *    'update_element' => 'span'
   *  </code>
   *
   *  NOTE: If you add the <b>update</b> option with a value other than the default one added automatically by the widget
   *  you will have to add the HTML element to be updated manually in the template.
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormPropelSelect
  */
  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);
    $this->addRequiredOption('url');
    $this->addOption('update', strtolower($options['model']).'_update');
    $this->addOption('with', null);
    $this->addOption('complete', null);
    $this->addOption('error', null);
    $this->addOption('beforeSend', null);
    $this->addOption('event', 'change');
    $this->addOption('update_element', 'div');
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
  public function render($name, $value  = null, $attributes = array(), $errors = array())
  {
    $default_update = true;
    $update = '';
    if ($this->getOption('update') == strtolower($this->getOption('model')).'_update')
    {
      $update = strtolower($this->getOption('model')).'_update';
    }
    else
    {
      $update = $this->getOption('update');
      $default_update = false;
    }
    $optional = '';
    
    $optional .= sfJQueryUtilities::getCompleteOption($this->getOption('complete'));
    $optional .= sfJQueryUtilities::getBeforeSendOption($this->getOption('beforeSend'));
    $optional .= sfJQueryUtilities::getErrorOption($this->getOption('error'));
    
    // call parent render method
    $html = parent::render($name, $value, $attributes, $errors);
    if($default_update)
    {
      if (!is_null($this->getOption('update_element')))
      {
        // add defined HTML element
        $html .= '<'.$this->getOption('update_element').' id="'.$this->getOption('update').'"></'.$this->getOption('update_element').'>';
      } 
      else
      {
        // use default HTML element
        $html .= '<div id="'.$this->getOption('update').'"></div>';
      }
    }
    
    // add ajax code to the defined event
    $html .= sprintf(<<<EOF
<script type="text/javascript" charset="utf-8">
  jQuery('#%s').%s(function(event){
    jQuery.ajax({
      type:        'POST',
      dataType:    'html',
      url:         '%s',
      data:        "id="+jQuery('#'+event.target.id).val(),
      success:
        function(data, textStatu)
        {
          jQuery('#%s').html(data);
        },%s
    })
  });
</script>
EOF
      ,
      $this->generateId($name),
      $this->getOption('event'),
      url_for($this->getOption('url')),
      $update,
      $optional
    );

    return $html;
  }
  
  public function getJavascripts()
  {
    return array('/sfJqueryWidgetsPlugin/js/jquery-1.2.6.min.js');
  }
	
}