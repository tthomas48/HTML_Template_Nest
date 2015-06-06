<?php
class nestView__home_tthomas_git_HTML_Template_Nest_test_viewsjson
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
            ?>{
    "glossary": {
        "title": "<?php /* {title} */ echo htmlentities($_o($p, 'title'))?>",
        "GlossDiv": {
            "title": "S",
            "GlossList": {
                "GlossEntry": {
                    "ID": "SGML",
                    "SortAs": "SGML",
                    "GlossTerm": "Standard Generalized Markup Language",
                    "Acronym": "SGML",
                    "Abbrev": "ISO 8879:1986",
                    "GlossDef": {
                        "para": "A meta-markup language, used to create markup languages such as DocBook.",
                        "GlossSeeAlso": ["GML", "XML"]
                    },
                    "GlossSee": "markup"
                }
            }
        }
    }
}<?php
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
