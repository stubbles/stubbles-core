<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\exception;
/**
 * Tests for net\stubbles\lang\exception\FileNotFoundException.
 *
 * @group  lang
 * @group  lang_exception
 */
class FileNotFoundExceptionTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function fileNameIsUsedForErrorMessage()
    {
        $fnfe = new FileNotFoundException('example.txt');
        $this->assertEquals('The file example.txt could not be found or is not readable.',
                            $fnfe->getMessage()
        );
    }
}
?>