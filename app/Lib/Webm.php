<?php

namespace App\Lib;

class Webm
{
    public $debug = False;

    // https://chromium.googlesource.com/webm/libvpx/+/master/third_party/libwebm/common/webmids.h

    public $ebmlStruct = [
        '1a45dfa3' => [
            'name' => 'EBMLHeader',
            'format' => 'master',
            'mandatory' => true,
            'struct' => [
                '4286' => [
                    'name' => 'EBMLVersion',
                    'format' => 'int'
                ],
                '42f7' => [
                    'name' => 'EBMLReadVersion',
                    'format' => 'int'
                ],
                '42f2' => [
                    'name' => 'EBMLMaxIDLength',
                    'format' => 'int'
                ],
                '42f3' => [
                    'name' => 'EBMLMaxSizeLength',
                    'format' => 'int'
                ],
                '4282' => [
                    'name' => 'DocType',
                    'format' => 'str'
                ],
                '4287' => [
                    'name' => 'DocTypeVersion',
                    'format' => 'int'
                ],
                '4285' => [
                    'name' => 'DocTypeReadVersion',
                    'format' => 'int'
                ],
            ]
        ],        
        '18538067' => [
            'name' => 'Segment',
            'format' => 'master',
            'mandatory' => true,
            'struct' => [
                '114d9b74' => [
                    'name' => 'SeekHead',
                    'format' => 'master',
                    'struct' => [
                        '4dbb' => [
                            'name' => 'Seek',
                            'format' => 'master',
                            'mandatory' => true,
                            'multiple' => true,
                            'stuct' => [
                                '53ab' => [
                                    'name' => 'SeekID',
                                    'format' => 'bin',
                                    'mandatory' => true
                                ],
                                '53ac' => [
                                    'name' => 'SeekPosition',
                                    'format' => 'bin',
                                    'mandatory' => true
                                ],
                            ]
                        ]
                    ]
                ],
                '1549a966' => [
                    'name' => 'Info',
                    'format' => 'master',
                    'mandatory' => true,
                    'multiple' => true,
                    'struct' => [
                        '2ad7b1' => [
                            'name' => 'TimecodeScale',
                            'format' => 'uint',
                            'mandatory' => true
                        ],
                        '4489' => [
                            'name' => 'Duration',
                            'format' => 'str'
                        ],        
                        '4d80' => [
                            'name' => 'MuxingApp',
                            'format' => 'str'
                        ],
                        '5741' => [
                            'name' => 'WritingApp',
                            'format' => 'str'
                        ],                
                    ]
                ],
                '1654ae6b' => [
                    'name' => 'Tracks',
                    'format' => 'master',
                    'multiple' => true,
                    'struct' => [
                        'ae' => [
                            'name' => 'TrackEntry',
                            'format' => 'master',
                            'mandatory' => true,
                            'multiple' => true,
                            'struct' => [
                                'd7' => [
                                    'name' => 'TrackNumber',
                                    'format' => 'uint',
                                    'mandatory' => true
                                ],        
                                '73c5' => [
                                    'name' => 'TrackUID',
                                    'format' => 'uint',
                                    'mandatory' => true
                                ],
                                '83' => [
                                    'name' => 'TrackType',
                                    'format' => 'uint',
                                    'mandatory' => true
                                ],
                                'b9' => [
                                    'name' => 'FlagEnabled',
                                    'format' => 'uint',
                                    'mandatory' => true
                                ],
                                '88' => [
                                    'name' => 'FlagDefault',
                                    'format' => 'uint',
                                    'mandatory' => true
                                ],
                                '55aa' => [
                                    'name' => 'FlagForced',
                                    'format' => 'uint',
                                    'mandatory' => true
                                ],        
                                '9c' => [
                                    'name' => 'FlagLacing',
                                    'format' => 'str',
                                    'mandatory' => true
                                ],
                                '6de7' => [
                                    'name' => 'MinCache',
                                    'format' => 'str',
                                    'mandatory' => false // Not supported for webm
                                ],
                                '55ee' => [
                                    'name' => 'MaxBlockAdditionID',
                                    'format' => 'str',
                                    'mandatory' => false // Not supported for webm
                                ],
                                '86' => [
                                    'name' => 'CodecID',
                                    'format' => 'str',
                                    'mandatory' => true
                                ],
                                '258688' => [
                                    'name' => 'CodecName',
                                    'format' => 'utf8str'
                                ],
                                '63a2' => [
                                    'name' => 'CodecPrivate',
                                    'format' => 'bin'
                                ],                
                                'aa' => [
                                    'name' => 'CodecDecodeAll',
                                    'format' => 'unit',
                                    'mandatory' => false // Not supported for webm
                                ],
                                '55bb' => [
                                    'name' => 'SeekPreRoll',
                                    'format' => 'uint',
                                    'mandatory' => true
                                ],
                                'e0' => [
                                    'name' => 'Video',
                                    'format' => 'master',
                                    'struct' => [
                                        '9a' => [
                                            'name' => 'FlagInterlaced',
                                            'format' => 'uint',
                                            'mandatory' => true
                                        ],
                                        '9d' => [
                                            'name' => 'FieldOrder',
                                            'format' => 'uint',
                                            'mandatory' => true
                                        ],
                                        'b0' => [
                                            'name' => 'PixelWidth',
                                            'format' => 'uint',
                                            'mandatory' => true
                                        ],
                                        'ba' => [
                                            'name' => 'PixelHeight',
                                            'format' => 'uint',
                                            'mandatory' => true
                                        ],
                                        '54b3' => [
                                            'name' => 'AspectRatioType',
                                            'format' => 'uint',
                                        ],
                                    ]
                                ],
                                'e1' => [
                                    'name' => 'Audio',
                                    'format' => 'master',
                                    'struct' => [
                                        'b5' => [
                                            'name' => 'SamplingFrequency',
                                            'format' => 'float',
                                            'mandatory' => true
                                        ],
                                        '9f' => [
                                            'name' => 'Channels',
                                            'format' => 'uint',
                                            'mandatory' => true
                                        ],
                                    ]
                                ]
                            ]
                        ],
                    ]
                ],
                '1043a770' => [
                    'name' => 'Chapters',
                    'format' => 'master',
                ],
                '1f43b675' => [
                    'name' => 'Cluster',
                    'format' => 'master',
                    'multiple' => true,
                    'struct' => [
                        'e7' => [
                            'name' => 'Timecode',
                            'format' => 'uint',
                            'mandatory' => true
                        ],
                        'a7' => [
                            'name' => 'Position',
                            'format' => 'uint',
                            'mandatory' => false
                        ],
                        'a3' => [
                            'name' => 'SimpleBlock',
                            'format' => 'bin',
                            'mandatory' => false,
                            'multiple' => true
                        ],
                        'a0' => [
                            'name' => 'BlockGroup',
                            'format' => 'master',
                            'mandatory' => false,
                            'multiple' => true,
                            'struct' => [
                                'a1' => [
                                    'name' => 'Block',
                                    'format' => 'bin',
                                    'mandatory' => true,
                                ],
                                '9b' => [
                                    'name' => 'BlockDuration',
                                    'format' => 'uint',
                                ],
                            ]
                        ],
                    ]
                ],
                '1c53bb6b' => [
                    'name' => 'Cues',
                    'format' => 'master',
                    'mandatory' => false // Optional for live stream
                ],
                '1941a469' => [
                    'name' => 'Attachments',
                    'format' => 'master',
                ],
                '1254c367' => [
                    'name' => 'Tags',
                    'format' => 'master',
                    'multiple' => true
                ],        
            ]
        ],
    ];

    private $kMaxIdLengthInBytes = 4;
    private $kMkvEBMLMaxSizeLength = 8;

    private function formatValue($format, $value) {
        switch ($format) {
            case 'int':
            case 'uint':
                return $this->UnserializeUInt($value);
            case 'str':
            case 'utf8str':
                return substr($value, 0, 25);
            case 'bin':
                return bin2hex(substr($value, 0, 25));
        }
    }

    private function readId($stream) {
        // https://matroska.org/technical/specs/index.html
        $id = '';
        $size = 0;
        $checkBytes = 0x80;
        while (($char = fread($stream, 1)) && $size < $this->kMaxIdLengthInBytes) {
            $id .= $char;
            // The leading bits of the EBML IDs are used to identify the length of the ID.
            // The number of leading 0's + 1 is the length of the ID in octets.
            // We will refer to the leading bits as the Length Descriptor.
            if (($checkBytes >> $size) & ord($id[0])) {
                break;
            }
            $size++;
        }

        return $id;
    }

    /**
     * https://chromium.googlesource.com/webm/libvpx/+/master/third_party/libwebm/mkvparser/mkvparser.cc#172
     */
    private function getUIntLength($stream) {
        // First bit will tell size
        $size = 0;
        $length = '';
        $checkBytes = 0x80;
        while (($char = fread($stream, 1)) && $size < $this->kMkvEBMLMaxSizeLength) {
            $length .= $char;
            if (($checkBytes >> $size) & ord($length[0])) {
                break;
            }
            $size++;
        }
        if  (!$length) {
            return 0;
        }
        $length[0] = chr(ord($length[0]) ^ ($checkBytes >> $size));

        if (bin2hex($length) == "00ffffffffffffff") // Unkown size for streaming
            return -1;
        return hexdec(bin2hex($length));
    }

    private function UnserializeUInt($bin) {
        $bin = str_pad($bin, 4, chr(0), STR_PAD_LEFT);
        $r = unpack('N', $bin);
        if (is_array($r))
            return $r[1];
        return $r;
    }

    private function log($o) {
        if ($this->debug)
            var_dump($o);
    }

    /**
     * Extract the header from the first tracks to allow replay at anytime
     */
    public function parseElements($stream, $struct, $structMaxOffset=-1, $extractData=false, $extractNested=false)
    {
        $elements = [];
        while (!feof($stream) && (ftell($stream) < $structMaxOffset  || $structMaxOffset == -1)) {
            // Read Id
            $tagStart = ftell($stream);
            $id = $this->readId($stream);
            if (!$id) {
                // TODO feof is false but cant read char
                break;
            }
            $valueLength = $this->getUIntLength($stream);
            $valueStart = ftell($stream);
            $element = [
                'offset' => $tagStart,
                'valueOffset' => $valueStart,
                'valueLength' => $valueLength
            ];
            $this->log(bin2hex($id));
            if (isset($struct[bin2hex($id)])) {
                $elementStruct = $struct[bin2hex($id)];
                $this->log('Found Id [' . bin2hex($id) . '] ' . $elementStruct['name']);
                if ($elementStruct['format'] == 'master') {
                    $this->log(bin2hex($id) . ":N:" . $elementStruct['name'] .  ":O:" . $tagStart . ":L:" . $valueLength);
                    // Recursiv
                    $element['elements'] = $this->parseElements($stream, $elementStruct['struct'],
                        $valueLength != -1 ? $valueStart + $valueLength : -1,
                        $extractData, $extractNested);
                    // TODO Check mandatory
                } else {
                    // Value
                    if ($valueLength == -1) { // Unknown size
                        $value = "Unkown";
                    } else if ($valueLength == 0) {
                        $value = null;
                    } else {
                        $value = $this->formatValue($elementStruct['format'], fread($stream, $valueLength));
                    }
                    $element['value'] = $value;
                    $this->log(bin2hex($id) . ":N:" . $elementStruct['name'] .  ":O:" . $tagStart . ":L:" . $valueLength . ":V:" . $element['value']);
                }                
                // TODO Check Multiple
                if (isset($elementStruct['multiple'])) {
                    if (isset($elements[$elementStruct['name']])) {
                        $elements[$elementStruct['name']][] = $element;
                    } else {
                        $elements[$elementStruct['name']] = [$element];
                    }
                } else {
                    $elements[$elementStruct['name']] = $element;
                }                
            } else {
                $this->log('Ignored Id [' . bin2hex($id) . ']');
                fseek($stream, $valueStart + $valueLength);
            }
        }
        return $elements;
    }

    /**
     * Return pos of Next cluster
     */
    public function seekNextId($stream, $hexTagId)
    {
        $tag = hex2bin($hexTagId);
        $tagSize = strlen($tag);
        $successChain = 0;
        while (!feof($stream) && ($successChain < $tagSize)) {
            $char = fgetc($stream);
            if ($char === $tag[$successChain]) {
                $this->log("Found [" . bin2hex($char) . "] at " . dechex(ftell($stream)));
                $successChain++;
            } else {
                if ($successChain) {
                    $this->log('Missmatch');
                }
                $successChain = 0;
            }
        }
        if ($successChain == $tagSize) {
            return ftell($stream) - $tagSize; // Offset
        }
        return null;
    }

    /**
     * Extract the header from the first tracks to allow replay at anytime
     */
    public function parse($stream, $extractData=false, $extractNested=false)
    {
        $ebml = $this->parseElements($stream, $this->ebmlStruct, -1, $extractData, $extractNested);
        return $ebml;
    }
}

