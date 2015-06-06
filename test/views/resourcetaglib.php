<?php
class nestView__home_tthomas_git_HTML_Template_Nest_test_viewsresourcetaglib
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
	<?php $nst_r_cssHTML_Template_Nest_Taglib_Resource_Css55724643228bf_name = "res/css.min.css";
?><?php /*<r:css>*/?><link rel="stylesheet" href="res/css.min.db45bda72b702e346f91f3adbe1345da.css" /><?php unset($nst_r_cssHTML_Template_Nest_Taglib_Resource_Css55724643228bf_name);
?><?php /*</r:css>*/?>
	<?php $nst_r_jsHTML_Template_Nest_Taglib_Resource_Javascript557246432298b_name = "res/js.min.js";
?><?php /*<r:js>*/?><script type="text/javascript" src="res/js.min.83bf736035367a0b493361f094f7ef5f.js">
</script><?php unset($nst_r_jsHTML_Template_Nest_Taglib_Resource_Javascript557246432298b_name);
?><?php /*</r:js>*/?>
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
