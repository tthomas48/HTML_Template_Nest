<?php
class nestView__home_tthomas_git_HTML_Template_Nest_test_viewsattribute
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
            ?><div class="<?php ob_start(); ?><?php /*<c:attribute>*/?>
        <?php /*<c:foreach>*/?><?php foreach($_o($p, 'list') as $nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag557246413bf9c_classNum) {
?>
            classname<?php /* {classNum} */ echo htmlentities($nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag557246413bf9c_classNum)?>
        <?php }?><?php /*</c:foreach>*/?>
    <?php $output = ob_get_clean();$output = preg_replace('/(\s+)/', ' ', $output);$output = trim($output);print $output;?>">
    <?php /*</c:attribute>*/?>
    This is just some stuff inside the div  <b> with some nested</b> tags.
    <div>And stuff</div>
    <div v="'quoted'">Test</div>
    <div v='"quoted"'>Test</div>
</div><?php
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
