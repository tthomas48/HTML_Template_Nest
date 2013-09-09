<?php
/**
 * Base class for Nest Tagfiles.
 *
 * Tag files are an easy way to build components without having to write code.
 * Create a valid html snippet and put ${#processBody#} in where you would
 * like nested code to be processed. The template file is specified by setting
 * filename.
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
require_once 'Tag.php';
require_once 'TagException.php';
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
class HTML_Template_Nest_TagFile extends HTML_Template_Nest_Tag
{
  public $filename;
  protected $document;
  protected $startDocument = null;
  protected $endDocument = null;
  protected $childNodes = array();
  private $initialized = false;

  /**
   * Constructor
   *
   * @param HTML_Template_Nest_Node_IRender   $renderer     current renderer
   * @param DomNode                     $node       current node
   * @param Array                       $attributes current attributes
   *
   * @throws HTML_Template_Nest_TagException
   * @return HTML_Template_Nest_TagFile instance
   */
  public function __construct(HTML_Template_Nest_Node_IRender &$renderer, $node, $attributes, $filename = "")
  {
    parent::__construct($renderer, $node, $attributes);
    $this->initialized = false;
    $this->filename = $filename;
  }

  public function init() {
    if($this->initialized) {
      return true;
    }
    $node = $this->node;

    $fullFilename = "";
    
    $foundFile = false;
    if(file_exists($this->getTagFilename())) {
      $fullFilename = $this->getTagFilename();
      $foundFile = true;
    }
    if(!$foundFile) {
      foreach(HTML_Template_Nest_View::$INCLUDE_PATHS as $path) {
        if (file_exists($path . "/" . $this->getTagFilename())) {
          $fullFilename = $path . "/" . $this->getTagFilename();
          $foundFile = true;
        }
      }
    }
    if (!$foundFile) {
      throw new HTML_Template_Nest_TagException(
          "Unable to find file " . $this->getTagFilename() . " for " . $node->tagName,
          $node
      );
    }
    $oldChildren = $this->renderer->getChildren();

    $newChild = $node->ownerDocument->createDocumentFragment();
    try {
      if(!$newChild->appendXML(file_get_contents($fullFilename))) {
        $message = "Error appending tagfile: " .
            $this->getTagFilename();
        throw new HTML_Template_Nest_TagException($message, $node);
      }
    } catch(Exception $e) {
      $message = "Error parsing tagfile: " .
          $this->getTagFilename() . "; " . $e->getMessage();
      throw new HTML_Template_Nest_TagException($message, $node);
    }
    
    foreach($oldChildren as $child) {
      $this->renderer->removeChild($child);
    }
    $this->renderer->appendXML($newChild);
    
    $this->_processBodyToken($this->renderer, $oldChildren);

    $this->initialized = true;
    return false;
  }

  /**
   * Recursively looks for the <nst:processBody /> tag. It removes the tag
   * and appends the original node's children to the parent of the tag.
   *
   * @param HTML_Template_Nest_Node_IRender     $renderer     the current renderer to inspect
   * @param DomNodeList $children the children of the the original tag
   *
   * @return null
   */
  private function _processBodyToken(&$renderer, &$bodyChildren)
  {
    if($renderer->hasChildren()) {
      $rendererChildren = $renderer->getChildren();
      foreach($rendererChildren as $child) {
        if ($child->hasAttribute("_replace") && $child->getAttribute("_replace") == "true") {
          $replacedNodes = false;
          $toUnset = array();
          for($i = 0; $i < count($bodyChildren); $i++) {
            $nestedChild = $bodyChildren[$i];
            if($nestedChild->getName() == $child->getName()) {
              $replacedNodes = true;
              
              $renderer->insertBefore($child, $nestedChild);
              $toUnset[] = $nestedChild;
            }
          }
          foreach($toUnset as $nestedChild) {
            $index = -1;
            for($i = 0; $i < count($bodyChildren); $i++) {
              if($bodyChildren[$i] === $nestedChild) {
                $index = $i;
              }
            }
            //$index = array_search($nestedChild, $bodyChildren);
            if($index >= 0) {
              array_splice($bodyChildren, $index, 1);
            }
          }
          if($replacedNodes) {
            $renderer->removeChild($child);
          }
        }
        elseif ($child->getNamespaceUri() == "http://nest.sourceforge.net/") {
          if (trim($child->getLocalName()) == 'processBody') {
            foreach ($bodyChildren as $nestedChild) {
              $renderer->insertBefore($child, $nestedChild);
            }
            $renderer->removeChild($child);
          }
          elseif (trim($child->getLocalName()) == 'attribute') {
            if($child->hasAttribute("name")) {
              $this->declaredAttributes[] = $child->getAttribute("name");
              if($child->hasAttribute("defaultValue")) {
                $this->attributes[$child->getAttribute("name")] = $child->getAttribute("defaultValue");
              }
              if($child->hasAttribute("type")) {
                $this->attributeTypes[$child->getAttribute("name")] = $child->getAttribute("type");
              }
              $renderer->removeChild($child);
            }
          }
        }
        if (count($child->getChildren()) > 0) {
          $this->_processBodyToken($child, $bodyChildren);
        }
      }
    }
    
    /*
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
          $toUnset = array();
          for($i = 0; $i < count($bodyChildren); $i++) {
            $nestedChild = $bodyChildren[$i];
            if($nestedChild->nodeName == $child->nodeName) {
              $replacedNodes = true;
              $node->insertBefore($nestedChild, $child);
              $toUnset[] = $nestedChild;
            }
          }
          foreach($toUnset as $nestedChild) {
            $index = array_search($nestedChild, $bodyChildren);
            array_splice($bodyChildren, $index, 1);
          }
          if($replacedNodes) {
            $node->removeChild($child);
          }
        }
        if ($child->lookupNamespaceURI($child->prefix) == "http://nest.sourceforge.net/") {
          if (trim($child->localName) == 'processBody') {
            foreach ($bodyChildren as $nestedChild) {
              $node->insertBefore($nestedChild, $child);
            }
            $node->removeChild($child);
          }
          elseif (trim($child->localName) == 'attribute') {
            if($child->hasAttribute("name")) {
              $this->declaredAttributes[] = $child->getAttribute("name");
              if($child->hasAttribute("defaultValue")) {
                $this->attributes[$child->getAttribute("name")] = $child->getAttribute("defaultValue");
              }
              if($child->hasAttribute("type")) {
                $this->attributeTypes[$child->getAttribute("name")] = $child->getAttribute("type");
              }
              $node->removeChild($child);
            }
          }
        }
        if ($child->hasChildNodes()) {
          $bodyChildren = $this->_processBodyToken($child, $bodyChildren);
        }
      }
    }
    return $bodyChildren;
    */
  }

  public function getTagFilename() {
    return $this->filename;
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
   * Returns the current node's children
   *
   * @return Array current node children
   */
  public function getNodeChildren()
  {
    return $this->childNodes;
  }
}
