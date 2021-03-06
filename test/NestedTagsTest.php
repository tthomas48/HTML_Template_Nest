<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Tests the standard tag library
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
 * @see       HTML_Template_Nest_Taglib_Standard
 * @since     File available since Release 1.0
 */
/**
 * Tests the standard tag library
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @see       HTML_Template_Nest_Taglib_Standard
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_NestedTagsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests nested tags that use the same local variable
     * 
     * @return unknown_type
     */   
    public function testNested()
    {
        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");
        HTML_Template_Nest_View::$HTML_ERRORS = false;


        $view = new HTML_Template_Nest_View("nested");
        $view->addAttribute("outval", "Output Me!");
        $filename = dirname(__FILE__) . "/viewoutput/nested.html";
        $this->assertEquals(
            $view->render(), 
            trim(file_get_contents($filename))
        );        
    }

    public function testNestedVars()
    {
        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");
        HTML_Template_Nest_View::$HTML_ERRORS = false;


        $view = new HTML_Template_Nest_View("nestedvars");
        $view->addAttribute("outval", "Output Me!");
        $filename = dirname(__FILE__) . "/viewoutput/nestedvars.html";
        $this->assertEquals(
            $view->render(), 
            trim(file_get_contents($filename))
        );        
    }

    
}
