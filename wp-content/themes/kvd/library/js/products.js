var $ = jQuery.noConflict();
$(window).load(function() {
    productBoxSize();
    createProductImageSlider();
    var scene = productDetailSize(true);
    console.log(scene);
    setSCrollActive();
    $(window).bind("scroll", setSCrollActive);
    $(window).resize(function() {
        productBoxSize();
        if (scene) {
            scene.destroy(true);
           // scene.controller().destroy();
            scene = null;
        }
        scene = productDetailSize();


    });
    //$(window).bind("scroll", productDetailSize);
    var dragIcon = {}
    $("#owl-instagram").owlCarousel({
        items: 7,
        margin: 10,
        responsiveClass: true,
        nav: false,
        onInitialized: function() {
            
            dragIcon.wasSeen = false;
            var dragIndicator = $("<div>", {
                class: "dragIndicator"
            });
            $("#owl-instagram").append(dragIndicator);
            $("#owl-instagram").on("mouseenter", function(e) {
                if (dragIcon.wasSeen) {
                    return;
                }
                dragIcon.isActive = true;
                $(".dragIndicator").addClass("active");
            });
            $("#owl-instagram").on("mouseleave", function(e) {
                dragIcon.isActive = false;
                $(".dragIndicator").removeClass("active");
            });
            
             $("#owl-instagram").on("mousemove", function(e) {
                 if( dragIcon.isActive ){
                     $(".dragIndicator").css("left", e.clientX + 'px');
                     $(".dragIndicator").css("top", e.clientY + 'px');
                 }
             });
        },
        onDrag: function(){
          if( dragIcon.isActive ){
              dragIcon.wasSeen = true;
              $(".dragIndicator").removeClass("active");
           }  
        },
        responsive: {
            0: {
                items: 2
            },
            479: {
                items: 4
            },
            1024: {
                items: 5
            },
            1025: {
                items: 7
            }
        }
    });
});

var footerScrollMark, scrollLast = 0;

function productBoxSize() {
   
    $("body").find("[data-ratio]").each(function() {
        var r;
        if ($(window).width() >= 768 && $(window).width() <= 992 && $(this).attr("data-tablet-ratio")) {
            r = $(this).attr("data-tablet-ratio");
        } else if ($(window).width() < 768 && $(this).attr("data-mobile-ratio")) {
            r = $(this).attr("data-mobile-ratio");
        } else {
            r = $(this).attr("data-ratio");
        }

        if (r != "none") {
            imageRation($(this), r);
        } else {
            $(this).css("width", "").css("height", "");
        }

    })
}

function imageRation($t, r) {
    var w = $t.outerWidth();

    if (r != "fullscreen") {
        r = parseFloat(r);
        $t.css("height", w / r);
    } else {
        var h = $(window).height();
        $t.css("height", h);
    }
}

function createProductImageSlider() {
    var len = $(".product-images .product-image").length;
    $(".product-images").append('<div class="product-images-nav desktop-only hide"><div class="fake-table"><div class="fake-cell"></div></div></div>');

    var $image_nav = $(".product-images-nav");
    $(".product-images .product-image").each(function() {
        $image_nav.find(".fake-cell").append('<div class="nav-wrap"><div class="nav-bit"></div></div>');
    })

    $image_nav.removeClass("hide");
    $image_nav.find(".nav-wrap").each(function() {
        $(this).click(function(e) {
            e.preventDefault();
            var index = $(this).index() + 1;
            var $p = $(".product-images .product-image:nth-child(" + index + ")");
            var pos = $p.offset();
            var top = pos.top;
            if (index > 1) {
                top -= 88;
            }
            $("html,body").animate({
                scrollTop: top
            }, 300, 'swing')
        })
    })
    $image_nav.find(".nav-wrap").first().addClass("active");
}

function productDetailSize() {
    var $d = $(".details");
    
    var h = $d.height();
    var totalH = $(window).height();
    var top =  0;
    var top_h = top + h;
    
    if (top_h > totalH) {
        return null;
    } else {
        var productBox = $(".product-box");
        if ($(window).width() > 991 ) {
            var controller = new ScrollMagic.Controller();
            var scene = new ScrollMagic.Scene({triggerElement: "#trigger1", duration: jQuery('.product-images').outerHeight()-h})
                            .setPin("#pin1")
                            .setClassToggle("body", "stopped") // add class toggle
                            .addTo(controller);
            return scene;
        } else {
            return null;
        }
    }


}

function setSCrollActive() {
    if ($(".product-image").length > 0) {
        var dir = true;
        var scroll = $(window).scrollTop();
        if (scroll < scrollLast) {
            dir = false;
        }

        scrollLast = scroll;
        var wh = $(window).height();
        var scrollMax = 0.5 * wh; // use 20% of the height as the breakpoint
        var scrollMin = (wh * -1) + scrollMax;

        $(".product-image").each(function() {
            var index = $(this).index() + 1;
            var $image_nav = $(".product-images-nav .nav-wrap:nth-child(" + index + ")");

            var t = $(this).offset().top - scroll;
            if (dir) {
                if (t <= scrollMax && t >= scrollMin) {
                    if (!$(this).hasClass("active")) {
                        $(this).siblings(".product-image.active").removeClass("active");
                        $image_nav.siblings(".product-images-nav .nav-wrap.active").removeClass("active");
                        $(this).addClass("active");
                        $image_nav.addClass("active");
                    }
                }
            } else {
                if (t < scrollMax && t >= scrollMin) {
                    if (!$(this).hasClass("active")) {
                        $(this).siblings(".product-image.active").removeClass("active");
                        $image_nav.siblings(".product-images-nav .nav-wrap.active").removeClass("active");
                        $(this).addClass("active");
                        $image_nav.addClass("active")
                    } else {}
                } else {}
            }
        })

        var $d = $(".details");
        //if($(window).height() < $d.height() + parseFloat($d.css("top"))){    
        if (scroll >= footerScrollMark) {
            if (!$d.hasClass("bottomFixed")) {
                if(!$d.hasClass("relative")){
                    $d.addClass("bottomFixed");
                }
                $(".product-images-nav").addClass("hide");
            }
        } else {
            if ($d.hasClass("bottomFixed")) {
                $d.removeClass("bottomFixed");
                $(".product-images-nav").removeClass("hide");
            }
        }
        //}    
    }
    
}
