<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Runs all tests
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
require_once 'AttributeTest.php';
require_once 'PreTest.php';
require_once 'ErrorTest.php';
require_once 'ParserTest.php';
require_once 'StandardTaglibTest.php';
require_once 'TaglibTest.php';
require_once 'NestedTagsTest.php';
require_once 'ReplaceTagTest.php';
require_once 'PHPUnit/Framework/TestSuite.php';
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
class HTML_Template_Nest_AllTests
{

    /**
     * Run test suite
     * 
     * @see PHPUnit_Framework_TestSuite
     * @return null
     */
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('HTML_Template_Nest');
        $suite->addTestSuite('HTML_Template_Nest_AttributeTest');        
        $suite->addTestSuite('HTML_Template_Nest_ErrorTest');
        $suite->addTestSuite('HTML_Template_Nest_ParserTest');
        $suite->addTestSuite('HTML_Template_Nest_PreTest');        
        $suite->addTestSuite('HTML_Template_Nest_StandardTaglibTest');
        $suite->addTestSuite('HTML_Template_Nest_TagLibTest');
        $suite->addTestSuite('HTML_Template_Nest_NestedTagsTest');
        $suite->addTestSuite('HTML_Template_Nest_ReplaceTagTest');
        return $suite;
    }
}
