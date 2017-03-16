{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div>
	<h3>{l s='An error occurred' mod='virtual_pos'}:</h3>
	<ul class="alert alert-danger">
		{foreach from=$errors item='error'}
			<li>{$error|escape:'htmlall':'UTF-8'}.</li>
		{/foreach}
	</ul>
</div>
<div align="right">
	<a href="{$link->getPageLink("$order_process", true)|escape:"html":"UTF-8"}"
		class="exclusive{if $order_process == 'order-opc'}_large{/if}"
		title="{l s='Check out' mod='blockcart'}">
		{l s='Go To Check out' mod='blockcart'}
	</a>
</div>
