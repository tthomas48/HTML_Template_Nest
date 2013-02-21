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
require_once 'HTML/Template/Nest/View.php';
require_once 'PHPUnit/Framework/TestCase.php';
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
class HTML_Template_Nest_StandardTaglibTest extends PHPUnit_Framework_TestCase
{

    public function setup()
    {

        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");
        HTML_Template_Nest_View::$HTML_ERRORS = false;
    }

    /**
     * Tests the various tags of the standard tag library using the
     * standardtaglib.nst file.
     * 
     * @return unknown_type
     */   
    public function testStandardLib()
    {
        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");

        $view = new HTML_Template_Nest_View("standardtaglib");
        $view->addAttribute("outval", "Output Me!");
        $view->addAttribute("myArray", Array(1, 2, 3));
        $view->addAttribute(
            "associativeArray", 
            Array("Jan" => "January", 
                "Feb" => "February", 
                "Mar" => "March", 
                "Apr" => "April", 
                "May" => "May", 
                "Jun" => "June", 
                "Jul" => "July", 
                "Aug" => "August", 
                "Sep" => "September", 
                "Oct" => "October",
                "Nov" => "November", 
                "Dec" => "December"
            )
        );
        $view->addAttribute("var", 4);
        $view->addAttribute("bulletClass", "tinyBullets");
        
        $filename = dirname(__FILE__) . "/viewoutput/standardtaglib.html";
        $this->assertEquals(
            $view->render(), 
            trim(file_get_contents($filename))
        );        
    }
    public function testInclude()
    {
        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");

        $view = new HTML_Template_Nest_View("standardtaglib-include");

        $view->addAttribute("headervar", "Header");
        $view->addAttribute("footervar", "Footer");
        $view->addAttribute("childvar", "Child");
        $filename = dirname(__FILE__) . "/viewoutput/standardtaglib-include.html";
        $this->assertEquals(
            trim($view->render()), 
            trim(file_get_contents($filename))
        );        
    }
}
