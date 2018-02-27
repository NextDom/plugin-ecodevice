<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function ecodevice_install() {
    $cron = cron::byClassAndFunction('ecodevice', 'pull');
	if ( ! is_object($cron)) {
        $cron = new cron();
        $cron->setClass('ecodevice');
        $cron->setFunction('pull');
        $cron->setEnable(1);
        $cron->setDeamon(0);
        $cron->setSchedule('* * * * *');
        $cron->save();
	}
	$daemon = cron::byClassAndFunction('ecodevice', 'daemon');
	if (!is_object($daemon)) {
		$daemon = new cron();
		$daemon->setClass('ecodevice');
		$daemon->setFunction('daemon');
		$daemon->setEnable(1);
		$daemon->setDeamon(1);
		$daemon->setTimeout(1440);
		$daemon->setSchedule('* * * * *');
		$daemon->save();
	}
	config::save('temporisation_lecture', 10, 'ecodevice');
	$daemon->start();
	config::save('subClass', 'ecodevice_compteur;ecodevice_teleinfo', 'ecodevice');
	jeedom::getApiKey('ecodevice');
	if (config::byKey('api::ecodevice::mode') == '') {
		config::save('api::ecodevice::mode', 'enable');
	}
}

function ecodevice_update() {
    $cron = cron::byClassAndFunction('ecodevice', 'pull');
	if ( ! is_object($cron)) {
        $cron = new cron();
        $cron->setClass('ecodevice');
        $cron->setFunction('pull');
        $cron->setEnable(1);
        $cron->setDeamon(0);
        $cron->setSchedule('* * * * *');
        $cron->save();
	}
    $cron = cron::byClassAndFunction('ecodevice', 'cron');
	if (is_object($cron)) {
		$cron->stop();
		$cron->remove();
	}
	if ( config::byKey('temporisation_lecture', 'ecodevice', '') == "" )
	{
		config::save('temporisation_lecture', 10, 'ecodevice');
	}
	config::remove('listChildren', 'ecodevice');
	config::remove('subClass', 'ecodevice');
	$FlagBasculeClass = false;
	foreach (eqLogic::byType('ecodevice_teleinfo') as $SubeqLogic) {
		$SubeqLogic->setConfiguration('type', 'teleinfo');
		$SubeqLogic->setEqType_name('ecodevice');
		$SubeqLogic->save();
		foreach (cmd::byEqLogicId($SubeqLogic->getId()) as $cmd) {
			$cmd->setEqType('ecodevice');
			$cmd->save();
		}
	}
	foreach (eqLogic::byType('ecodevice_compteur') as $SubeqLogic) {
		$SubeqLogic->setConfiguration('type', 'compteur');
		$SubeqLogic->setEqType_name('ecodevice');
		$SubeqLogic->save();
		foreach (cmd::byEqLogicId($SubeqLogic->getId()) as $cmd) {
			$cmd->setEqType('ecodevice');
			$cmd->save();
		}
	}
	foreach (eqLogic::byType('ecodevice') as $eqLogic) {
		if ( $eqLogic->getConfiguration('type', '') == '' )
		{
			$eqLogic->setConfiguration('type', 'carte');
			$eqLogic->save();
		}
		foreach (cmd::byEqLogicId($eqLogic->getId()) as $cmd) {
			if ( $cmd->getEqType() != 'ecodevice')
			{
				$cmd->setEqType('ecodevice');
				$cmd->save();
			}
		}
	}
	foreach (eqLogic::byType('ecodevice') as $eqLogic) {
		if ( $eqLogic->getConfiguration('type', '') == 'compteur' )
		{
			if ( $eqLogic->getIsEnable() )
			{
				$eqLogic->postAjax();
			}
		}
		$eqLogic->save();
	}
	jeedom::getApiKey('ecodevice');
	if (config::byKey('api::ecodevice::mode') == '') {
		config::save('api::ecodevice::mode', 'enable');
	}
	foreach (array("compteur", "teleinfo") as $type)
	{
		if (file_exists (dirname(__FILE__) . '/../core/class/ecodevice_'.$type.'.class.php'))
			unlink(dirname(__FILE__) . '/../core/class/ecodevice_'.$type.'.class.php');
		if (file_exists (dirname(__FILE__) . '/../desktop/php/ecodevice_'.$type.'.php'))
			unlink(dirname(__FILE__) . '/../desktop/php/ecodevice_'.$type.'.php');
	}
	$daemon = cron::byClassAndFunction('ecodevice', 'daemon');
	if (!is_object($daemon)) {
		$daemon = new cron();
		$daemon->setClass('ecodevice');
		$daemon->setFunction('daemon');
		$daemon->setEnable(1);
		$daemon->setDeamon(1);
		$daemon->setTimeout(1440);
		$daemon->setSchedule('* * * * *');
		$daemon->save();
		$daemon->start();
	}
	else
	{
		ecodevice::deamon_start();
	}
}

function ecodevice_remove() {
    $daemon = cron::byClassAndFunction('ecodevice', 'daemon');
    if (is_object($daemon)) {
        $daemon->remove();
    }
    $cron = cron::byClassAndFunction('ecodevice', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
	config::remove('subClass', 'ecodevice');
	config::remove('listChildren', 'ecodevice');
	config::remove('temporisation_lecture', 'ecodevice');
}
?>