<?php
/**
 * sfJQueryUtilities contains utility methods used in the widgets
 *
 * @package    symfony
 * @subpackage widget
 * @author     Ahmed El.Hussaini <eng.ahmed.elhussaini@sandbox-ws.com>
 */
class sfJQueryUtilities
{
  public static function getCompleteOption($option)
  { 
    $script = '';
    if (!is_null($option))
    {
      $script = sprintf(<<<EOF

      complete:
        function(xhrObj, textStatus)
        {
          %s
        },
EOF
        ,
        $option
      );
    }
    
    return $script;
  }
  
  public static function getSuccessOption($option)
  {
    $script = '';
    if (!is_null($option))
    {
      $script = sprintf(<<<EOF
      success:
        function(data, textStatus)
        {
          %s
        },
EOF
        ,
        $option
      );
    }
    
    return $script;
  }
  
  public static function getErrorOption($option)
  {
    $script = '';
    if (!is_null($option))
    {
      $script = sprintf(<<<EOF

      error:
        function(xhrObj, textStatus, errorThrow)
        {
          %s
        },
EOF
        ,
        $option
      );
    }
    return  $script;
  }
  
  public static function getBeforeSendOption($option)
  {
    $script ='';
    if (!is_null($option))
    {
      $script = sprintf(<<<EOF

      beforeSend:
        function(xhrObj)
        {
          %s
        },
EOF
        ,
        $option
      );
    }
    
    return $script;
  }
}