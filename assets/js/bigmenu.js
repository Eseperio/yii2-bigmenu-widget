(function ($) {

    $.fn.bigmenu = function (options) {

        var self = this;
        var settings = $.extend({
            widthToEnableResponsive: 768
        }, options);
        /* Pages are stored here instead loading again */
        var contentBuffer = {};

        /* Main selector for dynamic loaded content */
        var pageBoxSelector = ".bigmenu-page-panel";

        var bigmenu = {
            resize: function (e) {
                var o = $(self).closest('.bigmenu');
                if (window.innerWidth >= settings.widthToEnableResponsive) {
                    o.removeClass('responsive');
                } else {
                    o.addClass('responsive');
                }
            }, loadContent: function (o) {
                this.resize();
                if (window.innerWidth >= settings.widthToEnableResponsive) {
                    this._load(o);
                }
            }, _load: function (o) {
                var pageToLoad = o.data('bigmenu-page');
                o.hoverIntent(function () {
                    if (typeof contentBuffer[pageToLoad] != "string") {
                        $.ajax({
                            method: "GET", url: pageToLoad, beforeSend: function () {
                                /* todo: Replace with a css loading animation*/
                                //pageTarget.html("Loading");
                            }
                        }).done(function (data, textStatus, jqXHR) {
                            if (data) {
                                contentBuffer[pageToLoad] = data;
                                pageTarget.html(data).addClass('opened');
                            }

                        }).fail(function (data, textStatus, jqXHR) {
                            if (console) {
                                console.error("Bigmenu could not load page.", textStatus);
                            }

                        })

                    } else {
                        pageTarget.html(contentBuffer[pageToLoad]).addClass('opened')
                    }

                }, function () {
                    pageTarget.removeClass("opened");
                    /* hoverOut */
                })

            }
        };

        var pageTarget = $(pageBoxSelector);
        $(window).resize(function () {
            self.each(function () {
                var o = $(this);
                bigmenu.resize(o);
            });
        })
        return self.each(function () {
            var o = $(this);
            bigmenu.resize(o);
            var ajaxEls = o.find('.bigmenu-ajax');
            ajaxEls.each(function () {
                /* hoverIn */
                var ajaxEl = $(this);
                bigmenu.loadContent(ajaxEl);
            });
        });
    };
}(jQuery));
$(".bigmenu").bigmenu(YII2_BIGMENU_WIDGET_OPTIONS);
