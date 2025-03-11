<?php

declare(strict_types=1);

/**
 * This file is part of the Flexplorer package
 *
 * github.com/VitexSoftware/Flexplorer
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flexplorer\ui;

/**
 * CSV to HTML Permissions.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class PermissionsViewer extends \Ease\Html\TableTag
{
    public function __construct($permissionsCsv)
    {
        parent::__construct(null, ['class' => 'table table-striped table-hover']);

        if (is_file($permissionsCsv)) {
            $permissionsDataRaw = file($permissionsCsv);

            $roles = explode(';', trim(current($permissionsDataRaw)));

            $this->addRowHeaderColumns($roles);
            array_shift($permissionsDataRaw);

            foreach ($permissionsDataRaw as $permissionsDataRow) {
                $row = $this->addRowColumns(explode(
                    ';',
                    trim($permissionsDataRow),
                ));
                $columnID = 0;

                foreach ($row->getContents() as $column) {
                    switch (current($column->getContents())) {
                        case 'true':
                            $column->setTagCss(['background-color' => '#58d68d']);
                            $column->setTagProperties(['title' => _('true').' '.$roles[$columnID]]);
                            $column->pageParts[0] = '✅';

                            break;
                        case 'false':
                            $column->setTagCss(['background-color' => '#ec7063']);
                            $column->setTagProperties(['title' => _('false').' '.$roles[$columnID]]);
                            $column->pageParts[0] = '❌';

                            break;

                        default:
                            $column->setTagCss(['font-weight' => 'bold']);

                            break;
                    }

                    ++$columnID;
                }
            }
        } else {
            $this->addItem(sprintf(_('Error reading file %s'), $permissionsCsv));
        }
    }
}
