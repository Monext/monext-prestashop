/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

function toggleWidgetCustomizationGroup() {
    if($('select#form_PAYLINE_WEB_CASH_UX').val() != 'redirect') {
        $('.widget_customization_head').show();
    }else {
        $('.widget_customization_head').hide();
    }
    if($('#form_PAYLINE_WEB_WIDGET_CUSTOM_1').is(':checked')) {
        $('#web-payment-configuration div.widget_customization').removeClass('hidden');
    } else {
        $('#web-payment-configuration div.widget_customization').addClass('hidden');
    }
}

function toggleRedirectOnly() {
    if($('select.ux_field').val() != 'redirect') {
        $('div.payline-redirect-only').addClass('hidden');
    }else {
        $('div.payline-redirect-only').removeClass('hidden');
    }
}

function toogleCashAction() {
    const $select = $("#form_PAYLINE_WEB_CASH_ACTION")
    if ($select.val() === '100') {
        $('#web-payment-configuration div.payline-autorization-only').removeClass('hidden');
    } else {
        $('#web-payment-configuration div.payline-autorization-only').addClass('hidden');
    }
}

function initializeProductSearch() {
    // Modern approach: EntitySearchInput for ProductSearchType (PS 8.x/9.x)
    if (window.prestashop && window.prestashop.component && window.prestashop.component.EntitySearchInput) {
        $('.entity-search-widget').each(function () {
            new window.prestashop.component.EntitySearchInput($(this), {});
        });
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
    $(document).on('change', 'select#form_PAYLINE_WEB_CASH_ACTION', function() {
        toogleCashAction();
    });
    $(document).on('change', 'select#form_PAYLINE_WEB_CASH_UX', function() {
        toggleWidgetCustomizationGroup();
    });
    $(document).on('change', 'select.ux_field', function() {
        toggleRedirectOnly();
    });
    $(document).on('change', 'input[name="form[PAYLINE_WEB_WIDGET_CUSTOM]"]', function() {
        toggleWidgetCustomizationGroup();
    });

    toggleWidgetCustomizationGroup();
    toogleCashAction();
    toggleRedirectOnly();

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

    // Toggle alternative contracts section based on switch
    function toggleAltContractsSection() {
        const $altContractsSwitch = $('input[name="form[PAYLINE_ALT_CONTRACTS_AS_MAIN]"]');
        if ($altContractsSwitch.length === 0) {
            return; // Not on contracts page
        }

        const useMainContracts = $altContractsSwitch.filter(':checked').val() === '1';
        if (useMainContracts) {
            $('#alt-contracts-section').hide();
        } else {
            $('#alt-contracts-section').show();
        }
    }

    // Initialize alternative contracts section visibility on page load
    toggleAltContractsSection();

    // Listen for changes on the PAYLINE_ALT_CONTRACTS_AS_MAIN switch
    $(document).on('change', 'input[name="form[PAYLINE_ALT_CONTRACTS_AS_MAIN]"]', function() {
        toggleAltContractsSection();
    });

    // Logs Viewer
    $(document).on('change', 'select#logs-files-list-select', function () {
        $('#log_display').html("<p>Loading...</p>");

        $.ajax({
            url: window.logs_viewer_controller_url,
            type: 'GET',
            data: {
                logfile: $('#logs-files-list-select').val(),
                ajax: true,
            },
            success: (data) => {
                $('#log_display').html("");
                const resultArray = JSON.parse(data);
                resultArray.forEach((logLine) => {
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
            error: (xhr, textstatus, error) => {
                debugger;
                $('#log_display').html("<p>Cannot show this log file, because : " + textstatus + "</p>");
            }
        });
    });

    // Suppression directe sans modale pour TypeaheadProductCollectionType
    $(document).on('click', '#form_PAYLINE_SUBSCRIBE_PLIST-data .delete', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Empêche product_page.bundle.js de l'attraper

        $(this).closest('li').hide(300, function() {
            $(this).remove();
        });
    });
    initializeProductSearch();

    /*
    * Preview payline CTA
    * */

    const previewContainer = document.getElementById("paylineCtaPreviewContainer");
    const previewButton = document.getElementById('paylineCtaPreview');
    const previewTextUnderCta = document.querySelector('#paylineCtaPreviewContainer p');
    const ctaBgColorSelect = document.getElementById("form_PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR");
    const ctaBgColorHexadecimalSelect = document.querySelector('input[name="form[PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR_HEXADECIMAL]"]');
    const ctaHoverSelect = document.getElementById("form_PAYLINE_WEPAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR_HEXADECIMALB_WIDGET_CSS_CTA_BG_COLOR_HOVER");
    const ctaColorSelect = document.getElementById("form_PAYLINE_WEB_WIDGET_CSS_CTA_TEXT_COLOR");
    const ctaFontSizeSelect = document.getElementById("form_PAYLINE_WEB_WIDGET_CSS_FONT_SIZE");
    const ctaBorderRadiusSelect = document.getElementById("form_PAYLINE_WEB_WIDGET_CSS_BORDER_RADIUS");
    const widgetContainerBgColorSelect = document.getElementById("form_PAYLINE_WEB_WIDGET_CSS_BG_COLOR");

    // //--> Champs multilingues
    let inputCtaText = document.querySelectorAll("[id*='PAYLINE_WEB_WIDGET_CTA_LABEL']");
    let ctaTextUnder = document.querySelectorAll("[id*='PAYLINE_WEB_WIDGET_TEXT_UNDER_CTA']");

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
        previewTextUnderCta.innerText = textUnderCta.replace('[[amount]]', '155.25 EUR');

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
        const $select = $('select[name="form[PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR]"]');
        const $hexInputGroup = $('.hexadecimal-input');
        const $hexInput = $hexInputGroup.find('input');

        function toggleHexInput() {
            if ($select.val() === 'hexadecimal') {
                $hexInputGroup.show();
            } else {
                $hexInputGroup.hide();
                $hexInput.val('');
                if (previewButton) {
                    previewButton.style.backgroundColor = $select.val();
                }
            }
        }

        if ($select.length > 0) {
            toggleHexInput();
            $select.on('change', toggleHexInput);
        }
        if ($hexInput.length > 0) {
            $hexInput.on('change', updateWidgetPreview);
        }
    }

    toggleHexInputOnColorChange();
    updateWidgetPreview();
});

