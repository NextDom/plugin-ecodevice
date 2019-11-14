<?php
/*
 * This file is part of the NextDom software (https://github.com/NextDom or http://nextdom.github.io).
 * Copyright (c) 2018 NextDom.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 2.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
class ecodeviceCmd extends cmd
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*     * **********************Getteur Setteur*************************** */
    public function execute($_options = null) {
		$eqLogic = $this->getEqLogic();
        if (!is_object($eqLogic) || $eqLogic->getIsEnable() != 1) {
            throw new Exception(__('Equipement desactivé impossible d\éxecuter la commande : ' . $this->getHumanName(), __FILE__));
        }
		$url = $eqLogic->getUrl();

		if ( $this->getLogicalId() == 'reboot' )
		{
			$url .= "protect/settings/reboot.htm";
		}
		else
			return false;
		log::add('ecodevice','debug','get '.preg_replace("/:[^:]*@/", ":XXXX@", $url).'?'.http_build_query($data));
		$result = @file_get_contents($url.'?'.http_build_query($data));
		$count = 0;
		while ( $result === false )
		{
			$result = @file_get_contents($url.'?'.http_build_query($data));
			if ( $count < 3 ) {
				log::add('ecodevice','error',__('L\'ecodevice ne repond pas.',__FILE__)." ".$this->getName()." get ".preg_replace("/:[^:]*@/", ":XXXX@", $url)."?".http_build_query($data));
				throw new Exception(__('L\'ecodevice ne repond pas.',__FILE__)." ".$this->getName());
			}
			$count ++;
		}
        return false;
    }

	public function dontRemoveCmd() {
		return true;
	}
}
