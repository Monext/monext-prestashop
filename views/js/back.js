/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

let myExtraParams = {};

function payline_initProductsAutocomplete()
{
      const autocompleteInput = $('#product_autocomplete_input');

      myExtraParams = {
        search_phrase: () => {
          return autocompleteInput.val();
        },
        token: () => {
          return autocompleteInput.attr('data-token');
        }
      };

      const ajaxUrl = window.admin_base_link + "/sell/orders/products/search";

      autocompleteInput.autocomplete(ajaxUrl, {
        minLength: 2,
        minChars: 1,
        // Disable to prevent json to be displayed as autocompletion
        autoFill: true,
        max: 10,
        matchContains: true,
        mustMatch: false,
        scroll: false,
        cacheLength: 0,
        extraParams : {
          ...myExtraParams
        },
        parse: function(data) {
            var parsed = [];
            if (payline_isPrestaShop16) {
                var rows = data.split("\n");
                for (var index in rows) {
                    var row = rows[index].split("|");
                    if (row.length == 2) {
                        parsed[parsed.length] = {
                            data: row,
                            value: row[0],
                            result: row
                        };
                    }
                }
            } else {
                let rows = [];
                if(data.products){
                  rows = data.products
                }

                const excludedProductsID = payline_getProductsIds().split(',').filter(item => item !== '').map(item => parseInt(item, 10));
                const filtredRows = rows.filter(row => !excludedProductsID.includes(row.productId));

                for (var index in filtredRows) {
                  var row = filtredRows[index];

                  //--> Add image link, not present in data
                  row.image = '/' + row.productId + '-small_default/' + formatFileName(row.name) + '.jpg';

                  parsed[parsed.length] = {
                    data: row,
                    value: row.name,
                    result: row
                  };
                }
            }
            return parsed;
        },
        formatItem: function(item) {
            if (payline_isPrestaShop16) {
                return item[1] + ' - ' + item[0];
            } else {
                return '<div style="margin-right: 10px;float:left;"><img width=45 height=45 src="'+ item.image +'" /></div>' + '<h4 class="media-heading">' + item.name + '</h4>';
            }
        }
    }).result(payline_addProduct);

    $('#product_autocomplete_input').setOptions({
      extraParams: {
        ...myExtraParams,
        excludeIds: payline_getProductsIds(),
        exclude_packs : 0
      },
    });
};

function payline_getProductsIds()
{
    if ($('#PAYLINE_SUBSCRIBE_PLIST').val() === "") {
        $('#PAYLINE_SUBSCRIBE_PLIST').val(',');
        $('#PAYLINE_SUBSCRIBE_PLIST_PRODUCTS').val('¤');
    }
    return $('#PAYLINE_SUBSCRIBE_PLIST').val();
}

function payline_addProduct(event, data, formatted)
{
    if (data == null) {
        return false;
    }

    if (payline_isPrestaShop16) {
        var productId = data[1];
        var productName = data[0];
        var productImage = '../img/tmp/product_mini_' + productId + '_' + payline_idShop + '.jpg';
    } else {
        var productId = data.productId;
        var productName = data.name;
        var productImage = data.image;
    }

    var $divProducts = $('#PAYLINE_SUBSCRIBE_PLIST_CONTAINER');
    var $inputProducts = $('#PAYLINE_SUBSCRIBE_PLIST');
    var $nameProducts = $('#PAYLINE_SUBSCRIBE_PLIST_PRODUCTS');

    /* delete product from select + add product line to the div, input_name, input_ids elements */
    $divProducts.html($divProducts.html() + '<div id="PAYLINE_SUBSCRIBE_PLIST-PRODUCT-'+ productId +'" class="form-control-static"><button type="button" class="btn btn-default" onclick="payline_delProduct('+ productId +')" name="' + productId + '"><i class="icon-remove text-danger"></i></button><img width=45 height=45 src="' + productImage + '" />&nbsp;' + productName +'</div>');
    $nameProducts.val($nameProducts.val() + productName + '¤');
    $inputProducts.val($inputProducts.val() + productId + ',');
    $('#product_autocomplete_input').val('');
    $('#product_autocomplete_input').setOptions({
      extraParams: {
        ...myExtraParams,
        excludeIds : payline_getProductsIds(),
        exclude_packs : 0
      }
    });
};

function payline_delProduct(id)
{
    var input = getE('PAYLINE_SUBSCRIBE_PLIST');
    var name = getE('PAYLINE_SUBSCRIBE_PLIST_PRODUCTS');

    // Cut hidden fields in array
    var inputCut = input.value.split(',');
    var nameCut = name.value.split('¤');

    if (inputCut.length != nameCut.length) {
        return jAlert('Bad size');
    }

    // Reset all hidden fields
    input.value = '';
    name.value = '';

    for (i in inputCut) {
        // If empty, error, next
        if (!inputCut[i] || !nameCut[i]) {
            continue;
        }
        if (inputCut[i] == '' && nameCut[i] == '') {
            continue;
        }

        // Add to hidden fields no selected products OR add to select field selected product
        if (inputCut[i] != id) {
            input.value += inputCut[i] + ',';
            name.value += nameCut[i] + '¤';
        }
    }

    // Remove div containing the product from the list
    $("#PAYLINE_SUBSCRIBE_PLIST-PRODUCT-" + id).remove();

    $('#product_autocomplete_input').setOptions({
      extraParams: {
        ...myExtraParams,
        excludeIds : payline_getProductsIds(),
        exclude_packs : 0
      }
    });
};

function toggleWidgetCustomizationGroup()
{
    if($('#PAYLINE_WEB_WIDGET_CUSTOM_on').is(':checked') && $('select#PAYLINE_WEB_CASH_UX').val() != 'redirect') {
        $('#web-payment-configuration div.widget_customization').removeClass('hidden');
    } else {
        $('#web-payment-configuration div.widget_customization').addClass('hidden');
    }
}

function formatFileName(input) {
  return input
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9\s]/g, '')
    .trim()
    .replace(/\s+/g, '-');
}

// AdminModules
$(document).ready(function() {
    // Module configuration tab
    $(document).on('click', 'a.list-group-item[data-toggle=tab]', function(e) {
        $('a.list-group-item').removeClass('active');
        $(this).addClass('active');
        $('input[name=selected_tab]').val($(this).data('identifier'));
    });
    $(document).on('change', 'select#PAYLINE_WEB_CASH_ACTION', function() {
        $('#web-payment-configuration div.payline-autorization-only').toggleClass('hidden');
    });
    $(document).on('change', 'select#PAYLINE_WEB_CASH_UX', function() {
        if ($(this).val() == 'redirect') {
            $('#web-payment-configuration div.payline-redirect-only').removeClass('hidden');
            $('.widget_customization_head').hide();
        } else {
            $('#web-payment-configuration div.payline-redirect-only').addClass('hidden');
            $('.widget_customization_head').show();
        }
        toggleWidgetCustomizationGroup();
    });
    $(document).on('change', 'select#PAYLINE_RECURRING_UX', function() {
        if ($(this).val() == 'redirect') {
            $('#recurring-payment-configuration div.payline-redirect-only').removeClass('hidden');
        } else {
            $('#recurring-payment-configuration div.payline-redirect-only').addClass('hidden');
        }
    });
    $(document).on('change', 'input[name="PAYLINE_WEB_WIDGET_CUSTOM"]', function() {
        toggleWidgetCustomizationGroup();
    });

    toggleWidgetCustomizationGroup();

    // Contracts
    $('.payline-contracts-list').sortable({
        placeholder: 'sortable-placeholder active list-group-item',
        start: function(e, ui){
            ui.placeholder.height(ui.item.height());
            ui.placeholder.width(ui.item.width());
        },
        update: function(event, ui) {
            inputId = $(this).attr('data-input-id');
            $('#' + inputId).val(JSON.stringify($('#payline-contracts-list-' + inputId).sortable('toArray', {attribute: 'data-contract-id'})));
        }
    });
    $(document).on('change', '.payline-contract-switch input', function() {
        if ($(this).val() == 1) {
            $(this).parents('.list-group-item').addClass('payline-active-contract').attr('data-contract-id', $(this).attr('data-contract-id'));
        } else {
            $(this).parents('.list-group-item').removeClass('payline-active-contract').attr('data-contract-id', '');
        }
        inputId = $(this).attr('data-input-id');
        $('#' + inputId).val(JSON.stringify($('#payline-contracts-list-' + inputId).sortable('toArray', {attribute: 'data-contract-id'})));
    });

    $(document).on('change', 'select#logs-files-list-select', function() {
      $('#log_display').html("<p>Loading...</p>");

      $.ajax({
        url: window.logs_viewer_controller_url,
        type: 'GET',
        dataType: 'JSON',
        data: {
          action: 'getLogsLines',
          logfile: $('#logs-files-list-select').val(),
          ajax: true,
        },
        success: (data) => {
          $('#log_display').html("");

            data.message.forEach((logLine) => {
            let html = "<p>" + logLine.date + " - " + logLine.logger + " " + logLine.level + " : " + logLine.message;

            if (logLine['context'].length !== 0) {
              html += "<details><summary>[ View Context ]</summary><div style='white-space: pre'>"
                + JSON.stringify(logLine.context, null, 2)
                + "</div></details>";
            }

            html += "</p>";
            $('#log_display').append(html);
          })
        },
          error: (xhr, textstatus ,error) => {
            $('#log_display').html("<p>Cannot show this log file, because : " + textstatus + "</p>");
          }
      });
    });

    // Product autocomplete
    payline_initProductsAutocomplete();

    /*
    * Preview payline CTA
    * */

    const previewContainer = document.getElementById("paylineCtaPreviewContainer");
    const previewButton = document.getElementById('paylineCtaPreview');
    const previewTextUnderCta = document.querySelector('#paylineCtaPreviewContainer p');
    const ctaBgColorSelect = document.getElementById("PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR");
    const ctaBgColorHexadecimalSelect = document.querySelector('input[name="PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR_HEXADECIMAL"]');
    const ctaHoverSelect = document.getElementById("PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR_HOVER");
    const ctaColorSelect = document.getElementById("PAYLINE_WEB_WIDGET_CSS_CTA_TEXT_COLOR");
    const ctaFontSizeSelect = document.getElementById("PAYLINE_WEB_WIDGET_CSS_FONT_SIZE");
    const ctaBorderRadiusSelect = document.getElementById("PAYLINE_WEB_WIDGET_CSS_BORDER_RADIUS");
    const widgetContainerBgColorSelect = document.getElementById("PAYLINE_WEB_WIDGET_CSS_BG_COLOR");
    
    //--> Champs multilingues
    const inputCtaText = document.querySelectorAll("[id^='PAYLINE_WEB_WIDGET_CTA_LABEL_']");
    const ctaTextUnder = document.querySelectorAll("[id^='PAYLINE_WEB_WIDGET_TEXT_UNDER_CTA_']");

    const MLElements = [];
    inputCtaText.forEach(element => MLElements.push(element));
    ctaTextUnder.forEach(element => MLElements.push(element));

    const eventsListeners = [
        {
            type: 'blur',
            elements: MLElements
        },
        {
            type: 'change',
            elements: [ctaBgColorSelect, ctaColorSelect, ctaFontSizeSelect, ctaBorderRadiusSelect, widgetContainerBgColorSelect]
        }
    ];

    eventsListeners.forEach(evtListener => {
        evtListener.elements.forEach(evtListenerElement => {
            if (evtListenerElement) {
                evtListenerElement.addEventListener(evtListener.type, e => {
                    updateWidgetPreview();
                });
            }
        })
    })

    if (previewButton) {

        //--> Prevent click on preview Button
        previewButton.addEventListener('click', e => {
            e.preventDefault();
            return false;
        })

        //--> Couleur du hover
        previewButton.addEventListener('mouseover', function () {
            let hoverCtaBgColor = getCtaBgColor();
            let amount = 0;
            if (ctaHoverSelect) {
                const hoverAmountValue = ctaHoverSelect.value.trim();
                if (hoverAmountValue) {
                    amount = parseInt(hoverAmountValue);
                    hoverCtaBgColor = getCtaBgColor();
                }
            }

            previewButton.style.backgroundColor = adjustHexColor(hoverCtaBgColor, amount); // couleur de hover
        });

        previewButton.addEventListener('mouseout', function () {
            previewButton.style.backgroundColor = getCtaBgColor(); // couleur normale
        });

        previewButton.style.textDecoration = 'none';
    }

    function adjustHexColor(hex, amount) {

        hex = hex.replace(/^#/, '');
        if (hex.length === 3) {
            hex = hex.split('').map(x => x + x).join('');
        }
        let num = parseInt(hex, 16);
        let r = (num >> 16) & 0xFF;
        let g = (num >> 8) & 0xFF;
        let b = num & 0xFF;
        amount = 1 - (amount / 100);

        r = Math.min(255, Math.round(r * amount));
        g = Math.min(255, Math.round(g * amount));
        b = Math.min(255, Math.round(b * amount));

        return '#' + [r, g, b].map(x => x.toString(16).padStart(2, '0')).join('');
    }


    function getCtaBgColor() {
        const defaultColor = '#26A434';
        const colorFromSelect = ctaBgColorSelect?.value.trim();
        const colorFromHex = ctaBgColorHexadecimalSelect?.value.trim();

        return colorFromHex || colorFromSelect || defaultColor;
    }

    //--> Update on language change
    var originalHideOtherLanguage = window.hideOtherLanguage;

    window.hideOtherLanguage = function() {
        if (typeof originalHideOtherLanguage === 'function') {
            originalHideOtherLanguage.apply(this, arguments);
            updateWidgetPreview();
        }
    };

    function isVisible(el) {
        return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
    }

    function getMlFieldValue(fieldElements) {
        let retVal = '';
        Array.from(fieldElements).forEach(element => {
            if ( isVisible(element) ) {
                const elementValue = element.value.trim();
                if (elementValue) {
                    retVal = elementValue;
                }
            }
        })
        return retVal;
    }

    //--> Preview du bouton
    function updateWidgetPreview() {
        if (!previewContainer || !previewButton) {
            return;
        }

        //--> Update button text
        let buttonText = "Payer par carte";

        if (inputCtaText) {
            const newTextCta = getMlFieldValue(inputCtaText).replace('[[amount]]', '155.25 EUR');
            if (newTextCta) {
                buttonText = newTextCta;
            }
        }
        previewButton.innerText = buttonText;

        //--> Test under CTA
        let textUnderCta = '';
        if (ctaTextUnder) {
            textUnderCta = getMlFieldValue(ctaTextUnder);
        }
        previewTextUnderCta.innerText = textUnderCta;

        //--> Cta BG Color
        previewButton.style.backgroundColor = getCtaBgColor();

        //--> Text color
        let ctaColor = '#fff';
        if (ctaColorSelect) {
            const newCtaColor = ctaColorSelect.value.trim();
            if (newCtaColor) {
                ctaColor = newCtaColor;
            }
        }
        previewButton.style.color = ctaColor;

        //--> font Size
        let ctaFontSize = '18px';
        const fontSizes = {
            'small': '14px',
            'average': '20px',
            'big': '24px',
        }
        if (ctaFontSizeSelect) {
            const newCtaFontSize = ctaFontSizeSelect.value.trim();
            if (newCtaFontSize) {
                ctaFontSize = fontSizes[newCtaFontSize];
            }
        }
        previewButton.style.fontSize = ctaFontSize;

        //--> Border Radius
        let ctaBorderRadius = '6px';
        const bordersRadius = {
            'none': '0',
            'small': '3px',
            'average': '8px',
            'big': '24px'
        }
        if (ctaBorderRadiusSelect) {
            const newCtaBorderRadius = ctaBorderRadiusSelect.value.trim();
            if (newCtaBorderRadius) {
                ctaBorderRadius = bordersRadius[newCtaBorderRadius];
            }
        }

        previewButton.style.borderRadius = ctaBorderRadius;

        //--> Container background color
        let widgetContainerBgColor = '#f8f8f8';
        const widgetContainerBgColors = {
            'lighter': '#fefefe',
            'darker': '#dfdfdf'
        }
        if (widgetContainerBgColorSelect) {
            const newWidgetContainerBgColor = widgetContainerBgColorSelect.value.trim();
            if (newWidgetContainerBgColor) {
                widgetContainerBgColor = widgetContainerBgColors[newWidgetContainerBgColor];
            }
        }

        previewContainer.style.backgroundColor = widgetContainerBgColor;
    }

    function toggleHexInputOnColorChange() {
        const $select = $('select[name="PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR"]');
        const $hexInputGroup = $('.hexadecimal-input');
        const $hexInput = $hexInputGroup.find('input');

        function toggleHexInput() {
            if ($select.val() === 'hexadecimal') {
                $hexInputGroup.show();
            } else {
                $hexInputGroup.hide();
                $hexInput.val('');
                previewButton.style.backgroundColor = $select.val();
            }
        }

        toggleHexInput();
        $select.on('change', toggleHexInput);
        $hexInput.on('change', updateWidgetPreview);
    }

    toggleHexInputOnColorChange();
    updateWidgetPreview();
});

