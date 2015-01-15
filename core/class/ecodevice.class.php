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

include_file('core', 'ecodevice_compteur', 'class', 'ecodevice');
include_file('core', 'ecodevice_teleinfo', 'class', 'ecodevice');

class ecodevice extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

	public static function cron() {
		foreach (self::byType('ecodevice') as $eqLogic) {
			$eqLogic->pull();
		}
	}

	public function getUrl() {
		$url = 'http://';
		if ( $this->getConfiguration('username') != '' )
		{
			$url .= $this->getConfiguration('username').':'.$this->getConfiguration('password').'@';
		} 
		$url .= $this->getConfiguration('ip');
		if ( $this->getConfiguration('port') != '' )
		{
			$url .= ':'.$this->getConfiguration('port');
		}
		return $url."/";
	}

	public function preUpdate()
	{
		if ( $this->getIsEnable() )
		{
			log::add('ecodevice','debug','get '.$this->getUrl(). 'status.xml');
			$this->xmlstatus = simplexml_load_file($this->getUrl(). 'status.xml');
			if ( $this->xmlstatus === false )
				throw new Exception(__('L\'ecodevice ne repond pas.',__FILE__)." ".$this->getName());
		}
	}

	public function preInsert()
	{
		$this->setIsVisible(0);
	}

	public function postInsert()
	{
		$ecodeviceCmd = $this->getCmd(null, 'updatetime');
		if ( ! is_object($ecodeviceCmd)) {
			$ecodeviceCmd = new ecodeviceCmd();
			$ecodeviceCmd->setName('Dernier refresh');
			$ecodeviceCmd->setEqLogic_id($this->getId());
			$ecodeviceCmd->setLogicalId('updatetime');
			$ecodeviceCmd->setUnite('');
			$ecodeviceCmd->setType('info');
			$ecodeviceCmd->setSubType('string');
			$ecodeviceCmd->setIsHistorized(0);
			$ecodeviceCmd->setEventOnly(1);
			$ecodeviceCmd->save();		
		}

		$cmd = $this->getCmd(null, 'status');
		if ( ! is_object($cmd) ) {
			$cmd = new ecodeviceCmd();
			$cmd->setName('Etat');
			$cmd->setEqLogic_id($this->getId());
			$cmd->setType('info');
			$cmd->setSubType('binary');
			$cmd->setLogicalId('status');
			$cmd->setIsVisible(1);
			$cmd->setEventOnly(1);
			$cmd->save();
		}
		for ($compteurId = 0; $compteurId <= 1; $compteurId++) {
			if ( ! is_object(self::byLogicalId($this->getId()."_C".$compteurId, 'ecodevice_compteur')) ) {
				log::add('ecodevice','debug','Creation compteur : '.$this->getId().'_C'.$compteurId);
				$eqLogic = new ecodevice_compteur();
				$eqLogic->setEqType_name('ecodevice_compteur');
				$eqLogic->setIsEnable(1);
				$eqLogic->setName('Compteur ' . $compteurId);
				$eqLogic->setLogicalId($this->getId().'_C'.$compteurId);
				$eqLogic->setIsVisible(1);
				$eqLogic->save();
			}
		}
		for ($compteurId = 1; $compteurId <= 2; $compteurId++) {
			if ( ! is_object(self::byLogicalId($this->getId()."_T".$compteurId, 'ecodevice_teleinfo')) ) {
				log::add('ecodevice','debug','Creation teleinfo : '.$this->getId().'_T'.$compteurId);
				$eqLogic = new ecodevice_teleinfo();
				$eqLogic->setEqType_name('ecodevice_teleinfo');
				$eqLogic->setIsEnable(1);
				$eqLogic->setName('Teleinfo ' . $compteurId);
				$eqLogic->setLogicalId($this->getId().'_T'.$compteurId);
				$eqLogic->setIsVisible(1);
				$eqLogic->setCategory("energy", "Energie");
				$eqLogic->save();
			}
		}
	}

	public function postUpdate()
	{
		$cmd = $this->getCmd(null, 'status');
		if ( ! is_object($cmd) ) {
			$cmd = new ecodeviceCmd();
			$cmd->setName('Etat');
			$cmd->setEqLogic_id($this->getId());
			$cmd->setType('info');
			$cmd->setSubType('binary');
			$cmd->setLogicalId('status');
			$cmd->setIsVisible(1);
			$cmd->setEventOnly(1);
			$cmd->save();
		}
	}

	public function preRemove()
	{
		foreach (self::byType('ecodevice_compteur') as $eqLogic) {
			if ( substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
				log::add('ecodevice','debug','Suppression compteur : '.$eqLogic->getName());
				$eqLogic->remove();
			}
		}
		foreach (self::byType('ecodevice_teleinfo') as $eqLogic) {
			if ( substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
				log::add('ecodevice','debug','Suppression teleinfo : '.$eqLogic->getName());
				$eqLogic->remove();
			}
		}
	}

	public function configPush() {
		if ( config::byKey('internalAddr') == "" ) {
			throw new Exception(__('L\'adresse IP du serveur Jeedom doit être renseignée.<br>Général -> Administration -> Configuration.<br>Configuration réseaux -> Adresse interne',__FILE__));
		}
		if ( $this->getIsEnable() ) {
			throw new Exception('Configurer l\'URL suivante pour un rafraichissement plus rapide dans l\'ecodevice : page index=>notification :<br>http://'.config::byKey('internalAddr').'/jeedom/core/api/jeeApi.php?api='.config::byKey('api').'&type=ecodevice&id='.substr($this->getLogicalId(), 0, strpos($this->getLogicalId(),"_")).'&message=data_change<br>Attention surcharge possible importante.');
			$this->xmlstatus = simplexml_load_file($this->getUrl(). 'status.xml');
			foreach (self::byType('ecodevice_compteur') as $eqLogicCompteur) {
				if ( $eqLogicCompteur->getIsEnable() && substr($eqLogicCompteur->getLogicalId(), 0, strpos($eqLogicCompteur->getLogicalId(),"_")) == $this->getId() ) {
					$eqLogicCompteur->configPush($this->getUrl());
				}
			}
			foreach (self::byType('ecodevice_teleinfo') as $eqLogicTeleinfo) {
				if ( $eqLogicTeleinfo->getIsEnable() && substr($eqLogicTeleinfo->getLogicalId(), 0, strpos($eqLogicTeleinfo->getLogicalId(),"_")) == $this->getId() ) {
					$eqLogicTeleinfo->configPush($this->getUrl());
				}
			}
		}
	}

	public function event() {
		foreach (eqLogic::byType('ecodevice') as $eqLogic) {
			if ( $eqLogic->getId() == init('id') ) {
				$eqLogic->pull();
			}
		}
	}

	public function pull() {
		if ( $this->getIsEnable() ) {
			$statuscmd = $this->getCmd(null, 'status');
			$this->xmlstatus = simplexml_load_file($this->getUrl(). 'status.xml');
			$count = 0;
			while ( $this->xmlstatus === false && $count < 3 ) {
				$this->xmlstatus = simplexml_load_file($this->getUrl(). 'status.xml');
				$count++;
			}
			if ( $this->xmlstatus === false ) {
				if ($statuscmd->execCmd() != 0) {
					$statuscmd->setCollectDate('');
					$statuscmd->event(0);
				}
				log::add('ecodevice','error',__('L\'ecodevice ne repond pas.',__FILE__)." ".$eqLogic->getName()." get ".preg_replace("/:[^:]*@/", ":XXXX@", $this->getUrl()). 'status.xml');
				return false;
			}
			if ($statuscmd->execCmd() != 1) {
				$statuscmd->setCollectDate('');
				$statuscmd->event(1);
			}
			$eqLogic_cmd = $this->getCmd(null, 'updatetime');
			$eqLogic_cmd->event(time());
			foreach (self::byType('ecodevice_compteur') as $eqLogicCompteur) {
				if ( $eqLogicCompteur->getIsEnable() && substr($eqLogicCompteur->getLogicalId(), 0, strpos($eqLogicCompteur->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = substr($eqLogicCompteur->getLogicalId(), strpos($eqLogicCompteur->getLogicalId(),"_")+2, 1);
					$xpathModele = '//count'.$gceid;
					$status = $this->xmlstatus->xpath($xpathModele);
					
					if ( count($status) != 0 )
					{
						$nbimpulsion_cmd = $eqLogicCompteur->getCmd(null, 'nbimpulsion');
						$nbimpulsion = $nbimpulsion_cmd->execCmd(null, 2);
						$nbimpulsionminute_cmd = $eqLogicCompteur->getCmd(null, 'nbimpulsionminute');
						if ($nbimpulsion != $status[0]) {
							if ( $nbimpulsion_cmd->getCollectDate() == '' ) {
								$nbimpulsionminute = 0;
							} else {
								if ( $status[0] > $nbimpulsion ) {
									$nbimpulsionminute = round (($status[0] - $nbimpulsion)/(time() - strtotime($nbimpulsion_cmd->getCollectDate()))*60, 6);
								} else {
									$nbimpulsionminute = round ($status[0]/(time() - strtotime($nbimpulsionminute_cmd->getCollectDate())*60), 6);
								}
							}
							$nbimpulsionminute_cmd->setCollectDate(date('Y-m-d H:i:s'));
							$nbimpulsionminute_cmd->event($nbimpulsionminute);
							$nbimpulsion_cmd->setCollectDate(date('Y-m-d H:i:s'));
							$nbimpulsion_cmd->event($status[0]);
						} else {
							$nbimpulsionminute_cmd->setCollectDate(date('Y-m-d H:i:s'));
							$nbimpulsionminute_cmd->event(0);
						}
					}
				}
			}
			foreach (self::byType('ecodevice_teleinfo') as $eqLogicTeleinfo) {
				if ( $eqLogicTeleinfo->getIsEnable() && substr($eqLogicTeleinfo->getLogicalId(), 0, strpos($eqLogicTeleinfo->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = substr($eqLogicTeleinfo->getLogicalId(), strpos($eqLogicTeleinfo->getLogicalId(),"_")+2, 1);
					$this->xmlstatus = simplexml_load_file($this->getUrl(). 'protect/settings/teleinfo'.$gceid.'.xml');
					if ( $this->xmlstatus === false ) {
						if ($statuscmd->execCmd() != 0) {
							$statuscmd->setCollectDate('');
							$statuscmd->event(0);
						}
						log::add('ecodevice','error',__('L\'ecodevice ne repond pas.',__FILE__)." ".$eqLogic->getName()." get ".preg_replace("/:[^:]*@/", ":XXXX@", $this->getUrl()). 'protect/settings/teleinfo'.$gceid.'.xml');
						return false;
					}
					if ($statuscmd->execCmd() != 1) {
						$statuscmd->setCollectDate('');
						$statuscmd->event(1);
					}
					$xpathModele = '//response';
					$status = $this->xmlstatus->xpath($xpathModele);
					
					if ( count($status) != 0 )
					{
						foreach($status[0] as $item => $data) {
							if ( substr($item, 0, 3) == "T".$gceid."_" ) {
								$eqLogic_cmd = $eqLogicTeleinfo->getCmd(null, substr($item, 3));
								if ( is_object($eqLogic_cmd) ) {
									if ($data != $eqLogic_cmd->execCmd()) {
										log::add('ecodevice', 'debug', $eqLogic_cmd->getName().' Change '.$data);
										$eqLogic_cmd->setCollectDate('');
										$eqLogic_cmd->event($data);
									}
								}
							}
						}
					}
				}
			}
		}
	}
    /*     * **********************Getteur Setteur*************************** */
}

class ecodeviceCmd extends cmd 
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*     * **********************Getteur Setteur*************************** */
}
include_file('core', 'ecodevice_compteur', 'class', 'ecodevice');
include_file('core', 'ecodevice_teleinfo', 'class', 'ecodevice');

?>
