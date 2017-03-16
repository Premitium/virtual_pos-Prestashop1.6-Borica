<div class="box">
	<p class="cheque-indent">
		<strong class="dark">{l s='Your order on %s is complete.' sprintf=$shop_name mod='virtual_pos'}</strong>
	</p><br>

	<p>
        {l s='Please make a check of %s payable to the order of' sprintf=$total_to_pay mod='virtual_pos'} <strong>{$VIRTUAL_POS_CH_ORDER}</strong><br>
		{l s='And send it to:' mod='virtual_pos'} <span class="price"> <strong>{$VIRTUAL_POS_CH_ADDRESS}</strong></span>
	</p><br>

    <p>
        {l s='Or send us a bank wire with' mod='virtual_pos'}<br>
        - {l s='Amount' mod='virtual_pos'} <span class="price"> <strong>{$total_to_pay}</strong></span><br>
        - {l s='Name of account owner' mod='virtual_pos'}  <strong>{$VIRTUAL_POS_BA_OWNER}</strong><br>
        - {l s='Include these details:' mod='virtual_pos'}  <strong>{$VIRTUAL_POS_BA_DETAILS}</strong>
    </p><br>

    <p>
    {if !isset($reference)}
	    {l s='Do not forget to insert your order number #%d in the subject of your bank wire' sprintf=$id_order mod='virtual_pos'}
    {else}
	    {l s='Do not forget to insert your order reference %s in the subject of your bank wire.' sprintf=$reference mod='virtual_pos'}
    {/if}
	</p>

	<p><strong>{l s='Your order will be sent as soon as we receive payment.' mod='virtual_pos'}</strong></p><br>
	<p>{l s='If you have questions, comments or concerns, please contact our' mod='virtual_pos'} <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='expert customer support team.' mod='virtual_pos'}</a>.</p>
</div>
