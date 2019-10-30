(function ($, window) {

    window.initTlpTeam = function () {
        $(".tlp-team").each(function () {
            var container = $(this),
                isIsotope = container.find('.tlp-team-isotope'),
                isCarousel = container.find('.layout-carousel, .tlp-team-carousel');
            if (isIsotope.length && $.fn.isotope) {
                var isotope = isIsotope.imagesLoaded(function () {
                    isotope.isotope({
                        getSortData: {
                            name: '.name',
                            designation: '.designation',
                        },
                        sortAscending: true,
                        itemSelector: '.team-member',
                    });
                });
                var isotopeButtonGroup = $(this).find('.button-group.sort-by-button-group');
                isotopeButtonGroup.on('click', 'button', function (e) {
                    e.preventDefault();
                    var sortByValue = $(this).attr('data-sort-by');
                    isotope.isotope({sortBy: sortByValue});
                    $(this).parent().find('.selected').removeClass('selected');
                    $(this).addClass('selected');
                });
            }
            if (isCarousel.length && $.fn.owlCarousel) {
                isCarousel.imagesLoaded(function () {
                    var options = isCarousel.data('owl-options');
                    console.log(options);
                    isCarousel.addClass('owl-carousel owl-theme').owlCarousel({
                        nav: !!options.nav,
                        navElement: 'div',
                        dots: !!options.dots,
                        autoplay: !!options.autoplay,
                        autoplayHoverPause: !!options.autoplayHoverPause,
                        loop: !!options.loop,
                        autoHeight: !!options.autoHeight,
                        lazyLoad: !!options.lazyLoad,
                        rtl: !!options.rtl,
                        navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
                        responsiveClass: true,
                        responsive: {
                            0: {
                                items: 1
                            },
                            767: {
                                items: 2
                            },
                            991: {
                                items: parseInt(options.items, 10) || 3
                            }
                        }
                    });
                });
            }
        });
    };
    initTlpTeam();
})(jQuery, window);