<?php 
class HTML_Template_Nest_Node_Document extends HTML_Template_Nest_Node_Node {

  private $document;

  public function __construct(HTML_Template_Nest_Parser $parser, DOMDocument $domDocument) {
    $this->document = $this->convertNode($parser, $domDocument);
  }

  public function init() {
    $this->document->init();
  }
  public function isRenderable() {
    return $this->document->isRenderable();
  }
  public function render() {
    return $this->document->render();
  }
}