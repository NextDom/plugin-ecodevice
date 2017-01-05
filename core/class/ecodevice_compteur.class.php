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

class ecodevice_compteur extends eqLogic {
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

	public function getListeName()
	{
		return (substr($this->getLogicalId(), strpos($this->getLogicalId(),"_")+2, 1)+1)." - ".parent::getName();
	}

	public function postInsert()
	{
		$consommationjour = $this->getCmd(null, 'consommationjour');
		if ( ! is_object($consommationjour) ) {
			$consommationjour = new ecodevice_compteurCmd();
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
			$consommationtotal = new ecodevice_compteurCmd();
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
			$debitinstantane = new ecodevice_compteurCmd();
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
	}

	public function preUpdate()
	{
		if ( $this->getIsEnable() )
		{
			if ( $this->getConfiguration('typecompteur') == "" )
			{
				throw new Exception(__('Le type de compteur doit être défini.',__FILE__));
			}
		}
	}

	public function postAjax()
	{
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
					$tempsfonctionnement = new ecodevice_compteurCmd();
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
					$tempsfonctionnementminute = new ecodevice_compteurCmd();
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
					$nbimpulsiontotal = new ecodevice_compteurCmd();
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
					$nbimpulsionminute = new ecodevice_compteurCmd();
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
					$nbimpulsionjour = new ecodevice_compteurCmd();
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
					$consommationinstantane = new ecodevice_compteurCmd();
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
					$consommationtotal = new ecodevice_compteurCmd();
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
					$consommationjour = new ecodevice_compteurCmd();
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
					$consommationjour = new ecodevice_compteurCmd();
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
					$consommationtotal = new ecodevice_compteurCmd();
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
					$debitinstantane = new ecodevice_compteurCmd();
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
					$consommationjour = new ecodevice_compteurCmd();
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
					$consommationtotal = new ecodevice_compteurCmd();
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
					$debitinstantane = new ecodevice_compteurCmd();
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
					$consommationjour = new ecodevice_compteurCmd();
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
					$consommationtotal = new ecodevice_compteurCmd();
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
					$debitinstantane = new ecodevice_compteurCmd();
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
	}

    public static function event() {
        $cmd = ecodevice_compteurCmd::byId(init('id'));
        if (!is_object($cmd)) {
            throw new Exception('Commande ID virtuel inconnu : ' . init('id'));
        }
		$cmd->event(init('value'));
    }

	public function configPush($url) {
	}

    public function getLinkToConfiguration() {
        return 'index.php?v=d&p=ecodevice&m=ecodevice&id=' . $this->getId();
    }
    /*     * **********************Getteur Setteur*************************** */
}

class ecodevice_compteurCmd extends cmd 
{
}
?>
