<?php
declare(strict_types=1);
namespace Aoe\AoeDbSequenzer\Tests\Unit\Form;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 AOE GmbH <dev@aoe.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

use Aoe\AoeDbSequenzer\Domain\Model\OverwriteProtection;
use Aoe\AoeDbSequenzer\Form\AbstractOverwriteElement;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;

class AbstractOverwriteElementTest extends FunctionalTestCase
{
    /**
     * @var AbstractOverwriteElement
     */
    protected $subject;

    //protected $coreExtensionsToLoad = ['cms', 'core'];

    protected $testExtensionsToLoad = ['typo3conf/ext/aoe_dbsequenzer'];

    public function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../Fixtures/tx_aoedbsequenzer_domain_model_overwriteprotection.xml');
        $this->subject = $this->getAccessibleMockForAbstractClass(AbstractOverwriteElement::class, [], '', false);
    }

    /**
     * @test
     */
    public function hasOverwriteProtection_returnsFalseAsNotNumericProtectedUid()
    {
        $this->assertFalse($this->subject->_call('hasOverwriteProtection', 'im-not-numberic', 'pages'));
    }

    /**
     * @test
     */
    public function hasOverwriteProtection_returnsFalseNoProtectionFound()
    {
        $this->assertFalse($this->subject->_call('hasOverwriteProtection', 1234, 'pages'));
    }

    /**
     * @test
     */
    public function hasOverwriteProtection_returnsTrueProtectionFound()
    {
        $this->assertTrue($this->subject->_call('hasOverwriteProtection', 1, 'pages'));
    }

    /**
     * @test
     */
    public function getOverwriteProtection_returnsProtections()
    {
        /** @var OverwriteProtection $protection */
        $protection = $this->subject->_call('getOverwriteProtection', 1, 'pages');

        $this->assertSame(
            'pages',
            $protection->getProtectedTablename()
        );

        $this->assertSame(
            1,
            $protection->getProtectedUid()
        );
    }
}
