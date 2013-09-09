<?php
interface HTML_Template_Nest_Node_IRender {
  public function init();
  public function isRenderable();
  public function render();
  public function getChildren();
  public function hasChildren();
  public function removeChild(HTML_Template_Nest_Node_IRender $child);
  public function appendXML(DomNode $xml);
  public function getParser();
  public function getParent();
  public function hasAttribute($name);
  public function getAttribute($name);
  public function insertBefore(HTML_Template_Nest_Node_IRender $existingChild, HTML_Template_Nest_Node_IRender $newChild);
  public function getNamespaceUri();
  public function getLocalName();
  public function getName();
  public function setAttribute($name, $value);
}