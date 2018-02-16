<?php
namespace Aoe\AoeDbSequenzer\Command;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 AOE GmbH (dev@aoe.com)
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

use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Extbase command controller to manage overwrite protection records
 *
 * @package Aoe\AoeDbSequenzer\Command
 */
class OverwriteProtectionCommandController extends CommandController
{
    /**
     * @var \Aoe\AoeDbSequenzer\Domain\Repository\OverwriteProtectionRepository
     * @inject
     */
    private $overwriteProtectionRepository;

    /**
     * Permanently deletes expired overwrite protection records.
     *
     * @return void
     */
    public function deleteExpiredRecordsCommand()
    {
        $query = $this->overwriteProtectionRepository->createQuery();

        $expiredRecords = $query->matching($query->lessThan('protected_time', time()))->execute();
        foreach ($expiredRecords as $expiredRecord) {
            $this->overwriteProtectionRepository->remove($expiredRecord);
        }

        $this->outputLine('Deleted %s expired overwrite protection records from database.', [$expiredRecords->count()]);
    }
}
