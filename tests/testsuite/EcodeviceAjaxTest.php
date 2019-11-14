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

use PHPUnit\Framework\TestCase;

require_once('../../core/php/core.inc.php');

class EcodeviceAjaxTest extends TestCase
{
    /**
     * Appelée avant chaque test
     */
    protected function setUp()
    {
        MockedActions::clear();
    }

    /**
     * Appelée après chaque test
     */
    protected function tearDown()
    {

    }

    /**
     * Obtenir le rendu du fichier à tester
     */
    public function getTestRender()
    {
        ob_start();
        require_once('core/ajax/ecodevice.ajax.php');
        return ob_get_clean();
    }

    public function testWithoutUserConnected()
    {
        JeedomVars::$isConnected = false;

        $result = $this->getTestRender();
        $actions = MockedActions::get();

        $this->assertEquals('', $result);

        $this->assertCount(2, $actions);
        $this->assertEquals('include_file', $actions[0]['action']);
        $this->assertEquals('authentification', $actions[0]['content']['name']);
        $this->assertEquals('ajax_error', $actions[1]['action']);
        $this->assertEquals('401 - Accès non autorisé', $actions[1]['content']['msg']->getMessage());
    }

    public function testAnswerWithoutRequest()
    {
        JeedomVars::$isConnected = true;
        JeedomVars::$initAnswers['action'] = 'action';

        $result = $this->getTestRender();
        $actions = MockedActions::get();

        $this->assertEquals('', $result);
        $this->assertCount(2, $actions);
        $this->assertEquals('include_file', $actions[0]['action']);
        $this->assertEquals('authentification', $actions[0]['content']['name']);
        $this->assertEquals('ajax_error', $actions[1]['action']);
        $this->assertEquals('Aucune methode correspondante à : action', $actions[1]['content']['msg']->getMessage());
    }

    public function testAnswerWithconfigPush()
    {
        JeedomVars::$isConnected = true;
        JeedomVars::$initAnswers['action'] = 'configPush';
        JeedomVars::$initAnswers['id'] = '1';

        $result = $this->getTestRender();
        $actions = MockedActions::get();
#        print($actions[1]['content']['msg']->getMessage());
# print_r($actions);
        $this->assertEquals('', $result);

        $this->assertCount(3, $actions);
        $this->assertEquals('include_file', $actions[0]['action']);
        $this->assertEquals('authentification', $actions[0]['content']['name']);
        $this->assertEquals('save', $actions[1]['action']);
        $this->assertEquals('api', $actions[1]['content']['key']);
        $this->assertEquals('thisisanewkey', $actions[1]['content']['data']);
        $this->assertEquals('ecodevice', $actions[1]['content']['plugin']);
        $this->assertEquals('save', $actions[1]['action']);
        $this->assertEquals('ajax_error', $actions[2]['action']);
        $this->assertEquals("Configurer l'URL suivante pour un rafraichissement plus rapide dans l'ecodevice : page index=>notification :<br>http://192.168.1.1/jeedom/core/api/jeeApi.php?api=&type=ecodevice&id=&message=data_change<br>Attention surcharge possible importante.", $actions[2]['content']['msg']->getMessage());
    }
}
