<?php
/**
 *  Copyright (C) Lk Interactive - All Rights Reserved.
 *
 *  This is proprietary software therefore it cannot be distributed or reselled.
 *  Unauthorized copying of this file, via any medium is strictly prohibited.
 *  Proprietary and confidential.
 *
 * @author    Lk Interactive <contact@lk-interactive.fr>
 * @copyright 2007.
 * @license   Commercial license
 */

$sql = array();
// Install table for customer validation
$sql[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "lk_countdown` (
          `id`          int(10) unsigned NOT NULL AUTO_INCREMENT,
          `id_lang`     int(10) unsigned NOT NULL,
          `date_add`    datetime NOT NULL,
          `date_upd`    datetime NOT NULL,
          `text`        TEXT NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=UTF8";

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return $sql;
    }
}
