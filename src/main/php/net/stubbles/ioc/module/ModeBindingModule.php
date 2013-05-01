<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc\module;
use net\stubbles\ioc\Binder;
use net\stubbles\lang\Mode;
/**
 * Binding module to configure the binder with a runtime mode.
 */
class ModeBindingModule implements BindingModule
{
    /**
     * mode instance to bind
     *
     * @type  Mode
     */
    protected $mode;

    /**
     * constructor
     *
     * @param  string  $projectPath
     * @param  Mode    $mode
     */
    public function __construct($projectPath, Mode $mode = null)
    {
        if (null === $mode) {
            $mode = $this->getFallbackMode();
        }

        $mode->registerErrorHandler($projectPath);
        $mode->registerExceptionHandler($projectPath);
        $this->mode = $mode;
    }

    /**
     * returns fallback mode
     *
     * @return  Mode
     */
    protected function getFallbackMode()
    {
        return \net\stubbles\lang\DefaultMode::prod();
    }

    /**
     * configure the binder
     *
     * @param  Binder  $binder
     */
    public function configure(Binder $binder)
    {
        $binder->bind('net\\stubbles\\lang\\Mode')
               ->toInstance($this->mode);
    }
}
?>