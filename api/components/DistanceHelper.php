<?php

namespace api\components;

class DistanceHelper
{
    public static function hello($name) {
        return "Hello $name";
    }

    // replate 3959 to 6371 if needed km
    public static function distance($lat, $lon) {
       return "TRUNCATE( ( 3959 * acos( cos( radians($lat) ) * cos( radians( latitude ) ) 
       * cos( radians(longitude) - radians($lon)) + sin(radians($lat)) 
       * sin( radians(latitude)))) , 2)";
    }

    public static function distance_driver($lat, $lon, $lat2, $lon2) {
        if( !is_null($lat) AND !is_null($lon) AND !is_null($lat2) AND !is_null($lon2) ) {
        $coord = ( 3959 * acos( cos( deg2rad($lat) ) * cos( deg2rad( $lat2 ) ) 
        * cos( deg2rad($lon2) - deg2rad($lon)) + sin(deg2rad($lat)) 
        * sin( deg2rad($lat2))));
            return round($coord, 2);
        } else {
            return NULL;
        }
    }
	
}
