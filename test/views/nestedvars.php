<?php
class nestView__home_tthomas_git_HTML_Template_Nest_test_viewsnestedvars
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
            ?><div>
    <?php $nst_test_tabindexaHTML_Template_Nest_TagFile55724642e8e5c_tabIndex = "1";
?><?php /*<test:tabindexa>*/?>
    
    
        <?php /*<test:tabindexb>*/?>
    <input type="text"<?php ob_start()?><?php /* {tabIndex++} */ echo htmlentities($nst_test_tabindexaHTML_Template_Nest_TagFile55724642e8e5c_tabIndex++)?><?php $output = ob_get_clean();echo ' tabindex="' . $output . '"';?>/>

<?php /*</test:tabindexb>*/?>
        <?php /*<test:tabindexb>*/?>
    <input type="text"<?php ob_start()?><?php /* {tabIndex++} */ echo htmlentities($nst_test_tabindexaHTML_Template_Nest_TagFile55724642e8e5c_tabIndex++)?><?php $output = ob_get_clean();echo ' tabindex="' . $output . '"';?>/>

<?php /*</test:tabindexb>*/?>
        <?php /*<test:tabindexb>*/?>
    <input type="text"<?php ob_start()?><?php /* {tabIndex++} */ echo htmlentities($nst_test_tabindexaHTML_Template_Nest_TagFile55724642e8e5c_tabIndex++)?><?php $output = ob_get_clean();echo ' tabindex="' . $output . '"';?>/>

<?php /*</test:tabindexb>*/?>
        <?php /*<test:tabindexb>*/?>
    <input type="text"<?php ob_start()?><?php /* {tabIndex++} */ echo htmlentities($nst_test_tabindexaHTML_Template_Nest_TagFile55724642e8e5c_tabIndex++)?><?php $output = ob_get_clean();echo ' tabindex="' . $output . '"';?>/>

<?php /*</test:tabindexb>*/?>
        <?php /*<test:tabindexb>*/?>
    <input type="text"<?php ob_start()?><?php /* {tabIndex++} */ echo htmlentities($nst_test_tabindexaHTML_Template_Nest_TagFile55724642e8e5c_tabIndex++)?><?php $output = ob_get_clean();echo ' tabindex="' . $output . '"';?>/>

<?php /*</test:tabindexb>*/?>
        <?php /*<test:tabindexb>*/?>
    <input type="text"<?php ob_start()?><?php /* {tabIndex++} */ echo htmlentities($nst_test_tabindexaHTML_Template_Nest_TagFile55724642e8e5c_tabIndex++)?><?php $output = ob_get_clean();echo ' tabindex="' . $output . '"';?>/>

<?php /*</test:tabindexb>*/?>
    

<?php unset($nst_test_tabindexaHTML_Template_Nest_TagFile55724642e8e5c_tabIndex);
?><?php /*</test:tabindexa>*/?>
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
