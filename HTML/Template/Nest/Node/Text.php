<?php 
class HTML_Template_Nest_Node_Text extends HTML_Template_Nest_Node_Node {
  
  public function init() {
    // no-op
  }
  public function isRenderable() {
    return true;
  }
  public function render() {
    return $this->parser->parse($this->node->nodeValue);
  }  
  
  public function __toString() {
    return $this->node->nodeValue;
  }
  
}