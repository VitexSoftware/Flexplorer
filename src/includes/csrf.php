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

namespace Flexplorer;

/**
 * Session bound CSRF token for destructive actions.
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Button posting to $action with CSRF token (and optional extra hidden fields).
 *
 * @param array<string,string> $fields
 */
function csrfButton(string $action, string $label, string $class, string $title, array $fields = []): string
{
    $hidden = '<input type="hidden" name="csrf_token" value="'.htmlspecialchars(csrfToken()).'">';

    foreach ($fields as $name => $value) {
        $hidden .= '<input type="hidden" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'">';
    }

    return '<form method="post" action="'.htmlspecialchars($action).'" onsubmit="'
        .htmlspecialchars("$('#Preloader').css('visibility', 'visible');").'">'
        .$hidden
        .'<button type="submit" class="btn btn-'.$class.'" title="'.htmlspecialchars($title).'">'
        .htmlspecialchars($label).'</button></form>';
}

/**
 * Allow only POST with valid CSRF token, otherwise redirect away.
 */
function requireCsrfPost(\Flexplorer\ui\WebPage $oPage, string $backUrl): void
{
    $sent = (string) ($_POST['csrf_token'] ?? '');

    if (
        ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'
        || empty($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $sent)
    ) {
        \Ease\Shared::user()->addStatusMessage(_('Invalid or missing security token'), 'warning');
        $oPage->redirect($backUrl);

        exit;
    }
}
