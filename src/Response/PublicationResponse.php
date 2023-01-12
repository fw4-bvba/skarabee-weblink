<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Response;

class PublicationResponse extends Response
{
    public function __construct($data)
    {
        parent::__construct($data, [
            'Info' => [
                'Options',
            ],
            'Property' => [
                'GlazingTypes',
                'HeatingTypes',
                'Flashes',
                'PlumbingTypes',
                'IsolationTypes',
                'CadastrallInformations',
                'Locations',
                'Floors',
                'RightsInRem',
                'Certifications',
                'WarmWaterTypes',
                'AllFlashes',
                'MarketingTypes',
                'TypesOfGarden',
                'GarageFacilities',
                'Levels',
                'PartnerUrls',
                'MarketingTypes' => [
                    'Descriptions'
                ],
                'Floors' => [
                    'LivingRoomTypes',
                    'KitchenTypes',
                    'Bathroom1Types',
                    'Bathroom2Types',
                ],
                'OpenHouse' => [
                    'FromToDates',
                ],
                'CommonSection' => [
                    'Procedure',
                ],
                'Levels' => [
                    'areas',
                ],
            ],
            'Pictures',
            'Documents',
            'FloorPlans',
        ]);

        // Parse SurfaceStock
        if (isset($this->Property->SurfaceStock)) {
            $this->Property->SurfaceStock = $this->Property->SurfaceStock ? floatval(str_replace(',', '.', $this->Property->SurfaceStock)) : null;
        }
    }
}
