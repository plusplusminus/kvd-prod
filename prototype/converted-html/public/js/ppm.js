$('.awards-toggle a').on('click', function(event) {
    $('.awards-toggle a').removeClass('active'); // remove active class from tabs
    $(this).addClass('active'); // add active class to clicked tab
});