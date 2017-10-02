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

	public static function pull() {
		log::add('ecodevice','debug','start cron');
		foreach (self::byType('ecodevice') as $eqLogic) {
			$eqLogic->scan();
		}
		log::add('ecodevice','debug','stop cron');
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
			$this->xmlstatus = @simplexml_load_file($this->getUrl(). 'status.xml');
			if ( $this->xmlstatus === false )
				throw new Exception(__('L\'ecodevice ne repond pas.',__FILE__)." ".$this->getName());
		}
	}

	public function GetPhase($gceid)
	{
		if ( $this->getIsEnable() )
		{
			log::add('ecodevice','debug','get '.$this->getUrl(). 'protect/settings/teleinfo'.$gceid.'.xml');
			$this->xmlstatus = @simplexml_load_file($this->getUrl(). 'protect/settings/teleinfo'.$gceid.'.xml');
			if ( $this->xmlstatus === false )
				throw new Exception(__('L\'ecodevice ne repond pas.',__FILE__)." ".$this->getName());
			$xpathModele = '//T'.$gceid.'_IMAX2';
			$status = $this->xmlstatus->xpath($xpathModele);

			if ( count($status) != 0 )
			{
				if ( $status[0] != "0" )
				{
					return "Tri";
				}
			}
			$xpathModele = '//T'.$gceid.'_IMAX';
			$status = $this->xmlstatus->xpath($xpathModele);

			if ( count($status) != 0 )
			{
				if ( $status[0] != "0" )
				{
					return "Mono";
				}
			}
		}
		return "";
	}

	public function preInsert()
	{
		$this->setIsVisible(0);
	}

	public function postInsert()
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
			$cmd->setDisplay('generic_type','GENERIC_INFO');
			$cmd->save();
		}
        $reboot = $this->getCmd(null, 'reboot');
        if ( ! is_object($reboot) ) {
            $reboot = new ecodeviceCmd();
			$reboot->setName('Reboot');
			$reboot->setEqLogic_id($this->getId());
			$reboot->setType('action');
			$reboot->setSubType('other');
			$reboot->setLogicalId('reboot');
			$reboot->setEventOnly(1);
			$reboot->setIsVisible(0);
			$reboot->setDisplay('generic_type','GENERIC_ACTION');
			$reboot->save();
		}
		for ($compteurId = 0; $compteurId <= 1; $compteurId++) {
			if ( ! is_object(self::byLogicalId($this->getId()."_C".$compteurId, 'ecodevice_compteur')) ) {
				log::add('ecodevice','debug','Creation compteur : '.$this->getId().'_C'.$compteurId);
				$eqLogic = new ecodevice_compteur();
				$eqLogic->setEqType_name('ecodevice_compteur');
				$eqLogic->setIsEnable(0);
				$eqLogic->setName('Compteur ' . $compteurId);
				$eqLogic->setLogicalId($this->getId().'_C'.$compteurId);
				$eqLogic->setIsVisible(0);
				$eqLogic->save();
			}
		}
		for ($compteurId = 1; $compteurId <= 2; $compteurId++) {
			if ( ! is_object(self::byLogicalId($this->getId()."_T".$compteurId, 'ecodevice_teleinfo')) ) {
				log::add('ecodevice','debug','Creation teleinfo : '.$this->getId().'_T'.$compteurId);
				$eqLogic = new ecodevice_teleinfo();
				$eqLogic->setEqType_name('ecodevice_teleinfo');
				$eqLogic->setIsEnable(0);
				$eqLogic->setName('Teleinfo ' . $compteurId);
				$eqLogic->setLogicalId($this->getId().'_T'.$compteurId);
				$eqLogic->setIsVisible(0);
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
			$cmd->setDisplay('generic_type','GENERIC_INFO');
			$cmd->save();
		}
 		else
		{
			if ( $cmd->getDisplay('generic_type') == "" )
			{
				$cmd->setDisplay('generic_type','GENERIC_INFO');
				$cmd->save();
			}
		}
		$reboot = $this->getCmd(null, 'reboot');
		if ( is_object($reboot) && get_class ($reboot) != "ecodeviceCmd" ) {
			$reboot->remove();		
		}
        $reboot = $this->getCmd(null, 'reboot');
			
        if ( ! is_object($reboot) ) {
            $reboot = new ecodeviceCmd();
			$reboot->setName('Reboot');
			$reboot->setEqLogic_id($this->getId());
			$reboot->setType('action');
			$reboot->setSubType('other');
			$reboot->setLogicalId('reboot');
			$reboot->setEventOnly(1);
			$reboot->setIsVisible(0);
			$reboot->setDisplay('generic_type','GENERIC_ACTION');
			$reboot->save();
		}
		else
		{
			if ( $cmd->getDisplay('generic_type') == "" )
			{
				$cmd->setDisplay('generic_type','GENERIC_INFO');
				$cmd->save();
			}
		}

		$ecodeviceCmd = $this->getCmd(null, 'updatetime');
		if ( is_object($ecodeviceCmd)) {
			$ecodeviceCmd->remove();		
		}

		$this->xmlstatus = @simplexml_load_file($this->getUrl(). 'status.xml');
		$count = 0;
		while ( $this->xmlstatus === false && $count < 3 ) {
			$this->xmlstatus = @simplexml_load_file($this->getUrl(). 'status.xml');
			$count++;
		}
		if ( $this->xmlstatus !== false ) {
			for ($compteurId = 0; $compteurId <= 1; $compteurId++) {
				if ( ! is_object(self::byLogicalId($this->getId()."_C".$compteurId, 'ecodevice_compteur')) ) {
					log::add('ecodevice','debug','Creation compteur : '.$this->getId().'_C'.$compteurId);
					$eqLogic = new ecodevice_compteur();
					$eqLogic->setEqType_name('ecodevice_compteur');
					$eqLogic->setIsEnable(0);
					$eqLogic->setName('Compteur ' . $compteurId);
					$eqLogic->setLogicalId($this->getId().'_C'.$compteurId);
					$eqLogic->setIsVisible(0);
					$eqLogic->save();
				}
				else
				{
					$eqLogic = self::byLogicalId($this->getId()."_C".$compteurId, 'ecodevice_compteur');
					# Verifie la configuration des compteurs fuel
					$xpathModele = '//c'.$compteurId.'_fuel';
					$status = $this->xmlstatus->xpath($xpathModele);

					if ( count($status) != 0 )
					{
						if ( $status[0] != "selected" )
						{
							if ( $eqLogic->getConfiguration('typecompteur') == "Fuel" || $eqLogic->getConfiguration('typecompteur') == "Temps de fonctionnement" )
							{
								throw new Exception(__('Le compteur '.$eqLogic->getName().' ne doit pas être configuré en mode fuel dans l\'ecodevice.',__FILE__));
							}
							elseif ( $eqLogic->getConfiguration('typecompteur') == "" )
							{
								$eqLogic->setConfiguration('typecompteur', "Eau");
								$eqLogic->save();
							}
						}
						else
						{
							$eqLogic->setConfiguration('typecompteur', "Fuel");
							$eqLogic->save();
						}
					}
					elseif ( $eqLogic->getConfiguration('typecompteur') == "Fuel" || $eqLogic->getConfiguration('typecompteur') == "Temps de fonctionnement" )
					{
						throw new Exception(__('Le compteur '.$eqLogic->getName().' ne doit pas être configuré en mode fuel dans l\'ecodevice.',__FILE__));
					}
					elseif ( $eqLogic->getConfiguration('typecompteur') == "" )
					{
						$eqLogic->setConfiguration('typecompteur', "Eau");
						$eqLogic->save();
					}
				}
			}
			for ($compteurId = 1; $compteurId <= 2; $compteurId++) {
				if ( ! is_object(self::byLogicalId($this->getId()."_T".$compteurId, 'ecodevice_teleinfo')) ) {
					log::add('ecodevice','debug','Creation teleinfo : '.$this->getId().'_T'.$compteurId);
					$eqLogic = new ecodevice_teleinfo();
					$eqLogic->setEqType_name('ecodevice_teleinfo');
					$eqLogic->setIsEnable(0);
					$eqLogic->setName('Teleinfo ' . $compteurId);
					$eqLogic->setLogicalId($this->getId().'_T'.$compteurId);
					$eqLogic->setIsVisible(0);
					$eqLogic->setCategory("energy", "Energie");
					$eqLogic->save();
				}
			}
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
			throw new Exception('Configurer l\'URL suivante pour un rafraichissement plus rapide dans l\'ecodevice : page index=>notification :<br>http://'.config::byKey('internalAddr').'/jeedom/core/api/jeeApi.php?api='.jeedom::getApiKey('ecodevice').'&type=ecodevice&id='.substr($this->getLogicalId(), 0, strpos($this->getLogicalId(),"_")).'&message=data_change<br>Attention surcharge possible importante.');
			$this->xmlstatus = @simplexml_load_file($this->getUrl(). 'status.xml');
			$count = 0;
			while ( $this->xmlstatus === false && $count < 3 ) {
				$this->xmlstatus = @simplexml_load_file($this->getUrl(). 'status.xml');
				$count++;
			}
			if ( $this->xmlstatus === false ) {
				log::add('ecodevice','error',__('L\'ecodevice ne repond pas.',__FILE__)." ".$this->getName()." get ".preg_replace("/:[^:]*@/", ":XXXX@", $this->getUrl()). 'status.xml');
				return false;
			}
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
				$eqLogic->scan();
			}
		}
	}

	public function scan() {
		if ( $this->getIsEnable() ) {
			log::add('ecodevice','debug',"Scan ".$this->getName());
			$statuscmd = $this->getCmd(null, 'status');
			$this->xmlstatus = @simplexml_load_file($this->getUrl(). 'status.xml');
			$count = 0;
			while ( $this->xmlstatus === false && $count < 3 ) {
				$this->xmlstatus = @simplexml_load_file($this->getUrl(). 'status.xml');
				$count++;
			}
			if ( $this->xmlstatus === false ) {
				if ($statuscmd->execCmd() != 0) {
					$statuscmd->setCollectDate(date('Y-m-d H:i:s'));
					$statuscmd->event(0);
				}
				log::add('ecodevice','error',__('L\'ecodevice ne repond pas.',__FILE__)." ".$this->getName()." get ".preg_replace("/:[^:]*@/", ":XXXX@", $this->getUrl()). 'status.xml');
				return false;
			}
			foreach (self::byType('ecodevice_compteur') as $eqLogicCompteur) {
				if ( $eqLogicCompteur->getIsEnable() && substr($eqLogicCompteur->getLogicalId(), 0, strpos($eqLogicCompteur->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = substr($eqLogicCompteur->getLogicalId(), strpos($eqLogicCompteur->getLogicalId(),"_")+2, 1);
					if ( $eqLogicCompteur->getConfiguration('typecompteur') == "Temps de fonctionnement" )
					{
						# Verifie la configuration des compteurs de temps de fonctionnement
						$xpathModele = '//c'.$gceid.'_fuel';
						$status = $this->xmlstatus->xpath($xpathModele);

						if ( count($status) != 0 )
						{
							if ( $status[0] != "selected" )
							{
								throw new Exception(__('Le compteur '.$eqLogicCompteur->getName().' doit être configuré en mode fuel dans l\'ecodevice.',__FILE__));
							}
						}
						else
						{
							throw new Exception(__('Le compteur '.$eqLogicCompteur->getName().' doit être configuré en mode fuel dans l\'ecodevice.',__FILE__));
						}
						$xpathModele = '//count'.$gceid;
						$status = $this->xmlstatus->xpath($xpathModele);
						
						if ( count($status) != 0 )
						{
							$nbimpulsiontotal_cmd = $eqLogicCompteur->getCmd(null, 'nbimpulsiontotal');
							$nbimpulsiontotal = $nbimpulsiontotal_cmd->execCmd();
							$nbimpulsionminute_cmd = $eqLogicCompteur->getCmd(null, 'nbimpulsionminute');
							if ($nbimpulsiontotal != $status[0]) {
								log::add('ecodevice','debug',"Change nbimpulsiontotal of ".$eqLogicCompteur->getName());
								$lastCollectDate = $nbimpulsiontotal_cmd->getCollectDate();
								if ( $lastCollectDate == '' ) {
									log::add('ecodevice','debug',"Change nbimpulsionminute 0");
									$nbimpulsionminute = 0;
								} else {
									$DeltaSeconde = (time() - strtotime($lastCollectDate))*60;
									if ( $DeltaSeconde != 0 )
									{
										if ( $status[0] > $nbimpulsiontotal ) {
											$DeltaValeur = $status[0] - $nbimpulsiontotal;
										} else {
											$DeltaValeur = $status[0];
										}
										$nbimpulsionminute = round (($status[0] - $nbimpulsiontotal)/(time() - strtotime($lastCollectDate))*60, 6);
									} else {
										$nbimpulsionminute = 0;
									}
								}
								log::add('ecodevice','debug',"Change nbimpulsionminute ".$nbimpulsionminute);
								$nbimpulsionminute_cmd->setCollectDate(date('Y-m-d H:i:s'));
								$nbimpulsionminute_cmd->event($nbimpulsionminute);
							} else {
								$nbimpulsionminute_cmd->setCollectDate(date('Y-m-d H:i:s'));
								$nbimpulsionminute_cmd->event(0);
							}
							$nbimpulsiontotal_cmd->setCollectDate(date('Y-m-d H:i:s'));
							$nbimpulsiontotal_cmd->event($status[0]);
						}
						$xpathModele = '//c'.$gceid.'day';
						$status = $this->xmlstatus->xpath($xpathModele);
						log::add('ecodevice','debug','duree fonctionnement '.$status[0]);
						$eqLogic_cmd = $eqLogicCompteur->getCmd(null, 'tempsfonctionnement');
						$tempsfonctionnement = $eqLogic_cmd->execCmd();
						$eqLogic_cmd_evol = $eqLogicCompteur->getCmd(null, 'tempsfonctionnementminute');
						if ($tempsfonctionnement != $status[0] * 3.6) {
							if ( $eqLogic_cmd->getCollectDate() == '' ) {
								$tempsfonctionnementminute = 0;
							} else {
								if ( $status[0] * 3.6 > $tempsfonctionnement ) {
									$tempsfonctionnementminute = round (($status[0] * 3.6 - $tempsfonctionnement)/(time() - strtotime($eqLogic_cmd->getCollectDate()))*60, 6);
								} else {
									$tempsfonctionnementminute = round ($status[0] * 3.6/(time() - strtotime($eqLogic_cmd_evol->getCollectDate())*60), 6);
								}
							}
							$eqLogic_cmd_evol->setCollectDate(date('Y-m-d H:i:s'));
							$eqLogic_cmd_evol->event($tempsfonctionnementminute);
						} else {
							$eqLogic_cmd_evol->setCollectDate(date('Y-m-d H:i:s'));
							$eqLogic_cmd_evol->event(0);
						}
						$eqLogic_cmd->setCollectDate(date('Y-m-d H:i:s'));
						$eqLogic_cmd->event($status[0] * 3.6);
					}
					elseif ( $eqLogicCompteur->getConfiguration('typecompteur') == "Fuel" )
					{
						# Verifie la configuration des compteurs fuel
						$xpathModele = '//c'.$gceid.'_fuel';
						$status = $this->xmlstatus->xpath($xpathModele);

						if ( count($status) != 0 )
						{
							if ( $status[0] != "selected" )
							{
								throw new Exception(__('Le compteur '.$eqLogicCompteur->getName().' doit être configuré en mode fuel dans l\'ecodevice.',__FILE__));
							}
						}
						else
						{
							throw new Exception(__('Le compteur '.$eqLogicCompteur->getName().' doit être configuré en mode fuel dans l\'ecodevice.',__FILE__));
						}
						$xpathModele = '//count'.$gceid;
						$status = $this->xmlstatus->xpath($xpathModele);
						if ( count($status) != 0 )
						{
							$consommationtotal = intval($status[0]);
							$consommationtotal_cmd = $eqLogicCompteur->getCmd(null, 'consommationtotal');
							if ($consommationtotal_cmd->execCmd() != $consommationtotal) {
								log::add('ecodevice','debug',"Change consommationtotal of ".$eqLogicCompteur->getName());
								$consommationtotal_cmd->setCollectDate(date('Y-m-d H:i:s'));
								$consommationtotal_cmd->event($consommationtotal);
							}
						}
						$xpathModele = '//c'.$gceid."day";
						$status = $this->xmlstatus->xpath($xpathModele);
						if ( count($status) != 0 )
						{
							$consommationjour = intval($status[0]);
							$consommationjour_cmd = $eqLogicCompteur->getCmd(null, 'consommationjour');
							if ($consommationjour_cmd->execCmd() != $consommationjour) {
								log::add('ecodevice','debug',"Change consommationjour of ".$eqLogicCompteur->getName());
								$consommationjour_cmd->setCollectDate(date('Y-m-d H:i:s'));
								$consommationjour_cmd->event($consommationjour);
							}
						}
						$xpathModele = '//meter'.($gceid+2);
						$status = $this->xmlstatus->xpath($xpathModele);
						if ( count($status) != 0 )
						{
							$consommationinstantane = intval($status[0]) * 10;
							$consommationinstantane_cmd = $eqLogicCompteur->getCmd(null, 'consommationinstantane');
							if ($consommationinstantane_cmd->execCmd() != $consommationinstantane) {
								log::add('ecodevice','debug',"Change consommationinstantane of ".$eqLogicCompteur->getName());
								$consommationinstantane_cmd->setCollectDate(date('Y-m-d H:i:s'));
								$consommationinstantane_cmd->event($consommationinstantane);
							}
						}
					}
					else
					{
						# mode eau, gaz, electricité
						$xpathModele = '//meter'.($gceid+2);
						$status = $this->xmlstatus->xpath($xpathModele);
						
						if ( count($status) != 0 )
						{
							$eqLogic_cmd = $eqLogicCompteur->getCmd(null, 'debitinstantane');
							if ($eqLogic_cmd->execCmd() != $eqLogic_cmd->formatValue($status[0])) {
								log::add('ecodevice','debug',"Change debitinstantane of ".$eqLogicCompteur->getName());
								$eqLogic_cmd->setCollectDate('');
								$eqLogic_cmd->event($status[0]);
							}
						}
						$xpathModele = '//c'.$gceid.'day';
						$status = $this->xmlstatus->xpath($xpathModele);
						
						if ( count($status) != 0 )
						{
							$eqLogic_cmd = $eqLogicCompteur->getCmd(null, 'consommationjour');
							if ($eqLogic_cmd->execCmd() != $eqLogic_cmd->formatValue($status[0])) {
								log::add('ecodevice','debug',"Change consommationjour of ".$eqLogicCompteur->getName());
								$eqLogic_cmd->setCollectDate('');
								$eqLogic_cmd->event($status[0]);
							}
						}
						$xpathModele = '//count'.$gceid;
						$status = $this->xmlstatus->xpath($xpathModele);
						
						if ( count($status) != 0 )
						{
							$consommationtotal_cmd = $eqLogicCompteur->getCmd(null, 'consommationtotal');
							if ($consommationtotal_cmd->execCmd() != $status[0]) {
								log::add('ecodevice','debug',"Change consommationtotal of ".$eqLogicCompteur->getName());
								$consommationtotal_cmd->setCollectDate(date('Y-m-d H:i:s'));
								$consommationtotal_cmd->event($status[0]);
							}
						}
					}
				}
			}
			foreach (self::byType('ecodevice_teleinfo') as $eqLogicTeleinfo) {
				if ( $eqLogicTeleinfo->getIsEnable() && substr($eqLogicTeleinfo->getLogicalId(), 0, strpos($eqLogicTeleinfo->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = substr($eqLogicTeleinfo->getLogicalId(), strpos($eqLogicTeleinfo->getLogicalId(),"_")+2, 1);
					$this->xmlstatus = @simplexml_load_file($this->getUrl(). 'protect/settings/teleinfo'.$gceid.'.xml');
					if ( $this->xmlstatus === false ) {
						if ($statuscmd->execCmd() != 0) {
							$statuscmd->setCollectDate(date('Y-m-d H:i:s'));
							$statuscmd->event(0);
						}
						log::add('ecodevice','error',__('L\'ecodevice ne repond pas.',__FILE__)." ".$this->getName()." get ".preg_replace("/:[^:]*@/", ":XXXX@", $this->getUrl()). 'protect/settings/teleinfo'.$gceid.'.xml');
						return false;
					}
					$xpathModele = '//response';
					$status = $this->xmlstatus->xpath($xpathModele);
					
					if ( count($status) != 0 )
					{
						foreach($status[0] as $item => $data) {
							if ( substr($item, 0, 3) == "T".$gceid."_" ) {
								$eqLogic_cmd = $eqLogicTeleinfo->getCmd(null, substr($item, 3));
								if ( is_object($eqLogic_cmd) ) {
									$eqLogic_cmd_evol = $eqLogicTeleinfo->getCmd(null, substr($item, 3)."_evolution");
									if ( is_object($eqLogic_cmd_evol) ) {
										$ancien_data = $eqLogic_cmd->execCmd();
										if ($ancien_data != $data) {
											log::add('ecodevice', 'debug', $eqLogic_cmd->getName().' Change '.$data);
											if ( $eqLogic_cmd->getCollectDate() == '' ) {
												$nbimpulsionminute = 0;
											} else {
												if ( $data > $ancien_data ) {
													$nbimpulsionminute = round (($data - $ancien_data)/(time() - strtotime($eqLogic_cmd->getCollectDate()))*60);
												} else {
													$nbimpulsionminute = round ($data/(time() - strtotime($eqLogic_cmd_evol->getCollectDate())*60));
												}
											}
											$eqLogic_cmd_evol->setCollectDate(date('Y-m-d H:i:s'));
											$eqLogic_cmd_evol->event($nbimpulsionminute);
										} else {
											$eqLogic_cmd_evol->setCollectDate(date('Y-m-d H:i:s'));
											$eqLogic_cmd_evol->event(0);
										}
										$eqLogic_cmd->setCollectDate(date('Y-m-d H:i:s'));
										$eqLogic_cmd->event($data);
									} else {
										$eqLogic_cmd->setCollectDate(date('Y-m-d H:i:s'));
										$eqLogic_cmd->event($data);
									}
								}
							}
						}
					}
				}
			}
			if ($statuscmd->execCmd() != 1) {
				$statuscmd->setCollectDate(date('Y-m-d H:i:s'));
				$statuscmd->event(1);
			}
		}
	}

	public function scan_rapide() {
		if ( $this->getIsEnable() ) {
			log::add('ecodevice','debug',"Scan rapide ".$this->getName());
			$statuscmd = $this->getCmd(null, 'status');
			$this->xmlstatus = @simplexml_load_file($this->getUrl(). 'status.xml');
			$count = 0;
			while ( $this->xmlstatus === false && $count < 3 ) {
				$this->xmlstatus = @simplexml_load_file($this->getUrl(). 'status.xml');
				$count++;
			}
			if ( $this->xmlstatus === false ) {
				if ($statuscmd->execCmd() != 0) {
					$statuscmd->setCollectDate(date('Y-m-d H:i:s'));
					$statuscmd->event(0);
				}
				log::add('ecodevice','error',__('L\'ecodevice ne repond pas.',__FILE__)." ".$this->getName()." get ".preg_replace("/:[^:]*@/", ":XXXX@", $this->getUrl()). 'status.xml');
				return false;
			}
			foreach (self::byType('ecodevice_compteur') as $eqLogicCompteur) {
				if ( $eqLogicCompteur->getIsEnable() && substr($eqLogicCompteur->getLogicalId(), 0, strpos($eqLogicCompteur->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = substr($eqLogicCompteur->getLogicalId(), strpos($eqLogicCompteur->getLogicalId(),"_")+2, 1);
					if ( $eqLogicCompteur->getConfiguration('typecompteur') == "Fuel" )
					{
						# mode fuel
						$xpathModele = '//meter'.($gceid+2);
						$status = $this->xmlstatus->xpath($xpathModele);
						
						if ( count($status) != 0 )
						{
							$consommationinstantane = $status[0] / 100;
							$eqLogic_cmd = $eqLogicCompteur->getCmd(null, 'consommationinstantane');
							if ($eqLogic_cmd->execCmd() != $eqLogic_cmd->formatValue($consommationinstantane)) {
								log::add('ecodevice','debug',"Change consommationinstantane of ".$eqLogicCompteur->getName());
								$eqLogic_cmd->setCollectDate('');
								$eqLogic_cmd->event($consommationinstantane);
							}
						}
					}
					else
					{
						# mode eau
						$xpathModele = '//meter'.($gceid+2);
						$status = $this->xmlstatus->xpath($xpathModele);
						
						if ( count($status) != 0 )
						{
							$eqLogic_cmd = $eqLogicCompteur->getCmd(null, 'debitinstantane');
							if ($eqLogic_cmd->execCmd() != $eqLogic_cmd->formatValue($status[0])) {
								log::add('ecodevice','debug',"Change debitinstantane of ".$eqLogicCompteur->getName());
								$eqLogic_cmd->setCollectDate('');
								$eqLogic_cmd->event($status[0]);
							}
						}
					}
				}
			}
			foreach (self::byType('ecodevice_teleinfo') as $eqLogicTeleinfo) {
				if ( $eqLogicTeleinfo->getIsEnable() && substr($eqLogicTeleinfo->getLogicalId(), 0, strpos($eqLogicTeleinfo->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = substr($eqLogicTeleinfo->getLogicalId(), strpos($eqLogicTeleinfo->getLogicalId(),"_")+2, 1);
					$item = "T".$gceid."_PPAP";
					$xpathModele = '//'.$item;
					$status = $this->xmlstatus->xpath($xpathModele);
					
					if ( count($status) != 0 )
					{
						$eqLogic_cmd = $eqLogicTeleinfo->getCmd(null, substr($item, 3));
						if ( is_object($eqLogic_cmd) && $eqLogic_cmd->execCmd() != $eqLogic_cmd->formatValue($status[0])) {
							log::add('ecodevice','debug',"Change ".$item." of ".$eqLogicTeleinfo->getName());
							$eqLogic_cmd->setCollectDate('');
							$eqLogic_cmd->event($status[0]);
						}
					}
				}
			}
		}
	}
	
	public static function daemon() {
		$starttime = microtime (true);
		foreach (self::byType('ecodevice') as $eqLogic) {
			$eqLogic->scan_rapide();
		}
		$endtime = microtime (true);
		if ( $endtime - $starttime < config::byKey('temporisation_lecture', 'ecodevice', 60, true) )
		{
			usleep(floor((config::byKey('temporisation_lecture', 'ecodevice') + $starttime - $endtime)*1000000));
		}
	}

	public static function deamon_info() {
		$return = array();
		$return['log'] = '';
		$return['state'] = 'nok';
		$cron = cron::byClassAndFunction('ecodevice', 'daemon');
		if (is_object($cron) && $cron->running()) {
			$return['state'] = 'ok';
		}
		$return['launchable'] = 'ok';
		return $return;
	}

	public static function deamon_start($_debug = false) {
		self::deamon_stop();
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') {
			throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
		}
		$cron = cron::byClassAndFunction('ecodevice', 'daemon');
		if (!is_object($cron)) {
			throw new Exception(__('Tâche cron introuvable', __FILE__));
		}
		log::add('ecodevice','debug','daemon start');
		$cron->run();
	}

	public static function deamon_stop() {
		$cron = cron::byClassAndFunction('ecodevice', 'daemon');
		if (!is_object($cron)) {
			throw new Exception(__('Tâche cron introuvable', __FILE__));
		}
		log::add('ecodevice','debug','daemon stop');
		$cron->halt();
	}

	public static function deamon_changeAutoMode($_mode) {
		$cron = cron::byClassAndFunction('ecodevice', 'daemon');
		if (!is_object($cron)) {
			throw new Exception(__('Tâche cron introuvable', __FILE__));
		}
		$cron->setEnable($_mode);
		$cron->save();
	}
    /*     * **********************Getteur Setteur*************************** */
}

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
}
include_file('core', 'ecodevice_compteur', 'class', 'ecodevice');
include_file('core', 'ecodevice_teleinfo', 'class', 'ecodevice');

?>
