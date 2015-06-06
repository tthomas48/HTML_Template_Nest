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
class HTML_Template_Nest_AttributeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests the various tags of the standard tag library using the
     * standardtaglib.nst file.
     * 
     * @return unknown_type
     */   
    public function testPre()
    {
        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");
        HTML_Template_Nest_View::$HTML_ERRORS = false;


        $view = new HTML_Template_Nest_View("attribute");
        $view->addAttribute("list", array(1,2,3,4,5));
        $view->addAttribute("2quoteString", '"quoted"');
        $view->addAttribute("1quoteString", "'quoted'");
        $filename = dirname(__FILE__) . "/viewoutput/attribute.html";
        $this->assertEquals(
            $view->render(), 
            trim(file_get_contents($filename))
        );        
    }
   public function testPrune()
    {
        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");
        HTML_Template_Nest_View::$HTML_ERRORS = false;

        $prices = array();
        $price = new stdClass();
        $price->UID = "abc";
        $price->DEFAULT = 'false';
        $price->NAME = 'General Admission';
        $price->PRICE = '15.00';
        $prices[] = $price;

        $price2 = new stdClass();
        $price2->UID = "def";
        $price2->DEFAULT = 'True';
        $price2->NAME = 'Students';
        $price2->PRICE = '12.00';
        $prices[] = $price2;

        $view = new HTML_Template_Nest_View("prunetest");
        $view->addAttribute("prices", $prices);
        $filename = dirname(__FILE__) . "/viewoutput/prunetest.html";
        $this->assertEquals(
            $view->render(), 
            file_get_contents($filename)
        );        
    }

}
