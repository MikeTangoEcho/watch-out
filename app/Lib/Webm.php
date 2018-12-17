<?php

namespace App\Lib;

class Webm
{
    public $debug = False;

    // https://chromium.googlesource.com/webm/libvpx/+/master/third_party/libwebm/common/webmids.h
    private $kMkvEBML = '1a45dfa3';

    private $ebmlStruct = [
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
        '18538067' => [
            'name' => 'Segment',
            'format' => 'str'
        ],
        '114d9b74' => [
            'name' => 'SeekHead',
            'format' => 'str'
        ],
        '1549a966' => [
            'name' => 'Info',
            'format' => 'str'
        ],
        '2ad7b1' => [
            'name' => 'TimecodeScale',
            'format' => 'str'
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
        '1654ae6b' => [
            'name' => 'Tracks',
            'format' => 'str'
        ],
        'ae' => [
            'name' => 'TrackEntry',
            'format' => 'str'
        ],
        'd7' => [
            'name' => 'TrackNumber',
            'format' => 'str'
        ],
        '73c5' => [
            'name' => 'TrackUID',
            'format' => 'str'
        ],
        '73c5' => [
            'name' => 'TrackUID',
            'format' => 'str'
        ],
        '83' => [
            'name' => 'TrackType',
            'format' => 'str'
        ],
        'b9' => [
            'name' => 'FlagEnabled',
            'format' => 'str'
        ],
        '88' => [
            'name' => 'FlagDefault',
            'format' => 'str'
        ],
        '55aa' => [
            'name' => 'FlagForced',
            'format' => 'str'
        ],        
        '9c' => [
            'name' => 'FlagLacing',
            'format' => 'str'
        ],
        '86' => [
            'name' => 'CodecID',
            'format' => 'str'
        ],
        '258688' => [
            'name' => 'CodecName',
            'format' => 'str'
        ],
        '63a2' => [
            'name' => 'CodecPrivate',
            'format' => 'str'
        ],
        'e0' => [
            'name' => 'Video',
            'format' => 'str'
        ],
        'b0' => [
            'name' => 'PixelWidth',
            'format' => 'int'
        ],
        'ba' => [
            'name' => 'PixelHeight',
            'format' => 'int'
        ],
        '2383e3' => [
            'name' => 'FrameRate',
            'format' => 'str'
        ],
        '1f43b675' => [
            'name' => 'Cluster',
            'format' => 'str'
        ],
    ];

    private $kMaxIdLengthInBytes = 4;
    private $kMkvEBMLMaxSizeLength = 8;

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
    public function parse($stream, $extractData=false, $extractNested=false)
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
                throw new \Exception('Unknown element Id ' . bin2hex($id));
            }
        }
        $this->log($ebml);
        return $ebml;
    }
}
