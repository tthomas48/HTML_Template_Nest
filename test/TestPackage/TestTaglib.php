<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Custom tag library for testing.
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
require_once 'HTML/Template/Nest/Tag.php';
require_once 'HTML/Template/Nest/TagFile.php';
require_once 'HTML/Template/Nest/Taglib.php';
/**
 * Custom tag library for testing.
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
class TestPackage_TestTaglib extends HTML_Template_Nest_Taglib
{
    protected $tags = array(
        "testTagFile" => "TestPackage_TestTaglib_TestTagFile",
        "wrappableWithAttributes" => "TestPackage_TestTaglib_WrappableWithAttributesTagFile",
    );
}

/**
 * A simple tag based upon a tagfile.
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @see       HTML_Template_Nest_TagFile
 * @since     Class available since Release 1.0.0
 */
class TestPackage_TestTaglib_TestTagFile extends HTML_Template_Nest_TagFile
{

    public $declaredAttributes = array("name", "key", "bar", "boo");
    /**
     * gets the tag's filename
     * 
     * @see HTML_Template_Nest_Tagfile::getTagFilename()
     * @return string filename
     */
    public function getTagFilename() 
    {
        return HTML_Template_Nest_View::$VIEW_DIR . "/templates/testpackage/testtaglib/tagfile.tmpl";
    } 
}

class TestPackage_TestTaglib_WrappableWithAttributesTagFile 
    extends HTML_Template_Nest_TagFile
{
    protected $declaredAttributes = array(
        "attribute1", "attribute2", "attribute3"
    );
    /**
     * gets the tag's filename
     * 
     * @see HTML_Template_Nest_Tagfile::getTagFilename()
     * @return string filename
     */
    public function getTagFilename() 
    {
        return HTML_Template_Nest_View::$VIEW_DIR . "/templates/testpackage/testtaglib/wrappableTagFile.tmpl";
    } 
}
