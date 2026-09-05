<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RouteWaypoint;

class ImportGpxRoute extends Command
{
    protected $signature =
        'route:import-gpx
        {route_id}
        {file}';

    protected $description =
        'Import GPX route into route_waypoints';

    public function handle()
    {
        $routeId =
            (int)$this->argument('route_id');

        $file =
            $this->argument('file');

        if (!file_exists($file))
        {
            $this->error(
                "File not found: {$file}"
            );
            return 1;
        }

        $xml =
            simplexml_load_file($file);

        if (!$xml)
        {
            $this->error(
                "Invalid GPX file"
            );
            return 1;
        }

        $sequence = 1;

        /*
         * GPX Route
         */
        if (isset($xml->rte))
        {
            foreach ($xml->rte->rtept as $point)
            {
                RouteWaypoint::create([
                    'route_id'  => $routeId,
                    'sequence'  => $sequence++,
                    'latitude'  => (float)$point['lat'],
                    'longitude' => (float)$point['lon'],
                ]);
            }
        }

        /*
         * GPX Track
         */
        elseif (isset($xml->trk))
        {
            foreach ($xml->trk->trkseg as $segment)
            {
                foreach ($segment->trkpt as $point)
                {
                    RouteWaypoint::create([
                        'route_id'  => $routeId,
                        'sequence'  => $sequence++,
                        'latitude'  => (float)$point['lat'],
                        'longitude' => (float)$point['lon'],
                    ]);
                }
            }
        }
        else
        {
            $this->error(
                "No route points found"
            );
            return 1;
        }

        $this->info(
            "Imported ".($sequence - 1)." waypoints"
        );

        return 0;
    }
}