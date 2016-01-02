<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer;
use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\peer\ParsedUri.
 *
 * @group  peer
 * @since  5.1.1
 */
class ParsedUriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function transposeKeepsChangedParameters()
    {
        $parsedUri = new ParsedUri('http://example.com/?foo=bar&baz=303');
        $parsedUri->queryString()->addParam('baz', '313');
        $parsedUri->queryString()->addParam('dummy', 'example');
        assert(
                $parsedUri->transpose(['scheme' => 'https'])
                        ->asStringWithoutPort(),
                equals('https://example.com/?foo=bar&baz=313&dummy=example')
        );
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function canBeCastedToString()
    {
        $uri = 'http://example.com/?foo=bar&baz=303';
        $parsedUri = new ParsedUri($uri);
        assert((string) $parsedUri, equals($uri));
    }
}
