<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Tests the resource tag library
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
require_once 'HTML/Template/Nest/Taglib/Resource.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once dirname(__FILE__) . "/../vendor/autoload.php";
/**
 * Tests the resource tag library
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
class HTML_Template_Nest_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests the various tags of the resource tag library using the
     * resourcetaglib.nst file.
     * 
     * @return unknown_type
     */   
    public function testPre()
    {
        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");
        HTML_Template_Nest_View::$HTML_ERRORS = false;
        HTML_Template_Nest_Taglib_Resource::$BASE_PATH = dirname(__FILE__) . "/";


        $view = new HTML_Template_Nest_View("resourcetaglib");
//         $view->addAttribute("list", array(1,2,3,4,5));
//         $view->addAttribute("2quoteString", '"quoted"');
//         $view->addAttribute("1quoteString", "'quoted'");
        $filename = dirname(__FILE__) . "/viewoutput/resourcetaglib.html";
#        print $view->render();
#        exit;
        $this->assertEquals(
            trim($view->render()), 
            trim(file_get_contents($filename))
        );       
        $this->assertEquals(
            file_get_contents(dirname(__FILE__) . "/viewoutput/css.min.css"),
            file_get_contents(dirname(__FILE__) . "/res/css.min.css")
        ); 
        $this->assertEquals(
            file_get_contents(dirname(__FILE__) . "/viewoutput/js.min.js"),
            file_get_contents(dirname(__FILE__) . "/res/js.min.js")
        ); 

    }
}
