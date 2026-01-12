{*
*
* Payline module for PrestaShop
*
* @author    Monext <support@payline.com>
* @copyright Monext - http://www.payline.com
*
*}

<a class="col-md-6 col-lg-4 {if $page.page_name == 'module-payline-subscriptions'} active{/if}" id="payline-subscriptions-link" href="{$subscriptionControllerLink}">
    <span class="link-item">
        <i class="material-icons">subscriptions</i>
        {l s='Subscriptions' mod='payline'}
    </span>
</a>

{if $walletIsEnable}
<a class="col-md-6 col-lg-4 {if $page.page_name == 'module-payline-wallet'} active{/if}" id="payline-wallet-link" href="{$walletControllerLink}">
    <span class="link-item">
        <i class="material-icons">account_balance_wallet</i>
        {l s='My wallet' mod='payline'}
    </span>
</a>
{/if}
