<?php 
abstract class HTML_Template_Nest_Node_Node implements HTML_Template_Nest_Node_IRender {

  protected $node;
  protected $children;
  protected $parser;
  protected $parent;

  public function __construct(HTML_Template_Nest_Parser &$parser, DOMNode $node, HTML_Template_Nest_Node_IRender &$parent = NULL) {
    $this->node = $node;
    $this->parser = $parser;
    $this->parent = $parent;
    $this->children = $this->convertChildren($node);
  }

  public function isRenderable() {
    foreach($this->children as $child) {
      
      if(!$child->isRenderable()) {
        return false;
      }
    }
    return true;
  }
  
  public function init() {
    $this->initChildren();
  }

  public function hasChildren() {
    return count($this->children) > 0;
  }

  protected function renderChildren() {
    $output = "";
    foreach($this->children as $child) {
      $output .= $child->render();
    }
    return $output;
  }
  
  protected function initChildren() {
    foreach($this->children as $child) {
      $child->init();
    }
  }
  

  protected function convertNode(HTML_Template_Nest_Parser $parser, DOMNode $node) {
    if ($node->nodeType == XML_TEXT_NODE) {
      return new HTML_Template_Nest_Node_Text($parser, $node, $this);
    }

    $taglib = $this->getNamespace($node);

    $tag = null;
    $rootTag = false;
    if (strpos($taglib, "urn:nsttl:") !== false) {
      return new HTML_Template_Nest_Node_LibraryTag($parser, $node, $this);
    }
    elseif ($taglib == "http://nest.sourceforge.net/" && trim($node->localName) == 'root') {
      return new HTML_Template_Nest_Node_RootTag($parser, $node, $this);
    }
    return new HTML_Template_Nest_Node_XmlTag($parser, $node, $this);
  }

  private function convertChildren(DOMNode $node) {
    if(!$node->hasChildNodes()) {
      return array();
    }

    $nodeChildren = $node->childNodes;

    $childrenList = array();
    foreach ($nodeChildren as $child) {
      $childrenList[] = $child;
    }
    $children = array();
    foreach ($childrenList as $child) {
      $children[] = $this->convertNode($this->parser, $child);
    }
    return $children;
  }

  protected function getNamespace($node) {
    $taglib = $node->lookupNamespaceURI($node->prefix);
    if($node instanceof DomElement && $taglib == null && strpos($node->nodeName, ":") !== false) {
      // must be some sort of DOM bug. Look and see if we can figure it out from simplexml
      $sxe = simplexml_import_dom($node);
      $namespaces = $sxe->getNamespaces();
      foreach($namespaces as $prefix => $namespace) {
        if(strpos($node->nodeName, $prefix . ":") == 0) {
          $taglib = $namespace;
          break;
        }
      }
    }
    if($taglib == NULL && $node->namespaceURI != NULL) {
      return $node->namespaceURI;
    }
    return $taglib;
  }

  /**
   * Processes any namespaces that are not nest tag libraries.
   *
   * @param DomNode $node node to process
   *
   * @return string string of xml namespace declarations
   */
  protected function processNamespace($node)
  {
    $output = "";
    if(strlen($node->prefix) == 0) {
      if($node->namespaceURI != NULL) {
        $output .= " xmlns=\"" . $node->namespaceURI . "\" ";
      }
      return $output;
    }
    $uri = $this->getNamespace($node);
    $output .= " xmlns:" . $node->prefix . "=\"" . str_replace('"', '&quot;', $uri) . "\"";
    return $output;
  }

  protected function addAttributes($node)
  {
    if(!$node->hasAttributes()) {
      return "";
    }
    $uris = array();
    $output = "";
    foreach ($node->attributes as $attribute) {
      $uri = $this->getNamespace($attribute);
      if(!empty($uri) && !in_array($uri, $uris)) {
        $uris[] = $uri;
        $output .= " xmlns:" . $attribute->prefix . "=\"$uri\" ";
      }
      if(strpos($attribute->name, 'prune.') !== 0 && !$this->parser->isParseable($attribute->value)) {
        $q = '"';
        if(strpos($attribute->value, '"') !== false) {
          $q = "'";
        }
        $output .= " " . $attribute->name . "=" . $q . $attribute->value . $q;
        continue;
      }
      $value = $this->parser->parse($attribute->value);
      $output .= '<?php ob_start()?>';
      $output .= str_replace('"', '&quot;', $value);
      $output .= '<?php $output = ob_get_clean();';

      $name = $attribute->name;
      $prefix = (!empty($attribute->prefix) ? $attribute->prefix . ":" : "");
      if(strpos($attribute->name, 'prune.') === 0) {
        $name = str_replace("prune.", "", $name);
        $output .= 'if(!empty($output)) { ';
      }

      $output .= 'echo \' ' . $prefix . $name . '="\' . $output . \'"\';';
      if(strpos($attribute->name, 'prune.') === 0) {
        $output .= "}";
      }
      $output .= '?>';
    }
    return $output;
  }
  
  public function __toString() {
    $output = $this->node->localName . ":" . spl_object_hash($this) . "\n";
    foreach($this->children as $child) {
      $output .= "\t" . $child . "\n";
    }
    return $output;
  }
  
  public function getParser() {
    return $this->parser;
  }
  
  public function getChildren() {
    return $this->children;
  }
  
  public function removeChild(HTML_Template_Nest_Node_IRender $child) {
    $index = -1;
    for($i = 0; $i < count($this->children); $i++) {
      if($this->children[$i] === $child) {
        $index = $i;
      }
    }
    
    if($index >= 0) {
      array_splice($this->children, $index, 1);
    }
  }
  
  public function appendXML(DomNode $xml) {
    $this->children[] = $this->convertNode($this->parser, $xml);
  }
  
  public function appendChild(HTML_Template_Nest_Node_IRender $child) {
    $this->children[] = $child;
  }
  
  public function getParent() {
    
    return $this->parent;
  }
  
  public function hasAttribute($name) {
    if($this->node == NULL || !method_exists($this->node, "hasAttribute")) {
      return false;
    }
    return $this->node->hasAttribute($name);
  }
  
  public function getAttribute($name) {
    if($this->node == NULL || !method_exists($this->node, "getAttribute")) {
      return false;
    }
    return $this->node->getAttribute($name);
  }
  
  public function setAttribute($name, $value) {
    if($this->node == NULL || !method_exists($this->node, "setAttribute")) {
      return false;
    }
    return $this->node->setAttribute($name, $value);
  }
  
  public function insertBefore(HTML_Template_Nest_Node_IRender $existingChild, HTML_Template_Nest_Node_IRender $newChild) {
    $index = -1;
    for($i = 0; $i < count($this->children); $i++) {
      if($this->children[$i] === $existingChild) {
        $index = $i;
      }
    }
    
    if($index >= 0) {
      $newChildren = array();
      if($index > 0) {
        $newChildren = array_slice($this->children, 0, $index, true);
      }
      $newChildren[] = $newChild;
      $this->children = array_merge($newChildren, array_slice($this->children, $index, count($this->children), true));
    }
  }
  public function getNamespaceUri() {
    if($this->node == NULL) {
      return;
    }
    return $this->node->lookupNamespaceURI($this->node->prefix);
  }
  public function getLocalName() {
    if($this->node == NULL) {
      return NULL;
    }
    return $this->node->localName;
  }  
  
  public function getName() {
    if($this->node == NULL) {
      return ;
    }
    return $this->node->nodeName;
  }

  public function getTextContent() {
    if($this->node == NULL) {
      return ;
    }
    return $this->node->textContent;
  }

  public function getParentRendererByTag($tag) {
    $renderer = $this;
    $parent = $renderer->getParent();
    while($parent != NULL) {
      if(is_a($parent, "HTML_Template_Nest_Node_LibraryTag") && $parent->getTagType() == $tag) {
        return $parent;
      }
      $parent = $parent->getParent();
    }
    return NULL;
  }
}
