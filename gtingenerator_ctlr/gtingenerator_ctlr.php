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

class Gtingenerator_ctlr extends Module
{
    protected $config_form = false;
    public function __construct()
    {
        $this->name = 'gtingenerator_ctlr';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'CTLR di La Rosa Vincenzo';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('GTIN Generator');
        $this->description = $this->l('Generate automatic gtin number for your product');
    }
    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('GTINGENERATOR_CTLR_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionProductAdd') &&
            $this->registerHook('actionProductSave') &&
            $this->registerHook('actionProductUpdate');
    }
    public function uninstall()
    {
        Configuration::deleteByName('GTINGENERATOR_CTLR_LIVE_MODE');

        return parent::uninstall();
    }
    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitGtingenerator_ctlrModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output;
    }
    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGtingenerator_ctlrModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }
    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'GTINGENERATOR_CTLR_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'GTINGENERATOR_CTLR_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'GTINGENERATOR_CTLR_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }
    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'GTINGENERATOR_CTLR_LIVE_MODE' => Configuration::get('GTINGENERATOR_CTLR_LIVE_MODE', true),
            'GTINGENERATOR_CTLR_ACCOUNT_EMAIL' => Configuration::get('GTINGENERATOR_CTLR_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'GTINGENERATOR_CTLR_ACCOUNT_PASSWORD' => Configuration::get('GTINGENERATOR_CTLR_ACCOUNT_PASSWORD', null),
        );
    }
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
    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }
    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookActionProductAdd($params)
    {
        $id = $params['id_product'];
        static::makeEanUpcProduct($id);
    }
    public function hookActionProductSave($params)
    {
        $id = $params['id_product'];
        static::makeEanUpcProduct($id);
    }

    public function hookActionProductUpdate($params)
    {
        $id = $params['id_product'];
        static::makeEanUpcProduct($id);
    }

    private static function makeEanUpcProduct($id){
        $product = new Product($id);
        Db::getInstance()->update('stock', array(
            'ean13'     => self::gtin_make(@$product->ean13, 13),
            'upc'        => self::gtin_make(@$product->upc),
        ), 'id_product = '.(int)$id);
    }
    private static function gtin_make($c = "", $l = "12"){
        $start = $l == 12 ? "100000000000" : "1000000000000";
        $stop  = $l == 12 ? "999999999999" : "9999999999999";

        $base = $c == "" ? rand($start,$stop) : $c;
        $len = strlen($base);
        if ($len >= $l){
            $base = substr($base, 0,strlen($base)-1);
            return self::gtin_make($base,$l);
        }
        $base[$l-1] = self::gtin_check($base,$l);
        return (string) $base;

    }
    private static function gtin_check($str,$l){
        $len = strlen($str);
        if ($len >= $l){
            $str = substr($str, 0,$len-1);
            return self::gtin_check($str);
        }
        $sum = 0;
        for ($i = 0; $i < $len ;$i++){
            if ($i % 2 == 0){
                //echo "POSIZIONE ".($i+1).": {$str[$i]} * 1 = {$str[$i]}";
                $sum += (int) $str[$i];
                //echo " | Nuovo valore della somma $sum<br />";

            }else{
                //echo "POSIZIONE ".($i+1).": {$str[$i]} * 3 = ".$str[$i]*3;
                $sum += (int) $str[$i] * 3;
                //echo " | Nuovo valore della somma $sum<br />";
            }
        }
        $r = self::arrotonda($sum,10);
        //$dif = $r-$sum;
        //echo "Il valore della somma &egrave; $sum il valore più vicino &egrave; $r la sua differenza è uguale a $dif<br />";
        return (string) $r - $sum;
    }
    private static function arrotonda($number, $significance = 1){
        return ( is_numeric($number) && is_numeric($significance) ) ? (ceil($number/$significance)*$significance) : false;
    }
}