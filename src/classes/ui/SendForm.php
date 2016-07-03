<?php
/**
 * Flexplorer - formulář pro odeslání požadavku.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Vitex Software
 */

namespace Flexplorer\ui;

/**
 * Description of SendForm
 *
 * @author vitex
 */
class SendForm extends \Ease\TWB\Form
{

    /**
     * Formulář pro odeslání uživatelského požadavku
     *
     * @param string $url
     * @param string $method
     * @param string $body
     */
    public function __construct($url, $method = 'GET', $body = '')
    {
        parent::__construct('Req', 'index.php');

        $this->addInput(new \Ease\Html\InputTextTag('url', $url), _('URL'),
            $url,
            new \Ease\Html\ATag('https://www.flexibee.eu/api/dokumentace/ref/urls',
            _('Sestavování URL')));
        $this->addInput(new \Ease\TWB\Textarea('body', $body,
            ['id' => 'editor', 'class' => 'animated']), _('Tělo dotazu'));
        $this->addInput(new \Ease\Html\Select('method',
            ['GET' => 'GET', 'POST' => 'POST', 'PUT' => 'PUT', 'PATCH' => 'PATCH',
            'DELETE' => 'DELETE'], $method), _('Metoda'), null,
            new \Ease\Html\ATag('https://www.flexibee.eu/api/dokumentace/ref/http-operations',
            _('Podporované HTTP Operace')));
        $this->addItem(new \Ease\TWB\SubmitButton(_('Odeslat'), 'success'));
    }

    /**
     * TODO:  https://ace.c9.io/
     */
    function finalize()
    {
        \Ease\Shared::webPage()->includeJavaScript('js/jquery.autosize.min.js');
        \Ease\Shared::webPage()->addJavaScript('
         var textarea = $("textarea[name=\'body\']");
         textarea.autosize();
         var unformated = textarea.val();
         var formated = JSON.stringify($.parseJSON( unformated ),undefined, 4);
         textarea.val(formated)
//         textarea.autosize.update();
        ');

        parent::finalize();
    }
}