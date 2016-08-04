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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class ecodevice_teleinfo extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */
	public function getListeName()
	{
		return substr($this->getLogicalId(), strpos($this->getLogicalId(),"_")+2, 1)." - ".parent::getName();
	}

	private function getListeDefaultCommandes()
	{
		return array("BASE" => array('Index (base)', 'numeric', 'W', 1, "BASE"),
		"HCHC" => array('Index (heures creuses)', 'numeric', 'W', 1, "HC"),
		"HCHP" => array('Index (heures pleines)', 'numeric', 'W', 1, "HC"),
		"BBRHCJB" => array('Index (heures creuses jours bleus Tempo)', 'numeric', 'W', 0, "BBRH"),
		"BBRHPJB" => array('Index (heures pleines jours bleus Tempo)', 'numeric', 'W', 0, "BBRH"),
		"BBRHCJW" => array('Index (heures creuses jours blancs Tempo)', 'numeric', 'W', 0, "BBRH"),
		"BBRHPJW" => array('Index (heures pleines jours blancs Tempo)', 'numeric', 'W', 0, "BBRH"),
		"BBRHCJR" => array('Index (heures creuses jours rouges Tempo)', 'numeric', 'W', 0, "BBRH"),
		"BBRHPJR" => array('Index (heures pleines jours rouges Tempo)', 'numeric', 'W', 0, "BBRH"),
		"EJPHN" => array('Index (normal EJP)', 'numeric', 'W', 0, "EJP"),
		"EJPHPM" => array('Index (pointe mobile EJP)', 'numeric', 'W', 0, "EJP"),
		"IINST" => array('Intensité instantanée', 'numeric', 'ampere', 1, ""),
		"IINST1" => array('Intensité instantanée 1', 'numeric', 'ampere', 0, ""),
		"IINST2" => array('Intensité instantanée 2', 'numeric', 'ampere', 0, ""),
		"IINST3" => array('Intensité instantanée 3', 'numeric', 'ampere', 0, ""),
		"PPAP" => array('Puissance Apparente', 'numeric', 'W', 1, ""),
		"OPTARIF" => array('Option tarif', 'string', '', 1, ""),
		"DEMAIN" => array('Couleur demain', 'string', '', 0, "BBRH"),
		"PTEC" => array('Tarif en cours', 'string', '', 1, ""),
		"BASE_evolution" => array('Evolution index (base)', 'numeric', 'W/min', 1, "BASE"),
		"HCHC_evolution" => array('Evolution index (heures creuses)', 'numeric', 'W/min', 1, "HC"),
		"HCHP_evolution" => array('Evolution index (heures pleines)', 'numeric', 'W/min', 1, "HC"),
		"BBRHCJB_evolution" => array('Evolution index (heures creuses jours bleus Tempo)', 'numeric', 'W/min', 0, "BBRH"),
		"BBRHPJB_evolution" => array('Evolution index (heures pleines jours bleus Tempo)', 'numeric', 'W/min', 0, "BBRH"),
		"BBRHCJW_evolution" => array('Evolution index (heures creuses jours blancs Tempo)', 'numeric', 'W/min', 0, "BBRH"),
		"BBRHPJW_evolution" => array('Evolution index (heures pleines jours blancs Tempo)', 'numeric', 'W/min', 0, "BBRH"),
		"BBRHCJR_evolution" => array('Evolution index (heures creuses jours rouges Tempo)', 'numeric', 'W/min', 0, "BBRH"),
		"BBRHPJR_evolution" => array('Evolution index (heures pleines jours rouges Tempo)', 'numeric', 'W/min', 0, "BBRH"),
		"EJPHN_evolution" => array('Evolution index (normal EJP)', 'numeric', 'W', 0, "EJP"),
		"EJPHPM_evolution" => array('Evolution index (pointe mobile EJP)', 'numeric', 'W', 0, "EJP"),
		"ISOUSC" => array('Intensité souscrite', 'numeric', 'ampere', 1, ""),
		"IMAX" => array('Intensité maximale', 'numeric', 'ampere', 1, ""),
		"IMAX1" => array('Intensité maximale 1', 'numeric', 'ampere', 0, ""),
		"IMAX2" => array('Intensité maximale 2', 'numeric', 'ampere', 0, ""),
		"IMAX3" => array('Intensité maximale 3', 'numeric', 'ampere', 0, "")
		);
	}

	public function postInsert()
	{
		foreach( $this->getListeDefaultCommandes() as $label => $data)
		{
			if ( $this->getConfiguration('tarification') == $data[4] || $data[4] == "" ) {
				$cmd = $this->getCmd(null, $label);
				if ( ! is_object($cmd) ) {
					$cmd = new ecodevice_teleinfoCmd();
					$cmd->setName($data[0]);
					$cmd->setEqLogic_id($this->getId());
					$cmd->setType('info');
					$cmd->setSubType($data[1]);
					$cmd->setLogicalId($label);
					$cmd->setUnite($data[2]);
					$cmd->setIsVisible($data[3]);
					$cmd->setEventOnly(1);
					$cmd->save();
				}
			} else {
				$cmd = $this->getCmd(null, $label);
				if ( is_object($cmd) ) {
					$cmd->remove();
				}
			}
		}
	}

	public function postUpdate() {
		foreach( $this->getListeDefaultCommandes() as $label => $data)
		{
			if ( $this->getConfiguration('tarification') == "" || $this->getConfiguration('tarification') == $data[4] || $data[4] == "" ) {
				$cmd = $this->getCmd(null, $label);
				if ( ! is_object($cmd) ) {
					$cmd = new ecodevice_teleinfoCmd();
					$cmd->setName($data[0]);
					$cmd->setEqLogic_id($this->getId());
					$cmd->setType('info');
					$cmd->setSubType($data[1]);
					$cmd->setLogicalId($label);
					$cmd->setUnite($data[2]);
					$cmd->setIsVisible($data[3]);
					$cmd->setEventOnly(1);
					$cmd->save();
				}
			} else {
				$cmd = $this->getCmd(null, $label);
				if ( is_object($cmd) ) {
					$cmd->remove();
				}
			}
		}
	}

    public static function event() {
        $cmd = ecodevice_teleinfoCmd::byId(init('id'));
        if (!is_object($cmd)) {
            throw new Exception('Commande ID virtuel inconnu : ' . init('id'));
        }
		$cmd->event(init('value'));
    }

	public function configPush($url) {
		$gceid = substr($this->getLogicalId(), strpos($this->getLogicalId(),"_")+2, 1);
		$url .= 'protect/settings/notif'.$gceid.'P.htm';
		for ($compteur = 0; $compteur < 6; $compteur++) {
			log::add('ecodevice','debug','Url '.$url);
			$data = array('num' => $compteur + ($gceid -1)*6,
					'act' => $compteur+3,
					'serv' => config::byKey('internalAddr'),
					'port' => 80,
					'url' => '/jeedom/core/api/jeeApi.php?api='.config::byKey('api').'&type=ecodevice&id='.substr($this->getLogicalId(), 0, strpos($this->getLogicalId(),"_")).'&message=data_change');
//					'url' => '/jeedom/core/api/jeeApi.php?api='.config::byKey('api').'&type=ecodevice_teleinfo&id='.$this->getId().'&message=data_change');
			
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data),
				),
			);
			$context  = stream_context_create($options);
			$result = @file_get_contents($url, false, $context);
		}
	}

    public function getLinkToConfiguration() {
        return 'index.php?v=d&p=ecodevice&m=ecodevice&id=' . $this->getId();
    }
    /*     * **********************Getteur Setteur*************************** */
}

class ecodevice_teleinfoCmd extends cmd 
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */

    /*     * **********************Getteur Setteur*************************** */
}
?>