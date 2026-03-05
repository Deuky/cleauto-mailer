<?php

namespace App\Service;

class PdfEncryptionService
{
    public function __construct(
        public readonly DockerService $dockerService
    ) {}

    public function encrypt($file)
    {
        $encryptUri = tempnam(sys_get_temp_dir(), uniqid('encrypt-'));
        $encrypt = fopen($encryptUri, 'w');
        $output = '/tmp/'.uniqid().'.pdf';
        $input = '/tmp/'.uniqid().'.pdf';
        $importTarFile = sys_get_temp_dir().'/'.uniqid('import-').'.tar';
        $tmp = fopen($importTarFile, 'w+');

        $container = $this->dockerService->create([
                "image" => "pdfbox:latest",
                "env" => [
                    "INPUT=".$input,
                    "OUTPUT=".$output
                ]
            ])
            ->exportFile($file, $input)
            ->start()
            ->waitingStatus('exited')
            ->importFile($output, $tmp);

        fclose($tmp);
        $phar = new \PharData($importTarFile);
        fwrite($encrypt, $phar[basename($output)]->getContent());
        unset($phar);
        unlink($importTarFile);

        return $encryptUri;
    }
}