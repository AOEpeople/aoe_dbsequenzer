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

class ConnectionTest extends UnitTestCase
{
    /**
     * @var Connection
     */
    protected $subject;

    public function setUp()
    {
        $this->subject = $this->createPartialMock(Connection::class, ['dummy']);
    }

    /**
     * @test
     */
    public function expectsTypo3ServiceIsInitiated()
    {
        $this->callInaccessibleMethod($this->subject, 'getTypo3Service');

        $this->assertInstanceOf(
            Typo3Service::class,
            $this->readAttribute($this->subject, 'typo3Service')
        );

    }

}
