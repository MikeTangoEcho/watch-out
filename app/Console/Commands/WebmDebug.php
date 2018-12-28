<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use App\Lib\Webm;

class WebmDebug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webm:debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $readId = function($stream) {
            // https://matroska.org/technical/specs/index.html
            $id = '';
            $size = 0;
            $checkBytes = 0x80;
            while (($char = fread($stream, 1)) && $size < 4) {
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
        };
        $getUIntLength = function($stream) {
            // First bit will tell size
            $size = 0;
            $length = '';
            $checkBytes = 0x80;
            while ((!feof($stream)) && $size < 4) {
                $length .= fread($stream, 1);
                var_dump(bin2hex($checkBytes >> $size));
                if (($checkBytes >> $size) & ord($length[0])) {
                    var_dump('BROKE');
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
        };
    
        $h =fopen('php://temp', 'wb');
        fwrite($h, hex2bin('a310003093810000'));
        rewind($h);
        $id = $readId($h);
        var_dump(bin2hex($id));
        var_dump(ftell($h));
        var_dump("LGT");
        $l = $getUIntLength($h);
        var_dump("LGT");
        var_dump($l);
        var_dump(ftell($h));
        fclose($h);
    }
}
