{*
*
* Payline module for PrestaShop
*
* @author    Monext <support@payline.com>
* @copyright Monext - http://www.payline.com
*
*}

{include file="modules/payline/views/templates/front/1.7/widget_js_customization.tpl" payline_widget_customization=$payline_widget_customization }


<script>
  function waitForElement(selector, callback) {
    const observer = new MutationObserver((mutations, observer) => {
      const element = document.querySelector(selector);
      if (element) {
        callback(element);
        observer.disconnect();
      }
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
  }

  function onDidShowState(event) {
      if ( event.state !== 'PAYMENT_METHODS_LIST' ) {
          return;
      }

  // Cocher les cgv au chargement du module
  //   const termsCheckbox = document.querySelector('input[name="conditions_to_approve[terms-and-conditions]"]');
  //   if (termsCheckbox && !termsCheckbox.checked) {
  //       termsCheckbox.checked = true;
  //       termsCheckbox.dispatchEvent(new Event('change'));
  //       termsCheckbox.dataset.paylineAutoChecked = 'true';
  //   }

    const paylineParentID = document.querySelector('[data-js-selector="{$jsSelector}"]').parentElement.id;
    const paylineOptionID = paylineParentID.replaceAll('-additional-information', '');
    const paymentConfirmation = document.querySelector('#payment-confirmation button[type="submit"]');
    const agreements = document.querySelectorAll('input[name="conditions_to_approve[terms-and-conditions]"]');

    //---> Wait agreements to be available
    let paylinePaymentsButton = Array.from(document.querySelectorAll('.pl-pay-btn'));
    let amazonPaymentButton = null;

    //--> Amazon is not a button, we do specific
    //--> Wait for Amazon image to be available
    waitForElement('.pl-amazon-pay .pl-pay-btn-container img', element => {
      amazonPaymentButton = element;

      amazonPaymentButton.addEventListener("click", e => {
        const acceptedAgreements = areAggreementsAccepted();
        if (!acceptedAgreements) {
          e.preventDefault();
          e.stopImmediatePropagation();
          return false;
        }
      }, true);

      //--> Disable attribute does nothing on images. Here it's just for CSS
      paylinePaymentsButton.push(amazonPaymentButton);
      setPaylineWidgetState();
    });

    let paymentConfirmationOriginalVisibity = '';
    let wasPaylineBefore = false;

    if (paymentConfirmation) {
      paymentConfirmationOriginalVisibity = paymentConfirmation.style.visibility;
    }

    const areAggreementsAccepted = () => {
      let isChecked = true;
      Array.from(agreements).forEach(agreement => {
        if (agreement.checked === false) {
          isChecked = false;
        }
      });
      return isChecked;
    }

    const setPaylineWidgetState = () => {
      const acceptedAgreements = areAggreementsAccepted();
      const widgetContainer = document.querySelector('[data-js-selector="{$jsSelector}"]');
      const notVisibleMessage = document.querySelector('[data-js-selector="{$jsSelector}-not-visible"]');
      if (!acceptedAgreements) {
            if (widgetContainer) {
                widgetContainer.style.display = 'none';
            }
            if (notVisibleMessage) {
              notVisibleMessage.style.display = '';
            }
        } else {
            if (widgetContainer) {
                widgetContainer.style.display = '';
            }
            if (notVisibleMessage) {
              notVisibleMessage.style.display = 'none';
            }
        }
    }

    Array.from(document.querySelectorAll('.payment-options input[type="radio"]')).forEach(paymentMethodRadio => {
      paymentMethodRadio.addEventListener('change', (e) => {
        if (e.target.getAttribute('id') === paylineOptionID) {
          wasPaylineBefore = true;

          //--> Hide the command button
          if (paymentConfirmation) {
            paymentConfirmation.style.visibility = "hidden";
          }

          //--> Init payment buttons state
          setPaylineWidgetState();

          //--> Add event listener to agreements
          Array.from(agreements).forEach(agreement => {
            agreement.addEventListener('change', setPaylineWidgetState);
          });
        } else {
          //--> Clean up
          if ( wasPaylineBefore === true ) {

            //--> Restore the command button
            if (paymentConfirmation) {
              paymentConfirmation.style.visibility = paymentConfirmationOriginalVisibity;
            }

            //--> Remove event listener to agreements
            Array.from(agreements).forEach(agreement => {
              agreement.removeEventListener('change', setPaylineWidgetState);
            });
            wasPaylineBefore = false;
          }
        }
      });
    });

    customizeWidget();
  }

  function onFinalStateHasBeenReached (e) {
    if ( e.state === "PAYMENT_SUCCESS" ) {
      //--> Redirect to success page
      //--> Ticket is hidden by CSS
      //--> Wait for DOM update to simulate a click on the ticket confirmation button
      window.setTimeout(() => {
        const ticketConfirmationButton = document.getElementById("pl-ticket-default-ticket_btn");
        if ( ticketConfirmationButton ) {
          ticketConfirmationButton.click();
        }
      }, 0);
    }
  }
</script>

{include file="modules/payline/views/templates/front/1.7/widget_css_customization.tpl" payline_widget_customization=$payline_widget_customization }

<section id="content" data-js-selector="{$jsSelector}">
      <div
        id="PaylineWidget"
        data-auto-init="true"
        data-token="{$payline_token}"
        data-template="{$payline_ux_mode}"
        data-embeddedredirectionallowed="true"
        data-event-didshowstate="onDidShowState"
        data-event-finalstatehasbeenreached="onFinalStateHasBeenReached"
      >
      </div>
</section>

<p data-js-selector="{$jsSelector}-not-visible">
{l s='Please accept the Terms and Conditions to proceed with payment.' mod='payline'}
</p>

{foreach from=$payline_assets item=paylineAssetsUrls key=assetType}
  {foreach from=$paylineAssetsUrls item=paylineAssetsUrl}
    {if $assetType == 'js'}
      <script src="{$paylineAssetsUrl}"></script>
    {elseif $assetType == 'css'}
      <link href="{$paylineAssetsUrl}" rel="stylesheet" />
    {/if}
  {/foreach}
{/foreach}
