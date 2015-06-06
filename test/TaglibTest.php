<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Tests custom tag libraries.
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
 * @see       HTML_Template_Nest_Taglib
 * @since     File available since Release 1.0
 */
/**
 * Tests custom tag libraries.
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @see       HTML_Template_Nest_Taglib
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_TaglibTest extends PHPUnit_Framework_TestCase
{

    public function setup()
    {

        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");
        HTML_Template_Nest_View::$HTML_ERRORS = false;
    }

    
    /**
     * Tests custom tag library.
     * 
     * @return unknown_type
     */
    public function testTaglib() 
    {
        
        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");

        $view = new HTML_Template_Nest_View("testtaglib");
        $view->addAttribute("testAttribute", "Testing 1..2..3..");
        $view->addAttribute("foo", "a");
        $view->addAttribute("person", new DataBean());
        
        $view->addAttribute("director", new Director());
        $filename = dirname(__FILE__) . "/viewoutput/testtaglib.html";
        $this->assertEquals(
            $view->render(), 
            trim(file_get_contents($filename))
        );       
    }
}
class DataBean {
    public function isNew() {
        return true;
    }
}
class Director {
    public function getProducerPath() {
        return "/";
    }
    public function getImagePath() {
        return "/images/";
    }
}
