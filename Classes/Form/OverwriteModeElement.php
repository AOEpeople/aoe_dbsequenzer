<?php
namespace Aoe\AoeDbSequenzer\Form;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 AOE GmbH (dev@aoe.com)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Aoe\AoeDbSequenzer\OverwriteProtectionService;
use TYPO3\CMS\Backend\Form\Element\SelectSingleElement;

/**
 * @package Aoe\AoeDbSequenzer\Form
 */
class OverwriteModeElement extends AbstractOverwriteElement {
    /**
     * @param array $PA
     * @return String
     */
    public function render(array $PA)
    {
        $itemFormElValue = '';
        if ($this->hasOverwriteProtection($PA['row']['uid'], $PA['table'])) {
            $overwriteProtection = $this->getOverwriteProtection($PA['row']['uid'], $PA['table']);
            $itemFormElValue = $overwriteProtection->getProtectedMode();
        }

        $data = [
            'inlineStructure' => [],
            'parameterArray' => [
                'itemFormElName' => 'data['.$PA['table'].']['.$PA['row']['uid'].'][tx_aoe_dbsquenzer_protectoverwrite_mode]',
                'itemFormElValue' => [$itemFormElValue],
                'fieldName' => 'tx_aoe_dbsquenzer_protectoverwrite_mode',
                'fieldChangeFunc' => [],
                'fieldConf' => [
                    'label' => '',
                    'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'size' => 1,
                        'maxitems' => 1,
                        'items' => [
                            [
                                $this->getLanguageService()->sL('LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:mode_conflict'),
                                OverwriteProtectionService::OVERWRITE_PROTECTION_MODE_CONFLICT
                            ],
                            [
                                $this->getLanguageService()->sL('LLL:EXT:aoe_dbsequenzer/Resources/Private/Language/locallang_db.xml:mode_overwrite'),
                                OverwriteProtectionService::OVERWRITE_PROTECTION_MODE_OVERWRITE
                            ]
                        ]
                    ],
                ],
            ],
        ];

        $element = new SelectSingleElement($this->getNodeFactory(), $data);
        $resultArray = $element->render();
        return $resultArray['html'];
    }
}
