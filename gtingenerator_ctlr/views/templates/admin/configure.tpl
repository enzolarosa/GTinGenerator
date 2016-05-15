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
<div class="panel">
	<div class="row moduleconfig-header">
		<div class="col-xs-2 text-right">
			<img src="{$module_dir|escape:'html':'UTF-8'}views/img/ctlr-logo.png" />
		</div>
		<div class="col-xs-7 text-left">
			<h2>{l s='GTin Generator' mod='gtingenerator_ctlr'}</h2>
			<h4>{l s='Automatically creates the code EAN13 and UPC of each product, taking into account your store nationwide' mod='gtingenerator_ctlr'}</h4>
		</div>
	</div>
	<hr />
	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-12">
				<p class="text-center">
					<h4>{l s='Generates for all Products' mod='gtingenerator_ctlr'}</h4>
					<form action="#" method="post">
						<div class="form-group">
                            <label>{l s='Clean & Generates' mod='gtingenerator_ctlr'}</label>
                            <input class="form-control" type="checkbox" name="new" value="1" />
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success" type="submit">{l s='Generate' mod='gtingenerator_ctlr'}</button>
                        </div>
					</form>
				</p>
				<br />
				<p class="text-center">
					<strong>
						<a href="http://www.ctlr.it" target="_blank" title="ctlr - Sviluppo Software Personalizzato">
							{l s='ctlr - Sviluppo Software Personalizzato' mod='gtingenerator_ctlr' }
						</a>
					</strong>
				</p>
			</div>
		</div>
	</div>
</div>