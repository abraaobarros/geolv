<?php

use GeoLV\Locality;
use Illuminate\Database\Seeder;
use ShapeFile\ShapeFile;
use Symfony\Component\Console\Helper\ProgressBar;

class LocalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shapefile = new ShapeFile(storage_path('app/shapefiles/brazil/localities.shp'));
        $progress = new ProgressBar($this->command->getOutput(), 5565);

        Locality::truncate();

        foreach ($shapefile as $record) {
            $this->fromShapeFileRecord($record)->save();
            $progress->advance();
        }

        $progress->finish();
    }

    private function fromShapeFileRecord(array $record)
    {
        $min_lat = null;
        $min_lng = null;
        $max_lat = null;
        $max_lng = null;

        foreach ($record['shp']['parts'] as $part) {
            foreach ($part['rings'] as $ring) {
                foreach ($ring['points'] as $point) {
                    $lat = (float) $point['y'];
                    $lng = (float) $point['x'];

                    if (!$min_lat || $lat < $min_lat)
                        $min_lat = $lat;

                    if (!$min_lng || $lng < $min_lng)
                        $min_lng = $lng;

                    if (!$max_lat || $lat > $max_lat)
                        $max_lat = $lat;

                    if (!$max_lng || $lng > $max_lng)
                        $max_lng = $lng;
                }
            }
        }

        return new Locality([
            'name' => $record['dbf']['nome'],
            'state' => $record['dbf']['uf'],
            'state_id' => $record['dbf']['estado_id'],
            'ibge_code' => $record['dbf']['codigo_ibg'],
            'min_lat' => $min_lat,
            'min_lng' => $min_lng,
            'max_lat' => $max_lat,
            'max_lng' => $max_lng
        ]);
    }
}
