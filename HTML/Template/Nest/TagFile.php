<?php
/**
 * Base class for Nest Tagfiles.
 *
 * Tag files are an easy way to build components without having to write code.
 * Create a valid html snippet and put ${#processBody#} in where you would
 * like nested code to be processed. The abstract method getTagFilename is
 * the only code that should need to be implemented.
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
require_once 'HTML/Template/Nest/Tag.php';
require_once 'HTML/Template/Nest/TagException.php';
/**
 * Base class for Nest Tagfiles.
 *
 * Tag files are an easy way to build components without having to write code.
 * Create a valid html snippet and put ${#processBody#} in where you would
 * like nested code to be processed. The abstract method getTagFilename is
 * the only code that should need to be implemented.
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
abstract class HTML_Template_Nest_TagFile extends HTML_Template_Nest_Tag
{
    protected $compiler;
    protected $document;
    protected $startDocument = null;
    protected $endDocument = null;
    protected $childNodes = array();

    /**
     * Constructor
     *
     * @param HTML_Template_Nest_Compiler $compiler   current compiler
     * @param DomNode                     $node       current node
     * @param Array                       $attributes current attributes 
     *
     * @throws HTML_Template_Nest_TagException
     * @return HTML_Template_Nest_TagFile instance
     */
    public function __construct($compiler, $node, $attributes)
    {
        if (!file_exists($this->getTagFilename())) {
            throw new HTML_Template_Nest_TagException(
                "Unable to find file: " . $this->getTagFilename()
            );
        }
        parent::__construct($compiler, $node, $attributes);
        $children = new DomNodeList();
        if ($node->hasChildNodes()) {
            $children = $node->childNodes;
        }

        $newChild = $node->ownerDocument->createDocumentFragment();
        try {
            $newChild->appendXML(file_get_contents($this->getTagFilename()));
        } catch(Exception $e) {
            $message = "Error parsing tagfile: " .
                $this->getTagFilename() . "; " . $e->getMessage();
            throw new HTML_Template_Nest_TagException($message, $node);
        }
        $childrenList = array();
        foreach ($children as $child) {
            $childrenList[] = $child;
        }
        foreach ($childrenList as $child) {
            $node->removeChild($child);
        }
        $node->appendChild($newChild);
        
        $this->_processBodyToken($node, $childrenList);

        
        // now we need to take all the node's children, append them to the
        // parent at the same position
        
        $parentNode = $node->parentNode;
        $children = $node->childNodes;
        $oldNodes = array(); 
        $childrenList = array();
        foreach ($children as $child) {
            $childrenList[] = $child;
        }
        
        foreach ($childrenList as $child) {
            $this->childNodes[] = $parentNode->insertBefore(
                $node->removeChild($child), 
                $node
            );
        }
        $node->parentNode->removeChild($node);
    }

    /**
     * Recursively looks for the ${#processBody#} token. It removes the token
     * and appends the original node's children to the parent of the token.
     *
     * @param DomNode     $node     the current node to inspect
     * @param DomNodeList $children the children of the the original tag
     *
     * @return null
     */
    private function _processBodyToken($node, $bodyChildren)
    {
        if ($node->hasChildNodes()) {
            $currentChildren = array();
            foreach ($node->childNodes as $child) {
                $currentChildren[] = $child;
            }
            foreach ($currentChildren as $child) {
                if ($child->hasAttributes()
                    && $child->getAttribute("_replace") == "true"
                ) {
                    $replacedNodes = false;
                    for($i = 0; $i < count($bodyChildren); $i++) {
                        $nestedChild = $bodyChildren[$i];
                        if($nestedChild->nodeName == $child->nodeName) {
                            $replacedNodes = true;
                            $node->insertBefore($nestedChild, $child);    
                            unset($bodyChildren[$i]);
                        }
                    }
                    if($replacedNodes) {
                        $node->removeChild($child);
                    }
                }
                if ($child->nodeType == XML_TEXT_NODE) {
                    if (trim($child->nodeValue) == '${#processBody#}') {
                        foreach ($bodyChildren as $nestedChild) {
                            $node->insertBefore($nestedChild, $child);
                        }
                        $node->removeChild($child);                        
                    }
                }
                if ($child->hasChildNodes()) {
                    $this->_processBodyToken($child, $bodyChildren);
                }
            }
        }
    }

    /**
     * Evaluated before the content of the tag.
     *
     * @return string content to add to php file
     * 
     * @see HTML_Template_Nest_Tag::start()
     */   
    public function start()
    {
    }

    /**
     * Evaluated after the content of the tag.
     *
     * @return string content to add to php file
     * 
     * @see HTML_Template_Nest_Tag::end()
     */   
    public function end()
    {
    }

    /**
     * Returns the name of the tagfile to parse.
     * 
     * @return string filename
     */
    public abstract function getTagFilename();
    
    /**
     * Returns the current node's children
     * 
     * @return Array current node children
     */
    public function getNodeChildren()
    {
        return $this->childNodes;
    }    
}
