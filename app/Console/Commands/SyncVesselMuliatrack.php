<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class SyncVesselMuliatrack extends Command
{
    protected $signature = 'sync:vessel-muliatrack';
    protected $description = 'Ambil data vessel dari API Muliatrack dan simpan ke DB';

    public function handle()
    {
        $url = "https://app1.muliatrack.com/wspublogindo/service.asmx/LastPosition";
        $params = [
            "sTokenKey" => "Lo61Nd0-6P5",
            "GPSID"     => "ALL",
        ];

        $this->info("Mulai sync vessel positions (Ctrl+C untuk berhenti)");

        while (true) {
            try {
                $response = Http::timeout(30)->get($url, $params);

                if (!$response->ok()) {
                    $this->error("API Error: " . $response->status());
                    sleep(10);
                    continue;
                }

                $json = $response->json();

                if (!isset($json['data']) || !is_array($json['data'])) {
                    $this->warn("Format API tidak sesuai");
                    sleep(10);
                    continue;
                }

                foreach ($json['data'] as $vessel) {
                    DB::table('location')->updateOrInsert(
                        [
                            'gpsid'        => $vessel['GPSID'],
                            'datetime_utc' => $vessel['DateTime'],
                        ],
                        [
                            'vname'     => $vessel['VName'],
                            'status'    => $vessel['Status'],
                            'longitude' => $vessel['Lon'],
                            'latitude'  => $vessel['Lat'],
                            'speed'     => $vessel['Speed'],
                            'direct'    => $vessel['Direct'],
                            'mileage'   => $vessel['Mileage'],
                        ]
                    );

                    $this->info("[{$vessel['DateTime']}] {$vessel['VName']} saved.");
                }
            } catch (\Throwable $e) {
                $this->error("Error: " . $e->getMessage());
            }

            sleep(10); // refresh tiap 10 detik
        }
    }
}
