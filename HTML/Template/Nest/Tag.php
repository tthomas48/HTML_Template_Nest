<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Base class for Nest Tags.
 *
 * Nest tags are a way to easily build components including nested ones. The Nest
 * tag class defines a start and end tag. The body will be evaluated in between.
 * The current node is provided so that you can do more complex operations based upon
 * the context of the current DOM node.
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
require_once 'TaglibException.php';
require_once 'ParseException.php';
/**
 * Base class for Nest Tags.
 *
 * Nest tags are a way to easily build components including nested ones. The Nest
 * tag class defines a start and end tag. The body will be evaluated in between.
 * The current node is provided so that you can do more complex operations based upon
 * the context of the current DOM node.
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: 1.3.4
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_Tag
{
    protected $node;
    protected $compiler;
    protected $attributes;
    protected $declaredAttributes = array();
    protected $attributeTypes = array();
    protected $id;

    /**
     * Constructor
     *
     * @param HTML_Template_Nest_Compiler $compiler   current compiler
     * @param DomNode                     $node       current node
     * @param Array                       $attributes current attributes
     *
     * @return HTML_Template_Nest_Tag instance
     */
    public function __construct($compiler, $node, $attributes)
    {
        $this->compiler = $compiler;
        $this->node = $node;
        $this->attributes = $attributes;
        $this->id = $node->tagName . uniqid(get_class($this));
    }

    public function getAttributeDeclarations() {
        if(count($this->declaredAttributes) == 0) {
            return "";
        }
        $output = "<?php ";
        foreach($this->declaredAttributes as $key) {
            $value = "";

            $type = "string";
            if(array_key_exists($key, $this->attributeTypes)) {
                $type = $this->attributeTypes[$key];
            }
            
            switch($type) {
                case 'string':
                    if(array_key_exists($key, $this->attributes)) {
                        $value = $this->compiler->parser->parse($this->attributes[$key], false, "\"");
                    }
                    $this->registerVariable($key);

                    $output .= "\$" . $this->getVariableName($key) . " = \"" . $value . "\";\n";
                    break;
                case 'object':
                default:

                    if(array_key_exists($key, $this->attributes)) {
                        $value = $this->compiler->parser->parse($this->attributes[$key], false, "");
                    }
                    $this->registerVariable($key);
  
                    // just output the value, no string handling
                    $output .= "\$" . $this->getVariableName($key) . " = " . ($value === NULL || $value == "" ? "NULL" : $value) . ";\n";
                    break;
            }

        }
        $output .= "?>";
        return $output;
    }

    public function getAttributeUnsets() {
        if(count($this->declaredAttributes) == 0) {
            return "";
        }

        $output = "<?php ";
        foreach($this->declaredAttributes as $key) {
            $output .= "unset(\$" . $this->getVariableName($key) . ");\n";
            $this->unregisterVariable($key);
        }
        $output .= "?>";
        return $output;
    }

    /**
     * Registers a local variable. This variable will be used literally
     * rather than pulling a key from the $p global array until
     * unregisterVariable is called.
     *
     * @param string $key name of variable
     *
     * @return null
     */
    protected function registerVariable($key)
    {
        $this->compiler->parser->registerVariable($this->id, $key);
    }

    /**
     * Unregisters a local variable. The variable specified will again be
     * pulled from the $p global array.
     *
     * @param string $key name of variable
     *
     * @return null
     */
    protected function unregisterVariable($key)
    {
        $this->compiler->parser->unregisterVariable($this->id, $key);
    }

    protected function getVariableName($key)
    {
        return $this->compiler->parser->getLocalVariableName($key);
    }

    /**
     * Evaluated before the content of the tag.
     *
     * @return string content to add to php file
     */
    public function start()
    {

    }

    /**
     * Evaluated after all child content is processed. Allows filtering of child body.
     *
     * @return string content to add to php file
     */
    public function filter($output)
    {
        return $output;
    }

    /**
     * Evaluated after the content of the tag.
     *
     * @return string content to add to php file
     */
    public function end()
    {

    }

    /**
     * A utility function to find the next non-text sibling node.
     *
     * @param DomNode $node the current node
     *
     * @return DomNode next sibling node
     */
    public function findNextSibling($node)
    {
        $nextSibling = null;
        if ($node->nextSibling != null
        && $node->nextSibling->nodeType == XML_TEXT_NODE
        ) {
            return $this->findNextSibling($node->nextSibling);
        }
        return $node->nextSibling;
    }

    /**
     * Simply wraps the input in php tags.
     *
     * @param string $output input to wrap
     *
     * @return string wrapped input
     */
    public function wrapOutput($output)
    {
        return "<?php " . $output . "?>";
    }

    /**
     * Helper function that gets a required attribute and
     * throws an exception if it doesn't exist.
     *
     * @param string $name sname of attribute
     *
     * @throws HTML_Template_Nest_ParseException
     * @return string attribute value
     */
    public function getRequiredAttribute($name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            throw new HTML_Template_Nest_ParseException(
                "Missing required attribute '$name'", 
            $this->node
            );
        }
        return $this->attributes[$name];
    }

    /**
     * Helper function that gets an optional attribute and
     * returns null if it doesn't exist.
     *
     * @param string $name sname of attribute
     *
     * @return string attribute value
     */

    public function getOptionalAttribute($name, $default_value = NULL)
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $default_value;
        }
        return $this->attributes[$name];
    }

    /**
     * Returns the current node's children
     *
     * @return Array current node children
     */
    public function getNodeChildren()
    {
        return $this->node->childNodes;
    }
    public function isPhpEnabled()
    {
        return true;
    }
}
