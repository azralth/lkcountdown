<?php
/**
 *  Copyright (C) Lk Interactive - All Rights Reserved.
 *
 *  This is proprietary software therefore it cannot be distributed or reselled.
 *  Unauthorized copying of this file, via any medium is strictly prohibited.
 *  Proprietary and confidential.
 *
 * @author    Lk Interactive <contact@lk-interactive.fr>
 * @copyright 2020.
 * @license   Commercial license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Lkcountdown extends Module
{
    private $sessionTime;
    private $activeModule;

    /**
     * Lk_Neonegoce constructor.
     */
    public function __construct()
    {
        $this->name = 'lkcountdown';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Lk Interactive';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.7.6.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Lk Interactive - Countdown.', array(), 'Modules.Lkcountdown.Admin');
        $this->description = $this->trans('This add coutdown in home.', array(), 'Modules.Lkcountdown.Admin');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall ?', 'Lkcountdown');
    }

    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';
        $db = DB::getInstance();

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $result = $db->insert('lk_countdown', [
                'id_lang' => (int) $lang['id_lang'],
                'text' => pSQL('votre text de description'),
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s'),
            ]);
        }

        return (parent::install()
            && Configuration::updateValue('LKCOUNTDOWN_DATE', '2021-08-25 10:30:30')
            && Configuration::updateValue('LKCOUNTDOWN_NB_DAY', 3)
            && $this->registerHook('actionFrontControllerAfterInit')
            && $this->registerHook('header')
        );
    }

    public function uninstall()
    {
        include dirname(__FILE__) . '/sql/uninstall.php';
        if (!parent::uninstall() ||
            !Configuration::deleteByName('LKCOUNTDOWN_DATE') ||
            !Configuration::deleteByName('LKCOUNTDOWN_NB_DAY')) {
            return false;
        }
        return true;
    }

    public function getContent()
    {

        if (((bool)Tools::isSubmit('submitLkCountdownConf')) == true) {
            $this->postProcess();
        }

        $output = $this->renderForm();

        return $output;
    }

    protected function postProcess()
    {
        if (Tools::isSubmit('submitLkCountdownConf')) {
            $languages = Language::getLanguages(false);
            $values = [];

            foreach ($languages as $lang) {
                DB::getInstance()->update(
                    'lk_countdown',
                    array(
                        'text' => Tools::getValue('LKCOUNTDOWN_TEXT_'.$lang['id_lang']),
                        'id_lang' => $lang['id_lang'],
                        'date_upd' => date('Y-m-d H:i:s'),
                    ),
                    'id_lang = '.$lang['id_lang'].''
                );
            }

            Configuration::updateValue('LKCOUNTDOWN_DATE', Tools::getValue('LKCOUNTDOWN_DATE'));
            Configuration::updateValue('LKCOUNTDOWN_NB_DAY', Tools::getValue('LKCOUNTDOWN_NB_DAY'));

            return $this->displayConfirmation($this->trans('The settings have been updated.', array(), 'Admin.Notifications.Success'));
        }

        return '';
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
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLkCountdownConf';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
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
                    'title' => $this->trans('Countdown settings', array(), 'Modules.Lkcountdown.Admin'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'datetime',
                        'label' => $this->trans('Open date shop', array(), 'Modules.Lkcountdown.Admin'),
                        'name' => 'LKCOUNTDOWN_DATE',
                        'desc' => $this->trans('The date before your shop will open', array(), 'Modules.Lkcountdown.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('days number for hours view', array(), 'Modules.Lkcountdown.Admin'),
                        'name' => 'LKCOUNTDOWN_NB_DAY',
                        'desc' => $this->trans('Days number before the coutdown is displaying only by hours', array(), 'Modules.Lkcountdown.Admin'),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->trans('Text countdown', array(), 'Modules.Lkcountdown.Admin'),
                        'name' => 'LKCOUNTDOWN_TEXT',
                        'cols' => 40,
                        'rows' => 10,
                        'autoload_rte' => true,
                        'class' => 'rte',
                        'lang' => true,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * @return array
     */
    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages(false);
        $fields = array();

        foreach ($languages as $lang) {
            $value = DB::getInstance()->getValue('SELECT `text` FROM '._DB_PREFIX_.'lk_countdown WHERE id_lang = '.$lang['id_lang'].'');
            $fields['LKCOUNTDOWN_TEXT'][$lang['id_lang']] = Tools::getValue('LKCOUNTDOWN_TEXT_'.$lang['id_lang'], $value);
        }
        $fields['LKCOUNTDOWN_DATE'] = Configuration::get('LKCOUNTDOWN_DATE');
        $fields['LKCOUNTDOWN_NB_DAY'] = Configuration::get('LKCOUNTDOWN_NB_DAY');

        return $fields;
    }

    public function hookHeader()
    {
        $this->context->controller->registerStylesheet('lk-countdown-css', 'modules/' . $this->name . '/assets/css/lkcountdown.css');
        $this->context->controller->registerJavascript('lk-countdown-js', 'modules/' . $this->name . '/assets/js/lkcountdown.js', ['position' => 'bottom', 'priority' => 150]);
    }

    public function hookActionFrontControllerAfterInit($params)
    {
        $controller = isset($this->context->controller->page_name);

        $open_date = new DateTime(Configuration::get('LKCOUNTDOWN_DATE'));
        $nbDays = Configuration::get('LKCOUNTDOWN_NB_DAY');
        $now = new DateTime();

        if ($now > $open_date) {
            return false;
        }

        if($controller != 'module-lkcountdown-countdown') {
            Tools::redirect(Context::getContext()->link->getModuleLink($this->name, 'countdown'));
        }
    }
}