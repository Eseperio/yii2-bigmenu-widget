(function ($) {

    $.fn.bigmenu = function (options) {


        var settings = $.extend({
            color: "#556b2f",
            backgroundColor: "white"
        }, options);

        /* Pages are stored here instead loading again */
        var contentBuffer = {};

        /* Main selector for dynamic loaded content */
        var pageBoxSelector = ".bigmenu-page-panel";

        var bigmenu = {
            loadContent: function (o) {
                if(window.innerWidth >= 768)
                {
                    this._load(o)
                }
            },
            _load: function (o) {
                var pageToLoad = o.data('bigmenu-page');

                o.hover(function () {
                    if (typeof contentBuffer[pageToLoad] != "string") {
                        $.ajax({
                            method: "GET",
                            url: pageToLoad,
                            beforeSend: function () {
                                /* todo: Replace with a css loading animation*/
                                //pageTarget.html("Loading");
                            }
                        }).done(function (data, textStatus, jqXHR) {
                            contentBuffer[pageToLoad] = data;
                            pageTarget.html(data).addClass('opened');

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
        return this.each(function () {
            /* hoverIn */
            var o = $(this);

            bigmenu.loadContent(o);


        })

    };

}(jQuery));

$(".bigmenu-ajax").bigmenu();