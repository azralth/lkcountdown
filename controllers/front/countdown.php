<?php

/**
 * Lk Countdown
 *
 * NOTICE OF LICENSE
 *
 * This product is licensed for one customer to use on one installation (test stores and multishop included).
 * Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
 * whole or in part. Any other use of this module constitues a violation of the user agreement.
 *
 * DISCLAIMER
 *
 * NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
 * ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
 * WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
 * PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
 * IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
 *
 *  @author    lk-interactive
 *  @copyright 2021 lk-interactive
 *  @license   See above
 */

if (!defined('_PS_VERSION_'))
    exit;


class lkcountdownCountdownModuleFrontController extends ModuleFrontController {
    public function __construct() {
        parent::__construct();

        $this->context = Context::getContext();
    }

    public function initContent()
    {
        $this->display_column_right = false;
        $this->display_column_left = false;
        parent::initContent();

        $open_date = new DateTime(Configuration::get('LKCOUNTDOWN_DATE'));
        $nbDays = Configuration::get('LKCOUNTDOWN_NB_DAY');
        $now = new DateTime();

        if ($now > $open_date) {
            Tools::redirect(__PS_BASE_URI__);
        }

        $languages = Language::getLanguages(false);
        $fields = [];
        foreach ($languages as $lang) {
            $value = DB::getInstance()->getValue('SELECT `text` FROM '._DB_PREFIX_.'lk_countdown WHERE id_lang = '.$lang['id_lang'].'');
            $fields['open_date_text'][$lang['id_lang']] = $value;
        }

        // Chek the days separet now and opendate
        $diff = $now->diff($open_date);

        $this->context->smarty->assign(array(
            'id_lang' => $this->context->language->id,
            'open_date' => $open_date->format(DateTime::ATOM),
            'open_text' => $fields['open_date_text'],
            'coutdown_format' => $diff->d < $nbDays ? 'hour' : 'classic'
        ));

        $this->setTemplate('module:lkcountdown/views/templates/front/countdown.tpl');
    }

}