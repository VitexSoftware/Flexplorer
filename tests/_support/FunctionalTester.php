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

/**
 * Inherited Methods.
 *
 * @method void                    am($role)
 * @method void                    amGoingTo($argumentation)
 * @method void                    comment($description)
 * @method void                    execute($callable)
 * @method void                    expect($prediction)
 * @method void                    expectTo($prediction)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 * @method void                    lookForwardTo($achieveValue)
 * @method void                    wantTo($text)
 * @method void                    wantToTest($text)
 *
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

    /**
     * Define custom actions here.
     */
}
