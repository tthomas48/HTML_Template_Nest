<?php
class nestView__home_tthomas_git_HTML_Template_Nest_test_viewsfilter
{
    public $lastModified = 1378351033;

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
	<?php $nst_r_cssHTML_Template_Nest_Taglib_Resource_Css5572464360ed0_name = "res/scss.min.css";
?><?php /*<r:css>*/?><link rel="stylesheet" href="res/scss.min.17ab4a24de5b8c3af1de3c700e080ba1.css" /><?php unset($nst_r_cssHTML_Template_Nest_Taglib_Resource_Css5572464360ed0_name);
?><?php /*</r:css>*/?>
	<?php $nst_r_jsHTML_Template_Nest_Taglib_Resource_Javascript5572464360f5b_name = "res/js.filter.min.js";
?><?php /*<r:js>*/?><script type="text/javascript" src="res/js.filter.min.0b6ecf17e30037994d3ffee51b525914.js">
</script><?php unset($nst_r_jsHTML_Template_Nest_Taglib_Resource_Javascript5572464360f5b_name);
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
