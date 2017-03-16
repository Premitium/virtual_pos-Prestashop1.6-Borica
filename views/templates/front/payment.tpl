{capture name=path}
    {l s='Virtual POS Borica Bank Service' mod='virtual_pos'}
{/capture}

<h1 class="page-heading">
{l s='Order summary' mod='virtual_pos'}
</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nb_products <= 0}
    <p class="alert alert-warning">
        {l s='Your shopping cart is empty.' mod='virtual_pos'}
    </p>
{else}
    <form action="{$link->getModuleLink('virtual_pos', 'validation', ['id'=>$total_amount], true)|escape:'html'}" method="post">
    	<div class="box cheque-box">
    		<h3 class="page-subheading">
                {l s='Virtual POS with Borica Bank Service' mod='virtual_pos'}
    		</h3>
    		<p class="cheque-indent">
    			<strong class="dark">
                    {l s='You have chosen to pay with credit or debit card.' mod='virtual_pos'} {l s='Here is a short summary of your order:' mod='virtual_pos'}
    			</strong>
    		</p>
    		<p>
    			- {l s='The total amount of your order is' mod='virtual_pos'}
    			<span id="amount" class="price">{displayPrice price=$total_amount}</span>
                <!-- {if $use_taxes == 1}
                    {l s='(tax incl.)' mod='virtual_pos'}
                {/if} -->
    		</p>
        <p>
          - {l s='The Virual POS supported by Borika Bank Service Payment offers only payments in Bulgarian Leva.' mod='virtual_pos'}
        </p>
    	<!-- <p>
    		-
            {if $currencies|@count > 1}
                {l s='We allow several currencies to be sent via Borika Bank Service Payment.' mod='virtual_pos'}
    			<div class="form-group">
    				<label>{l s='Choose one of the following:' mod='virtual_pos'}</label>
    				<select id="currency_payment" class="form-control" name="currency_payment">
                        {foreach from=$currencies item=currency}
    						<option value="{$currency.id_currency}" {if $currency.id_currency == $cart_currency}selected="selected"{/if}>
                                {$currency.name}
    						</option>
                        {/foreach}
    				</select>
    			</div>
                {else}
                {l s='We allow the following currency to be sent via Borika Bank Service Payment:' mod='virtual_pos'}&nbsp;<b>{$currencies.0.name}</b>
    			<input type="hidden" name="currency_payment" value="{$currencies.0.id_currency}" />
            {/if}
    		</p> -->
    		<p>
    			<!-- - {l s='Brew POS account information will be displayed on the next page.' mod='virtual_pos'}
    			<br /> -->
    			- {l s='Please confirm your order by clicking "I confirm my order"' mod='virtual_pos'}.
          <br />
          - {l s='After you confirm you will be taken to the Borika website to enter your card information"' mod='virtual_pos'}.
    		</p>
    	</div><!-- .cheque-box -->

    	<p class="cart_navigation clearfix" id="cart_navigation">
    		<a
    				class="button-exclusive btn btn-default"
    				href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
    			<i class="icon-chevron-left"></i>{l s='Other payment methods' mod='virtual_pos'}
    		</a>
    		<button
    				class="button btn btn-default button-medium"
    				type="submit">
    			<span>{l s='I confirm my order' mod='virtual_pos'}<i class="icon-chevron-right right"></i></span>
    		</button>
    	</p>
    </form>
{/if}
