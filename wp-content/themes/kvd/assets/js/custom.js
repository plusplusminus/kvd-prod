var $ = jQuery.noConflict();
var $window = $(window);
var sign_up_label_width, sign_up_input_width;
var fixheaderHeight, windowWidth;
(function($) {
    $(document).ready(function() {
        // Set max length for personalise
        $('input.addon-custom').attr('maxlength', 3);
        

        $('.reset_variations').click(function(){
           $('.variations select').val('').change();
           return false;
       });



        $( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
            var $product_img = jQuery( '.js-main-image' ).find('img');
            var variation_image = variation.image_src,
            variation_link  = variation.image_link,
            variation_caption = variation.image_caption,
            variation_title = variation.image_title;

            if ( variation_image && variation_image.length > 1 ) {
                $product_img
                    .attr( 'src', variation_image )
                    .attr( 'srcset', variation.image_srcset )
                    .attr( 'sizes', variation.image_sizes )
                    .attr( 'alt', variation_title )
                    .attr( 'title', variation_title );
            } 

        } );
        $( ".variations_form" ).on( "woocommerce_variation_select_change", function () {
            // Fires whenever variation selects are changed
            console.log("EQwewq");
        } );
        jQuery('.owl-carousel').owlCarousel({
            nav: false,
            items:1,
            loop:true,
            dots: false,
            autoplay:true,
            onInitialize: callback,
            animateOut: 'fadeOut'
        });

        function callback(event) {
            jQuery('.slider-item').each(function(e){
                jQuery(this).css('height',window.innerHeight-jQuery("#header").height());
            })
        }

        jQuery('.slick').slick({
          centerMode: true,
          centerPadding: '140px',
          slidesToShow: 1,
          dots: true,
          arrows: false,
          responsive: [
            {
              breakpoint: 1024,
              settings: {
                arrows: false,
                centerMode: true,
                centerPadding: '40px',
                slidesToShow: 1
              }
            },
            {
              breakpoint: 992,
              settings: {
                arrows: false,
                centerMode: true,
                centerPadding: '40px',
                slidesToShow: 1
              }
            },
            {
              breakpoint: 768,
              settings: {
                arrows: false,
                centerMode: true,
                centerPadding: '40px',
                slidesToShow: 1
              }
            },
            {
              breakpoint: 480,
              settings: {
                arrows: false,
                centerMode: true,
                centerPadding: '0px',
                slidesToShow: 1
              }
            }
          ]
        });

        jQuery(".content-description__text").fitVids();


        jQuery('.product-addon-personalise-with-initials').append('<a href="'+kvd_urls.personalise+'" class="js-personal">Personalisation Guide</a>');
        jQuery('.js-pa_size .product-info-label').append(' (<a href="'+kvd_urls.sizing+'" class="js-personal link--size">Size Chart</a>)');

        jQuery(document).on('click','.js-personal',function(e) {
            e.preventDefault();

            var overlay = jQuery('#product-modal');

            jQuery('#product-modal').modal('show');

            var area = jQuery(overlay).find('.js-content').empty();

            var toLoad = jQuery(this).attr('href') + ' .page-main > *';

            jQuery(area).load(toLoad);

        });


        jQuery(document).on('click','.js-overlay-link,.js-overlay-image',function(e) {
            e.preventDefault();

            var overlay = jQuery('#content-modal');

            jQuery('#content-modal').modal('show');

            var area = jQuery(overlay).find('.js-content').empty();

            var toLoad = jQuery(this).attr('href') + ' .page-main > *';

            jQuery(area).load(toLoad,function(){

                $('[data-toggle="popover"]').popover({
                    html: true, 
                    content: function() {
                        return $('#social-content').html();
                    }
                })

            });

        });

        var $container = jQuery('.container_grid');
            $container.imagesLoaded( function() {
                $container.masonry({
                    itemSelector: '.grid_article',
                });
        });

        
        jQuery('.bxslider').bxSlider({
            "auto":true,
            "mode":"fade",
            "speed":2000,
            "easing":"linear",
            "pager":false

        }); 

        $('[data-toggle="popover"]').popover({
            html: true, 
            content: function() {
                return $('#social-content').html();
            }
        })

        $('body').on('click', function (e) {
            //did not click a popover toggle, or icon in popover toggle, or popover
            if ($(e.target).data('toggle') !== 'popover'
                && $(e.target).parents('[data-toggle="popover"]').length === 0
                && $(e.target).parents('.popover.in').length === 0) { 
                $('[data-toggle="popover"]').popover('hide');
            }
        });


        $('ul').each(function() {
            $(this).find('li').first().addClass('first');
            $(this).find('li').last().addClass('last');
        });


         
        /* Fixed Condensed Header Call */
        fixedHeader();
        /* Fixed Condensed Header Call Ends */
        windowWidth = $(window).width();
        $(window).resize(function() {
            //asignDynwidth();
            fixedHeader();
            setTimeout(function(){
                if(windowWidth != $(window).width()){
                    headerHeight1 = $("#header").height();
                }
            },1000);
        });
        $(window).scroll(fixedHeader);
        
       
        $(".dropdown_product_cat,.mobile-filter-panel select,.orderby.selectbox").selectbox({
            hide_duplicate_option: true
        });
       

        $( ".variations_form" ).on( "change", function () {
            // Fires whenever variation selects are changed
            

        } );


        msieversion();
 
        $(".open-currency").click(function(){
            if($(".menu-icon").is(":visible") && $("body").hasClass("menu-open"))
            {
                $(".menu-icon").trigger('click');
            }
            if($(".currency-div-hidden").hasClass("show-on-rollover-open"))
            {
                $(".currency-div-hidden").removeClass("show-on-rollover-open");
            }else{
                    $(".currency-div-hidden").addClass("show-on-rollover-open");
                }
        });
         
        $(document).on('click',".js-cart,.close-cart,.wc-forward",function(e){
            e.preventDefault();

            if ($(".shopping-cart-list").hasClass("shopping-cart-list-open"))
            {
                $(".shopping-cart-list").removeClass("shopping-cart-list-open");
            } else{
                $(".shopping-cart-list").addClass("shopping-cart-list-open");
            }

        });




        $("body").on('opencart',function(e){
            
            if ($(".shopping-cart-list").hasClass("shopping-cart-list-open"))
            {
                $(".shopping-cart-list").removeClass("shopping-cart-list-open");
            } else{
                $(".shopping-cart-list").addClass("shopping-cart-list-open");
            }

        });

        $(".js-toggle-currency").on('click', function () { // submit post data on link click
            if ($(this).hasClass("selected")) { // if currency is already chosen
                return false; // do nothing
            } else {
                $("#wcumcs-currency-input").val($(this).data('currency')); // assign data attribute from clicked link to invisible input field
                $("#wcumcs-currency-form").submit(); // and submit it
            }
        })

        $('.js-country-select').on('click',function() {
            if ($(this).hasClass("selected")) { // if currency is already chosen
                return false; // do nothing
            } else {
                $(this).parent().find('li').removeClass('selected');
                $(this).addClass("selected");
                $("#wcumcs-currency-input").val($(this).data('currency')); // assign data attribute from clicked link to invisible input field
            }
        });

        $('.js-country-save').on('click',function() {
             $.cookie('country-check-cookie', true, {
                path: '/',
                expires: 365
            });
            $("#wcumcs-currency-form").submit(); // and submit it
        });

        $('.js-country-reveal').on('click',function() {
            $('.country__select').slideToggle();
        });

        var my_cookie = $.cookie('country-check-cookie');

        if (my_cookie && my_cookie == "true") {
            console.log('no country check');
        }
        else{
            $('#country-modal').modal('show');   
        }

        $('#country-modal').on('hidden.bs.modal', function () {
            $.cookie('country-check', true, {
                path: '/',
                expires: 365
            });
        })
         
        $(".open-search").click(function(e){
            e.preventDefault();
            if($(".menu-icon").is(":visible") && $("body").hasClass("menu-open"))
            {
                $(".menu-icon").trigger('click');
            }
             
            if($(".shopping-cart-search").hasClass("shopping-cart-search-open"))
            {
                $(".shopping-cart-search").removeClass("shopping-cart-search-open");
            }else{
                    $(".shopping-cart-search").addClass("shopping-cart-search-open");
                }
        });
         
         
        $("#close-search").click(function(){
            $(".shopping-cart-search").removeClass("shopping-cart-search-open");
        });
         
        $("#close-currency").click(function(){
            $(".currency-div-hidden").removeClass("show-on-rollover-open");
        });
         
      
         
        $(".side-open #page").on('click',function(e){
            if($(e.target).closest(".currency-div-hidden").length == 0 && $(e.target).closest(".open-currency").length == 0 && $(e.target).closest(".country-selector_show-more").length == 0 && $(".currency-div-hidden").hasClass("show-on-rollover-open")){
                e.preventDefault();
                $(".currency-div-hidden").removeClass("show-on-rollover-open");
            }
 
            if($(e.target).closest(".shopping-cart-list").length == 0 && $(e.target).closest("#show-cart").length == 0 && $(".shopping-cart-list").hasClass("shopping-cart-list-open")){
                e.preventDefault();
                $(".shopping-cart-list").removeClass("shopping-cart-list-open");
            }
             
            if($(e.target).closest(".shopping-cart-search").length == 0 && $(e.target).closest(".open-search").length == 0 && $(".shopping-cart-search").hasClass("shopping-cart-search-open")){
                e.preventDefault();
                $(".shopping-cart-search").removeClass("shopping-cart-search-open");
            }
        });
         
        $("#more-currency").click(function() {
            $(".country-selector-country_extra").css("display", "block");
            $(".country-selector_show-more").remove();
        });

        if($(".back-top").length){
            $(".back-top").click(function(e){
                e.preventDefault();
                $("html, body").animate({scrollTop:0},1500);
            });
        }
    }); /* Document Ready Ends Here */
 
$(function(){
            function isTouchDevice(){
                return typeof window.ontouchstart !== 'undefined';
            }
            if(isTouchDevice() == false){
                $(".product-name a, .product-img a").mouseenter(function () {
                    $(".kvd-product").removeClass("hover");
                    $(this).closest(".kvd-product").addClass("hover");
                })
                // handle the mouseleave functionality
                .mouseleave(function () {
                    $(".kvd-product").removeClass("hover");
                    $(this).closest(".kvd-product").removeClass("hover");
                });
                 
                $(".js-cat-link a").mouseenter(function () {
                    var type = $(this).data('type');
                    $(this).text("View All "+ type);
                })
                // handle the mouseleave functionality
                .mouseleave(function () {
                    var type = $(this).data('type');
                    $(this).text(type);
                });
                 
                 
            } else{
                $(".product-name a, .product-img a").on("click", function() {
                    $(".kvd-product").removeClass("hover");
                    $(this).closest(".kvd-product").addClass("hover");
                });
                 
                $(".product-dtl-link a").on("click", function() {
                    $(this).addClass("hover");
                });
            }

            // create a function to actually fire the search
            function dosearch(t) {
                $("#search-loader").text('loading').show();
                // do the ajax request for job search
                    $.ajax({
                        
                        type: 'post',
                        url: woocommerce_params.ajax_url, // the localized name of your file
                        data: {
                            action: 'kvd_ajax_search_products', // the wp_ajax_ hook name
                            search: t
                        },
                    
                        // what happens on success
                        success: function( result ) {
                            $("#search-loader").hide();

                            $( '.search-container' ).html( result );
                            
                        }
                    
                    });
                
            }
            
            var thread = null;
            
            // when the keyboard press is relased in the input with the class ajax-search
            $('.search-input').keyup(function() {
                  
                  // clear our timeout variable - to start the timer again
                  clearTimeout(thread);
                  
                  // set a variable to reference our current element ajax-search
                  var $this = $(this);
                  
                  // set a timeout to wait for a second before running the dosearch function
                  thread = setTimeout(
                      function() {
                        dosearch($this.val())
                      },
                      500
                  );
            });

            $('.personalise').on('click',function(e){
                e.preventDefault();

                $('.product-addon-personalise-with-initials').slideToggle();
            })
});
 
 
    $(window).load(function() {
        // setTimeout(asignDynwidth, 1000);
        var headerHeight1 = $("#header").height();
        //$(".site").css("padding-top",headerHeight1);
        $('.variations_form select,.product-custom select').select2({placeholder:"Choose an option",allowClear:true,minimumResultsForSearch: -1});
        console.log(kvd_urls.addtocart);
        
        if (kvd_urls.addtocart) {
            $("body").trigger('opencart');
        }
    });
         
        $('#like-accordion').click(function(e){ 
            e.preventDefault();
            if(!$(".fit-content").is(':visible')){
                $(".fit-content").slideDown();//open`
                $(this).addClass('active');
                $(this).find('span').text('Show less');
            }else{
                $(".fit-content").slideUp();//current close // only one
                $('#like-accordion').removeClass('active');
                $(this).find('span').text('Show more');
            }
        });
         
        $(".mobile-filter").click(function(e){
            e.preventDefault();
            $(this).toggleClass("active");
            $('.mobile-filter-panel').slideToggle();
        });
 
 
    function msieversion() {
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");
 
        if (msie > 0 || !! navigator.userAgent.match(/Trident.*rv\:11\./)) /* If Internet Explorer, return version number*/ {
            $('html').addClass('ie');
        } else { /*alert('not ie');*/ }
        return false;
    }
 
    function asignDynwidth() {
        /*if($('.right-header').length > 0){
            $('.right-header').css('width', '100%').css('width', '-=199px');
        }*/
        sign_up_label_width = jQuery(".footer-sign-up label").outerWidth(true);
        sign_up_input_width = sign_up_label_width + 42;
        if (jQuery('.subscribe-input').length > 0) {
            jQuery('.subscribe-input').css('width', '100%').css('width', '-=' + sign_up_input_width + 'px');
        }
 
    }
 
    function fixedHeader() {
        var headerHeight = $("#header").height();
        //$(".site").css("padding-top",headerHeight);
        //console.log($window.width());
        if($window.width() >= 992){
            if ($(window).scrollTop() > headerHeight) {
                if(!$("body").hasClass('fixed-on')){
                    $("#header").css("top", -headerHeight);
                    setTimeout(function(){
                        $('body').addClass('fixed-on');
                        $("#header").css("top","0");
                    }, 400);
                }
            } else {
                if($("body").hasClass('fixed-on')){
                    $("#header").css("top", -headerHeight);
                    setTimeout(function(){
                        $('body').removeClass('fixed-on');
                        $("#header").css("top","0");
                    }, 400);
                }
                 
            }
        }
    }
 
})(jQuery);

function initHeader() {
    
    height = window.innerHeight;

    largeHeader = document.getElementById('home_header');

    if (largeHeader) {

    largeHeader.style.height = height+'px';

    } else if (largeImage) {
        largeImage.style.height = height+'px';
    }
}

function toggleArrow(e) {
   jQuery(e.target)
       .prev('.panel-heading')
       .find("i")
       .toggleClass('up down');
}

jQuery('#accordion').on('hide.bs.collapse', toggleArrow);
jQuery('#accordion').on('show.bs.collapse', toggleArrow);
