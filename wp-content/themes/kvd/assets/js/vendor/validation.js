jQuery(document).ready(function() {
    if (jQuery("#subscribeForm").length) {
        
        jQuery("#subscribeForm").validate({
            ignore: [],
            rules: {
                email: {
                    required: true,
                    email: true,
                }
            },
            errorClass: 'error',
            validClass: 'valid',
            errorElement: 'div',
            highlight: function(element, errorClass, validClass) {
                jQuery(element).addClass(errorClass).removeClass(validClass);
            },
            unhighlight: function(element, errorClass, validClass) {
                jQuery(element).removeClass(errorClass).addClass(validClass);
            },
            messages: {
                email: {
                    required: "",
                    email: ""
                }
            },
            errorPlacement: function(error, element) {
                //error.fadeIn().insertAfter(element);
            },
            submitHandler: function(form) { // for demo
                jQuery('.successmsg').fadeIn();
                setTimeout(function() {
                    jQuery('.successmsg').fadeOut();
                    jQuery('#subscribeForm')[0].reset();
                    jQuery(".valid").each(function() {
                        jQuery(this).removeClass("valid")
                    })
                }, 3000)
                return false;
            }
        });
    }
    // propose username by combining first- and lastname

    jQuery('#subscribeForm input[type="text"]').on('blur', function() {
        jQuery("#subscribeForm").validate().element(this);
    });
    
});