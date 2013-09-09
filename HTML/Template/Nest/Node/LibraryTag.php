<?php
class HTML_Template_Nest_Node_LibraryTag extends HTML_Template_Nest_Node_Node {
  
  private $renderable;
  private $tag;
  
  public function __construct(HTML_Template_Nest_Parser &$parser, DOMNode $node, HTML_Template_Nest_Node_IRender &$parent = NULL) {
    parent::__construct($parser, $node, $parent);
    $this->renderable = false;
    $this->tag = $this->loadTag($node);
  }

  public function init() {
    $this->renderable = $this->tag->init();
    parent::init();
  }
  
  public function isRenderable() {
    if(!$this->renderable) {
      return false;
    }
    return parent::isRenderable();
  }
  
  public function render() {
    
    $output = $this->tag->getAttributeDeclarations();
    
    if($this->tag->isPhpEnabled()) {
      $output .= "<?php /*<" . $this->tag->getNode()->tagName . ">*/?>";
    }
    $output .= $this->tag->start();
    $output .= $this->renderChildren();
    
    $output = $this->tag->filter($output);

    $output .= $this->tag->end();
    $output .= $this->tag->getAttributeUnsets();
    if($this->tag->isPhpEnabled()) {
      $output .= "<?php /*</" . $this->tag->getNode()->tagName . ">*/?>";
    }
    return $output;
  }
  
  /**
   * Loads a tag. Includes the taglibrary and then returns the appropriate
   * tag.
   *
   * @param DomNode $node node to process with tag
   *
   * @return HTML_Template_Nest_Tag appropriate tag
   */
  private function loadTag($node)
  {
    $taglib = $this->getNamespace($node);
  
    $attributes = array();
    foreach ($node->attributes as $attr) {
      $attributes[$attr->name] = $attr->value;
    }
  
    $taglib = str_replace("urn:nsttl:", "", $taglib);
    $taglibFile = str_replace("_", "/", $taglib) . ".php";
  
    $className = $taglib;
    $isDefault = false;
    if(!class_exists($className, true)) {
      $isDefault = true;
      $className = "HTML_Template_Nest_Taglib";
    }
    $class = new $className();
    if($isDefault) {
      $class->setLibraryDirectory($taglib);
    }
    $tag = $class->getTagByName($this, $node, $attributes);
    return $tag;
  }
  
  
  
}