<script>
  function customizeWidget() {

    const ctaLabel = "{$payline_widget_customization.cta_label|strip_tags|escape:'javascript'}";
    const textUnderCta = "{$payline_widget_customization.text_under_cta|strip_tags|escape:'javascript'}";

    if (ctaLabel !== "") {
        document.querySelectorAll('.PaylineWidget .pl-pay-btn, .PaylineWidget .pl-btn').forEach(paylineCTA => {
            paylineCTA.innerHTML = ctaLabel.replace("[[amount]]", Payline.Api.getContextInfo("PaylineFormattedAmount"));
        });
    }

    if (textUnderCta) {
        document.querySelectorAll('.PaylineWidget .pl-pay-btn, .PaylineWidget .pl-btn').forEach(function(btn) {
            const p = document.createElement('p');
            p.innerHTML = textUnderCta;
            p.classList.add('pl-text-under-cta');
            btn.parentNode.insertBefore(p, btn.nextSibling);
        });
    }
  }
</script>