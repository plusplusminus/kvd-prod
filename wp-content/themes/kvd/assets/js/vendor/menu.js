/**
 * Create an anonymous function to avoid library conflicts
 */
(function($) {
    /**
     * Add our plugin to the jQuery.fn object
     */
    $.fn.responsiveMenu = function(options) {
        /**
         * Define some default settings
         */

        $.fn.responsiveMenu.defaultOptions = {
            "menuIcon_text": '',
            "menuslide_overlap": false,
            "menuslide_push": false,
            "menuslide_direction": ''
        };
        /**
         * Merge the runtime options with the default settings
         */
        var options = $.extend({}, $.fn.responsiveMenu.defaultOptions, options);
        /**
         * Iterate through the collection of elements and
         * return the object to preserve method chaining
         */
        return this.each(function(i) {
            var menuobj = $(this);
            var mobileSubMenu;
            var subMenuArrows;
            var mobFlag = false;
            var deskFlag = false;
            var defaultMenu = false;
            createMobileStructure(menuobj);
            mobileMenuInit(menuobj);

            function removeDesktopMenu(menuobj) {
                menuobj.removeClass('desk');
                mobileSubMenu.next().stop(true, true).slideUp();
                subMenuArrows.removeClass('up');
                //menuobj.find(".menu-icon").removeClass('active');
            }

            function createMobileStructure(menuobj) {
                menuobj.find("li").each(function() {
                    if ($(this).children("ul") || $(this).children("div")) {
                        $(this).children().prev('a').addClass('menubelow');
                    }
                });
                mobileSubMenu = menuobj.find('a.menubelow');
                if (menuobj.find('.arrow').length == 0) {
                    mobileSubMenu.each(function() {
                        //$(this).closest('li').prepend('<span class="arrow"></span>');
                        $(this).next().addClass("sb-menu");
                        $(this).wrap("<div class='mobileLinkWrap'></div>")
                        $(this).parent().prepend('<span class="arrow"></span>');
                    });
                    subMenuArrows = menuobj.find('.arrow');
                }
            }

            function bindClickonMobilemenu(menuobj) {
                menuobj.find('.arrow').on('touchstart click', function(e) {
                    e.stopImmediatePropagation();
                    e.preventDefault();
                    var submenu = $(this).closest('li').children('.sb-menu');
                    var sibilingsOfCurrent_obj = $(this).closest('li').siblings();
                    var this_parentLi = $(this).closest('li');
                    if ($(".menu-icon").is(":visible")) {
                        if (submenu.length > 0) {
                            sibilingsOfCurrent_obj.find('.sb-menu').stop(true, true).slideUp(); // comment to close
                            sibilingsOfCurrent_obj.find('.sb-menu').each(function() {
                                $(this).closest('li').find('>.mobileLinkWrap > span').removeClass('up'); // 
                            });
                        }
                        if (!submenu.is(':visible')) {
                            submenu.find('.sb-menu').each(function() {
                                $(this).stop().slideUp();
                                $(this).closest('li').find('span').removeClass('up')
                            });
                            submenu.stop().slideDown();
                            this_parentLi.find('>.mobileLinkWrap > span').addClass('up');
                        } else {
                            submenu.slideUp();
                            this_parentLi.find('>.mobileLinkWrap > span').removeClass('up');
                        }
                    }
                });
            }

            function removeMobileMenu(menuobj) {
                menuobj.find('.menubelow').each(function() {
                    $(this).removeAttr('style');
                    $(this).unwrap();
                    $(this).next().removeAttr('style');
                });
                menuobj.find('.arrow').remove();
                $(".menu-icon").removeClass('active');
                $("body").removeClass("menu-open");
                menuobj.addClass('desk').removeAttr("style");
                menuobj.removeAttr("style");
                deskFlag = false;
            }

            $(window).resize(function(e) {
                mobileMenuInit(menuobj);
            });

            function mobileMenuInit(menuobj) {
                if ($(".menu-icon").is(":visible")) {
                    if (!mobFlag) {
                        removeDesktopMenu(menuobj);
                        createMobileStructure(menuobj);
                        bindClickonMobilemenu(menuobj);
                        mobFlag = true;
                        deskFlag = false;
                        menuobj.removeClass('desk');
                        $('body').removeClass('desk');
                        menuobj.addClass('mob');
                        $('body').addClass('mob');
                    }
                } else {
                    if (!deskFlag) {
                        removeMobileMenu(menuobj);
                        mobFlag = false;
                        deskFlag = true;
                        menuobj.removeClass('mob');
                        $('body').removeClass('mob');
                        menuobj.addClass('desk');
                        $('body').addClass('desk');

                    }
                }
            }


            function closeMobileMenu(menuobj) {
                $("body").removeClass("menu-open");
                $(".menu-icon").removeClass('active');
                menuobj.find('.arrow').removeClass('up');
                menuobj.find('.sb-menu').stop(true, true).slideUp();
            }

            /*if ('ontouchstart' in window) {


                menuobj.find("a").click(function(e) {
                    if (!$(this).hasClass("link") && !$("body").hasClass("mob") && $(this).next().length > 0) {
                        e.preventDefault();
                        $(this).addClass("link");
                        $(this).parent().addClass('hover');
                    }
                })
                $('body').on('click touchstart', function(e) {
                    if ($(e.target).closest(".enumenu_container").length == 0) {
                        menuobj.find("a").each(function() {
                            $(this).removeClass("link");
                            $(this).parent().removeClass("hover");
                        });
                    }
                });
            } else {

                menuobj.find("li").mouseenter(function() {
                    $(this).addClass('hover');
                });
                menuobj.find("li").mouseleave(function() {
                    $(this).removeClass('hover');
                });
            }*/

        });
    };
    responsiveClass();
    $(window).resize(function() {
        responsiveClass();
    });

    function responsiveClass() {
        if ($(".menu-icon").is(":visible")) {
            $('body').addClass('menuOverlap');
            $('body').addClass('slidemenuRight');
        } else {
            $('body').removeClass('menuOverlap');
            $('body').removeClass('slidemenuRight');
        }
    }

    // Toggle menu
    $(".menu-icon").on('click', function(e) {
        if ($(this).hasClass('active')) {
			$(this).addClass("closing");
            closeMenu();
        } else {
            $(this).addClass("active");
            $("body").addClass("menu-open");
        }
    });
    $('body').on('click', function(e) {
        if ($(".menu-icon").is(":visible")) {
            if ($(e.target).closest(".enumenu_container").length == 0 && !$(e.target).hasClass('active')) {
                closeMenu();
            }
        }
    });
    $(".menu-one > ul").responsiveMenu();
    //$(".menu-two > ul").responsiveMenu();

    $(".close-btn > a").click(function(e) {
        e.preventDefault();
        closeMenu();
    });
	
		/*$('.sub-menu li a').click(function(){
			if ($(".menu-icon").is(":visible")) {
					closeMenu();
			}
		});*/
	
    
    function closeMenu(){
        $(".menu-icon").removeClass("active");
        $("body").removeClass("menu-open");
        $(".menu-one > ul").find('.arrow').removeClass('up');
        $(".menu-two > ul").find('.arrow').removeClass('up');
        $(".menu-one").find('.sb-menu').stop(true, true).slideUp();
        $(".menu-two").find('.sb-menu').stop(true, true).slideUp();
		setTimeout(function(){
			$(".menu-icon").removeClass("closing");
		},250);
    }
})(jQuery);