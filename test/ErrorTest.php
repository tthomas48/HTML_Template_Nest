<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Tests that test various error conditions.
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
 * @see       HTML_Template_Nest_View
 * @since     File available since Release 1.0
 */
require_once 'HTML/Template/Nest/View.php';
require_once 'HTML/Template/Nest/TagException.php';
require_once 'HTML/Template/Nest/CompilerException.php';
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * Runs all tests
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
class HTML_Template_Nest_ErrorTest extends PHPUnit_Framework_TestCase
{

    /**
     * Setup path for unit tests
     * 
     * @see PHPUnit_Framework_TestCase::setup()
     * @return null
     */
    public function setup()
    {
        
        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");
        HTML_Template_Nest_View::$HTML_ERRORS = false;
    }

    /**
     * Test an unparsable tag file.
     * 
     * @return null
     */
    public function testBadTagFile()
    {

        $view = new HTML_Template_Nest_View("badxml");

        $caughtException = false;
        try {
            $view->render();
        } catch (HTML_Template_Nest_TagException $e) {
            $caughtException = true;
        }
        $this->assertTrue($caughtException);
    }

    /**
     * Test an unparsable template file
     * 
     * @return null
     */
    public function testBadTemplate()
    {
        
        $view = new HTML_Template_Nest_View("badtemplate");
        
        $caughtException = false;
        try {
            $view->render();
        } catch (HTML_Template_Nest_CompilerException $e) {
            $caughtException = true;
        }
        $this->assertTrue($caughtException);
    }
}
