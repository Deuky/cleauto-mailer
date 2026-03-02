<?php

namespace App\Service;

use App\Entity\Car;
use App\Entity\Personal;
use App\Entity\RGPD;
use DateTimeInterface;
use function base_convert, dechex, implode, ord, str_pad, strtoupper, substr;

final class ServiceReference
{
    public function getReference(Personal $personal, Car $car): string
    {
        $fullname = explode(' ', $personal->name);

        $initials = implode (
            match(count($fullname)) {
                0 => ['*', '*'],
                1 => [
                    '*', 
                    strtoupper(current($fullname)[0])
                ],
                default => array_map(fn($n) => strtoupper($n[0]), $fullname)
            }
        );

        $crc16 = $this->calculateCrc16($car->VIN ?? '****');
        return implode('-', [$initials, $crc16]);
    }

    public function getVerification(string $reference, RGPD $rgpd): string
    {
        $compressedDate = $this->compressDate($rgpd->requestDate);
        $checksum = $this->calculateChecksum($reference . $compressedDate);
        return implode ('-', [$compressedDate, $checksum]);
    }

    public function getIdentification(Personal $personal, Car $car, RGPD $rgpd): array
    {
        $reference = $this->getReference($personal, $car);

        return [
            'reference' => $reference,
            'verification' => $this->getVerification($reference, $rgpd)
        ];
    }

    private function calculateCrc16(string $data): string
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= ord($data[$i]);
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 1) {
                    $crc = ($crc >> 1) ^ 0xA001;
                } else {
                    $crc >>= 1;
                }
            }
        }
        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    private function compressDate(DateTimeInterface $date): string
    {
        [$year, $month, $day, $hour] = explode(' ', $date->format('Y m d H'));
        $year -= 2026;

        $yearBase36 = strtoupper(base_convert($year, 10, 36));
        $monthHex = strtoupper(dechex($month));
        $dayBase36 = strtoupper(base_convert($day, 10, 36));
        $hourBase36 = strtoupper(base_convert($hour, 10, 36));

        return implode([
            $yearBase36,
            $monthHex,
            $dayBase36,
            $hourBase36
        ]);
    }

    private function calculateChecksum(string $data): string
    {
        return strtoupper(substr(md5($data), 0, 4));
    }
}