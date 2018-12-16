<?php

namespace App\Lib;

class Webm
{
    // https://chromium.googlesource.com/webm/libvpx/+/master/third_party/libwebm/common/webmids.h
    private $kMkvEBML = '1a45dfa3';
    private $kMkvEBMLVersion = '4286';
    private $kMkvEBMLReadVersion = '42f7';
    private $kMkvEBMLMaxIDLength = '42f2';
    private $kMkvEBMLMaxSizeLength = '42f3';
    private $kMkvDocType = '4282';
    private $kMkvDocTypeVersion = '4287';
    private $kMkvDocTypeReadVersion = '4285';
    private $kMkvSegment = '18538067';
    private $kMkvSeekHead = '114D9B74';

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
    ];

    private $kMaxIdLengthInBytes = 4;

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
        $b = fread($stream, 1);
        // TODO whats the fuck
        if (ord($b) > 0x80)
            return ord($b) - 0x80;
        if (ord($b) == 0x01)
            return 7;
        if (ord($b) < 0x80)
            return ord($b) + 6;
        
        throw new \Exception('Unknown length : ' . ord($b));
    }

    private function UnserializeUInt($bin) {
        return unpack('C', $bin)[1];
    }
    /**
     * Extract the header from the first tracks to allow replay at anytime
     */
    public function read($stream)
    {
        // Look for EBML header
        $pos = 0;
        $ebmlHeader = $this->readId($stream);
        //$ebmlHeader = fread($stream, 4);
        if (bin2hex($ebmlHeader) != $this->kMkvEBML) {
            throw new \Exception("Invalid file format");
        }
        // Read length of size field.
        $ebmlHeaderSizeLength = $this->getUIntLength($stream);
        // Read the EBML header size.
        $ebmlHeaderSize = fread($stream, $ebmlHeaderSizeLength);
        // Read header size to find start of payload
        $headers = [];
        while (!feof($stream)) {
            echo("-------------------------\n");
            $id = $this->readId($stream);
            var_dump(bin2hex($id));
            $valueLength = $this->getUIntLength($stream);
            $value = fread($stream, $valueLength);
            var_dump(bin2hex($id) . ":L:" . $valueLength . ":V:" . bin2hex($value));
            if (isset($this->ebmlStruct[bin2hex($id)])) {
                $struct = $this->ebmlStruct[bin2hex($id)];
                $headers[$struct['name']] = ($struct['format'] == 'int' ? $this->UnserializeUInt($value) : $value);
            } else {
                var_dump($headers);
                throw new \Exception('Unknown element Id ' . bin2hex($id));
            }
        }
        


        // ParseElementHeader until end

/*
long len = 0;
  const long long ebml_id = ReadID(pReader, pos, len);
  if (ebml_id == E_BUFFER_NOT_FULL)
    return E_BUFFER_NOT_FULL;
  if (len != 4 || ebml_id != libwebm::kMkvEBML)
    return E_FILE_FORMAT_INVALID;
  // Move read pos forward to the EBML header size field.
  pos += 4;
  // Read length of size field.
  long long result = GetUIntLength(pReader, pos, len);
  if (result < 0)  // error
    return E_FILE_FORMAT_INVALID;
  else if (result > 0)  // need more data
    return E_BUFFER_NOT_FULL;
  if (len < 1 || len > 8)
    return E_FILE_FORMAT_INVALID;
  if ((total >= 0) && ((total - pos) < len))
    return E_FILE_FORMAT_INVALID;
  if ((available - pos) < len)
    return pos + len;  // try again later
  // Read the EBML header size.
  result = ReadUInt(pReader, pos, len);
  if (result < 0)  // error
    return result;
n;  // consume size field
  // pos now designates start of payload
  if ((total >= 0) && ((total - pos) < result))
    return E_FILE_FORMAT_INVALID;
  if ((available - pos) < result)
    return pos + result;
  const long long end = pos + result;
  Init();
  while (pos < end) {
    long long id, size;
    status = ParseElementHeader(pReader, pos, end, id, size);
    if (status < 0)  // error
      return status;
    if (size == 0)
      return E_FILE_FORMAT_INVALID;
    if (id == libwebm::kMkvEBMLVersion) {
      m_version = UnserializeUInt(pReader, pos, size);
      if (m_version <= 0)
        return E_FILE_FORMAT_INVALID;
    } else if (id == libwebm::kMkvEBMLReadVersion) {
      m_readVersion = UnserializeUInt(pReader, pos, size);
      if (m_readVersion <= 0)
        return E_FILE_FORMAT_INVALID;
    } else if (id == libwebm::kMkvEBMLMaxIDLength) {
      m_maxIdLength = UnserializeUInt(pReader, pos, size);
      if (m_maxIdLength <= 0)
        return E_FILE_FORMAT_INVALID;
    } else if (id == libwebm::kMkvEBMLMaxSizeLength) {
      m_maxSizeLength = UnserializeUInt(pReader, pos, size);
      if (m_maxSizeLength <= 0)
        return E_FILE_FORMAT_INVALID;
    } else if (id == libwebm::kMkvDocType) {
      if (m_docType)
        return E_FILE_FORMAT_INVALID;
      status = UnserializeString(pReader, pos, size, m_docType);
      if (status)  // error
        return status;
    } else if (id == libwebm::kMkvDocTypeVersion) {
      m_docTypeVersion = UnserializeUInt(pReader, pos, size);
      if (m_docTypeVersion <= 0)
        return E_FILE_FORMAT_INVALID;
    } else if (id == libwebm::kMkvDocTypeReadVersion) {
      m_docTypeReadVersion = UnserializeUInt(pReader, pos, size);
      if (m_docTypeReadVersion <= 0)
        return E_FILE_FORMAT_INVALID;
    }
    pos += size;
  }
  if (pos != end)
    return E_FILE_FORMAT_INVALID;
  // Make sure DocType, DocTypeReadVersion, and DocTypeVersion are valid.
  if (m_docType == NULL || m_docTypeReadVersion <= 0 || m_docTypeVersion <= 0)
    return E_FILE_FORMAT_INVALID;
  // Make sure EBMLMaxIDLength and EBMLMaxSizeLength are valid.
  if (m_maxIdLength <= 0 || m_maxIdLength > 4 || m_maxSizeLength <= 0 ||
      m_maxSizeLength > 8)
    return E_FILE_FORMAT_INVALID;
  return 0;
*/
    }
}
