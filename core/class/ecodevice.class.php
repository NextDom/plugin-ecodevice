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

class ecodevice extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

	static function getTypeCompteur()
	{
		return array(	__('Eau',__FILE__),
						__('Fuel',__FILE__),
						__('Gaz',__FILE__),
						__('Electricité',__FILE__),
						__('Autre',__FILE__));
	}

	private function getListeDefaultCommandes()
	{
		return array("BASE" => array('Index (base)', 'numeric', 'Wh', 1, "BASE", "CONSUMPTION", 'badge', ''),
		"HCHC" => array('Index (heures creuses)', 'numeric', 'Wh', 1, "HC", "CONSUMPTION", 'badge', ''),
		"HCHP" => array('Index (heures pleines)', 'numeric', 'Wh', 1, "HC", "CONSUMPTION", 'badge', ''),
		"BBRHCJB" => array('Index (heures creuses jours bleus Tempo)', 'numeric', 'Wh', 0, "BBRH", "CONSUMPTION", 'badge', ''),
		"BBRHPJB" => array('Index (heures pleines jours bleus Tempo)', 'numeric', 'Wh', 0, "BBRH", "CONSUMPTION", 'badge', ''),
		"BBRHCJW" => array('Index (heures creuses jours blancs Tempo)', 'numeric', 'Wh', 0, "BBRH", "CONSUMPTION", 'badge', ''),
		"BBRHPJW" => array('Index (heures pleines jours blancs Tempo)', 'numeric', 'Wh', 0, "BBRH", "CONSUMPTION", 'badge', ''),
		"BBRHCJR" => array('Index (heures creuses jours rouges Tempo)', 'numeric', 'Wh', 0, "BBRH", "CONSUMPTION", 'badge', ''),
		"BBRHPJR" => array('Index (heures pleines jours rouges Tempo)', 'numeric', 'Wh', 0, "BBRH", "CONSUMPTION", 'badge', ''),
		"EJPHN" => array('Index (normal EJP)', 'numeric', 'Wh', 0, "EJP", "CONSUMPTION", 'badge', ''),
		"EJPHPM" => array('Index (pointe mobile EJP)', 'numeric', 'Wh', 0, "EJP", "CONSUMPTION", 'badge', ''),
		"IINST" => array('Intensité instantanée', 'numeric', 'A', 1, "", "POWER", 'default', 'Mono'),
		"IINST1" => array('Intensité instantanée 1', 'numeric', 'A', 0, "", "POWER", 'default', 'Tri'),
		"IINST2" => array('Intensité instantanée 2', 'numeric', 'A', 0, "", "POWER", 'default', 'Tri'),
		"IINST3" => array('Intensité instantanée 3', 'numeric', 'A', 0, "", "POWER", 'default', 'Tri'),
		"PPAP" => array('Puissance Apparente', 'numeric', 'VA', 1, "", "POWER", 'badge', ''),
		"OPTARIF" => array('Option tarif', 'string', '', 1, "", "GENERIC_INFO", 'badge', ''),
		"DEMAIN" => array('Couleur demain', 'string', '', 0, "BBRH", "GENERIC_INFO", 'badge', ''),
		"PTEC" => array('Tarif en cours', 'string', '', 1, "", "GENERIC_INFO", 'badge', ''),
		"BASE_evolution" => array('Evolution index (base)', 'numeric', 'W/min', 1, "BASE", "", 'badge', ''),
		"HCHC_evolution" => array('Evolution index (heures creuses)', 'numeric', 'W/min', 1, "HC", "", 'badge', ''),
		"HCHP_evolution" => array('Evolution index (heures pleines)', 'numeric', 'W/min', 1, "HC", "", 'badge', ''),
		"BBRHCJB_evolution" => array('Evolution index (heures creuses jours bleus Tempo)', 'numeric', 'W/min', 0, "BBRH", "", 'badge', ''),
		"BBRHPJB_evolution" => array('Evolution index (heures pleines jours bleus Tempo)', 'numeric', 'W/min', 0, "BBRH", "", 'badge', ''),
		"BBRHCJW_evolution" => array('Evolution index (heures creuses jours blancs Tempo)', 'numeric', 'W/min', 0, "BBRH", "", 'badge', ''),
		"BBRHPJW_evolution" => array('Evolution index (heures pleines jours blancs Tempo)', 'numeric', 'W/min', 0, "BBRH", "", 'badge', ''),
		"BBRHCJR_evolution" => array('Evolution index (heures creuses jours rouges Tempo)', 'numeric', 'W/min', 0, "BBRH", "", 'badge', ''),
		"BBRHPJR_evolution" => array('Evolution index (heures pleines jours rouges Tempo)', 'numeric', 'W/min', 0, "BBRH", "", 'badge', ''),
		"EJPHN_evolution" => array('Evolution index (normal EJP)', 'numeric', 'W', 0, "EJP", "", 'badge', ''),
		"EJPHPM_evolution" => array('Evolution index (pointe mobile EJP)', 'numeric', 'W', 0, "EJP", "", 'badge', ''),
		"ISOUSC" => array('Intensité souscrite', 'numeric', 'A', 1, "", "", 'badge', ''),
		"IMAX" => array('Intensité maximale', 'numeric', 'A', 1, "", "", 'badge', 'Mono'),
		"IMAX1" => array('Intensité maximale 1', 'numeric', 'A', 0, "", "", 'badge', 'Tri'),
		"IMAX2" => array('Intensité maximale 2', 'numeric', 'A', 0, "", "", 'badge', 'Tri'),
		"IMAX3" => array('Intensité maximale 3', 'numeric', 'A', 0, "", "", 'badge', 'Tri')
		);
	}

	public static function pull() {
		log::add('ecodevice','debug','start cron');
		foreach (eqLogic::byTypeAndSearhConfiguration('ecodevice', '"type":"carte"') as $eqLogic) {
			$eqLogic->scan();
		}
		log::add('ecodevice','debug','stop cron');
	}

	public function getUrl() {
		if ( $this->getConfiguration('type', '') == 'carte' )
		{
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
		else
		{
			$EcodeviceeqLogic = eqLogic::byId(substr ($this->getLogicalId(), 0, strpos($this->getLogicalId(),"_")));
			return $EcodeviceeqLogic->getUrl();
		}
	}

	public function preUpdate()
	{
		switch ($this->getConfiguration('type', '')) {
			case "carte":
				if ( $this->getIsEnable() )
				{
					log::add('ecodevice','debug','get '.$this->getUrl(). 'status.xml');
					$this->xmlstatus = @simplexml_load_file($this->getUrl(). 'status.xml');
					if ( $this->xmlstatus === false )
						throw new Exception(__('L\'ecodevice ne repond pas.',__FILE__)." ".$this->getName());
				}
				break;
			case "teleinfo":
				if ( $this->getIsEnable() ) {
					foreach (self::byType('ecodevice') as $eqLogic) {
						if ( substr($this->getLogicalId(), 0, strpos($this->getLogicalId(),"_")) == $eqLogic->getId() ) {
							$phase = $eqLogic->GetPhase(substr($this->getLogicalId(), strpos($this->getLogicalId(),"_")+2, 1));
							log::add('ecodevice','debug','Detection phase '.$phase);
						}
					}
					if ( $phase == "" )
					{
						throw new Exception(__('Le type de compteur est introuvable. Vérifier la communication entre l\'ecodevice et votre compteur.',__FILE__));
					}
				}
				break;
			case "compteur":
				if ( $this->getIsEnable() )
				{
					if ( $this->getConfiguration('typecompteur') == "" )
					{
						throw new Exception(__('Le type de compteur doit être défini.',__FILE__));
					}
				}
				break;
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
		switch ($this->getConfiguration('type', '')) {
			case "":
			case "carte":
				$this->setConfiguration('type', 'carte');
				$this->setIsVisible(0);
				break;
			case "teleinfo":
				$this->setIsEnable(0);
				$this->setIsVisible(0);
				break;
			case "compteur":
				$this->setIsEnable(0);
				$this->setIsVisible(0);
				break;
		}
	}

	public function postInsert()
	{
		switch ($this->getConfiguration('type', '')) {
			case "carte":
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
					if ( ! is_object(self::byLogicalId($this->getId()."_C".$compteurId, 'ecodevice')) ) {
						log::add('ecodevice','debug','Creation compteur : '.$this->getId().'_C'.$compteurId);
						$eqLogic = new ecodevice();
						$eqLogic->setEqType_name('ecodevice');
						$eqLogic->setConfiguration('type', 'compteur');
						$eqLogic->setIsEnable(0);
						$eqLogic->setName('Compteur ' . $compteurId);
						$eqLogic->setLogicalId($this->getId().'_C'.$compteurId);
						$eqLogic->setIsVisible(0);
						$eqLogic->save();
					}
				}
				for ($compteurId = 1; $compteurId <= 2; $compteurId++) {
					if ( ! is_object(self::byLogicalId($this->getId()."_T".$compteurId, 'ecodevice')) ) {
						log::add('ecodevice','debug','Creation teleinfo : '.$this->getId().'_T'.$compteurId);
						$eqLogic = new ecodevice();
						$eqLogic->setEqType_name('teleinfo');
						$eqLogic->setConfiguration('type', 'teleinfo');
						$eqLogic->setIsEnable(0);
						$eqLogic->setName('Teleinfo ' . $compteurId);
						$eqLogic->setLogicalId($this->getId().'_T'.$compteurId);
						$eqLogic->setIsVisible(0);
						$eqLogic->setCategory("energy", "Energie");
						$eqLogic->save();
					}
				}
				break;
			case "teleinfo":
				break;
			case "compteur":
				$consommationjour = $this->getCmd(null, 'consommationjour');
				if ( ! is_object($consommationjour) ) {
					$consommationjour = new ecodeviceCmd();
					$consommationjour->setName('Consommation journalière');
					$consommationjour->setEqLogic_id($this->getId());
					$consommationjour->setType('info');
					$consommationjour->setSubType('numeric');
					$consommationjour->setLogicalId('consommationjour');
					$consommationjour->setEventOnly(1);
					$consommationjour->setIsVisible(1);
					$consommationjour->setDisplay('generic_type','GENERIC_INFO');
					$consommationjour->setTemplate('dashboard', 'badge');
					$consommationjour->setTemplate('mobile', 'badge');  
					$consommationjour->setUnite("l");
					$consommationjour->save();
				}
				$consommationtotal = $this->getCmd(null, 'consommationtotal');
				if ( ! is_object($consommationtotal) ) {
					$consommationtotal = new ecodeviceCmd();
					$consommationtotal->setName('Consommation total');
					$consommationtotal->setEqLogic_id($this->getId());
					$consommationtotal->setType('info');
					$consommationtotal->setSubType('numeric');
					$consommationtotal->setLogicalId('consommationtotal');
					$consommationtotal->setEventOnly(1);
					$consommationtotal->setIsVisible(1);
					$consommationtotal->setDisplay('generic_type','GENERIC_INFO');
					$consommationtotal->setTemplate('dashboard', 'badge');
					$consommationtotal->setTemplate('mobile', 'badge');  
					$consommationtotal->setUnite("l");
					$consommationtotal->save();
				}
				$debitinstantane = $this->getCmd(null, 'debitinstantane');
				if ( ! is_object($debitinstantane) ) {
					$debitinstantane = new ecodeviceCmd();
					$debitinstantane->setName('Debit instantané');
					$debitinstantane->setEqLogic_id($this->getId());
					$debitinstantane->setType('info');
					$debitinstantane->setSubType('numeric');
					$debitinstantane->setLogicalId('debitinstantane');
					$debitinstantane->setEventOnly(1);
					$debitinstantane->setIsVisible(1);
					$debitinstantane->setDisplay('generic_type','GENERIC_INFO');
					$debitinstantane->setUnite("l/min");
					$debitinstantane->save();
				}
				$this->setConfiguration('typecompteur', "Eau");
				break;
		}
	}

	public function postUpdate()
	{
		switch ($this->getConfiguration('type', '')) {
			case "carte":
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
						if ( ! is_object(self::byLogicalId($this->getId()."_C".$compteurId, 'ecodevice')) ) {
							log::add('ecodevice','debug','Creation compteur : '.$this->getId().'_C'.$compteurId);
							$eqLogic = new ecodevice();
							$eqLogic->setEqType_name('ecodevice');
							$eqLogic->setConfiguration('type', 'compteur');
							$eqLogic->setIsEnable(0);
							$eqLogic->setName('Compteur ' . $compteurId);
							$eqLogic->setLogicalId($this->getId().'_C'.$compteurId);
							$eqLogic->setIsVisible(0);
							$eqLogic->save();
						}
						else
						{
							$eqLogic = self::byLogicalId($this->getId()."_C".$compteurId, 'ecodevice');
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
						if ( ! is_object(self::byLogicalId($this->getId()."_T".$compteurId, 'ecodevice')) ) {
							log::add('ecodevice','debug','Creation teleinfo : '.$this->getId().'_T'.$compteurId);
							$eqLogic = new ecodevice();
							$eqLogic->setEqType_name('ecodevice');
							$eqLogic->setConfiguration('type', 'teleinfo');
							$eqLogic->setIsEnable(0);
							$eqLogic->setName('Teleinfo ' . $compteurId);
							$eqLogic->setLogicalId($this->getId().'_T'.$compteurId);
							$eqLogic->setIsVisible(0);
							$eqLogic->setCategory("energy", "Energie");
							$eqLogic->save();
						}
					}
				}
				break;
			case "teleinfo":
				if ( $this->getIsEnable() ) {
					foreach (self::byType('ecodevice') as $eqLogic) {
						if ( substr($this->getLogicalId(), 0, strpos($this->getLogicalId(),"_")) == $eqLogic->getId() ) {
							$phase = $eqLogic->GetPhase(substr($this->getLogicalId(), strpos($this->getLogicalId(),"_")+2, 1));
							log::add('ecodevice','debug','Detection phase '.$phase);
						}
					}
					foreach( $this->getListeDefaultCommandes() as $label => $data)
					{
						if ( ( $this->getConfiguration('tarification') == "" || $this->getConfiguration('tarification') == $data[4] || $data[4] == "" ) && ( $phase == $data[7] || $data[7] == "" ) ) {
							$cmd = $this->getCmd(null, $label);
							if ( ! is_object($cmd) ) {
								$cmd = new ecodevice_Cmd();
								$cmd->setName($data[0]);
								$cmd->setEqLogic_id($this->getId());
								$cmd->setType('info');
								$cmd->setSubType($data[1]);
								$cmd->setLogicalId($label);
								$cmd->setUnite($data[2]);
								$cmd->setIsVisible($data[3]);
								$cmd->setEventOnly(1);
								$cmd->setDisplay('generic_type',$data[5]);
								$cmd->setTemplate('dashboard', $data[6]);
								$cmd->setTemplate('mobile', $data[6]);  
								$cmd->save();
							}
							else
							{
								if ( $cmd->getDisplay('generic_type') == "" )
								{
									$cmd->setDisplay('generic_type',$data[5]);
									$cmd->save();
								}
								if ( $cmd->getTemplate('dashboard') == "" )
								{
									$cmd->setTemplate('dashboard', $data[6]);
									$cmd->setTemplate('mobile', $data[6]);  
									$cmd->save();
								}
							}
						} else {
							$cmd = $this->getCmd(null, $label);
							if ( is_object($cmd) ) {
								$cmd->remove();
							}
						}
					}
				}
				break;
			case "compteur":
				break;
		}
	}

	public function postAjax()
	{
		switch ($this->getConfiguration('type', '')) {
			case "carte":
				break;
			case "teleinfo":
				break;
			case "compteur":
				if ( $this->getIsEnable() )
				{					
					foreach($this->getCmd() as $cmd)
					{	
						if ( ! in_array($cmd->getLogicalId(), array("consommationinstantane", "consommationjour", "consommationtotal", "debitinstantane", "tempsfonctionnement", "tempsfonctionnementminute", "nbimpulsiontotal", "nbimpulsionminute", "nbimpulsionjour") ) )
						{
							$cmd->remove();				
						}
					}			
					if ( $this->getConfiguration('typecompteur') == "Autre" )
					{
						$tempsfonctionnement = $this->getCmd(null, 'tempsfonctionnement');
						if ( ! is_object($tempsfonctionnement) ) {
							$tempsfonctionnement = new ecodeviceCmd();
							$tempsfonctionnement->setName('Temps de fonctionnement');
							$tempsfonctionnement->setEqLogic_id($this->getId());
							$tempsfonctionnement->setType('info');
							$tempsfonctionnement->setSubType('numeric');
							$tempsfonctionnement->setLogicalId('tempsfonctionnement');
							$tempsfonctionnement->setUnite("min");
							$tempsfonctionnement->setEventOnly(1);
							$tempsfonctionnement->setIsVisible(1);
							$tempsfonctionnement->setDisplay('generic_type','GENERIC_INFO');
							$tempsfonctionnement->save();
						}
						$tempsfonctionnementminute = $this->getCmd(null, 'tempsfonctionnementminute');
						if ( ! is_object($tempsfonctionnementminute) ) {
							$tempsfonctionnementminute = new ecodeviceCmd();
							$tempsfonctionnementminute->setName('Temps de fonctionnement par minute');
							$tempsfonctionnementminute->setEqLogic_id($this->getId());
							$tempsfonctionnementminute->setType('info');
							$tempsfonctionnementminute->setSubType('numeric');
							$tempsfonctionnementminute->setLogicalId('tempsfonctionnementminute');
							$tempsfonctionnementminute->setUnite("min/min");
							$tempsfonctionnementminute->setEventOnly(1);
							$tempsfonctionnementminute->setIsVisible(1);
							$tempsfonctionnementminute->setDisplay('generic_type','GENERIC_INFO');
							$tempsfonctionnementminute->save();
						}

						$nbimpulsiontotal = $this->getCmd(null, 'nbimpulsiontotal');
						if ( ! is_object($nbimpulsiontotal) ) {
							$nbimpulsiontotal = new ecodeviceCmd();
							$nbimpulsiontotal->setName('Nombre d impulsion total');
							$nbimpulsiontotal->setEqLogic_id($this->getId());
							$nbimpulsiontotal->setType('info');
							$nbimpulsiontotal->setSubType('numeric');
							$nbimpulsiontotal->setLogicalId('nbimpulsiontotal');
							$nbimpulsiontotal->setEventOnly(1);
							$nbimpulsiontotal->setIsVisible(1);
							$nbimpulsiontotal->setDisplay('generic_type','GENERIC_INFO');
							$nbimpulsiontotal->save();
						}
						$nbimpulsionminute = $this->getCmd(null, 'nbimpulsionminute');
						if ( ! is_object($nbimpulsionminute) ) {
							$nbimpulsionminute = new ecodeviceCmd();
							$nbimpulsionminute->setName('Nombre d impulsion par minute');
							$nbimpulsionminute->setEqLogic_id($this->getId());
							$nbimpulsionminute->setType('info');
							$nbimpulsionminute->setSubType('numeric');
							$nbimpulsionminute->setLogicalId('nbimpulsionminute');
							$nbimpulsionminute->setUnite("Imp/min");
							$nbimpulsionminute->setEventOnly(1);
							$nbimpulsionminute->setIsVisible(1);
							$nbimpulsionminute->setDisplay('generic_type','GENERIC_INFO');
							$nbimpulsionminute->save();
						}
						$nbimpulsionjour = $this->getCmd(null, 'nbimpulsionjour');
						if ( ! is_object($nbimpulsionjour) ) {
							$nbimpulsionjour = new ecodeviceCmd();
							$nbimpulsionjour->setName('Nombre d impulsion jour');
							$nbimpulsionjour->setEqLogic_id($this->getId());
							$nbimpulsionjour->setType('info');
							$nbimpulsionjour->setSubType('numeric');
							$nbimpulsionjour->setLogicalId('nbimpulsionjour');
							$nbimpulsionjour->setEventOnly(1);
							$nbimpulsionjour->setIsVisible(1);
							$nbimpulsionjour->setDisplay('generic_type','GENERIC_INFO');
							$nbimpulsionjour->save();
						}
					}
					elseif ( $this->getConfiguration('typecompteur') == "Fuel" )
					{
						$tempsfonctionnement = $this->getCmd(null, 'tempsfonctionnement');
						if ( is_object($tempsfonctionnement) ) {
							$tempsfonctionnement->remove();
						}
						$tempsfonctionnementminute = $this->getCmd(null, 'tempsfonctionnementminute');
						if ( is_object($tempsfonctionnementminute) ) {
							$tempsfonctionnementminute->remove();
						}
						$nbimpulsiontotal = $this->getCmd(null, 'nbimpulsiontotal');
						if ( is_object($nbimpulsiontotal) ) {
							$nbimpulsiontotal->remove();
						}
						$nbimpulsionminute = $this->getCmd(null, 'nbimpulsionminute');
						if ( is_object($nbimpulsionminute) ) {
							$nbimpulsionminute->remove();
						}
						$nbimpulsionjour = $this->getCmd(null, 'nbimpulsionjour');
						if ( is_object($nbimpulsionjour) ) {
							$nbimpulsionjour->remove();
						}
						$debitinstantane = $this->getCmd(null, 'debitinstantane');
						if ( is_object($debitinstantane) ) {
							$debitinstantane->remove();
						}
						$consommationinstantane = $this->getCmd(null, 'consommationinstantane');
						if ( ! is_object($consommationinstantane) ) {
							$consommationinstantane = new ecodevice_Cmd();
							$consommationinstantane->setName('Consommation instantané');
							$consommationinstantane->setEqLogic_id($this->getId());
							$consommationinstantane->setType('info');
							$consommationinstantane->setSubType('numeric');
							$consommationinstantane->setLogicalId('consommationinstantane');
							$consommationinstantane->setUnite("ml/h");
							$consommationinstantane->setEventOnly(1);
							$consommationinstantane->setIsVisible(1);
							$consommationinstantane->setDisplay('generic_type','GENERIC_INFO');
							$consommationinstantane->save();
						}
						$consommationtotal = $this->getCmd(null, 'consommationtotal');
						if ( ! is_object($consommationtotal) ) {
							$consommationtotal = new ecodevice_Cmd();
							$consommationtotal->setName('Consommation total');
							$consommationtotal->setEqLogic_id($this->getId());
							$consommationtotal->setType('info');
							$consommationtotal->setSubType('numeric');
							$consommationtotal->setLogicalId('consommationtotal');
							$consommationtotal->setUnite("ml");
							$consommationtotal->setEventOnly(1);
							$consommationtotal->setIsVisible(1);
							$consommationtotal->setDisplay('generic_type','GENERIC_INFO');
							$consommationtotal->setTemplate('dashboard', 'badge');
							$consommationtotal->setTemplate('mobile', 'badge');  
							$consommationtotal->save();
						}
						$consommationjour = $this->getCmd(null, 'consommationjour');
						if ( ! is_object($consommationjour) ) {
							$consommationjour = new ecodevice_Cmd();
							$consommationjour->setName('Consommation journalière');
							$consommationjour->setEqLogic_id($this->getId());
							$consommationjour->setType('info');
							$consommationjour->setSubType('numeric');
							$consommationjour->setLogicalId('consommationjour');
							$consommationjour->setUnite("ml");
							$consommationjour->setEventOnly(1);
							$consommationjour->setIsVisible(1);
							$consommationjour->setDisplay('generic_type','GENERIC_INFO');
							$consommationjour->setTemplate('dashboard', 'badge');
							$consommationjour->setTemplate('mobile', 'badge');  
							$consommationjour->save();
						}
					}
					elseif ( $this->getConfiguration('typecompteur') == "Eau" )
					{
						$tempsfonctionnement = $this->getCmd(null, 'tempsfonctionnement');
						if ( is_object($tempsfonctionnement) ) {
							$tempsfonctionnement->remove();
						}
						$tempsfonctionnementminute = $this->getCmd(null, 'tempsfonctionnementminute');
						if ( is_object($tempsfonctionnementminute) ) {
							$tempsfonctionnementminute->remove();
						}
						$nbimpulsiontotal = $this->getCmd(null, 'nbimpulsiontotal');
						if ( is_object($nbimpulsiontotal) ) {
							$nbimpulsiontotal->remove();
						}
						$nbimpulsionminute = $this->getCmd(null, 'nbimpulsionminute');
						if ( is_object($nbimpulsionminute) ) {
							$nbimpulsionminute->remove();
						}
						$nbimpulsionjour = $this->getCmd(null, 'nbimpulsionjour');
						if ( is_object($nbimpulsionjour) ) {
							$nbimpulsionjour->remove();
						}
						$consommationinstantane = $this->getCmd(null, 'consommationinstantane');
						if ( is_object($consommationinstantane) ) {
							$consommationinstantane->remove();
						}
						$consommationjour = $this->getCmd(null, 'consommationjour');
						if ( ! is_object($consommationjour) ) {
							$consommationjour = new ecodevice_Cmd();
							$consommationjour->setName('Consommation journalière');
							$consommationjour->setEqLogic_id($this->getId());
							$consommationjour->setType('info');
							$consommationjour->setSubType('numeric');
							$consommationjour->setLogicalId('consommationjour');
							$consommationjour->setEventOnly(1);
							$consommationjour->setIsVisible(1);
							$consommationjour->setDisplay('generic_type','GENERIC_INFO');
							$consommationjour->setTemplate('dashboard', 'badge');
							$consommationjour->setTemplate('mobile', 'badge');  
							$consommationjour->setUnite("l");
							$consommationjour->save();
						}
						$consommationtotal = $this->getCmd(null, 'consommationtotal');
						if ( ! is_object($consommationtotal) ) {
							$consommationtotal = new ecodevice_Cmd();
							$consommationtotal->setName('Consommation total');
							$consommationtotal->setEqLogic_id($this->getId());
							$consommationtotal->setType('info');
							$consommationtotal->setSubType('numeric');
							$consommationtotal->setLogicalId('consommationtotal');
							$consommationtotal->setEventOnly(1);
							$consommationtotal->setIsVisible(1);
							$consommationtotal->setDisplay('generic_type','GENERIC_INFO');
							$consommationtotal->setTemplate('dashboard', 'badge');
							$consommationtotal->setTemplate('mobile', 'badge');  
							$consommationtotal->setUnite("l");
							$consommationtotal->save();
						}
						$debitinstantane = $this->getCmd(null, 'debitinstantane');
						if ( ! is_object($debitinstantane) ) {
							$debitinstantane = new ecodevice_Cmd();
							$debitinstantane->setName('Debit instantané');
							$debitinstantane->setEqLogic_id($this->getId());
							$debitinstantane->setType('info');
							$debitinstantane->setSubType('numeric');
							$debitinstantane->setLogicalId('debitinstantane');
							$debitinstantane->setEventOnly(1);
							$debitinstantane->setIsVisible(1);
							$debitinstantane->setDisplay('generic_type','GENERIC_INFO');
							$debitinstantane->setUnite("l/min");
							$debitinstantane->save();
						}
					}
					elseif ( $this->getConfiguration('typecompteur') == "Gaz" )
					{
						$tempsfonctionnement = $this->getCmd(null, 'tempsfonctionnement');
						if ( is_object($tempsfonctionnement) ) {
							$tempsfonctionnement->remove();
						}
						$tempsfonctionnementminute = $this->getCmd(null, 'tempsfonctionnementminute');
						if ( is_object($tempsfonctionnementminute) ) {
							$tempsfonctionnementminute->remove();
						}
						$nbimpulsiontotal = $this->getCmd(null, 'nbimpulsiontotal');
						if ( is_object($nbimpulsiontotal) ) {
							$nbimpulsiontotal->remove();
						}
						$nbimpulsionminute = $this->getCmd(null, 'nbimpulsionminute');
						if ( is_object($nbimpulsionminute) ) {
							$nbimpulsionminute->remove();
						}
						$nbimpulsionjour = $this->getCmd(null, 'nbimpulsionjour');
						if ( is_object($nbimpulsionjour) ) {
							$nbimpulsionjour->remove();
						}

						$consommationinstantane = $this->getCmd(null, 'consommationinstantane');
						if ( is_object($consommationinstantane) ) {
							$consommationinstantane->remove();
						}
						$consommationjour = $this->getCmd(null, 'consommationjour');
						if ( ! is_object($consommationjour) ) {
							$consommationjour = new ecodevice_Cmd();
							$consommationjour->setName('Consommation journalière');
							$consommationjour->setEqLogic_id($this->getId());
							$consommationjour->setType('info');
							$consommationjour->setSubType('numeric');
							$consommationjour->setLogicalId('consommationjour');
							$consommationjour->setEventOnly(1);
							$consommationjour->setIsVisible(1);
							$consommationjour->setDisplay('generic_type','GENERIC_INFO');
							$consommationjour->setTemplate('dashboard', 'badge');
							$consommationjour->setTemplate('mobile', 'badge');  
							$consommationjour->setUnite("dm³");
							$consommationjour->save();
						}
						$consommationtotal = $this->getCmd(null, 'consommationtotal');
						if ( ! is_object($consommationtotal) ) {
							$consommationtotal = new ecodevice_Cmd();
							$consommationtotal->setName('Consommation total');
							$consommationtotal->setEqLogic_id($this->getId());
							$consommationtotal->setType('info');
							$consommationtotal->setSubType('numeric');
							$consommationtotal->setLogicalId('consommationtotal');
							$consommationtotal->setEventOnly(1);
							$consommationtotal->setIsVisible(1);
							$consommationtotal->setDisplay('generic_type','GENERIC_INFO');
							$consommationtotal->setTemplate('dashboard', 'badge');
							$consommationtotal->setTemplate('mobile', 'badge');  
							$consommationtotal->setUnite("dm³");
							$consommationtotal->save();
						}
						$debitinstantane = $this->getCmd(null, 'debitinstantane');
						if ( ! is_object($debitinstantane) ) {
							$debitinstantane = new ecodevice_Cmd();
							$debitinstantane->setName('Debit instantané');
							$debitinstantane->setEqLogic_id($this->getId());
							$debitinstantane->setType('info');
							$debitinstantane->setSubType('numeric');
							$debitinstantane->setLogicalId('debitinstantane');
							$debitinstantane->setEventOnly(1);
							$debitinstantane->setIsVisible(1);
							$debitinstantane->setDisplay('generic_type','GENERIC_INFO');
							$debitinstantane->setUnite("dm³/min");
							$debitinstantane->save();
						}
					}
					elseif ( $this->getConfiguration('typecompteur') == "Electricité" )
					{
						$tempsfonctionnement = $this->getCmd(null, 'tempsfonctionnement');
						if ( is_object($tempsfonctionnement) ) {
							$tempsfonctionnement->remove();
						}
						$tempsfonctionnementminute = $this->getCmd(null, 'tempsfonctionnementminute');
						if ( is_object($tempsfonctionnementminute) ) {
							$tempsfonctionnementminute->remove();
						}
						$nbimpulsiontotal = $this->getCmd(null, 'nbimpulsiontotal');
						if ( is_object($nbimpulsiontotal) ) {
							$nbimpulsiontotal->remove();
						}
						$nbimpulsionminute = $this->getCmd(null, 'nbimpulsionminute');
						if ( is_object($nbimpulsionminute) ) {
							$nbimpulsionminute->remove();
						}
						$nbimpulsionjour = $this->getCmd(null, 'nbimpulsionjour');
						if ( is_object($nbimpulsionjour) ) {
							$nbimpulsionjour->remove();
						}

						$consommationinstantane = $this->getCmd(null, 'consommationinstantane');
						if ( is_object($consommationinstantane) ) {
							$consommationinstantane->remove();
						}
						$consommationjour = $this->getCmd(null, 'consommationjour');
						if ( ! is_object($consommationjour) ) {
							$consommationjour = new ecodevice_Cmd();
							$consommationjour->setName('Consommation journalière');
							$consommationjour->setEqLogic_id($this->getId());
							$consommationjour->setType('info');
							$consommationjour->setSubType('numeric');
							$consommationjour->setLogicalId('consommationjour');
							$consommationjour->setEventOnly(1);
							$consommationjour->setIsVisible(1);
							$consommationjour->setDisplay('generic_type','GENERIC_INFO');
							$consommationjour->setTemplate('dashboard', 'badge');
							$consommationjour->setTemplate('mobile', 'badge');  
							$consommationjour->setUnite("Wh");
							$consommationjour->save();
						}
						$consommationtotal = $this->getCmd(null, 'consommationtotal');
						if ( ! is_object($consommationtotal) ) {
							$consommationtotal = new ecodevice_Cmd();
							$consommationtotal->setName('Consommation total');
							$consommationtotal->setEqLogic_id($this->getId());
							$consommationtotal->setType('info');
							$consommationtotal->setSubType('numeric');
							$consommationtotal->setLogicalId('consommationtotal');
							$consommationtotal->setEventOnly(1);
							$consommationtotal->setIsVisible(1);
							$consommationtotal->setDisplay('generic_type','GENERIC_INFO');
							$consommationtotal->setTemplate('dashboard', 'badge');
							$consommationtotal->setTemplate('mobile', 'badge');  
							$consommationtotal->setUnite("Wh");
							$consommationtotal->save();
						}
						$debitinstantane = $this->getCmd(null, 'debitinstantane');
						if ( ! is_object($debitinstantane) ) {
							$debitinstantane = new ecodevice_Cmd();
							$debitinstantane->setName('Consommation instantanée');
							$debitinstantane->setEqLogic_id($this->getId());
							$debitinstantane->setType('info');
							$debitinstantane->setSubType('numeric');
							$debitinstantane->setLogicalId('debitinstantane');
							$debitinstantane->setEventOnly(1);
							$debitinstantane->setIsVisible(1);
							$debitinstantane->setDisplay('generic_type','GENERIC_INFO');
							$debitinstantane->setUnite("Wh");
							$debitinstantane->save();
						}
					}
				}
				break;
		}
	}
	
	public function preRemove()
	{
		if ( $this->getConfiguration('type', '') == 'carte' )
		{
			foreach (eqLogic::byTypeAndSearhConfiguration('ecodevice', '"type":"compteur"') as $eqLogic) {
				if ( substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
					log::add('ecodevice','debug','Suppression compteur : '.$eqLogic->getName());
					$eqLogic->remove();
				}
			}
			foreach (eqLogic::byTypeAndSearhConfiguration('ecodevice', '"type":"teleinfo"') as $eqLogic) {
				if ( substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
					log::add('ecodevice','debug','Suppression teleinfo : '.$eqLogic->getName());
					$eqLogic->remove();
				}
			}
		}
	}

	public function configPush($url_serveur = null) {
		switch ($this->getConfiguration('type', '')) {
			case "carte":
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
					foreach (eqLogic::byTypeAndSearhConfiguration('ecodevice', '"type":"compteur"') as $eqLogic) {
						if ( $eqLogic->getIsEnable() && substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
							$eqLogic->configPush($this->getUrl());
						}
					}
					foreach (eqLogic::byTypeAndSearhConfiguration('ecodevice', '"type":"teleinfo"') as $eqLogic) {
						if ( $eqLogic->getIsEnable() && substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
							$eqLogic->configPush($this->getUrl());
						}
					}
				}
				break;
			case "teleinfo":
				$gceid = substr($this->getLogicalId(), strpos($this->getLogicalId(),"_")+2, 1);
				$url_serveur .= 'protect/settings/notif'.$gceid.'P.htm';
				for ($compteur = 0; $compteur < 6; $compteur++) {
					log::add('ecodevice','debug','Url '.$url_serveur);
					$data = array('num' => $compteur + ($gceid -1)*6,
							'act' => $compteur+3,
							'serv' => config::byKey('internalAddr'),
							'port' => 80,
							'url' => '/jeedom/core/api/jeeApi.php?api='.jeedom::getApiKey('ecodevice').'&type=ecodevice&plugin=ecodevice&id='.substr($this->getLogicalId(), 0, strpos($this->getLogicalId(),"_")).'&message=data_change');
		//					'url' => '/jeedom/core/api/jeeApi.php?api='.jeedom::getApiKey('ecodevice').'&type=ecodevice&id='.$this->getId().'&message=data_change');
					
					$options = array(
						'http' => array(
							'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
							'method'  => 'POST',
							'content' => http_build_query($data),
						),
					);
					$context  = stream_context_create($options);
					$result = @file_get_contents($url_serveur, false, $context);
				}
				break;
			case "compteur":
				break;
		}
	}

	public function event() {
		switch ($this->getConfiguration('type', '')) {
			case "carte":
				foreach (eqLogic::byType('ecodevice') as $eqLogic) {
					if ( $eqLogic->getId() == init('id') ) {
						$eqLogic->scan();
					}
				}
				break;
			case "teleinfo":
				$cmd = ecodevice_Cmd::byId(init('id'));
				if (!is_object($cmd)) {
					throw new Exception('Commande ID virtuel inconnu : ' . init('id'));
				}
				$cmd->event(init('value'));
				break;
			case "compteur":
			   $cmd = ecodevice_Cmd::byId(init('id'));
				if (!is_object($cmd)) {
					throw new Exception('Commande ID virtuel inconnu : ' . init('id'));
				}
				$cmd->event(init('value'));
				break;
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
			foreach (eqLogic::byTypeAndSearhConfiguration('ecodevice', '"type":"compteur"') as $eqLogic) {
				if ( $eqLogic->getIsEnable() && substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = substr($eqLogic->getLogicalId(), strpos($eqLogic->getLogicalId(),"_")+2, 1);
					if ( $eqLogic->getConfiguration('typecompteur') == "Temps de fonctionnement" )
					{
						# Verifie la configuration des compteurs de temps de fonctionnement
						$xpathModele = '//c'.$gceid.'_fuel';
						$status = $this->xmlstatus->xpath($xpathModele);

						if ( count($status) != 0 )
						{
							if ( $status[0] != "selected" )
							{
								throw new Exception(__('Le compteur '.$eqLogic->getName().' doit être configuré en mode fuel dans l\'ecodevice.',__FILE__));
							}
						}
						else
						{
							throw new Exception(__('Le compteur '.$eqLogic->getName().' doit être configuré en mode fuel dans l\'ecodevice.',__FILE__));
						}
						$xpathModele = '//count'.$gceid;
						$status = $this->xmlstatus->xpath($xpathModele);
						
						if ( count($status) != 0 )
						{
							$nbimpulsiontotal_cmd = $eqLogic->getCmd(null, 'nbimpulsiontotal');
							$nbimpulsiontotal = $nbimpulsiontotal_cmd->execCmd();
							$nbimpulsionminute_cmd = $eqLogic->getCmd(null, 'nbimpulsionminute');
							if ($nbimpulsiontotal != $status[0]) {
								log::add('ecodevice','debug',"Change nbimpulsiontotal of ".$eqLogic->getName());
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
						$eqLogic_cmd = $eqLogic->getCmd(null, 'tempsfonctionnement');
						$tempsfonctionnement = $eqLogic_cmd->execCmd();
						$eqLogic_cmd_evol = $eqLogic->getCmd(null, 'tempsfonctionnementminute');
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
					elseif ( $eqLogic->getConfiguration('typecompteur') == "Fuel" )
					{
						# Verifie la configuration des compteurs fuel
						$xpathModele = '//c'.$gceid.'_fuel';
						$status = $this->xmlstatus->xpath($xpathModele);

						if ( count($status) != 0 )
						{
							if ( $status[0] != "selected" )
							{
								throw new Exception(__('Le compteur '.$eqLogic->getName().' doit être configuré en mode fuel dans l\'ecodevice.',__FILE__));
							}
						}
						else
						{
							throw new Exception(__('Le compteur '.$eqLogic->getName().' doit être configuré en mode fuel dans l\'ecodevice.',__FILE__));
						}
						$xpathModele = '//count'.$gceid;
						$status = $this->xmlstatus->xpath($xpathModele);
						if ( count($status) != 0 )
						{
							$consommationtotal = intval($status[0]);
							$consommationtotal_cmd = $eqLogic->getCmd(null, 'consommationtotal');
							if ($consommationtotal_cmd->execCmd() != $consommationtotal) {
								log::add('ecodevice','debug',"Change consommationtotal of ".$eqLogic->getName());
								$consommationtotal_cmd->setCollectDate(date('Y-m-d H:i:s'));
								$consommationtotal_cmd->event($consommationtotal);
							}
						}
						$xpathModele = '//c'.$gceid."day";
						$status = $this->xmlstatus->xpath($xpathModele);
						if ( count($status) != 0 )
						{
							$consommationjour = intval($status[0]);
							$consommationjour_cmd = $eqLogic->getCmd(null, 'consommationjour');
							if ($consommationjour_cmd->execCmd() != $consommationjour) {
								log::add('ecodevice','debug',"Change consommationjour of ".$eqLogic->getName());
								$consommationjour_cmd->setCollectDate(date('Y-m-d H:i:s'));
								$consommationjour_cmd->event($consommationjour);
							}
						}
						$xpathModele = '//meter'.($gceid+2);
						$status = $this->xmlstatus->xpath($xpathModele);
						if ( count($status) != 0 )
						{
							$consommationinstantane = intval($status[0]) * 10;
							$consommationinstantane_cmd = $eqLogic->getCmd(null, 'consommationinstantane');
							if ($consommationinstantane_cmd->execCmd() != $consommationinstantane) {
								log::add('ecodevice','debug',"Change consommationinstantane of ".$eqLogic->getName());
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
							$eqLogic_cmd = $eqLogic->getCmd(null, 'debitinstantane');
							if ($eqLogic_cmd->execCmd() != $eqLogic_cmd->formatValue($status[0])) {
								log::add('ecodevice','debug',"Change debitinstantane of ".$eqLogic->getName());
								$eqLogic_cmd->setCollectDate('');
								$eqLogic_cmd->event($status[0]);
							}
						}
						$xpathModele = '//c'.$gceid.'day';
						$status = $this->xmlstatus->xpath($xpathModele);
						
						if ( count($status) != 0 )
						{
							$eqLogic_cmd = $eqLogic->getCmd(null, 'consommationjour');
							if ($eqLogic_cmd->execCmd() != $eqLogic_cmd->formatValue($status[0])) {
								log::add('ecodevice','debug',"Change consommationjour of ".$eqLogic->getName());
								$eqLogic_cmd->setCollectDate('');
								$eqLogic_cmd->event($status[0]);
							}
						}
						$xpathModele = '//count'.$gceid;
						$status = $this->xmlstatus->xpath($xpathModele);
						
						if ( count($status) != 0 )
						{
							$consommationtotal_cmd = $eqLogic->getCmd(null, 'consommationtotal');
							if ($consommationtotal_cmd->execCmd() != $status[0]) {
								log::add('ecodevice','debug',"Change consommationtotal of ".$eqLogic->getName());
								$consommationtotal_cmd->setCollectDate(date('Y-m-d H:i:s'));
								$consommationtotal_cmd->event($status[0]);
							}
						}
					}
				}
			}
			foreach (eqLogic::byTypeAndSearhConfiguration('ecodevice', '"type":"teleinfo"') as $eqLogic) {
				if ( $eqLogic->getIsEnable() && substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = substr($eqLogic->getLogicalId(), strpos($eqLogic->getLogicalId(),"_")+2, 1);
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
								$eqLogic_cmd = $eqLogic->getCmd(null, substr($item, 3));
								if ( is_object($eqLogic_cmd) ) {
									$eqLogic_cmd_evol = $eqLogic->getCmd(null, substr($item, 3)."_evolution");
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
			foreach (eqLogic::byTypeAndSearhConfiguration('ecodevice', '"type":"compteur"') as $eqLogic) {
				if ( $eqLogic->getIsEnable() && substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = substr($eqLogic->getLogicalId(), strpos($eqLogic->getLogicalId(),"_")+2, 1);
					if ( $eqLogic->getConfiguration('typecompteur') == "Fuel" )
					{
						# mode fuel
						$xpathModele = '//meter'.($gceid+2);
						$status = $this->xmlstatus->xpath($xpathModele);
						
						if ( count($status) != 0 )
						{
							$consommationinstantane = $status[0] / 100;
							$eqLogic_cmd = $eqLogic->getCmd(null, 'consommationinstantane');
							if ($eqLogic_cmd->execCmd() != $eqLogic_cmd->formatValue($consommationinstantane)) {
								log::add('ecodevice','debug',"Change consommationinstantane of ".$eqLogic->getName());
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
							$eqLogic_cmd = $eqLogic->getCmd(null, 'debitinstantane');
							if ($eqLogic_cmd->execCmd() != $eqLogic_cmd->formatValue($status[0])) {
								log::add('ecodevice','debug',"Change debitinstantane of ".$eqLogic->getName());
								$eqLogic_cmd->setCollectDate('');
								$eqLogic_cmd->event($status[0]);
							}
						}
					}
				}
			}
			foreach (eqLogic::byTypeAndSearhConfiguration('ecodevice', '"type":"teleinfo"') as $eqLogic) {
				if ( $eqLogic->getIsEnable() && substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = substr($eqLogic->getLogicalId(), strpos($eqLogic->getLogicalId(),"_")+2, 1);
					$item = "T".$gceid."_PPAP";
					$xpathModele = '//'.$item;
					$status = $this->xmlstatus->xpath($xpathModele);
					
					if ( count($status) != 0 )
					{
						$eqLogic_cmd = $eqLogic->getCmd(null, substr($item, 3));
						if ( is_object($eqLogic_cmd) && $eqLogic_cmd->execCmd() != $eqLogic_cmd->formatValue($status[0])) {
							log::add('ecodevice','debug',"Change ".$item." of ".$eqLogic->getName());
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
 
	public function getListeName()
	{
		return (substr($this->getLogicalId(), strpos($this->getLogicalId(),"_")+2, 1)+1)." - ".parent::getName();
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
?>