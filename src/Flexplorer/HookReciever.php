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
 * Description of HookReciver.
 *
 * @author vitex
 */
class HookReciever extends \Ease\Brick
{
    public $format = 'json';
    public $changes;
    public $globalVersion;
    public $lastFileName = '';

    /**
     * Posledni zpracovana verze.
     */
    public int $lastProcessedVersion = null;

    /**
     * Prijmac WebHooku.
     */
    public function __construct()
    {
        parent::__construct();
        $this->lastProcessedVersion = $this->getLastProcessedVersion();
    }

    public static function getSaveDir()
    {
        $tmpDir = sys_get_temp_dir();

        if (!is_writable($tmpDir)) { // IIS & C:\WINDOWS\TEMP
            $tmpDir = __DIR__; // Try to use current directory
        }

        return $tmpDir;
    }

    /**
     * Poslouchá standartní vstup.
     *
     * @param null|mixed $filename
     *
     * @return string zaslaná data
     */
    public function listen($filename = null)
    {
        $input = null;
        $inputJSON = file_get_contents('php://input');

        if (\strlen($inputJSON)) {
            if (null === $filename) {
                $filename = $this->getSaveName();
            }

            file_put_contents(self::getSaveDir().'/'.$filename, $inputJSON);
            $this->lastFileName = $filename;
            $input = json_decode($inputJSON, true); // convert JSON into array
        }

        return $input;
    }

    /**
     * Zpracuje změny.
     */
    public function processChanges(): void
    {
        if (\count($this->changes)) {
            foreach ($this->changes as $change) {
                $evidence = $change['@evidence'];
                $inVersion = (int) $change['@in-version'];
                $operation = $change['@operation'];
                $id = (int) $change['id'];

                if (isset($change['external-ids'])) {
                    $externalIDs = $change['external-ids'];
                }

                if ($inVersion <= $this->lastProcessedVersion) {
                    continue;
                }

                $this->saveLastProcessedVersion($inVersion);

                $this->addStatusMessage(
                    sprintf(
                        _('WebHook %s triggered'),
                        '<a href="changes.php?file='.$this->lastFileName.'">'.$this->lastFileName.'</a>',
                    ),
                    'info',
                );
            }
        } else {
            $this->addStatusMessage('No Data To Process', 'warning');
        }
    }

    /**
     * Převezme změny.
     *
     * @see https://www.flexibee.eu/api/dokumentace/ref/changes-api/ Changes API
     *
     * @param array $changes pole změn
     *
     * @return int Globální verze poslední změny
     */
    public function takeChanges($changes)
    {
        $result = null;

        if (!\is_array($changes)) {
            \Ease\Shared::logger()->addToLog(
                _('Empty WebHook request'),
                'Warning',
            );
        } else {
            if (\array_key_exists('winstrom', $changes)) {
                $this->globalVersion = (int) $changes['winstrom']['@globalVersion'];
                $this->changes = $changes['winstrom']['changes'];
            }

            $result = $this->globalVersion;
        }

        return $result;
    }

    /**
     * Ulozi posledni zpracovanou verzi.
     *
     * @param int $version
     */
    public function saveLastProcessedVersion($version): void
    {
        $this->lastProcessedVersion = $version;
        file_put_contents(
            sys_get_temp_dir().'/lastAbraFlexiVersion',
            $this->lastProcessedVersion,
        );
    }

    /**
     * Nacte posledni zpracovanou verzi.
     *
     * @return int $version
     */
    public function getLastProcessedVersion()
    {
        $lastProcessedVersion = null;
        $versionFile = sys_get_temp_dir().'/lastAbraFlexiVersion';

        if (file_exists($versionFile)) {
            $lastProcessedVersion = (int) file_get_contents($versionFile);
        }

        return $lastProcessedVersion;
    }

    /**
     * @return string Filename for current webhook data save
     */
    public function getSaveName()
    {
        $host = $_SERVER['REMOTE_HOST'] ?? $_SERVER['REMOTE_ADDR'];

        return 'flexplorer-changes-'.$host.'_'.time().'.json';
    }
}
