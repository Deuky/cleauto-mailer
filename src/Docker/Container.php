<?php

namespace App\Docker;

class Container
{
    protected string $serviceId;
    protected array $files;
    protected $curl;

	public function __construct(
		public readonly array $data,
        public readonly string $sock = '/var/run/docker.sock',
	) {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_UNIX_SOCKET_PATH, $sock);
    }

    public function create(): self
    {
        $ch = curl_copy_handle($this->curl);

        curl_setopt($ch, CURLOPT_URL, 'http://localhost/containers/create');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->data));

        $response = curl_exec($ch);

        $data = json_decode($response);
        curl_close($ch);

        $this->serviceId = $data->Id;

        return $this;
    }

    public function waitingStatus($state): self
    {
        $ms = 100;
        $req = curl_copy_handle($this->curl);
        curl_setopt($req, CURLOPT_URL, 'http://localhost/containers/'.$this->serviceId.'/json');
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_CUSTOMREQUEST, 'GET');

        while(true) {
            $ch = curl_copy_handle($req);

            $response = curl_exec($ch);

            curl_close($ch);
            $stats = json_decode($response);

            if ($stats->State->Status !== $state) {
                usleep($ms);
                continue;
            }

            break;
        } 

        curl_close($req);

        return $this;
    }

	public function start(): self
	{
        if (!$this->getServiceId()) {
            throw new \Exception();
        }

        $ch = curl_copy_handle($this->curl);
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/containers/'.$this->serviceId.'/start');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        return $this;
	}

    public function createTar(string $filename, $path): string
    {
        $tarName = sys_get_temp_dir() .'/'.uniqid('encrypt-').'.tar';
        $this->files[] = $tarName;
        $phar = new \PharData($tarName);
        $phar->addFile($filename, $path);

        return $tarName;
    }

	public function exportFile(string $filename, string $path): self
	{
        if (!$this->getServiceId()) {
            throw new \Exception();
        }

        if (!file_exists($filename)) {
            throw new \Exception();
        }

        $archive = $this->createTar($filename, $path);

        $ch = curl_copy_handle($this->curl);
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/containers/'.$this->serviceId.'/archive?path=/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-tar',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($archive));

        $response = curl_exec($ch);

        curl_close($ch);

        return $this;
	}

	public function importFile($in, $out): self
	{
        $ch = curl_copy_handle($this->curl);
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/containers/'.$this->serviceId.'/archive?path='.$in);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_FILE, $out);

        $response = curl_exec($ch);

        curl_close($ch);

        return $this;
	}

    public function getServiceId()
    {
        return $this->serviceId ?? null;
    }

    public function delete(): self
    {
        if (!$this->getServiceId()) {
            throw new \Exception();
        }

        $ch = curl_copy_handle($this->curl);
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/containers/'.$this->serviceId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        return $this;
    }

	public function __destruct()
	{
        $this->delete();
        array_map('unlink', $this->files);
	}
}