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
require_once 'HTML/Template/Nest/Compiler.php';
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
    public static $VIEW_DIR = "views";
    public static $ALWAYS_COMPILE = false;
 
    /**
     * Constructor
     *
     * @param string $name the view's name without the .php extension
     *
     * @return HTML_Template_Nest_View instance
     */
    public function __construct($name)
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
     * Renders the view using the current attributes and returns the rendred
     * value as a string.
     *
     * @return string output of the rendered template
     */
    public function render()
    {
        $uncompiledFilename = HTML_Template_Nest_View::$VIEW_DIR . "/" . 
            $this->_name . ".nst";
        $compiledFilename = HTML_Template_Nest_View::$VIEW_DIR . 
            "/" . $this->_name . ".php";
        if (HTML_Template_Nest_View::$ALWAYS_COMPILE 
            || !file_exists($compiledFilename)
        ) {
            $compiler = new HTML_Template_Nest_Compiler();
            $compiler->compileAndCache($uncompiledFilename);
        }
        
        ob_start();
        $p = $this->_attributes;
        include $compiledFilename;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
}
