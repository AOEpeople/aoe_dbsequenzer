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
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConnectionTest extends UnitTestCase
{
    /**
     * @var Connection
     */
    protected $subject;

    public function setUp(): void
    {
        $testConfiguration = [];
        $testConfiguration['aoe_dbsequenzer']['offset'] = '1';
        $testConfiguration['aoe_dbsequenzer']['system'] = 'testa';
        $testConfiguration['aoe_dbsequenzer']['tables'] = 'table1,table2';
        GeneralUtility::makeInstance(ExtensionConfiguration::class)->setAll($testConfiguration);

        $this->subject = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function expectsTypo3ServiceIsInitiated()
    {
        $typo3Service = $this->callInaccessibleMethod($this->subject, 'getTypo3Service');

        $this->assertInstanceOf(
            Typo3Service::class,
            $typo3Service
        );
    }
}
