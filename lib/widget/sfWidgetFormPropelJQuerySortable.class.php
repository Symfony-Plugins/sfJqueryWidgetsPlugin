<?php
/**
 * sfWidgetFormPropelJQuerySortable represents a sortable HTML list that fires an Ajax request upon change
 *
 * @package    symfony
 * @subpackage widget
 * @author     Ahmed El.Hussaini <eng.ahmed.elhussaini@sandbox-ws.com>
 */
class sfWidgetFormPropelJQuerySortable extends sfWidgetForm
{
  /**
  * @var array valid list types
  */
  private $list_types = array('ul', 'ol');
  
  /**
   *  Constructor.
   *
   *  Available options:
   *
   *  - <b>model:</b>              The model class (required)
   *  - <b>url:</b>                The url of the action(routing, or module/action) to be called using Ajax (required)
   *  - <b>list_id:</b>            The HTML id of the generated list (model_name_sortable by default)
   *  - <b>param:</b>              Name of the parameter sent to the action specified in the <b>url</b> option (id by default)
   *  - <b>key_method:</b>         Method used to retrieve the value of the list item (getId by default)
   *  - <b>list_type:</b>          Type of the list either <b>ul</b> or <b>ol</b> (ul by default)
   *  - <b>criteria:</b>           A criteria to use when retrieving objects (null by default)
   *  - <b>start:</b>              Function that gets called when sorting starts (before sending the Ajax request)
   *  - <b>success:</b>            A function to be called if the request succeeds
   *  - <b>complete:</b>           A function to be called when the request finishes (after success and error callbacks are executed)
   *  - <b>error:</b>              A function to be called if the request fails
   *  - <b>beforeSend:</b>         A pre-callback to modify the XMLHttpRequest object before it is sent
   *  - <b>peer_method:</b>        The peer method to use to fetch objects
   *  - <b>connection:</b>         The Propel connection to use (null by default)
   *  - <b>error:</b>              A function to be called if the request fails
   *
   *  <b>Usage</b>:
   *  <br />
   *  <code>
   *    $this->widgetSchema['categories'] = new sfWidgetFormPropelJQuerySortable(array(
   *      'models' => CategoryPeer::doSelect(new Criteria()),
   *      'url' => 'section/new',
   *    ));
   *  </code>
   *  Above code will render a sortable list, below is the generated HTML code
   *
   *  <code>
   *    <label for="category_category">Category</label>
   *    <ul id="category_sortable" class="ui-sortable" style="position: relative;">
   *      <li id="id-2">Fun</li>
   *      <li id="id-3">Stuff</li>
   *    </ul>
   *    <script charset="utf-8" type="text/javascript">
   *      jQuery('#category_sortable').sortable({
   *        change: function(e, ui)
   *        {
   *          serial = jQuery(this).sortable('serialize');
   *        },
   *        start:
   *          function(e, ui)
   *          {
   *        
   *          },
   *        stop:
   *          function(e, ui)
   *          {
   *            jQuery.ajax({
   *              type:       'POST',
   *              dataType:   'html',
   *              url:        '/sandbox/web/backend_dev.php/test/ajax',
   *              data:       serial,
   *          
   *            });
   *          }
   *      });
   *    </script>
   *  </code>
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormPropelSelect
  */
  protected function configure($options = array(), $attributes = array())
  { 
    //add required options
    $this->addRequiredOption('model');
    $this->addRequiredOption('url');
    
    //add optional options
    #$this->addOption('choices', array());
    $this->addOption('list_id', null);
    $this->addOption('param', 'id');
    $this->addOption('key_method', 'getId');
    $this->addOption('list_type', 'ul');
    $this->addOption('criteria', null);
    $this->addOption('start', null);
    $this->addOption('success', null);
    $this->addOption('complete', null);
    $this->addOption('error', null);
    $this->addOption('beforeSend', null);
    $this->addOption('peer_method', 'doSelect');
    $this->addOption('connection', null);
  }
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $list_type = '';
    //check that the provided list_type option is valid as specified in the instance member list_types
    if (in_array($this->getOption('list_type'), $this->list_types))
    {
      $list_type = $this->getOption('list_type');
    }
    else
    {
      //default to ul
      $list_type = 'ul';
    }
    
    $models = $this->getModels();
    //set the default list id attribute
    if (is_null($this->getOption('list_id')))
    {
      $this->setOption('list_id', strtolower($this->getOption('model')).'_sortable');
    }
    
    $html = '';
    $html .= "<".$list_type." id=\"".$this->getOption('list_id')."\">\n";
    foreach ($models as $obj)
    {
      $getter_callable = new sfCallable(array($obj, $this->getOption('key_method')));
      $html .= '<li id="'.$this->getOption('param').'-'.$getter_callable->call().'">'.$obj.'</li>';
    }
    $html .= "</".$list_type.">\n";
    
    //set optional jQuery parameters
    $optional = '';
    $optional .= sfJQueryUtilities::getCompleteOption($this->getOption('complete'));
    $optional .= sfJQueryUtilities::getBeforeSendOption($this->getOption('beforeSend'));
    $optional .= sfJQueryUtilities::getSuccessOption($this->getOption('success'));
    $optional .= sfJQueryUtilities::getErrorOption($this->getOption('error'));
    
    $html .= sprintf(<<<EOF
<script type="text/javascript" charset="utf-8">
  jQuery('#%s').sortable({
    change: function(e, ui)
    {
      serial = jQuery(this).sortable('serialize');
    },
    start:
      function(e, ui)
      {
        %s
      },
    stop:
      function(e, ui)
      {
        jQuery.ajax({
          type:       'POST',
          dataType:   'html',
          url:        '%s',
          data:       serial,
          %s
        });
      }
  });
</script>
EOF
      ,
      $this->getOption('list_id'),
      $this->getOption('start'),
      sfContext::getInstance()->getController()->genUrl($this->getOption('url')),
      $optional
    );
    
    return $html;
  }
  private function getModels()
  {
    $class = constant($this->getOption('model').'::PEER');
    $criteria = is_null($this->getOption('criteria')) ? new Criteria() : clone $this->getOption('criteria');
    $models = call_user_func(array($class, $this->getOption('peer_method')), $criteria, $this->getOption('connection'));
    $methodValue = $this->getOption('key_method');
    if (!method_exists($this->getOption('model'), $methodValue))
    {
      throw new RuntimeException(sprintf('Class "%s" must implement a "%s" method to be rendered in a "%s" widget', $this->getOption('model'), $methodValue, __CLASS__));
    }
    
    return $models;
  }
  public function getJavascripts()
  {
    return array('/sfJqueryWidgetsPlugin/js/jquery-1.2.6.min.js', '/sfJqueryWidgetsPlugin/js/jquery-ui-personalized-1.5.3.packed.js');
  }
}