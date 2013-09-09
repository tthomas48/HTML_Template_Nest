<?php
class HTML_Template_Nest_Node_XmlTag extends HTML_Template_Nest_Node_Node {
  
  public function render() {
    $node = $this->node;
    
    if($node instanceof DomDocument || $node instanceof DOMDocumentFragment || $node instanceof DOMDocumentType) {
      return $this->renderChildren();
    }
    
    if(!$this->hasChildren()) {
      return "<" . $node->nodeName . $this->addAttributes($node) . $this->processNamespace($node) . "/>";
    }
    
    $childOutput = $this->renderChildren();
    
    $output = "<" . $node->nodeName  . $this->addAttributes($node) . $this->processNamespace($node) .  ">";
    $output .= $childOutput;
    $output .= "</" . $node->nodeName . ">";
    
    return $output;
  }
  
}
