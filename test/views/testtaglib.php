<?php
class nestView__home_tthomas_git_HTML_Template_Nest_test_viewstesttaglib
{
    public $lastModified = 1345497157;

    public function render(Array $p)
    {

        $_o = function($params, $key) { return array_key_exists($key, $params) ? $params[$key] : null; };
        ob_start();
        register_shutdown_function(array($this, 'fatal_template_error'));
        
        $initialReporting = error_reporting();
        try {
            error_reporting(E_ERROR | E_USER_ERROR | E_PARSE);
            set_error_handler(function($errno, $errstr, $errfile, $errline) {
              if(($errno === E_ERROR) || ($errno === E_USER_ERROR) || ($errno === E_PARSE)) { 
                throw new HTML_Template_Nest_EvalException($errno, $errstr, $errfile, $errline); 
              }
            });
            ?><html>
<h:head xmlns:h="http://www.w3.org/TR/html4/">
    <script type="text/javascript">
        var basepath = '<?php /* {director->getProducerPath()} */ echo htmlentities($_o($p, 'director')->getProducerPath())?>';
        var imagepath = '<?php /* {director->getImagePath()} */ echo htmlentities($_o($p, 'director')->getImagePath())?>';
    </script>
</h:head>
<h:body xmlns:h="http://www.w3.org/TR/html4/">
<?php $nst_test_testTagFileTestPackage_TestTaglib_TestTagFile557246440ac25_name = "foo";
$nst_test_testTagFileTestPackage_TestTaglib_TestTagFile557246440ac25_key = "return '" . $_o($p, 'foo') . "';";
$nst_test_testTagFileTestPackage_TestTaglib_TestTagFile557246440ac25_bar = "" . $_o($p, 'foo') . "";
$nst_test_testTagFileTestPackage_TestTaglib_TestTagFile557246440ac25_boo = "\"foo\"" . $_o($p, 'foo') . "";
?><?php /*<test:testTagFile>*/?><b>
    <?php /* {testAttribute} */ echo htmlentities($_o($p, 'testAttribute'))?>
    <?php /*<c:if>*/?><?php if($_o($p, 'foo') == 'a') {?>
        Show me!
    <?php } ?><?php /*</c:if>*/?>
</b><?php unset($nst_test_testTagFileTestPackage_TestTaglib_TestTagFile557246440ac25_name);
unset($nst_test_testTagFileTestPackage_TestTaglib_TestTagFile557246440ac25_key);
unset($nst_test_testTagFileTestPackage_TestTaglib_TestTagFile557246440ac25_bar);
unset($nst_test_testTagFileTestPackage_TestTaglib_TestTagFile557246440ac25_boo);
?><?php /*</test:testTagFile>*/?>

<?php /*<test:wrappableTagFile>*/?><div id="wrapper">
    
    <b>This is wrapped</b>

</div>
<?php /*</test:wrappableTagFile>*/?>

<?php /*<test:wrappableTagFile>*/?><div id="wrapper">
    
</div>
<?php /*</test:wrappableTagFile>*/?>

<?php /*<test:wrappableTagFile>*/?><div id="wrapper">
    
    <?php /*<test:wrappableTagFile>*/?><div id="wrapper">
    
        This also is wrapped
    
</div>
<?php /*</test:wrappableTagFile>*/?>

</div>
<?php /*</test:wrappableTagFile>*/?>

<?php /*<test:nested>*/?><b>
    <span id="nestedSpan">
        <?php /*<test:wrappableTagFile>*/?><div id="wrapper">
    
            
    <div id="middleStaticDiv" v='["required"]'>
        <strong>This is some nested text.</strong>
    </div>

        
</div>
<?php /*</test:wrappableTagFile>*/?>
    </span>
</b>
<?php /*</test:nested>*/?>

<?php /*<test:body>*/?><body>
    <div class="rounded">

        <?php /*<test:roundedContainer>*/?><b class="rtop">
    <b class="r1">
    </b>
    <b class="r2">
    </b>
    <b class="r3">
    </b>
    <b class="r4">
    </b>
</b>

            <div id="mainBody">
                <div id="rightpane">
                    
This is the body text.

                        </div>
            </div>
        
<b class="rbottom">
    <b class="r4">
    </b>
    <b class="r3">
    </b>
    <b class="r2">
    </b>
    <b class="r1">
    </b>
</b>
<?php /*</test:roundedContainer>*/?>
    </div>
</body>
<?php /*</test:body>*/?>

<?php $nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440afb2_attribute1 = "true";
$nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440afb2_attribute2 = "false";
$nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440afb2_attribute3 = "99";
?><?php /*<test:wrappableWithAttributes>*/?><div id="wrapper">
    
    <?php /*<c:if>*/?><?php if($nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440afb2_attribute1 == 'true') {?>
        Show me.
    <?php } ?><?php /*</c:if>*/?>
    <?php /*<c:if>*/?><?php if($nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440afb2_attribute2 == 'true') {?>
        Don't show me
    <?php } ?><?php /*</c:if>*/?>
    <?php /*<c:if>*/?><?php if($nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440afb2_attribute3 > 50) {?>
        Show <?php /* {attribute3} */ echo htmlentities($nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440afb2_attribute3)?>
    <?php } ?><?php /*</c:if>*/?>

</div>
<?php unset($nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440afb2_attribute1);
unset($nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440afb2_attribute2);
unset($nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440afb2_attribute3);
?><?php /*</test:wrappableWithAttributes>*/?>

<?php /*<test:nestedFooter>*/?><div>
    <?php /*<test:roundedContainer>*/?><b class="rtop">
    <b class="r1">
    </b>
    <b class="r2">
    </b>
    <b class="r3">
    </b>
    <b class="r4">
    </b>
</b>

        <span class="footer">(c)2008</span>
    
<b class="rbottom">
    <b class="r4">
    </b>
    <b class="r3">
    </b>
    <b class="r2">
    </b>
    <b class="r1">
    </b>
</b>
<?php /*</test:roundedContainer>*/?>
    <div id="rightPane">
        
    Some copywritten text.

    </div>
</div>
<?php /*</test:nestedFooter>*/?>

<?php /*<test:nestedFooter>*/?><div>
    <?php /*<test:roundedContainer>*/?><b class="rtop">
    <b class="r1">
    </b>
    <b class="r2">
    </b>
    <b class="r3">
    </b>
    <b class="r4">
    </b>
</b>

        <span class="footer">(c)2010</span>
    
<b class="rbottom">
    <b class="r4">
    </b>
    <b class="r3">
    </b>
    <b class="r2">
    </b>
    <b class="r1">
    </b>
</b>
<?php /*</test:roundedContainer>*/?>
    <div id="rightPane">
        
    Some copywritten text.
    

    </div>
</div>
<?php /*</test:nestedFooter>*/?>

<?php /*<c:if>*/?><?php if(1 == 1) {?>
    <?php /*<test:body>*/?><body>
    <div class="rounded">

        <?php /*<test:roundedContainer>*/?><b class="rtop">
    <b class="r1">
    </b>
    <b class="r2">
    </b>
    <b class="r3">
    </b>
    <b class="r4">
    </b>
</b>

            <div id="mainBody">
                <div id="rightpane">
                    
        First body
    
                        </div>
            </div>
        
<b class="rbottom">
    <b class="r4">
    </b>
    <b class="r3">
    </b>
    <b class="r2">
    </b>
    <b class="r1">
    </b>
</b>
<?php /*</test:roundedContainer>*/?>
    </div>
</body>
<?php /*</test:body>*/?>
    <?php /*<test:body>*/?><body>
    <div class="rounded">

        <?php /*<test:roundedContainer>*/?><b class="rtop">
    <b class="r1">
    </b>
    <b class="r2">
    </b>
    <b class="r3">
    </b>
    <b class="r4">
    </b>
</b>

            <div id="mainBody">
                <div id="rightpane">
                    
        Second body
    
                        </div>
            </div>
        
<b class="rbottom">
    <b class="r4">
    </b>
    <b class="r3">
    </b>
    <b class="r2">
    </b>
    <b class="r1">
    </b>
</b>
<?php /*</test:roundedContainer>*/?>
    </div>
</body>
<?php /*</test:body>*/?>
<?php } ?><?php /*</c:if>*/?>

<?php $nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440b1b6_attribute1 = "" . $_o($p, 'person')->isNew() ? 'Create' : 'Save' . " Person";
$nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440b1b6_attribute2 = "";
$nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440b1b6_attribute3 = "";
?><?php /*<test:wrappableWithAttributes>*/?><div id="wrapper">
    
foo

</div>
<?php unset($nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440b1b6_attribute1);
unset($nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440b1b6_attribute2);
unset($nst_test_wrappableWithAttributesTestPackage_TestTaglib_WrappableWithAttributesTagFile557246440b1b6_attribute3);
?><?php /*</test:wrappableWithAttributes>*/?>
<?php /*<ntt:test>*/?>generated without taglib class
<?php /*</ntt:test>*/?>
<?php $nst_ntt_hasattrHTML_Template_Nest_TagFile557246440bf6e_attr1 = "bar";
$nst_ntt_hasattrHTML_Template_Nest_TagFile557246440bf6e_attr2 = "foo";
$nst_ntt_hasattrHTML_Template_Nest_TagFile557246440bf6e_attr3 = NULL;
?><?php /*<ntt:hasattr>*/?>
    
    
    

<?php unset($nst_ntt_hasattrHTML_Template_Nest_TagFile557246440bf6e_attr1);
unset($nst_ntt_hasattrHTML_Template_Nest_TagFile557246440bf6e_attr2);
unset($nst_ntt_hasattrHTML_Template_Nest_TagFile557246440bf6e_attr3);
?><?php /*</ntt:hasattr>*/?>
</h:body>
</html><?php
        } catch(HTML_Template_Nest_EvalException $e) {
            ob_clean();
            error_reporting($initialReporting);
            throw new HTML_Template_Nest_ViewException($e, $this->output);
        }
        error_reporting($initialReporting);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;

    }
}
