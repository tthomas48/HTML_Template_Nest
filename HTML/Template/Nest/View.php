<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * View class for Nest templates
 *
 * The main entry point for generating Nest views. The standard
 * pattern is to create a new object with the view name, set the
 * appropriate attributes and then render the view.
 *
 * PHP version 5
 *
 * This source file is subject to the New BSD license, That is bundled
 * with this package in the file LICENSE, and is available through
 * the world-wide-web at
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the new BSDlicense and are unable
 * to obtain it through the world-wide-web, please send a note to
 * tthomas48@php.net so we can mail you a copy immediately.
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @since     File available since Release 1.0
 */

/**
 * View class for Nest templates
 *
 * The main entry point for generating Nest views. The standard
 * pattern is to create a new object with the view name, set the
 * appropriate attributes and then render the view.
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @see       HTML_Template_Nest_View*
 * @since     Class available since Release 1.0.0
 */

class HTML_Template_Nest_View
{
    private $_attributes = Array();
    private $_name = "";
    public static $INCLUDE_PATHS = array("views");
    public static $CACHE = true;
    public static $HTML_ERRORS = true;
    private $viewInstance;

    /**
     * Constructor
     *
     * @param string $name the view's name without the .php extension
     *
     * @return HTML_Template_Nest_View instance
     */
    public function __construct($name = "")
    {
        $this->_name = $name;
    }

    /**
     * Adds an attribute to the attribute array for this page. Values
     * added to the view will be used at runtime in the compiled template.
     *
     * @param string $key   attribute key
     * @param string $value attribute value
     *
     * @return null
     */
    public function addAttribute($key, $value)
    {
        $this->_attributes[$key] = $value;
    }

    /**
     * Sets all attributes in the attribute array. Will erase
     * any existing attributes
     *
     * @param array $attributes attribute array
     *
     * @return null
     */
    public function setAttributes($attributes)
    {
        $this->_attributes = $attributes;
    }
    
    /**
     * Adds an include path to search for resources.
     *
     * @param array $path path to search
     *
     * @return null
     */
    public static function addIncludePath($path)
    {
    	HTML_Template_Nest_View::$INCLUDE_PATHS[] = $path;
    }
    

    /**
     * Renders the view using the current attributes and returns the rendred
     * value as a string.
     *
     * @return string output of the rendered template
     */
    public function render()
    {

   	  $viewPath = "";
   	  foreach(HTML_Template_Nest_View::$INCLUDE_PATHS as $path) {
   	  	if(file_exists($path  . "/" . $this->_name . ".nst")) {
   	  		$viewPath = $path;
   	  	}
   	  }

      $compiler = new HTML_Template_Nest_Compiler();
      $this->viewInstance = $compiler->compileAndCache($viewPath, $this->_name, HTML_Template_Nest_View::$CACHE);
      return $this->renderContent($this->viewInstance);
    }
    public function renderContent($viewInstance = "") 
    {
        register_shutdown_function(array($this, 'fatal_template_error'));
        return $viewInstance->render($this->_attributes);
    }

    public function fatal_template_error() {


        # Getting last error
        $error = error_get_last();
        // we only want this shutdown handler called by actual errors in the view
        if(strpos($error['file'], 'HTML/Template/Nest/View.php') === false) {
            return;
        }


        if(ob_get_level() > 0) {
            ob_end_clean();
        }
        # Checking if last error is a fatal error
        if(($error['type'] === E_ERROR) 
            || ($error['type'] === E_USER_ERROR)
            || ($error['type'] === E_PARSE))
        {
            # Here we handle the error, displaying HTML, logging, ...
            $lines = explode("\n", $this->output);
            
            $message = "Fatal Template Error: " . $error['message'] . " on line " . ($error['line'] - 2) . "\n";
            for($i = 0, $il = count($lines); $i < $il; $i++) {
                $line = $lines[$i];
                if(HTML_Template_Nest_View::$HTML_ERRORS) {
                    $line = htmlentities($line);
                }
                $message .= $i . ":" . $line . "\n";
            }
            
            if(HTML_Template_Nest_View::$HTML_ERRORS) {
                print "<pre>\n";
            }
            
            print $message;
            
            if(HTML_Template_Nest_View::$HTML_ERRORS) {
                print "\n</pre>";
            }
        }
    }
}
