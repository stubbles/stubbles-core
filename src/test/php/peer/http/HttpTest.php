<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer\http;
/**
 * Test for stubbles\peer\http\Http.
 *
 * @since  2.0.0
 * @group  peer
 * @group  peer_http
 */
class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function httpVersion1_0IsValid()
    {
        $this->assertTrue(Http::isVersionValid('HTTP/1.0'));
    }

    /**
     * @test
     */
    public function httpVersion1_1IsValid()
    {
        $this->assertTrue(Http::isVersionValid('HTTP/1.1'));
    }

    /**
     * @test
     */
    public function otherHttpVersionsAreNotValid()
    {
        $this->assertFalse(Http::isVersionValid('HTTP/0.1'));
    }

    /**
     * @return  array
     */
    public function getStatusCodeClassTuples()
    {
        return [[100, Http::STATUS_CLASS_INFO],
                [101, Http::STATUS_CLASS_INFO],
                [102, Http::STATUS_CLASS_INFO],
                [118, Http::STATUS_CLASS_INFO],
                [200, Http::STATUS_CLASS_SUCCESS],
                [201, Http::STATUS_CLASS_SUCCESS],
                [202, Http::STATUS_CLASS_SUCCESS],
                [203, Http::STATUS_CLASS_SUCCESS],
                [204, Http::STATUS_CLASS_SUCCESS],
                [205, Http::STATUS_CLASS_SUCCESS],
                [206, Http::STATUS_CLASS_SUCCESS],
                [207, Http::STATUS_CLASS_SUCCESS],
                [300, Http::STATUS_CLASS_REDIRECT],
                [301, Http::STATUS_CLASS_REDIRECT],
                [302, Http::STATUS_CLASS_REDIRECT],
                [303, Http::STATUS_CLASS_REDIRECT],
                [304, Http::STATUS_CLASS_REDIRECT],
                [305, Http::STATUS_CLASS_REDIRECT],
                [307, Http::STATUS_CLASS_REDIRECT],
                [400, Http::STATUS_CLASS_ERROR_CLIENT],
                [401, Http::STATUS_CLASS_ERROR_CLIENT],
                [402, Http::STATUS_CLASS_ERROR_CLIENT],
                [403, Http::STATUS_CLASS_ERROR_CLIENT],
                [404, Http::STATUS_CLASS_ERROR_CLIENT],
                [405, Http::STATUS_CLASS_ERROR_CLIENT],
                [406, Http::STATUS_CLASS_ERROR_CLIENT],
                [407, Http::STATUS_CLASS_ERROR_CLIENT],
                [408, Http::STATUS_CLASS_ERROR_CLIENT],
                [409, Http::STATUS_CLASS_ERROR_CLIENT],
                [410, Http::STATUS_CLASS_ERROR_CLIENT],
                [411, Http::STATUS_CLASS_ERROR_CLIENT],
                [412, Http::STATUS_CLASS_ERROR_CLIENT],
                [413, Http::STATUS_CLASS_ERROR_CLIENT],
                [414, Http::STATUS_CLASS_ERROR_CLIENT],
                [415, Http::STATUS_CLASS_ERROR_CLIENT],
                [416, Http::STATUS_CLASS_ERROR_CLIENT],
                [417, Http::STATUS_CLASS_ERROR_CLIENT],
                [418, Http::STATUS_CLASS_ERROR_CLIENT],
                [421, Http::STATUS_CLASS_ERROR_CLIENT],
                [422, Http::STATUS_CLASS_ERROR_CLIENT],
                [423, Http::STATUS_CLASS_ERROR_CLIENT],
                [424, Http::STATUS_CLASS_ERROR_CLIENT],
                [425, Http::STATUS_CLASS_ERROR_CLIENT],
                [426, Http::STATUS_CLASS_ERROR_CLIENT],
                [500, Http::STATUS_CLASS_ERROR_SERVER],
                [501, Http::STATUS_CLASS_ERROR_SERVER],
                [502, Http::STATUS_CLASS_ERROR_SERVER],
                [503, Http::STATUS_CLASS_ERROR_SERVER],
                [504, Http::STATUS_CLASS_ERROR_SERVER],
                [505, Http::STATUS_CLASS_ERROR_SERVER],
                [506, Http::STATUS_CLASS_ERROR_SERVER],
                [507, Http::STATUS_CLASS_ERROR_SERVER],
                [509, Http::STATUS_CLASS_ERROR_SERVER],
                [510, Http::STATUS_CLASS_ERROR_SERVER],
                [909, Http::STATUS_CLASS_UNKNOWN]
        ];
    }

    /**
     * @param  int     $statusCode
     * @param  string  $statusClass
     * @test
     * @dataProvider  getStatusCodeClassTuples
     */
    public function detectCorrectStatusClass($statusCode, $statusClass)
    {
        $this->assertEquals($statusClass,
                            Http::getStatusClass($statusCode)
        );
    }

    /**
     * @test
     */
    public function returnsListOfStatusCodes()
    {
        $this->assertNotCount(0, Http::getStatusCodes());
    }

    /**
     * @return  array
     */
    public function getStatusCodeReasonPhraseTuples()
    {
        $tuples = [];
        foreach (Http::getStatusCodes() as $statusCode => $reasonPhrase) {
            $tuples[] = [$statusCode, $reasonPhrase];
        }

        return $tuples;
    }

    /**
     * @param  int     $statusCode
     * @param  string  $reasonPhrase
     * @test
     * @dataProvider  getStatusCodeReasonPhraseTuples
     */
    public function returnsCorrectReasonPhrase($statusCode, $reasonPhrase)
    {
        $this->assertEquals($reasonPhrase,
                            Http::getReasonPhrase($statusCode)
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function getReasonPhraseForUnknownStatusCodeThrowsIllegalArgumentException()
    {
        Http::getReasonPhrase(1);
    }

    /**
     *@test
     */
    public function addsLineEnding()
    {
        $this->assertEquals('foo' . Http::END_OF_LINE,
                            Http::line('foo')
        );
    }

    /**
     *@test
     */
    public function emptyLineReturnsLineEndingOnly()
    {
        $this->assertEquals(Http::END_OF_LINE,
                            Http::emptyLine()
        );
    }
}