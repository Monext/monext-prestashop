{*
*
* Payline module for PrestaShop
*
* @author    Monext <support@payline.com>
* @copyright Monext - http://www.payline.com
*
*}

{if isset($payline_subtitle) && strlen($payline_subtitle)}
    <p>{$payline_subtitle}</p>
{/if}

{if isset($payline_contracts)}
    <ol class="list-unstyled">
        {foreach from=$payline_contracts item=payline_contract}
            {if !empty($payline_contract.enabled)}
                <li class="d-inline">
				<img src="{$urls.base_url}modules/payline/views/img/contracts/{$payline_contract.logo}" alt="{$payline_contract.label|escape:'html':'UTF-8'}" title="{$payline_contract.label|escape:'html':'UTF-8'}" />
                </li>
            {/if}
        {/foreach}
    </ol>
{/if}

{include file="modules/payline/views/templates/front/1.7/widget_js_customization.tpl" payline_widget_customization=$payline_widget_customization }

<script>
    let widgetHasBeenClosedBefore       = false;
    let widgetDidShowState              = false;
    let paymentOptionID                 = false;

    const confirmationButtonSelector    = '#payment-confirmation button[type="submit"]';
    const lightboxCloseSelector         = '#pl-container-lightbox-close';

    function onDidShowState(event) {
        if (event.state !== 'PAYMENT_METHODS_LIST') {
            return;
        }
        const selectedPaymentOption = document.querySelector('input[name="payment-option"]:checked');
        if (selectedPaymentOption) {
            paymentOptionID = selectedPaymentOption.getAttribute('id');
        }
        widgetDidShowState = true;
        customizeWidget();
    }

    function isPaylineCptCurrentPaymentOption() {
        const selectedPaymentOption = document.querySelector('input[name="payment-option"]:checked');

        if ( !paymentOptionID || !selectedPaymentOption ) {
            return false;
        }

        if (selectedPaymentOption) {
            return paymentOptionID === selectedPaymentOption.getAttribute('id');
        }
    }

    function onCloseLightBoxHandler() {
        widgetDidShowState = false;
        const confirmationButton = document.querySelector(confirmationButtonSelector);

        if (confirmationButton) {
            confirmationButton.classList.remove('disabled');
            widgetHasBeenClosedBefore = true;
        }
    };

    //--> Close lightbox with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && widgetDidShowState === true) {
            onCloseLightBoxHandler();
        }
    })

    
    document.addEventListener('click', (e) => {
        //--> Reset Widget if user click on confirmation button
        if (e.target.closest(confirmationButtonSelector) && isPaylineCptCurrentPaymentOption() === true) {
            if (!widgetHasBeenClosedBefore) {
                return true;
            }

            try {
                Payline.Api.reset();
            } catch (error) {
                console.error('Error resetting Payline widget:', error);
            }
        }

        //--> Close lightbox when clicking on close button
        if (e.target.closest(lightboxCloseSelector)) {
            onCloseLightBoxHandler();
        }
    });
</script>

{include file="modules/payline/views/templates/front/1.7/widget_css_customization.tpl" payline_widget_customization=$payline_widget_customization }

<div id="PaylineWidget" data-auto-init="false" data-token="{$payline_token}" data-template="{$payline_ux_mode}"
    data-embeddedredirectionallowed="false" data-event-didshowstate="onDidShowState">
</div>
{foreach from=$payline_assets item=paylineAssetsUrls key=assetType}
	{foreach from=$paylineAssetsUrls item=paylineAssetsUrl}
		{if $assetType == 'js'}
			<script src="{$paylineAssetsUrl}"></script>
		{elseif $assetType == 'css'}
			<link href="{$paylineAssetsUrl}" rel="stylesheet" />
		{/if}
	{/foreach}
{/foreach}