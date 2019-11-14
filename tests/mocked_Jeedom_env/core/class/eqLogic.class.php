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

require_once('../../mocked_core.php');
/**
 * Mock de la class eqLogic
 */
class eqLogic
{
    public function setConfiguration($_key, $_value) {
        $this->_attributesMocked[$_key] = $_value;
        return $this;
    }
    public function getConfiguration($_key) {
        return $this->_attributesMocked[$_key];
    }
    public static function byId($_id) {
        if ( file_exists("data/eqLogic".$_id.".json") )
        {
            $string = file_get_contents("data/eqLogic".$_id.".json");
            $json_a = json_decode($string, true);
            $currentEqLogic = new $json_a["eqType_name"];
            foreach ($json_a["configuration"] as $key => $val) {
                $currentEqLogic->setConfiguration($key, $val);
            }
            $currentEqLogic->isEnable = $json_a["isEnable"];
            $currentEqLogic->isVisible = $json_a["isVisible"];
            $currentEqLogic->logicalId = $json_a["logicalId"];

            return $currentEqLogic;
        }
        else {
            return;
        }
    }

    public function getIsVisible($_default = 0) {
		if ($this->isVisible == '' || !is_numeric($this->isVisible))
        {
			return $_default;
		}
		return $this->isVisible;
	}

    public function getIsEnable($_default = 0) {
		if ($this->isEnable == '' || !is_numeric($this->isEnable))
        {
			return $_default;
	    }
		return $this->isEnable;
	}

    public function getLogicalId() {
		return $this->logicalId;
	}
}
