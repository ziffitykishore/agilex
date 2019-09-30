define([
        'jquery',
        'jquery/ui',
        'jquery/validate',
        'mage/translate'
    ], function ($) {
        $('field-recaptcha').has('#g-recaptcha-response').attr("data-validation","{'required':true}"); 
    return function(param) {
        //Validate Image FileSize        

        $.validator.addMethod(
                'validate-filesize', function (v, elm) {
                    var maxSize = param.allowedFileSize[0] * 102400;
                    if (navigator.appName == "Microsoft Internet Explorer") {
                        if (elm.value) {
                            var oas = new ActiveXObject("Scripting.FileSystemObject");
                            var e = oas.getFile(elm.value);
                            var size = e.size;
                        }
                    } else {
                        if (elm.files[0] != undefined) {
                            size = elm.files[0].size;
                        }
                    }
                    if (size != undefined && size > maxSize) {
                        return false;
                    }
                    return true;
                }, $.mage.__(param.allowedFileSize[1]));

        //Validate Image Extensions

        $.validator.addMethod(
                'validate-fileextensions', function (v, elm) {

                    var extensions = param.allowedFileExtension[0];
                    if (!v) {
                        return true;
                    }
                    var ext = elm.value.substring(elm.value.lastIndexOf('.') + 1);
                    for (var i = 0; i < extensions.length; i++) {
                        if (ext == extensions[i]) {
                            return true;
                        }
                    }
                    return false;
                }, $.mage.__(param.allowedFileExtension[1]));
    }
});