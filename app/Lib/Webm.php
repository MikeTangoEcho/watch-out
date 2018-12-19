<?php

namespace App\Lib;

class Webm
{
    public $debug = False;

    // https://chromium.googlesource.com/webm/libvpx/+/master/third_party/libwebm/common/webmids.h

    private $ebmlStruct = [
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
                    'format' => 'str',
                    'multiple' => true,
                    'struct' => [
                        'ae' => [
                            'name' => 'TrackEntry',
                            'format' => 'str',
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
                    'format' => 'str',
                    'multiple' => true,
                    'struct' => [
                        'e7' => [
                            'name' => 'Timecode',
                            'format' => 'str',
                            'mandatory' => true
                        ]        
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
            case 'bin':
                return substr($value, 0, 25);
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
        return unpack('C', $bin)[1];
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
        $this->log(array_keys($struct));
        while (!feof($stream) && ($structMaxOffset < ftell($stream))) {
            // Read Id
            $tagStart = ftell($stream);
            $id = $this->readId($stream);
            $valueLength = $this->getUIntLength($stream);
            $valueStart = ftell($stream);
            $element = [
                'offset' => $tagStart,
                'valueOffset' => $valueStart,
                'valueLength' => $valueLength,
                'value' => null,
            ];
            $this->log(bin2hex($id));
            $this->log(isset($struct[bin2hex($id)]));
            if (isset($struct[bin2hex($id)])) {
                $elementStruct = $struct[bin2hex($id)];
                $this->log('Found Id [' . bin2hex($id) . '] ' . $elementStruct['name']);
                if ($elementStruct['format'] == 'master') {
                    $this->log('Next');
                    // Recursiv
                    $this->log(array_keys($elementStruct['struct']));
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
                }
                $this->log(bin2hex($id) . ":O:" . $tagStart . ":L:" . $valueLength . ":V:" . $element['value']);
                // TODO Check Multiple
                $elements[$elementStruct['name']] = $element;
            } else {
                $this->log(array_keys($struct));
                $this->log('Ignored Id [' . bin2hex($id) . ']');
            }
        }
        $this->log('RETURN');
        return $elements;
    }

    /**
     * Extract the header from the first tracks to allow replay at anytime
     */
    public function parse($stream, $extractData=false, $extractNested=false)
    {
        $ebml = $this->parseElements($stream, $this->ebmlStruct, -1, $extractData, $extractNested);
        $this->log($ebml);
        return $ebml;
    }

    /**
     * Extract the header from the first tracks to allow replay at anytime
     */
    public function oparse($stream, $extractData=false, $extractNested=false)
    {
        // Look for EBML header
        $pos = 0;
        $ebmlHeader = $this->readId($stream);
        if (bin2hex($ebmlHeader) != $this->kMkvEBML) {
            throw new \Exception("Invalid file format");
        }
        // Read the EBML header size.
        $ebmlHeaderSizeLength = $this->getUIntLength($stream);
        $ebml = [
            'EBMLHeaderOffset' => ftell($stream),
            'EBMLHeaderSize' => $ebmlHeaderSizeLength,            
        ];
        while (!feof($stream)) {
            $tagStart = ftell($stream);
            $id = $this->readId($stream);
            $valueLength = $this->getUIntLength($stream);
            $valueStart = ftell($stream);
            if ($valueLength == -1) { // Unknown sizen
                $value = "Unkown";
            } else if ($valueLength == 0) {
                $value = null;
            } else {
                $value = fread($stream, $valueLength);
            }
            $this->log(bin2hex($id) . ":L:" . $valueLength . ":V:" . substr(bin2hex($value), 0, 20));
            if (isset($this->ebmlStruct[bin2hex($id)])) {
                $struct = $this->ebmlStruct[bin2hex($id)];
                $ebml[$struct['name']] = [
                    'offset' => $tagStart,
                    'valueOffset' => $valueStart,
                    'valueLength' => $valueLength,
                    'value' => ($struct['format'] == 'int' ?
                        $this->UnserializeUInt($value) : substr($value, 0, 25))
                ];
            } else if ($id) {
                $this->log($ebml);
                $ebml[bin2hex($id)] = [
                    'offset' => $tagStart,
                    'valueOffset' => $valueStart,
                    'valueLength' => $valueLength,
                    'value' => substr($value, 0, 25)
                ];
                //throw new \Exception('Unknown element Id ' . bin2hex($id));
            }
        }
        $this->log($ebml);
        return $ebml;
    }
}
