<?php
declare(strict_types=1);
namespace Aoe\AoeDbSequenzer\Tests\Functional\Domain\Repository;

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

use Aoe\AoeDbSequenzer\Domain\Repository\OverwriteProtectionRepository;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class OverwriteProtectionRepositoryTest extends FunctionalTestCase
{
    /**
     * @var OverwriteProtectionRepository
     */
    protected $subject;

    protected $testExtensionsToLoad = ['typo3conf/ext/aoe_dbsequenzer'];

    protected function setUp()
    {
        parent::setUp();
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->subject = $objectManager->get(OverwriteProtectionRepository::class);
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_aoedbsequenzer_domain_model_overwriteprotection.xml');
    }

    /**
     * @test
     */
    public function findByProtectedUidAndTableName()
    {
        $this->assertSame(
            2,
            $this->subject->findByProtectedUidAndTableName(1, 'pages')->count()
        );

        $this->assertSame(
            1,
            $this->subject->findByProtectedUidAndTableName(10, 'tt_content')->count()
        );
    }
}
