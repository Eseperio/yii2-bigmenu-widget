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

        var resize = function (e) {
            var o = $(self).closest('.bigmenu');
            if (window.innerWidth >= settings.widthToEnableResponsive) {
                o.removeClass('responsive');
            } else {
                o.addClass('responsive');
            }
        };
        var bigmenu = {
            loadContent: function (o) {
                self.resize();
                if (window.innerWidth >= settings.widthToEnableResponsive) {
                    this._load(o);
                }
            },
            _load: function (o) {
                var pageToLoad = o.data('bigmenu-page');
                o.hoverIntent(function () {
                    if (typeof contentBuffer[pageToLoad] != "string") {
                        $.ajax({
                            method: "GET",
                            url: pageToLoad,
                            beforeSend: function () {
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

        $(window).resize(function(){
            resize();
        })
        return this.each(function () {
            /* hoverIn */
            var o = $(this);
            bigmenu.loadContent(o);
        })

    };

}(jQuery));

$(".bigmenu-ajax").bigmenu(YII2_BIGMENU_WIDGET_OPTIONS);
