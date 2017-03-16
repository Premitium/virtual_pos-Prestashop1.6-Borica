<?php

class Virtual_posDisplayPaymentReturnController
{
	public function __construct($module, $file, $path)
	{
		$this->file = $file;
		$this->module = $module;
		$this->context = Context::getContext();
		$this->_path = $path;
	}

	public function run($params)
	{
		if ($params['objOrder']->payment != $this->module->displayName)
			return '';

		$reference = $params['objOrder']->id;
		if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference))
			$reference = $params['objOrder']->reference;
		$total_to_pay = Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false);

		$this->context->smarty->assign(array(
			'VIRTUAL_POS_CH_ORDER' => Configuration::get('VIRTUAL_POS_CH_ORDER'),
			'VIRTUAL_POS_CH_ADDRESS' => Configuration::get('VIRTUAL_POS_CH_ADDRESS'),
			'VIRTUAL_POS_BA_OWNER' => Configuration::get('VIRTUAL_POS_BA_OWNER'),
			'VIRTUAL_POS_BA_DETAILS' => Configuration::get('VIRTUAL_POS_BA_DETAILS'),
			'reference' => $reference,
			'total_to_pay' => $total_to_pay,
		));

		return $this->module->display($this->file, 'displayPaymentReturn.tpl');
	}
}
