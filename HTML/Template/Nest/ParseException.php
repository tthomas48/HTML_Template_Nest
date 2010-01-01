<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Exception thrown by the nest expression parser
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
 * @see       HTML_Template_Nest_Parser
 * @since     File available since Release 1.0
 */
/**
 * Exception thrown by the nest expression parser
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @see       HTML_Template_Nest_Parser 
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_ParseException extends Exception
{
    /**
     * Constructor
     *
     * @param string  $message the error message
     * @param DomNode $node    the node where the error occurred
     *
     * @return HTML_Template_Nest_ParseException
     */
    public function __construct($message, $node)
    {
        // TODO: in php 5.3 we can print line number $node->getLineNo()
        parent::__construct($message);
    }
}