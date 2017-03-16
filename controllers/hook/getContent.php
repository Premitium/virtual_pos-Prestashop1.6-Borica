<?php

class Virtual_posGetContentController
{
	public function __construct($module, $file, $path)
	{
		$this->file = $file;
		$this->module = $module;
		$this->context = Context::getContext(); $this->_path = $path;
	}

	public function processConfiguration()
	{
		if (Tools::isSubmit('virtual_pos_payment_form'))
		{
			Configuration::updateValue('VIRTUAL_POS_CH_ORDER', Tools::getValue('VIRTUAL_POS_CH_ORDER'));
			Configuration::updateValue('VIRTUAL_POS_CH_ADDRESS', Tools::getValue('VIRTUAL_POS_CH_ADDRESS'));
			Configuration::updateValue('VIRTUAL_POS_BA_OWNER', Tools::getValue('VIRTUAL_POS_BA_OWNER'));
			Configuration::updateValue('VIRTUAL_POS_BA_DETAILS', Tools::getValue('VIRTUAL_POS_BA_DETAILS'));
			Configuration::updateValue('VIRTUAL_POS_API_URL', Tools::getValue('VIRTUAL_POS_API_URL'));
			Configuration::updateValue('VIRTUAL_POS_API_CRED_ID', Tools::getValue('VIRTUAL_POS_API_CRED_ID'));
			Configuration::updateValue('VIRTUAL_POS_API_CRED_SALT', Tools::getValue('VIRTUAL_POS_API_CRED_SALT'));
			$this->context->smarty->assign('confirmation', 'ok');
		}
	}

	public function renderForm()
	{
		$inputs = array(
			array('name' => 'VIRTUAL_POS_CH_ORDER', 'label' => $this->module->l('Check order'), 'type' => 'text'),
			array('name' => 'VIRTUAL_POS_CH_ADDRESS', 'label' => $this->module->l('Check address'), 'type' => 'textarea'),
			array('name' => 'VIRTUAL_POS_BA_OWNER', 'label' => $this->module->l('Bankwire owner'), 'type' => 'text'),
			array('name' => 'VIRTUAL_POS_BA_DETAILS', 'label' => $this->module->l('Bankwire details'), 'type' => 'textarea'),
			array('name' => 'VIRTUAL_POS_API_URL', 'label' => $this->module->l('API URL'), 'type' => 'text'),
			array('name' => 'VIRTUAL_POS_API_CRED_ID', 'label' => $this->module->l('API credentials ID'), 'type' => 'text'),
			array('name' => 'VIRTUAL_POS_API_CRED_SALT', 'label' => $this->module->l('API credentials SALT'), 'type' => 'text'),
		);

		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->module->l('VIRTUAL POS configuration'),
					'icon' => 'icon-wrench'
				),
				'input' => $inputs,
				'submit' => array('title' => $this->module->l('Save'))
			)
		);

		$helper = new HelperForm();
		$helper->table = 'virtual_pospayment';
		$helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$helper->allow_employee_form_lang = (int)Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
		$helper->submit_action = 'virtual_pos_payment_form';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->module->name.'&tab_module='.$this->module->tab.'&module_name='.$this->module->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => array(
				'VIRTUAL_POS_CH_ORDER' => Tools::getValue('VIRTUAL_POS_CH_ORDER', Configuration::get('VIRTUAL_POS_CH_ORDER')),
				'VIRTUAL_POS_CH_ADDRESS' => Tools::getValue('VIRTUAL_POS_CH_ADDRESS', Configuration::get('VIRTUAL_POS_CH_ADDRESS')),
				'VIRTUAL_POS_BA_OWNER' => Tools::getValue('VIRTUAL_POS_BA_OWNER', Configuration::get('VIRTUAL_POS_BA_OWNER')),
				'VIRTUAL_POS_BA_DETAILS' => Tools::getValue('VIRTUAL_POS_BA_DETAILS', Configuration::get('VIRTUAL_POS_BA_DETAILS')),
				'VIRTUAL_POS_API_URL' => Tools::getValue('VIRTUAL_POS_API_URL', Configuration::get('VIRTUAL_POS_API_URL')),
				'VIRTUAL_POS_API_CRED_ID' => Tools::getValue('VIRTUAL_POS_API_CRED_ID', Configuration::get('VIRTUAL_POS_API_CRED_ID')),
				'VIRTUAL_POS_API_CRED_SALT' => Tools::getValue('VIRTUAL_POS_API_CRED_SALT', Configuration::get('VIRTUAL_POS_API_CRED_SALT')),
			),
			'languages' => $this->context->controller->getLanguages()
		);

		return $helper->generateForm(array($fields_form));
	}

	public function run()
	{
		$this->processConfiguration();
		$html_confirmation_message = $this->module->display($this->file, 'getContent.tpl');
		$html_form = $this->renderForm();
		return $html_confirmation_message.$html_form;
	}
}
