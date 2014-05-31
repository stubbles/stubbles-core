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
class HttpTestCase extends \PHPUnit_Framework_TestCase
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
        return array(array(100, Http::STATUS_CLASS_INFO),
                     array(101, Http::STATUS_CLASS_INFO),
                     array(102, Http::STATUS_CLASS_INFO),
                     array(118, Http::STATUS_CLASS_INFO),
                     array(200, Http::STATUS_CLASS_SUCCESS),
                     array(201, Http::STATUS_CLASS_SUCCESS),
                     array(202, Http::STATUS_CLASS_SUCCESS),
                     array(203, Http::STATUS_CLASS_SUCCESS),
                     array(204, Http::STATUS_CLASS_SUCCESS),
                     array(205, Http::STATUS_CLASS_SUCCESS),
                     array(206, Http::STATUS_CLASS_SUCCESS),
                     array(207, Http::STATUS_CLASS_SUCCESS),
                     array(300, Http::STATUS_CLASS_REDIRECT),
                     array(301, Http::STATUS_CLASS_REDIRECT),
                     array(302, Http::STATUS_CLASS_REDIRECT),
                     array(303, Http::STATUS_CLASS_REDIRECT),
                     array(304, Http::STATUS_CLASS_REDIRECT),
                     array(305, Http::STATUS_CLASS_REDIRECT),
                     array(307, Http::STATUS_CLASS_REDIRECT),
                     array(400, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(401, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(402, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(403, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(404, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(405, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(406, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(407, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(408, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(409, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(410, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(411, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(412, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(413, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(414, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(415, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(416, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(417, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(418, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(421, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(422, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(423, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(424, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(425, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(426, Http::STATUS_CLASS_ERROR_CLIENT),
                     array(500, Http::STATUS_CLASS_ERROR_SERVER),
                     array(501, Http::STATUS_CLASS_ERROR_SERVER),
                     array(502, Http::STATUS_CLASS_ERROR_SERVER),
                     array(503, Http::STATUS_CLASS_ERROR_SERVER),
                     array(504, Http::STATUS_CLASS_ERROR_SERVER),
                     array(505, Http::STATUS_CLASS_ERROR_SERVER),
                     array(506, Http::STATUS_CLASS_ERROR_SERVER),
                     array(507, Http::STATUS_CLASS_ERROR_SERVER),
                     array(509, Http::STATUS_CLASS_ERROR_SERVER),
                     array(510, Http::STATUS_CLASS_ERROR_SERVER),
                     array(909, Http::STATUS_CLASS_UNKNOWN)
        );
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
        $tuples = array();
        foreach (Http::getStatusCodes() as $statusCode => $reasonPhrase) {
            $tuples[] = array($statusCode, $reasonPhrase);
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
