<?php

namespace Tests\Unit;

use App\Lib\Webm;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebmTest extends TestCase
{
    /**
     * Test Reading Id from stream
     *
     * @return void
     */
    public function testReadId()
    {
        $tmp = fopen('php://temp', 'wb');
        $webm = new Webm();
        // Empty Strem
        $id = $webm->readId($tmp);
        $this->assertNotNull($id);
        $this->assertEmpty($id);

        $webids = [
            '1a45dfa3', // EBML Header
            '18538067', // Segment
            '1654ae6b', // Tracks
            '1f43b675', // Cluster
        ]; // TODO Retrieve all possible Ids
        foreach ($webids as $webid) {
            fwrite($tmp, hex2bin($webid) . random_bytes(12));
            rewind($tmp);
            $id = $webm->readId($tmp);
            $this->assertNotNull($id);
            $this->assertEquals($webid, bin2hex($id));
            $this->assertEquals(strlen($webid) / 2, ftell($tmp),
                'Stream cursor at wrong position');
            rewind($tmp);
        }
        fclose($tmp);
    }

    /**
     * Test Reading Uint Length from stream
     *
     * @return void
     */
    public function testReadUintLength()
    {

        $tmp = fopen('php://temp', 'wb');
        $webm = new Webm();
        $length = $webm->getUIntLength($tmp);
        $this->assertEquals(0, $length);

        $uintlengths = [ // Same length coded on different scale
            '81' => 1,
            '4001' => 1,
            '200001' => 1,
            '10000001' => 1,
            '0800000001' => 1,
            '040000000001' => 1,
            '02000000000001' => 1,
            '0100000000000001' => 1,
            '00ffffffffffffff' => -1 // Infinite
        ];
        foreach ($uintlengths as $uintlength => $expectedlength) {
            fwrite($tmp, hex2bin($uintlength) . random_bytes(12));
            rewind($tmp);
            $length = $webm->getUIntLength($tmp);
            $this->assertNotNull($length);
            $this->assertEquals($expectedlength, $length);
            $this->assertEquals(strlen($uintlength) / 2, ftell($tmp),
                'Stream cursor at wrong position');
            rewind($tmp);
        }
        fclose($tmp);
    }

    /**
     * Test Parsing webm stream
     *
     * @return void
     */
    public function testParseChrome()
    {
        $stream = fopen('tests/Unit/assets/chrome-record.webm', 'rb');
        $webm = new Webm();
        $struct = $webm->parse($stream);
        // Check Header
        $this->assertEquals([
            'offset' => 0,
            'valueOffset' => 5,
            'valueLength' => 31,
            'elements' => [
                'EBMLVersion' => [
                    'offset' => 5,
                    'valueOffset' => 8,
                    'valueLength' => 1,
                    'value' => 1
                ],
                'EBMLReadVersion' => [
                    'offset' => 9,
                    'valueOffset' => 12,
                    'valueLength' => 1,
                    'value' => 1
                ],
                'EBMLMaxIDLength' => [
                    'offset' => 13,
                    'valueOffset' => 16,
                    'valueLength' => 1,
                    'value' => 4
                ],
                'EBMLMaxSizeLength' => [
                    'offset' => 17,
                    'valueOffset' => 20,
                    'valueLength' => 1,
                    'value' => 8
                ],
                'DocType' => [
                    'offset' => 21,
                    'valueOffset' => 24,
                    'valueLength' => 4,
                    'value' => "webm"
                ],
                'DocTypeVersion' => [
                    'offset' => 28,
                    'valueOffset' => 31,
                    'valueLength' => 1,
                    'value' => 4
                ],
                'DocTypeReadVersion' => [
                    'offset' => 32,
                    'valueOffset' => 35,
                    'valueLength' => 1,
                    'value' => 2
                ]
            ]
        ], $struct['EBMLHeader']);
        $this->assertArraySubset([
            'offset' => 36,
            'valueOffset' => 48,
            'valueLength' => -1,
            'elements' => [
                'Info' => [
                    0 => [
                        'offset' => 48,
                        'valueOffset' => 53,
                        'valueLength' => 25,
                        'elements' => [
                            'TimecodeScale' => [
                                'offset' => 53,
                                'valueOffset' => 57,
                                'valueLength' => 3,
                                'value' => 1000000
                            ],
                            'MuxingApp' => [
                                'offset' => 60,
                                'valueOffset' => 63,
                                'valueLength' => 6,
                                'value' => 'Chrome'
                            ],
                            'WritingApp' => [
                                'offset' => 69,
                                'valueOffset' => 72,
                                'valueLength' => 6,
                                'value' => 'Chrome'
                            ]
                        ]
                    ]
                ],
                'Tracks' => [
                    0 => [
                        'elements' => [
                            'TrackEntry' => [
                                0 => [
                                    'elements' => [
                                        'TrackNumber' => [
                                            'value' => 1
                                        ],
                                        'TrackType' => [
                                            'value' => 2
                                        ],
                                        'CodecID' => [
                                            'value' => "A_OPUS"
                                        ],
                                        'Audio' => []
                                    ]
                                ],
                                1 => [
                                    'elements' => [
                                        'TrackNumber' => [
                                            'value' => 2
                                        ],
                                        'TrackType' => [
                                            'value' => 1
                                        ],
                                        'CodecID' => [
                                            'value' => "V_VP8"
                                        ],
                                        'Video' => [
                                            'elements' => [
                                                'PixelWidth' => [
                                                    'value' => 320
                                                ],
                                                'PixelHeight' => [
                                                    'value' => 240
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'Cluster' => [
                    0 => [
                        'offset' => 188,
                        'valueOffset' => 200,
                        'valueLength' => -1, // Chrome does infinite clusters
                        'elements' => [
                            'Timecode' => [
                                'value'=> 0
                            ],
                            'SimpleBlock' => []
                        ]
                    ]
                ]

            ]
        ], $struct['Segment']);
    }

    /**
     * Test Check if webm need to be repaired
     *
     * @return void
     */
    public function testCheckRepair()
    {
        $webm = new Webm();
        $stream = fopen('tests/Unit/assets/firefox-ok-cluster.webm', 'rb');
        $this->assertFalse($webm->needRepairCluster($stream));
        fclose($stream);
        $stream = fopen('tests/Unit/assets/firefox-broken-cluster.webm', 'rb');
        $this->assertTrue($webm->needRepairCluster($stream));
        fclose($stream);
    }

    /**
     * Test Repair webm stream
     *
     * @return void
     */
    public function testRepair()
    {
        $webm = new Webm();
        $stream = fopen('tests/Unit/assets/firefox-broken-cluster.webm', 'rb');
        $repairedStream = $webm->repairCluster($stream);
        fclose($stream);
        rewind($repairedStream);
        $this->assertFalse($webm->needRepairCluster($repairedStream));
        fclose($repairedStream);
    }

}
