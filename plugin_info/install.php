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
	$cron = cron::byClassAndFunction('ecodevice', 'daemon');
	if (!is_object($cron)) {
		$cron = new cron();
		$cron->setClass('ecodevice');
		$cron->setFunction('daemon');
		$cron->setEnable(1);
		$cron->setDeamon(1);
		$cron->setTimeout(1440);
		$cron->setSchedule('* * * * *');
		$cron->save();
	}
	config::save('temporisation_lecture', 5, 'ecodevice');
	$cron->start();
	config::save('subClass', 'ecodevice_compteur;ecodevice_teleinfo', 'ecodevice');
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
	foreach (eqLogic::byType('ecodevice') as $eqLogic) {
		$eqLogic->save();
	}
	foreach (eqLogic::byType('ecodevice_teleinfo') as $SubeqLogic) {
		$SubeqLogic->save();
	}
	foreach (eqLogic::byType('ecodevice_compteur') as $SubeqLogic) {
		$SubeqLogic->save();
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
		$daemon->halt();
		$daemon->start();
	}
	if ( config::byKey('temporisation_lecture', 'ecodevice', '') == "" )
	{
		config::save('temporisation_lecture', 5, 'ecodevice');
	}
	config::save('subClass', 'ecodevice_compteur;ecodevice_teleinfo', 'ecodevice');
}

function ecodevice_remove() {
    $cron = cron::byClassAndFunction('ecodevice', 'daemon');
    if (is_object($cron)) {
        $cron->remove();
    }
    $cron = cron::byClassAndFunction('ecodevice', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
	config::remove('subClass', 'ecodevice');
}
?>