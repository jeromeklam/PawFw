<?php
namespace PhalconFw\Assets;

class Manager extends \Phalcon\Assets\Manager
{

    protected static $loaded = array();
    protected static $js     = array();
    protected static $ajax   = array();
    protected static $less   = array();
    
    public function __construct()
    {
        $this->collection('headerJS');
        $this->collection('headerCSS');
        $this->collection('footerJS');
        $this->collection('footerCSS');
    }

    /**
     * 
     * @param unknown $p_file
     */
    public function addLess ($p_file) {
        self::$less[] = $p_file;
        
        return $this;
    }

    public function addBootstrap ()
    {
        if (!isset(self::$loaded['jquery'])) {
            $this
                ->collection('footerJS')
                ->addJs('vendor/jquery/dist/jquery.min.js')
                ->addJs('vendor/jquery-ui/jquery-ui.min.js')
            ;
        }
        self::$loaded['jquery'] = true;
        if (!isset(self::$loaded['bootstrap'])) {
            $this
                ->collection('headerCSS')
                ->addCss('vendor/bootstrap/dist/css/bootstrap.min.css')
                ->addCss('vendor/bootstrap/dist/css/bootstrap-theme.min.css')
            ;
            $this
                ->collection('footerJS')
                ->addJs('vendor/bootstrap/dist/js/bootstrap.min.js')
                ->addJs('vendor/bootstrap-multiselect/dist/js/bootstrap-multiselect.js')
            ;
        }
        self::$loaded['bootstrap'] = true;
        
        return $this;
    }

    public function addMagnificPopup ()
    {
        if (!isset(self::$loaded['jquery'])) {
            $this
            ->collection('footerJS')
            ->addJs('vendor/jquery/dist/jquery.min.js')
            ->addJs('vendor/jquery-ui/jquery-ui.min.js')
            ;
        }
        self::$loaded['jquery'] = true;
        if (!isset(self::$loaded['magnific-popup'])) {
            $this
                ->collection('headerCSS')
                ->addCss('vendor/magnific-popup/dist/magnific-popup.css')
            ;
            $this
                ->collection('footerJS')
                ->addJs('vendor/magnific-popup/dist/jquery.magnific-popup.min.js')
            ;
        }
        self::$loaded['magnific-popup'] = true;
        
        return $this;
    }

    public function addFontAwesome ()
    {
        if (!isset(self::$loaded['font-awesome'])) {
            $this
                ->collection('headerCSS')
                ->addCss('vendor/components-font-awesome/css/font-awesome.min.css')
            ;
        }
        self::$loaded['font-awesome'] = true;
        
        return $this;
    }

    public function addDropZone ($p_selector = ".dropzone", $p_url = "/file/post", $p_remove = true)
    {
        if (!isset(self::$loaded['dropzone'])) {
            $this
                ->collection('headerJS')
                ->addCss('vendor/dropzone/dist/min/dropzone.min.css')
            ;
            $this
                ->collection('footerJS')
                ->addCss('vendor/dropzone/dist/min/dropzone.min.js')
            ;
        }
        self::$loaded['dropzone'] = true;
        
        $js = '
    Dropzone.autoDiscover = false;
    $("' . $p_selector . '").dropzone({
        url: "' . $p_url . '",
        addRemoveLinks: ' . ($p_remove === true ? 'true' : 'false') . ',
        success: function (file, response) {
            var imgName = response;
            file.previewElement.classList.add("dz-success");
            console.log("Successfully uploaded :" + imgName);
            $(\'#uploader\').modal(\'hide\');
            this.removeAllFiles(); 
            window.location.reload();
        },
        error: function (file, response) {
            file.previewElement.classList.add("dz-error");
        }
    });
    ';
        
        self::$js[] = $js;
        
        return $this;
    }
    
    public function addTextEditor ($p_selector = 'textarea', $p_ajax = false)
    {
        if (!isset(self::$loaded['tinymce'])) {
            $this
                ->collection('footerJS')
                ->addJs('vendor/tinymce/tinymce.min.js')
                ->addJs('js/tinymce/fusion/plugin.js')
            ;
        }
        self::$loaded['tinymce'] = true;
        $js         = 'tinymce.remove();
                       tinymce.init({
                        forced_root_block: "",
                        language_url: "/js/tinymce/langs/fr_FR.js",
                        language: "fr_FR",
                        selector:\'' . $p_selector . '\',
                        theme: "modern",
                        plugins: [
                            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                            "searchreplace wordcount visualblocks visualchars code fullscreen",
                            "insertdatetime media nonbreaking save table contextmenu directionality",
                            "emoticons template paste textcolor colorpicker textpattern imagetools fusion"
                        ],
                        toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                        toolbar2: "print preview media | forecolor backcolor emoticons",
                        image_advtab: true
                    });'. PHP_EOL;
        self::$js[] = $js;
        if ($p_ajax) {
            self::$ajax[] = $js;
        }
        return $this;
    }

    public function addMoment ()
    {
        $this->addBootstrap();
        if (!isset(self::$loaded['moment'])) {
            $this
                ->collection('footerJS')
                ->addJs('vendor/moment/min/moment-with-locales.min.js')
            ;
        }
        self::$loaded['moment'] = true;
    
        return $this;
    }

    public function addBootstrapDateTimepicker ($p_selector = 'date', $p_ajax = false)
    {
        $this->addBootstrap();
        $this->addMoment();
        if (!isset(self::$loaded['bootstrap-datetimepicker'])) {
            $this
                ->collection('headerCSS')
                ->addCss('vendor/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css')
            ;
            $this
                ->collection('footerJS')
                ->addJs('vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')
            ;
        }
        self::$loaded['bootstrap-datetimepicker'] = true;
        $js         = '$(\'' . $p_selector . '\').datetimepicker({format: \'DD/MM/YYYY LT\', extraFormats: [ \'YYYY-MM-DD LT\' ], locale: \'fr\'});';
        self::$js[] = $js;
        if ($p_ajax) {
            self::$ajax[] = $js;
        }
        
        return $this;
    }

    public function addJsTree ($p_selector, $p_ajax = false)
    {
        $this->addBootstrap();
        if (!isset(self::$loaded['jstree'])) {
            $this
                ->collection('headerCSS')
                ->addCss('vendor/jstree-bootstrap-theme/dist/themes/proton/style.min.css')
            ;
            $this
                ->collection('footerJS')
                ->addJs('vendor/jstree-bootstrap-theme/dist/jstree.min.js')
            ;
        }
        self::$loaded['jstree'] = true;
        $js = '$(\'' . $p_selector . '\').jstree({
                \'core\': {
                \'themes\': {
                    \'name\': \'proton\',
                    \'responsive\': true
                }
            }
          });
        ';
        self::$js[] = $js;
        if ($p_ajax) {
            self::$ajax[] = $js;
        }
        
        return $this;
    }

    public function addSpinner ($p_selector, $p_ajax = false)
    {
        if (!isset(self::$loaded['spinner'])) {
            $js = '        $(\'.spinner .btn:first-of-type\').on(\'click\', function() {
            var field = $(this).data(\'spinner\');
            var value = parseInt($("#" + field).val());
            if (isNaN(value)) {
                value = 0;
            } else {
                value = value + 1;
            }
            $("#" + field).val(value);
        });
        $(\'.spinner .btn:last-of-type\').on(\'click\', function() {
            var field = $(this).data(\'spinner\');
            var value = parseInt($("#" + field).val());
            if (isNaN(value)) {
                value = 0;
            } else {
                value = value - 1;
            }
            $("#" + field).val(value);
        });
        ';
            self::$js[] = $js;
            if ($p_ajax) {
                self::$ajax[] = $js;
            }
        }
        self::$loaded['spinner'] = true;

        return $this;
    }

    public function addReadyJS ($p_js)
    {
        self::$js[] = $p_js;
        
        return $this;
    }
    
    public function outputInlineJs ($collectionName = NULL)
    {
        $js = '';
        $js .= '$( document ).ready(function() {' . PHP_EOL . PHP_EOL;
        $js .= implode(PHP_EOL, self::$js) . PHP_EOL . PHP_EOL;
        $js .= '});' . PHP_EOL . PHP_EOL;
        $js .= 'function afterAjaxLoad () {' . PHP_EOL;
        $js .= implode(PHP_EOL, self::$ajax) . PHP_EOL . PHP_EOL;
        $js .= '};' . PHP_EOL;
        
        $this
            ->collection('footerInlJS')
            ->addInlineJs($js)
        ;
        
        return parent::outputInlineJs ($collectionName);
    }

}