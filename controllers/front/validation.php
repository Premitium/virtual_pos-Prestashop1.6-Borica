<?php

require_once(dirname(__FILE__).'/../../classes/eBorica.php');

class Virtual_posValidationModuleFrontController extends ModuleFrontController
{


  public function initContent()
  {
    // Disable left and right column
    $this->display_column_left = false;
    $this->display_column_right = false;

    //Call parent init content method
    parent::initContent();
  }


  public function postProcess()
  {
      $eBorica = new eBorica();
      //Get user and user cart.
      $customer_id = (int)$this->context->cookie->id_customer;
      $cart_id = $this->context->cart->id;
      $total_amount = $this->context->cart->getOrderTotal(true);

      $amount = $total_amount;
      $transaction_id = uniqid(true);
      $description = 'Wineo order';

      if (isset($_GET['eBorica']))
      {
        $response_data = $eBorica->read_response($_GET['eBorica']);
        $transaction_id = $response_data['transaction_id'];
        $transaction_info = $eBorica->get_transaction_info($transaction_id);

        $new_transaction_id = $transaction_info['transaction_id'];

        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
          Tools::redirect('index.php?controller=order&step=1');

        // Check if module is enabled
        $authorized = false;
        foreach (Module::getPaymentModules() as $module)
          if ($module['name'] == $this->module->name)
            $authorized = true;
        if (!$authorized)
          die('This payment method is not available.');

        // Check if customer exists
        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
          Tools::redirect('index.php?controller=order&step=1');

        // Set datas
        $currency = $this->context->currency;
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
        $extra_vars = array(
          '{total_to_pay}' => Tools::displayPrice($total),
          '{cheque_order}' => Configuration::get('VIRUAL_POS_CH_ORDER'),
          '{cheque_address}' => Configuration::get('VIRUAL_POS_CH_ADDRESS'),
          '{bankwire_details}' => Configuration::get('VIRUAL_POS_BA_DETAILS'),
          '{bankwire_owner}' => Configuration::get('VIRUAL_POS_BA_OWNER'),
        );

        // Validate order
        if ($response_data['response_code'] == "00")
        {
          $this->module->validateOrder($cart->id, Configuration::get('PS_OS_POS_VIRTUAL_PAYMENT'), $total,
            $this->module->displayName, NULL, $extra_vars, (int)$currency->id, false, $customer->secure_key);
          // Redirect on order confirmation page
          Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.
            $this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
        }else {
          // $this->module->validateOrder($cart->id, Configuration::get('PS_OS_ERROR'), $total,
          //   $this->module->displayName, NULL, $extra_vars, (int)$currency->id, false, $customer->secure_key);

            $this->errors[] = $this->module->l('An error occured. Please contact the merchant to have more information');

            $this->context->smarty->assign(array(
              'order_process' => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order'
            ));
            return $this->setTemplate('error.tpl');
        }

        if (isset($_GET['cancel']))
        {
          $cancel_transaction_info = $eBorica->cancel_transaction($new_transaction_id,
              $amount, $description);

          print_r($cancel_transaction_info);
        }
      }
      else
      {
        $eBorica->add_transaction($amount, $transaction_id, $description);
        $eBorica->run();
      }
    }
}
