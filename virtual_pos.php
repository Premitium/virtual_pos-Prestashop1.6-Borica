<?php
/**
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
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Virtual_pos extends PaymentModule
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'virtual_pos';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Simeon Parvanov';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Virtual POS Brewforia');
        $this->description = $this->l('Pay by credit or debit card directly from the webstore. RBB Bank ');

        // $this->limited_countries = array('FR');
        //
        // $this->limited_currencies = array('EUR');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (extension_loaded('curl') == false)
        {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }

        if (!$this->installOrderState())
          return false;

        // $iso_code = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));
        //
        // if (in_array($iso_code, $this->limited_countries) == false)
        // {
        //     $this->_errors[] = $this->l('This module is not available in your country');
        //     return false;
        // }

        Configuration::updateValue('VIRTUAL_POS_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('payment') &&
            $this->registerHook('paymentReturn') &&
            $this->registerHook('displayOrderConfirmation') &&
            $this->registerHook('displayPayment')&&
            $this->registerHook('displayPaymentReturn');
    }

    public function uninstall()
    {
        Configuration::deleteByName('VIRTUAL_POS_LIVE_MODE');

        return parent::uninstall();
    }

    public function installOrderState()
    {
      if (Configuration::get('PS_OS_POS_VIRTUAL_PAYMENT') < 1)
      {
        $order_state = new OrderState();
        $order_state->send_email = true;
        $order_state->module_name = $this->name;
        $order_state->invoice = false;
        $order_state->color = '#98c3ff';
        $order_state->logable = true;
        $order_state->shipped = false;
        $order_state->unremovable = false;
        $order_state->delivery = false;
        $order_state->hidden = false;
        $order_state->paid = false;
        $order_state->deleted = false;
        $order_state->name = array((int)Configuration::get('PS_LANG_DEFAULT') => pSQL($this->l('Virtual POS - Awaiting confirmation')));
        $order_state->template = array();
        foreach (LanguageCore::getLanguages() as $l)
          $order_state->template[$l['id_lang']] = 'virtual_pos';
        // We copy the mails templates in mail directory
        foreach (LanguageCore::getLanguages() as $l)
        {
          $module_path = dirname(__FILE__).'/views/templates/mails/'.$l['iso_code'].'/';
          $application_path = dirname(__FILE__).'/../../mails/'.$l['iso_code'].'/';
          if (!copy($module_path.'virtual_pos.txt',
          $application_path.'virtual_pos.txt') ||
            !copy($module_path.'virtual_pos.html',
          $application_path.'virtual_pos.html'))
            return false;
        }
        if ($order_state->add())
        {
          // We save the order State ID in Configuration database
          Configuration::updateValue('PS_OS_POS_VIRTUAL_PAYMENT', $order_state->id);
          // We copy the module logo in order state logo directory
          copy(dirname(__FILE__).'/logo.gif', dirname(__FILE__).'/../../img/os/'.$order_state->id.'.gif');
          copy(dirname(__FILE__).'/logo.gif', dirname(__FILE__).'/../../img/tmp/order_state_mini_'.$order_state->id.'.gif');
        }
        else
          return false;
      }
      return true;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        // if (((bool)Tools::isSubmit('submitBrew_posModule')) == true) {
        //     $this->postProcess();
        // }
        //
        // $this->context->smarty->assign('module_dir', $this->_path);
        //
        // $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        //
        // return $output.$this->renderForm();
        $controller = $this->getHookController('getContent');
    		return $controller->run();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    // protected function renderForm()
    // {
    //     $helper = new HelperForm();
    //
    //     $helper->show_toolbar = false;
    //     $helper->table = $this->table;
    //     $helper->module = $this;
    //     $helper->default_form_language = $this->context->language->id;
    //     $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
    //
    //     $helper->identifier = $this->identifier;
    //     $helper->submit_action = 'submitBrew_posModule';
    //     $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
    //         .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
    //     $helper->token = Tools::getAdminTokenLite('AdminModules');
    //
    //     $helper->tpl_vars = array(
    //         'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
    //         'languages' => $this->context->controller->getLanguages(),
    //         'id_language' => $this->context->language->id,
    //     );
    //
    //     return $helper->generateForm(array($this->getConfigForm()));
    // }

    /**
     * Create the structure of your form.
     */
    // protected function getConfigForm()
    // {
    //     return array(
    //         'form' => array(
    //             'legend' => array(
    //             'title' => $this->l('Settings'),
    //             'icon' => 'icon-cogs',
    //             ),
    //             'input' => array(
    //                 array(
    //                     'type' => 'switch',
    //                     'label' => $this->l('Live mode'),
    //                     'name' => 'BREW_POS_LIVE_MODE',
    //                     'is_bool' => true,
    //                     'desc' => $this->l('Use this module in live mode'),
    //                     'values' => array(
    //                         array(
    //                             'id' => 'active_on',
    //                             'value' => true,
    //                             'label' => $this->l('Enabled')
    //                         ),
    //                         array(
    //                             'id' => 'active_off',
    //                             'value' => false,
    //                             'label' => $this->l('Disabled')
    //                         )
    //                     ),
    //                 ),
    //                 array(
    //                     'col' => 3,
    //                     'type' => 'text',
    //                     'prefix' => '<i class="icon icon-envelope"></i>',
    //                     'desc' => $this->l('Enter a valid email address'),
    //                     'name' => 'BREW_POS_ACCOUNT_EMAIL',
    //                     'label' => $this->l('Email'),
    //                 ),
    //                 array(
    //                     'type' => 'password',
    //                     'name' => 'BREW_POS_ACCOUNT_PASSWORD',
    //                     'label' => $this->l('Password'),
    //                 ),
    //             ),
    //             'submit' => array(
    //                 'title' => $this->l('Save'),
    //             ),
    //         ),
    //     );
    // }

    /**
     * Set values for the inputs.
     */
    // protected function getConfigFormValues()
    // {
    //     return array(
    //         'BREW_POS_LIVE_MODE' => Configuration::get('BREW_POS_LIVE_MODE', true),
    //         'BREW_POS_ACCOUNT_EMAIL' => Configuration::get('BREW_POS_ACCOUNT_EMAIL', 'contact@prestashop.com'),
    //         'BREW_POS_ACCOUNT_PASSWORD' => Configuration::get('BREW_POS_ACCOUNT_PASSWORD', null),
    //     );
    // }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    // /**
    // * Add the CSS & JavaScript files you want to be loaded in the BO.
    // */
    // public function hookBackOfficeHeader()
    // {
    //     if (Tools::getValue('module_name') == $this->name) {
    //         $this->context->controller->addJS($this->_path.'views/js/back.js');
    //         $this->context->controller->addCSS($this->_path.'views/css/back.css');
    //     }
    // }
    //
    // /**
    //  * Add the CSS & JavaScript files you want to be added on the FO.
    //  */
    // public function hookHeader()
    // {
    //     $this->context->controller->addJS($this->_path.'/views/js/front.js');
    //     $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    // }

    /**
     * This method is used to render the payment button,
     * Take care if the button should be displayed or not.
     */
    public function hookPayment($params)
    {
        $currency_id = $params['cart']->id_currency;
        $currency = new Currency((int)$currency_id);

        if (in_array($currency->iso_code, $this->limited_currencies) == false)
            return false;

        $this->smarty->assign('module_dir', $this->_path);

        return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
    }

    /**
     * This hook is used to display the order confirmation page.
     */
    public function hookPaymentReturn($params)
    {
        if ($this->active == false)
            return;

        $order = $params['objOrder'];

        if ($order->getCurrentOrderState()->id != Configuration::get('PS_OS_ERROR'))
            $this->smarty->assign('status', 'ok');

        $this->smarty->assign(array(
            'id_order' => $order->id,
            'reference' => $order->reference,
            'params' => $params,
            'total' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
        ));

        return $this->display(__FILE__, 'views/templates/hook/confirmation.tpl');
    }

    public function hookDisplayOrderConfirmation()
    {
        /* Place your code here. */
    }
    public function getHookController($hook_name)
  	{
  		// Include the controller file
  		require_once(dirname(__FILE__).'/controllers/hook/'. $hook_name.'.php');
  		// Build dynamically the controller name
  		$controller_name = $this->name.$hook_name.'Controller';
  		// Instantiate controller
  		$controller = new $controller_name($this, __FILE__, $this->_path);
  		// Return the controller
  		return $controller;
  	}

    public function hookDisplayPayment($params)
    {
      $controller = $this->getHookController('displayPayment');
      return $controller->run($params);
    }

    public function hookDisplayPaymentReturn($params)
  	{
  		$controller = $this->getHookController('displayPaymentReturn');
  		return $controller->run($params);
  	}
}
