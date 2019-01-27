<?php

namespace App\Composer;

use Composer\IO\BaseIO;

class LogIO extends BaseIO
{
    private $logs;

    public function __construct()
    {
        $this->logs = '';
    }

    public function getLogs(): string
    {
        return $this->logs;
    }

    /**
     * {@inheritdoc}
     */
    public function isInteractive()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isVerbose()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isVeryVerbose()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isDecorated()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages, $newline = true, $verbosity = self::NORMAL)
    {
        $this->append($messages, 'info');
    }

    /**
     * {@inheritdoc}
     */
    public function writeError($messages, $newline = true, $verbosity = self::NORMAL)
    {
        $this->append($messages, 'error');
    }

    /**
     * {@inheritdoc}
     */
    public function overwrite($messages, $newline = true, $size = null, $verbosity = self::NORMAL)
    {
        $this->append($messages, 'info');
    }

    /**
     * {@inheritdoc}
     */
    public function overwriteError($messages, $newline = true, $size = null, $verbosity = self::NORMAL)
    {
        $this->append($messages, 'error');
    }

    /**
     * {@inheritdoc}
     */
    public function ask($question, $default = null)
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function askConfirmation($question, $default = true)
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function askAndValidate($question, $validator, $attempts = null, $default = null)
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function askAndHideAnswer($question)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function select($question, $choices, $default, $attempts = false, $errorMessage = 'Value "%s" is invalid', $multiselect = false)
    {
        return $default;
    }

    private function append($messages, $class)
    {
        $this->logs .= sprintf('<p class="%s">%s</p>', $class, $messages);
    }
}
