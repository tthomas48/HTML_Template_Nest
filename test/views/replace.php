<?php
class nestView__home_tthomas_git_HTML_Template_Nest_test_viewsreplace
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
            ?><div>
    <?php $nst_test_nestedbodyHTML_Template_Nest_TagFile557246431673e_nohelppane = "";
?><?php /*<test:nestedbody>*/?><body>
    
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
                <?php /*<c:if>*/?><?php if($nst_test_nestedbodyHTML_Template_Nest_TagFile557246431673e_nohelppane != 'false') {?>
                    <?php /*<test:helppane>*/?><div id="helpPane">
    
            <?php /*<c:if>*/?><?php if(1!=1) {?>
                This should not print.
            <?php } ?><?php /*</c:if>*/?>
            <?php /*<c:if>*/?><?php if(1==1) {?>
                This should print.
            <?php } ?><?php /*</c:if>*/?>
            foo
        
</div>
<?php /*</test:helppane>*/?>
                <?php } ?><?php /*</c:if>*/?>
                
                <div id="rightpane">
                    
        
        Foo body
    
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
<?php unset($nst_test_nestedbodyHTML_Template_Nest_TagFile557246431673e_nohelppane);
?><?php /*</test:nestedbody>*/?>
</div><?php
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
