<?php
declare(strict_types=1);

namespace Aoe\AoeDbSequenzer\Tests\Unit\Xclass;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 AOE GmbH <dev@aoe.com>
 *  All rights reserved
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Aoe\AoeDbSequenzer\Service\Typo3Service;
use Aoe\AoeDbSequenzer\Xclass\Connection;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ConnectionTest extends UnitTestCase
{
    /**
     * @var Connection
     */
    protected $subject;

    protected function setUp(): void
    {
        $testConfiguration = [];
        $testConfiguration['aoe_dbsequenzer']['offset'] = '1';
        $testConfiguration['aoe_dbsequenzer']['system'] = 'testa';
        $testConfiguration['aoe_dbsequenzer']['tables'] = 'table1,table2';
        GeneralUtility::makeInstance(ExtensionConfiguration::class)->setAll($testConfiguration);
    }

    public function testExpectsTypo3ServiceIsInitiated(): void
    {
        // $typo3Service = $this->callInaccessibleMethod($this->subject, 'getTypo3Service');
        $this->subject = $this->getAccessibleMock(Connection::class, ['getTypo3Service'], [], '', false);
        $typo3Service = $this->subject->_call('getTypo3Service');

        $this->assertInstanceOf(
            Typo3Service::class,
            $typo3Service
        );
    }
}
