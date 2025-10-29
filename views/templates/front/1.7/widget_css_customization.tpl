<style type="text/css">
    #PaylineWidget .pl-text-under-cta { text-align: center; margin-top: 26px; }

{if $payline_widget_customization.cta_bg_color == 'hexadecimal'}
    #PaylineWidget .pl-pay-btn { background-color: {{$payline_widget_customization.cta_bg_color_hexadecimal}}; }
{elseif $payline_widget_customization.cta_bg_color}
    #PaylineWidget .pl-pay-btn { background-color: {{$payline_widget_customization.cta_bg_color}}; }
{/if}

{if $payline_widget_customization.cta_bg_color_hover }
    #PaylineWidget .pl-pay-btn:hover { background-color: {{$payline_widget_customization.cta_bg_color_hover}}; }
{else }
    #PaylineWidget .pl-pay-btn:hover { background-color: #1c7b27; }
{/if}

{if $payline_widget_customization.cta_text_color }
    #PaylineWidget .pl-pay-btn { color: {{$payline_widget_customization.cta_text_color}}; }
{/if}

{assign var="fontSize" value=""}
    {if $payline_widget_customization.font_size == 'small'}
        {assign var="fontSize" value="14px"}
    {elseif $payline_widget_customization.font_size == 'average'}
        {assign var="fontSize" value="20px"}
    {elseif $payline_widget_customization.font_size == 'big'}
        {assign var="fontSize" value="24px"}
{/if}

{if $fontSize }
    #PaylineWidget .pl-pay-btn { font-size: {{$fontSize}}; }
{/if}

{assign var="borderRadius" value=""}
{if $payline_widget_customization.border_radius == 'none'}
    {assign var="borderRadius" value="0"}
{elseif $payline_widget_customization.border_radius == 'small'}
    {assign var="borderRadius" value="6px"}
{elseif $payline_widget_customization.border_radius == 'average'}
    {assign var="borderRadius" value="8px"}
{elseif $payline_widget_customization.border_radius == 'big'}
    {assign var="borderRadius" value="24px"}
{/if}
{if $borderRadius!=='' }
    #PaylineWidget .pl-pay-btn { border-radius: {{$borderRadius}}; }
{/if}

{assign var="widgetBgColor" value=""}
{if $payline_widget_customization.bg_color == 'lighter'}
    {assign var="widgetBgColor" value="#fefefe"}
{elseif $payline_widget_customization.bg_color == 'darker'}
    {assign var="widgetBgColor" value="#dfdfdf"}
{/if}

{if $widgetBgColor }
    #PaylineWidget.PaylineWidget.pl-layout-tab .pl-paymentMethods { background-color: {{$widgetBgColor}}; }
    #PaylineWidget.PaylineWidget.pl-container-default .pl-pmContainer { background-color: {{$widgetBgColor}}; }
    #PaylineWidget.PaylineWidget.pl-layout-tab .pl-tab.pl-active { background-color: {{$widgetBgColor}}; }
{/if}
</style>