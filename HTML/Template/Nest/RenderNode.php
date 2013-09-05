<?php
class HTML_Template_Nest_RenderNode {
  public $parent;
  public $children;
  public function __construct($parent, $children = array()) {
    $this->parent = $parent;
    $this->children = $children;
  }
}
