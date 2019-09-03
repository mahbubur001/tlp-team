(function ($) {
    if ($('.tlp-color').length && $.fn.wpColorPicker) {
        $('.tlp-color').wpColorPicker();
    }
    if ($("select.tlp-select").length && $.fn.select2) {
        $("select.tlp-select").select2({
            theme: "classic",
            dropdownAutoWidth: true,
            width: '100%'
        });
    }
    if ($("#tlp_team_sc_settings_meta .tlp-color").length && $.fn.wpColorPicker) {
        var cOptions = {
            defaultColor: false,
            change: function (event, ui) {
                renderTlpTeamPreview();
            },
            clear: function () {
                renderTlpTeamPreview();
            },
            hide: true,
            palettes: true
        };
        $("#tlp_team_sc_settings_meta .tlp-color").wpColorPicker(cOptions);
    }
    //   Depricated .............
    imageSize();
    $(window).on('load', function () {
        createShortCode();
    });
    $("#rt-feature-img-size").on('change', function () {
        imageSize();
    });
    $("#scg-wrapper").on('change', 'select,input,propertychange', function () {
        createShortCode();
    });
    $("#sc-layout").on('change', function () {
        var val = $(this).val();
        if (val == 'carousel') {
            $("#carousel-sc").slideDown();
        } else {
            $("#carousel-sc").slideUp();
        }
    });

    $("#scg-wrapper").on("input propertychange", function () {
        createShortCode();
    });
    //  End Depricated ...

    $("#tlp_team_sc_settings_meta").on('change', 'select,input', function () {
        renderTlpTeamPreview();
    });
    $("#tlp_team_sc_settings_meta").on("input propertychange", function () {
        renderTlpTeamPreview();
    });

    function createShortCode() {
        var sc = "[tlpteam";
        $("#scg-wrapper").find('input[name],select[name],textarea[name]').each(function (index, item) {
            var v = $(this).val(),
                name = this.name;
            if (this.type === 'checkbox') {
                if (this.checked && $("#sc-layout").val() == 'carousel') {
                    sc = v ? sc + " " + name + "=" + '"' + v + '"' : sc;
                }
            } else {
                sc = v ? sc + " " + name + "=" + '"' + v + '"' : sc;
            }

        });
        sc = sc + "]";
        $("#sc-output textarea").val(sc);
    }

    function featureImageEffect() {
        if ($("#ttp_image").is(':checked')) {
            $(".tlp-field-holder.ttp-feature-image-option").hide();
        } else {
            $(".tlp-field-holder.ttp-feature-image-option").show();
        }
    }

    function imageSize() {
        var size = $("#rt-feature-img-size").val();
        if (size == "ttp_custom") {
            $(".rt-custom-image-size-wrap").show();
        } else {
            $(".rt-custom-image-size-wrap").hide();
        }
    }

    function imageSizeEffect() {
        var size = $("#ttp_image_size").val();
        if (size == "ttp_custom") {
            $("#ttp_custom_image_size_holder").show();
        } else {
            $("#ttp_custom_image_size_holder").hide();
        }
    }

    function paginationEffect() {
        if ($("#ttp_pagination").is(':checked')) {
            $(".tlp-field-holder.tlp-pagination-item").show();
        } else {
            $(".tlp-field-holder.tlp-pagination-item").not('.pagination').hide();
        }
    }

    function useEffect() {
        imageSizeEffect();
        featureImageEffect();
        var layout = $("#layout").val();
        if (layout) {
            var isGrid = layout.match(/^layout/i),
                isCarousel = layout.match(/^carousel/i),
                isIsotope = layout.match(/^isotope/i);
            if (isGrid) {
                $(".tlp-field-holder.tlp-carousel-item").hide();
                $(".tlp-field-holder.tlp-pagination-item.pagination").show();
            } else if (isCarousel) {
                $(".tlp-field-holder.tlp-pagination-item").hide();
                $(".tlp-field-holder.tlp-carousel-item").show();
            } else if (isIsotope) {
                $(".tlp-field-holder.tlp-carousel-item").hide();
                $(".tlp-field-holder.tlp-pagination-item.pagination").show();
            }
        }

        if ($(".tlp-field-holder.tlp-pagination-item.pagination").is(':visible')) {
            paginationEffect();
        }
    }

    renderTlpTeamPreview();
    useEffect();
    $("#layout").on('change', function () {
        useEffect();
    });
    $("#ttp_image").on('change', function () {
        featureImageEffect();
    });
    $("#tlp_image_size").on('change', function () {
        imageSizeEffect();
    });
    $("#ttp_pagination").on('change', function () {
        paginationEffect();
    });

    $(".rt-tab-nav li").on('click', 'a', function (e) {
        e.preventDefault();
        var container = $(this).parents('.rt-tab-container'),
            nav = container.children('.rt-tab-nav'),
            content = container.children(".rt-tab-content"),
            $this = $(this),
            $id = $this.attr('href');
        content.hide();
        nav.find('li').removeClass('active');
        $this.parent().addClass('active');
        container.find($id).show();
    });

    function renderTlpTeamPreview() {
        var target = $('#tlp_team_sc_settings_meta');
        if (target.length) {
            var data = target.find('input[name],select[name],textarea[name]').serialize();
            // Add Shortcode ID
            data = data + '&' + $.param({'sc_id': $('#post_ID').val() || 0});
            TlpTeamPreviewAjaxCall(null, 'tlpTeamPreviewAjaxCall', data, function (res) {
                console.log(res);
                if (!data.error) {
                    $("#tlp-team-preview-container").html(res.data);
                    initTlpTeam();
                }
            });
        }
    }

    function TlpTeamPreviewAjaxCall(element, action, arg, handle) {
        var data;
        if (action) data = "action=" + action;
        if (arg) data = arg + "&action=" + action;
        if (arg && !action) data = arg;

        var n = data.search(ttp.nonceID);
        if (n < 0) {
            data = data + "&" + ttp.nonceID + "=" + ttp.nonce;
        }
        $.ajax({
            type: "post",
            url: ttp.ajaxurl,
            data: data,
            beforeSend: function () {
                $('#tlp-team-preview-container').addClass('loading');
                $('#tlp-team-response .spinner').addClass('is-active');
            },
            success: function (data) {
                $('#tlp-team-preview-container').removeClass('loading');
                $('#tlp-team-response .spinner').removeClass('is-active');
                handle(data);
            }
        });
    }

})(jQuery);

function tlpTeamSettings(e) {

    jQuery('#response').hide();
    arg = jQuery(e).serialize();
    bindElement = jQuery('#tlpSaveButton');
    AjaxCall(bindElement, 'tlpTeamSettings', arg, function (data) {
        console.log(data);
        if (data.error) {
            jQuery('#response').removeClass('error');
            jQuery('#response').show('slow').text(data.msg);
        } else {
            jQuery('#response').addClass('error');
            jQuery('#response').show('slow').text(data.msg);
        }
    });


}

function AjaxCall(element, action, arg, handle) {
    if (action) data = "action=" + action;
    if (arg) data = arg + "&action=" + action;
    if (arg && !action) data = arg;
    data = data;

    var n = data.search("tlp_nonce");
    if (n < 0) {
        data = data + "&tlp_nonce=" + tpl_nonce;
    }

    jQuery.ajax({
        type: "post",
        url: ajaxurl,
        data: data,
        beforeSend: function () {
            jQuery("<span class='tlp_loading'></span>").insertAfter(element);
        },
        success: function (data) {
            jQuery(".tlp_loading").remove();
            handle(data);
        }
    });
}
