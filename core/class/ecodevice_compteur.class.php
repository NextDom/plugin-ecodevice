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
						__('Electricité',__FILE__));
	}

	public function getListeName()
	{
		return (substr($this->getLogicalId(), strpos($this->getLogicalId(),"_")+2, 1)+1)." - ".parent::getName();
	}

	public function postInsert()
	{
        $nbimpulsion = $this->getCmd(null, 'nbimpulsion');
        if ( ! is_object($nbimpulsion) ) {
            $nbimpulsion = new ecodevice_compteurCmd();
			$nbimpulsion->setName('Nombre d impulsion');
			$nbimpulsion->setEqLogic_id($this->getId());
			$nbimpulsion->setType('info');
			$nbimpulsion->setSubType('numeric');
			$nbimpulsion->setLogicalId('nbimpulsion');
			$nbimpulsion->setEventOnly(1);
			$nbimpulsion->setDisplay('generic_type','GENERIC_INFO');
			$nbimpulsion->save();
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
			$nbimpulsionminute->setConfiguration('calcul', '#brut#');
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
			$nbimpulsionjour->setDisplay('generic_type','GENERIC_INFO');
			$nbimpulsionjour->save();
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
			$nbimpulsiontotal->setDisplay('generic_type','GENERIC_INFO');
			$nbimpulsiontotal->save();
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
			$debitinstantane->setDisplay('generic_type','GENERIC_INFO');
			$debitinstantane->save();
		}
		$this->setConfiguration('typecompteur', "Eau");
	}

	public function preUpdate()
	{
		if ( $this->getConfiguration('typecompteur') == "" )
		{
			throw new Exception(__('Le type de compteur pour '.$this->getName().' doit être définit.',__FILE__));
		}
	}

	public function postUpdate()
	{
 		if ( $this->getConfiguration('typecompteur') == "Fuel" )
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
				$tempsfonctionnement->setIsVisible(0);
				$tempsfonctionnement->setDisplay('generic_type','GENERIC_INFO');
				$tempsfonctionnement->save();
			}
			else
			{
				if ( $tempsfonctionnement->getDisplay('generic_type') == "" )
				{
					$tempsfonctionnement->setDisplay('generic_type','GENERIC_INFO');
					$tempsfonctionnement->save();
				}
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
				$tempsfonctionnementminute->setConfiguration('calcul', '#brut#');
				$tempsfonctionnementminute->setEventOnly(1);
				$tempsfonctionnementminute->setIsVisible(0);
				$tempsfonctionnementminute->setDisplay('generic_type','GENERIC_INFO');
				$tempsfonctionnementminute->save();
			}
			else
			{
				if ( $tempsfonctionnementminute->getDisplay('generic_type') == "" )
				{
					$tempsfonctionnementminute->setDisplay('generic_type','GENERIC_INFO');
					$tempsfonctionnementminute->save();
				}
			}
			$nbimpulsionminute = $this->getCmd(null, 'nbimpulsionminute');
			if ( is_object($nbimpulsionminute) ) {
				#$nbimpulsionminute->remove();
			}
			$nbimpulsionjour = $this->getCmd(null, 'nbimpulsionjour');
			if ( is_object($nbimpulsionjour) ) {
				#$nbimpulsionjour->remove();
			}
			$nbimpulsiontotal = $this->getCmd(null, 'nbimpulsiontotal');
			if ( is_object($nbimpulsiontotal) ) {
				#$nbimpulsiontotal->remove();
			}
			$debitinstantane = $this->getCmd(null, 'debitinstantane');
			if ( is_object($debitinstantane) ) {
				#$debitinstantane->remove();
			}
		}
		else
		{
			$tempsfonctionnement = $this->getCmd(null, 'tempsfonctionnement');
			if ( is_object($tempsfonctionnement) ) {
				#$tempsfonctionnement->remove();
			}
			$tempsfonctionnementminute = $this->getCmd(null, 'tempsfonctionnementminute');
			if ( is_object($tempsfonctionnementminute) ) {
				#$tempsfonctionnementminute->remove();
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
				$nbimpulsionminute->setConfiguration('calcul', '#brut#');
				$nbimpulsionminute->setEventOnly(1);
				$nbimpulsionminute->setDisplay('generic_type','GENERIC_INFO');
				$nbimpulsionminute->save();
			}
			else
			{
				if ( $nbimpulsionminute->getDisplay('generic_type') == "" )
				{
					$nbimpulsionminute->setDisplay('generic_type','GENERIC_INFO');
					$nbimpulsionminute->save();
				}
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
				$nbimpulsionjour->setDisplay('generic_type','GENERIC_INFO');
				$nbimpulsionjour->save();
			}
			else
			{
				if ( $nbimpulsionjour->getDisplay('generic_type') == "" )
				{
					$nbimpulsionjour->setDisplay('generic_type','GENERIC_INFO');
					$nbimpulsionjour->save();
				}
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
				$nbimpulsiontotal->setDisplay('generic_type','GENERIC_INFO');
				$nbimpulsiontotal->save();
			}
			else
			{
				if ( $nbimpulsiontotal->getDisplay('generic_type') == "" )
				{
					$nbimpulsiontotal->setDisplay('generic_type','GENERIC_INFO');
					$nbimpulsiontotal->save();
				}
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
				$debitinstantane->setDisplay('generic_type','GENERIC_INFO');
				$debitinstantane->save();
			}
			else
			{
				if ( $nbimpulsiontotal->getDisplay('generic_type') == "" )
				{
					$nbimpulsiontotal->setDisplay('generic_type','GENERIC_INFO');
					$nbimpulsiontotal->save();
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
    public function preSave() {
        if ( $this->getLogicalId() == 'nbimpulsionminute' ) {
            $calcul = $this->getConfiguration('calcul');
            if ( ! preg_match("/#brut#/", $calcul) ) {
				throw new Exception(__('La formule doit contenir une référecence à #brut#.',__FILE__));
			}
        }
        if ( $this->getLogicalId() == 'tempsfonctionnementminute' ) {
            $calcul = $this->getConfiguration('calcul');
            if ( ! preg_match("/#brut#/", $calcul) ) {
				throw new Exception(__('La formule doit contenir une référecence à #brut#.',__FILE__));
			}
        }
    }

    public function event($_value, $_loop = 1) {
        if ($this->getLogicalId() == 'nbimpulsionminute') {
			try {
				$calcul = $this->getConfiguration('calcul');
				$calcul = preg_replace("/#brut#/", $_value, $calcul);
				$calcul = scenarioExpression::setTags($calcul);
				$result = evaluate($calcul);
				parent::event($result, $_loop);
			} catch (Exception $e) {
				$EqLogic = $this->getEqLogic();
				log::add('ecodevice', 'error', $EqLogic->getName()." error in ".$this->getConfiguration('calcul')." : ".$e->getMessage());
				return scenarioExpression::setTags(str_replace('"', '', cmd::cmdToValue($this->getConfiguration('calcul'))));
			}
		} else {
			parent::event($_value, $_loop);
		}
    }
}
?>