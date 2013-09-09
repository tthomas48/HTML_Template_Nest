<?php
class HTML_Template_Nest_Node_RootTag extends HTML_Template_Nest_Node_Node {

  public function render() {
    return $this->renderChildren();
  }
}