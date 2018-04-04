<?php
/**
 * Flexplorer - PermissionsViewer
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2018 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * CSV to HTML Permissions 
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
            $this->addRowHeaderColumns(explode(';', current($permissionsDataRaw)));
            array_shift($permissionsDataRaw);
            foreach ($permissionsDataRaw as $permissionsDataRow) {
                $row = $this->addRowColumns(explode(';',
                        trim($permissionsDataRow)));
                foreach ($row->getContents() as $columnID => $column) {
                    switch (current($column->getContents())) {
                        case 'true':
                            $column->setTagCss(['background-color' => '#58d68d']);
                            $column->setTagProperties(['title'=>_('true')]);
                            $column->pageParts[0] = new \Ease\TWB\GlyphIcon('ok');
                            break;
                        case 'false':
                            $column->setTagCss(['background-color' => '#ec7063']);
                            $column->setTagProperties(['title'=>_('false')]);
                            $column->pageParts[0] = new \Ease\TWB\GlyphIcon('remove');
                            break;
                        default :
                            $column->setTagCss(['font-weight' => 'bold']);
                            break;
                    }
                }
            }
        } else {
            $this->addItem(sprintf(_('Error reading file %s'), $permissionsCsv));
        }
    }
}
