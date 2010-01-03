<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Base class for Nest Taglibs
 *
 * Taglibs are libraries of tags that can be referenced in a nest template
 * using xmlns:myref="urn:nsttl:MyClassName". Then the tags referred to in the 
 * tag library can be referenced simply by using an xml prefix.
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
require_once 'HTML/Template/Nest/TaglibException.php';
require_once 'HTML/Template/Nest/ParseException.php';
/**
 * Base class for Nest Taglibs
 *
 * Taglibs are libraries of tags that can be referenced in a nest template
 * using xmlns:myref="urn:nsttl:MyClassName". Then the tags referred to in the 
 * tag library can be referenced simply by using an xml prefix.
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_Taglib
{
    protected $tags = Array();

    /**
     * Constructor
     * 
     * @return HTML_Template_Nest_Taglib
     */
    public function __construct()
    {
    }

    /**
     * Looks up a tag by friendly name in the tag library and returns
     * an instance.
     * 
     * @param HTML_Template_Nest_Compiler $compiler   the current compiler
     * @param DomNode                     $node       the current node
     * @param Array                       $attributes the current attributes
     * 
     * @throws HTML_Template_Nest_TaglibException
     * @return HTML_Template_Nest_Tag tag instance
     */
    public function getTagByName($compiler, $node, $attributes)
    {
        $name = $node->localName;
        if (!array_key_exists($name, $this->tags)) {
            throw new HTML_Template_Nest_TaglibException(
                "Unable to find tag '$name'", $node
            );
        }
        $className = $this->tags[$name];
        return new $className($compiler, $node, $attributes);
    }
}