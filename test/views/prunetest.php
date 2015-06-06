<?php
class nestView__home_tthomas_git_HTML_Template_Nest_test_viewsprunetest
{
    public $lastModified = 1345497157;

    public function render(Array $p)
    {

        $_o = function($params, $key) { return array_key_exists($key, $params) ? $params[$key] : null; };
        ob_start();
        register_shutdown_function(array($this, 'fatal_template_error'));
        
        $initialReporting = error_reporting();
        try {
            error_reporting(E_ERROR | E_USER_ERROR | E_PARSE);
            set_error_handler(function($errno, $errstr, $errfile, $errline) {
              if(($errno === E_ERROR) || ($errno === E_USER_ERROR) || ($errno === E_PARSE)) { 
                throw new HTML_Template_Nest_EvalException($errno, $errstr, $errfile, $errline); 
              }
            });
            ?>				
<?php /*<c:foreach>*/?><?php foreach($_o($p, 'prices') as $nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724642b1235_price) {
?>
  <option<?php ob_start()?><?php /* {price->UID} */ echo htmlentities($nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724642b1235_price->UID)?><?php $output = ob_get_clean();echo ' value="' . $output . '"';?><?php ob_start()?><?php ob_start(); ?><?php /*<c:attribute>*/?><?php /* {price->DEFAULT == 'True' ? 'selected' : ''} */ echo htmlentities($nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724642b1235_price->DEFAULT == 'True' ? 'selected' : '')?><?php $output = ob_get_clean();$output = preg_replace('/(\s+)/', ' ', $output);$output = trim($output);print $output;?><?php $output = ob_get_clean();if(!empty($output)) { echo ' selected="' . $output . '"';}?>>
    <?php /*</c:attribute>*/?>
    <?php /*<c:if>*/?><?php if($nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724642b1235_price->NAME != '') {?><?php /* {price->NAME} */ echo htmlentities($nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724642b1235_price->NAME)?>(<?php /* {price->PRICE} */ echo htmlentities($nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724642b1235_price->PRICE)?>)<?php /*</c:if>*/?>
    <?php /*<c:else>*/?><?php } else {?><?php /* {price->PRICE} */ echo htmlentities($nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724642b1235_price->PRICE)?><?php } ?><?php /*</c:else>*/?>
  </option>
<?php }?><?php /*</c:foreach>*/?>
<?php
        } catch(HTML_Template_Nest_EvalException $e) {
            ob_clean();
            error_reporting($initialReporting);
            throw new HTML_Template_Nest_ViewException($e, $this->output);
        }
        error_reporting($initialReporting);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;

    }
}
