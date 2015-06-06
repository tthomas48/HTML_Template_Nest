<?php
class nestView__home_tthomas_git_HTML_Template_Nest_test_viewssnippet
{
    public $lastModified = 1349127497;

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
  var test_snippets = { <?php ob_start(); ?>
      'mysnippet': <?php ob_start(); ?>
        <div class="myclass" id="foo">
            Some text {{foo}}
        </div>
      <?php $output = ob_get_clean();print json_encode($output)?>,
      'otherbit': <?php ob_start(); ?>
          Just some text in here.
      <?php $output = ob_get_clean();print json_encode($output)?>
    <?php $output = ob_get_clean();print $output?> };      
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
