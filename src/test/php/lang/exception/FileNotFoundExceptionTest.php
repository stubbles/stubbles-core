<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\exception;
use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\lang\exception\FileNotFoundException.
 *
 * @group  lang
 * @group  lang_exception
 */
class FileNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function fileNameIsUsedForErrorMessage()
    {
        $fnfe = new FileNotFoundException('example.txt');
        assert(
                $fnfe->getMessage(),
                equals('The file example.txt could not be found or is not readable.')
        );
    }
}
