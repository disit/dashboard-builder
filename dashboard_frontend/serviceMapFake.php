<?php
    /* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

    error_reporting(E_ERROR | E_NOTICE);
    include 'config.php';
    
    $fakeGeoJson = '{  
        "Services":{  
           "fullCount":22,
           "type":"FeatureCollection",
           "features":[  
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.25046,
                       43.809734
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"Parcheggio Pieraccini Meyer",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"2.9441040717863722",
                    "serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":1
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.247714,
                       43.803726
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"Parcheggio Careggi",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"3.422013951339016",
                    "serviceUri":"http://www.disit.org/km4city/resource/CarParkCareggi",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":2
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.274482,
                       43.78772
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"Park Global Service S.n.c. Di Mini Miria E C.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"3.54242280072022",
                    "serviceUri":"http://www.disit.org/km4city/resource/d70c0c83e112a7a6bef1b1e4ff467d3e",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":3
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.274482,
                       43.78772
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"PARK GLOBAL SERVICE SRL",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"3.54242280072022",
                    "serviceUri":"http://www.disit.org/km4city/resource/c1cef1d97d721646586ee62b583fca8d",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":4
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.274482,
                       43.78772
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"Park Global Service S.n.c. Di Mini Miria E C.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"3.5424228007826857",
                    "serviceUri":"http://www.disit.org/km4city/resource/d70c0c83e112a7a6bef1b1e4ff467d3e",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":5
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.254965,
                       43.788956
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"IMMOBILIARE FRA - LA DI BARTOLINI GRAZIA & C. S.A.S.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.088876162993819",
                    "serviceUri":"http://www.disit.org/km4city/resource/a9ac455916a724b61e5a8dcd228f7fcd",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":6
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.263201,
                       43.78483
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"Parcheggio Parterre",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.14863899780566",
                    "serviceUri":"http://www.disit.org/km4city/resource/CarParkParterre",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":7
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.263377,
                       43.78208
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"autoservizi S.r.l.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.421702181269518",
                    "serviceUri":"http://www.disit.org/km4city/resource/85cd7a4e44a0a999a394cbf12703904e",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":8
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.263377,
                       43.78208
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"autoservizi S.r.l.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.421702181271311",
                    "serviceUri":"http://www.disit.org/km4city/resource/85cd7a4e44a0a999a394cbf12703904e",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":9
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.245034,
                       43.790302
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"GARAGE TANUCCI DI FEI FRANCO",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.497431585683565",
                    "serviceUri":"http://www.disit.org/km4city/resource/7fdea00c4256649af7f62d929e145f29",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":10
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.257204,
                       43.783096
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"Prato Parking S.r.l.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.5399488974975535",
                    "serviceUri":"http://www.disit.org/km4city/resource/caa1339ea9c415a9bc557a06e88278c8",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":11
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.257204,
                       43.783096
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"Prato Parking S.r.l.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.5399488975347735",
                    "serviceUri":"http://www.disit.org/km4city/resource/caa1339ea9c415a9bc557a06e88278c8",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":12
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.253976,
                       43.783848
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"A.r.n.o. Servizi S.r.l.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.602175647929225",
                    "serviceUri":"http://www.disit.org/km4city/resource/1ee16f3b6ba726db4001a7323d511cba",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":13
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.253976,
                       43.783848
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"A.r.n.o. Servizi S.r.l.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.602175647935055",
                    "serviceUri":"http://www.disit.org/km4city/resource/1ee16f3b6ba726db4001a7323d511cba",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":14
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.253976,
                       43.783848
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"F.A.S. PARKING S.R.L.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.602175647935055",
                    "serviceUri":"http://www.disit.org/km4city/resource/cce795a61a172eff7b994c84658f7cf8",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":15
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.244278,
                       43.789326
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"COOPERATIVA SOCIALE SERVIZI AUTO POSTEGGI CUSTODITI FIRENZE",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.616922539460642",
                    "serviceUri":"http://www.disit.org/km4city/resource/e12ae6d3d5587a5ae97e3f5c2c549926",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":16
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.258399,
                       43.781498
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"PARCO FOTOVOLTAICO VICOPISANO S.R.L.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.650698523143081",
                    "serviceUri":"http://www.disit.org/km4city/resource/75ca62d9761802fe80a9463462d51a8c",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":17
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.258399,
                       43.781498
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"PARCO FOTOVOLTAICO CHIANNI 1 S.R.L.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.650698523143081",
                    "serviceUri":"http://www.disit.org/km4city/resource/266da003b627fff1d012885886f4c9a7",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":18
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.258399,
                       43.781498
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"PARCO FOTOVOLTAICO CASCINA 1 S.R.L.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.650698523143081",
                    "serviceUri":"http://www.disit.org/km4city/resource/8bcb8fae261e4f089fc8e177e43a4c97",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":19
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.258399,
                       43.781498
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"PARCO FOTOVOLTAICO FAUGLIA S.R.L.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.650698523143081",
                    "serviceUri":"http://www.disit.org/km4city/resource/8f661bf7d8cdcabdd4ace8e44c254713",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":20
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.2739935,
                       43.77701
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"Firenze Amministrazioni E Gestioni S.r.l.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.713827609267173",
                    "serviceUri":"http://www.disit.org/km4city/resource/d5bd31508a67b36dd782efa2693332c4",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":21
              },
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.2739935,
                       43.77701
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"Firenze Amministrazioni E Gestioni S.r.l.",
                    "tipo":"Car_park",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "distance":"4.7138276092719895",
                    "serviceUri":"http://www.disit.org/km4city/resource/d5bd31508a67b36dd782efa2693332c4",
                    "photoThumbs":[  

                    ],
                    "multimedia":"",
                    "fake": "true"
                 },
                 "id":22
              }
           ]
        }
     }';
    
    $fakeSingleGeoJsons = [];
    $fakeSingleGeoJsons[1] = '{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"Parcheggio Pieraccini Meyer","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"200"},"freeParkingLots":{"value":"150"},"occupiedParkingLots":{"value":"50"},"occupancy":{"value":"25"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[2] = '{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"Parcheggio Careggi","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"150"},"freeParkingLots":{"value":"140"},"occupiedParkingLots":{"value":"10"},"occupancy":{"value":"6.6"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[3] = '{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"Park Global Service S.n.c. Di Mini Miria","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"120"},"freeParkingLots":{"value":"60"},"occupiedParkingLots":{"value":"60"},"occupancy":{"value":"50"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[4] = '{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"PARK GLOBAL SERVICE SRL":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"170"},"freeParkingLots":{"value":"170"},"occupiedParkingLots":{"value":"0"},"occupancy":{"value":"100"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[5] = '{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"Park Global Service S.n.c. Di Mini Miria","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"140"},"freeParkingLots":{"value":"40"},"occupiedParkingLots":{"value":"100"},"occupancy":{"value":"71.4"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[6] = '{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"Park Florence di LA DI BARTOLINI GRAZIA","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"300"},"freeParkingLots":{"value":"200"},"occupiedParkingLots":{"value":"100"},"occupancy":{"value":"33.3"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[7] = '{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"Parcheggio Parterre","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"220"},"freeParkingLots":{"value":"40"},"occupiedParkingLots":{"value":"180"},"occupancy":{"value":"81.8"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[8] = '{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"autoservizi S.r.l.","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"250"},"freeParkingLots":{"value":"50"},"occupiedParkingLots":{"value":"200"},"occupancy":{"value":"80"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[9] = '{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"autoservizi 2 S.r.l.","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"280"},"freeParkingLots":{"value":"80"},"occupiedParkingLots":{"value":"200"},"occupancy":{"value":"71.4"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[10] ='{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"GARAGE TANUCCI DI FEI FRANCO","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"210"},"freeParkingLots":{"value":"100"},"occupiedParkingLots":{"value":"110"},"occupancy":{"value":"52.4"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[11] ='{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"Prato Parking S.r.l.","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"130"},"freeParkingLots":{"value":"45"},"occupiedParkingLots":{"value":"85"},"occupancy":{"value":"65.4"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[12] ='{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"Prato Parking S.r.l.","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"100"},"freeParkingLots":{"value":"22"},"occupiedParkingLots":{"value":"78"},"occupancy":{"value":"78"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[13] ='{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"A.r.n.o. Servizi S.r.l.","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"150"},"freeParkingLots":{"value":"41"},"occupiedParkingLots":{"value":"109"},"occupancy":{"value":"72.7"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[14] ='{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"A.r.n.o. Servizi S.r.l.","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"190"},"freeParkingLots":{"value":"12"},"occupiedParkingLots":{"value":"178"},"occupancy":{"value":"93.7"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[15] ='{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"F.A.S. PARKING S.R.L.","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"110"},"freeParkingLots":{"value":"8"},"occupiedParkingLots":{"value":"102"},"occupancy":{"value":"92.7"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[16] ='{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"COOPERATIVA SOCIALE SERVIZI AUTO POSTEGGI CUSTODITI FIRENZE","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"80"},"freeParkingLots":{"value":"32"},"occupiedParkingLots":{"value":"48"},"occupancy":{"value":"60"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[17] ='{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"PARCO FOTOVOLTAICO VICOPISANO S.R.L.","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"70"},"freeParkingLots":{"value":"21"},"occupiedParkingLots":{"value":"49"},"occupancy":{"value":"70"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[18] ='{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"PARCO FOTOVOLTAICO CHIANNI 1 S.R.L.","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"90"},"freeParkingLots":{"value":"33"},"occupiedParkingLots":{"value":"57"},"occupancy":{"value":"63.3"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[19] ='{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"PARCO FOTOVOLTAICO CASCINA 1 S.R.L.","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"50"},"freeParkingLots":{"value":"4"},"occupiedParkingLots":{"value":"46"},"occupancy":{"value":"92"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[20] ='{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"PARCO FOTOVOLTAICO FAUGLIA S.R.L.","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"80"},"freeParkingLots":{"value":"61"},"occupiedParkingLots":{"value":"19"},"occupancy":{"value":"23.8"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[21] ='{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"Firenze Amministrazioni E Gestioni S.r.l.","typeLabel":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"110"},"freeParkingLots":{"value":"40"},"occupiedParkingLots":{"value":"70"},"occupancy":{"value":"63.6"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    $fakeSingleGeoJsons[22] ='{"Service":{"type":"FeatureCollection","features":[{"geometry":{"type":"Point","coordinates":[11.247714,43.803726]},"type":"Feature","properties":{"name":"Firenze Amministrazioni E Gestioni S.r.l.":"Car park","serviceType":"TransferServiceAndRenting_Car_park","phone":"055284784","fax":"","website":"","province":"FI","city":"FIRENZE","cap":"50100","email":"","linkDBpedia":[],"note":"","description":"","description2":"","multimedia":"","serviceUri":"http://www.disit.org/km4city/resource/CarParkPieracciniMeyer","address":"Via di Monnatessa","civic":"3A","wktGeometry":"","photos":[],"photoThumbs":[],"photoOrigs":[],"avgStars":0.0,"starsCount":0,"comments":[]},"id":1}]},"realtime":{"head":{"parkingArea":["Garage La Stazione Spa"],"vars":["capacity","freeParkingLots","occupiedParkingLots","occupancy","updating"]},"results":{"bindings":[{"capacity":{"value":"170"},"freeParkingLots":{"value":"65"},"occupiedParkingLots":{"value":"105"},"occupancy":{"value":"61.7"},"status":{"value":"enoughSpacesAvailable"},"updating":{"value":"2017-01-18T14:25:00+01:00"}}]}}}';
    
    $fakeSingleGeoJsonTimeTrend = '{  
        "Service":{  
           "type":"FeatureCollection",
           "features":[  
              {  
                 "geometry":{  
                    "type":"Point",
                    "coordinates":[  
                       11.24947,
                       43.77587
                    ]
                 },
                 "type":"Feature",
                 "properties":{  
                    "name":"Garage La Stazione Spa",
                    "typeLabel":"Car park",
                    "serviceType":"TransferServiceAndRenting_Car_park",
                    "phone":"055284784",
                    "fax":"",
                    "website":"",
                    "province":"FI",
                    "city":"FIRENZE",
                    "cap":"50123",
                    "email":"",
                    "linkDBpedia":[  

                    ],
                    "note":"",
                    "description":"",
                    "description2":"",
                    "multimedia":"",
                    "serviceUri":"http://www.disit.org/km4city/resource/RT04801702315PO",
                    "address":"PIAZZA DELLA STAZIONE",
                    "civic":"3A",
                    "wktGeometry":"",
                    "photos":[  

                    ],
                    "photoThumbs":[  

                    ],
                    "photoOrigs":[  

                    ],
                    "avgStars":0.0,
                    "starsCount":0,
                    "comments":[  

                    ]
                 },
                 "id":1
              }
           ]
        },
        "realtime":{  
           "head":{  
              "parkingArea":[  
                 "Garage La Stazione Spa"
              ],
              "vars":[  
                 "capacity",
                 "freeParkingLots",
                 "occupiedParkingLots",
                 "occupancy",
                 "updating"
              ]
           },
           "results":{  
              "bindings":[  
                 {  
                    "capacity":{  
                       "value":"617.6"
                    },
                    "freeParkingLots":{  
                       "value":"322"
                    },
                    "occupiedParkingLots":{  
                       "value":"555"
                    },
                    "occupancy":{  
                       "value":"97"
                    },
                    "status":{  
                       "value":"enoughSpacesAvailable"
                    },
                    "updating":{  
                       "value":"2018-10-06T12:50:00+02:00"
                    }
                 },
                 {  
                    "capacity":{  
                       "value":"598.2"
                    },
                    "freeParkingLots":{  
                       "value":"302"
                    },
                    "occupiedParkingLots":{  
                       "value":"302"
                    },
                    "occupancy":{  
                       "value":"68"
                    },
                    "status":{  
                       "value":"enoughSpacesAvailable"
                    },
                    "updating":{  
                       "value":"2018-10-06T12:40:00+02:00"
                    }
                 },
                 {  
                    "capacity":{  
                       "value":"577"
                    },
                    "freeParkingLots":{  
                       "value":"266"
                    },
                    "occupiedParkingLots":{  
                       "value":"242"
                    },
                    "occupancy":{  
                       "value":"97"
                    },
                    "status":{  
                       "value":"enoughSpacesAvailable"
                    },
                    "updating":{  
                       "value":"2018-10-06T12:30:00+02:00"
                    }
                 },
                 {  
                    "capacity":{  
                       "value":"622"
                    },
                    "freeParkingLots":{  
                       "value":"298"
                    },
                    "occupiedParkingLots":{  
                       "value":"255"
                    },
                    "occupancy":{  
                       "value":"55"
                    },
                    "status":{  
                       "value":"enoughSpacesAvailable"
                    },
                    "updating":{  
                       "value":"2018-10-06T12:20:00+02:00"
                    }
                 },
                 {  
                    "capacity":{  
                       "value":"615"
                    },
                    "freeParkingLots":{  
                       "value":"277"
                    },
                    "occupiedParkingLots":{  
                       "value":"231"
                    },
                    "occupancy":{  
                       "value":"68"
                    },
                    "status":{  
                       "value":"enoughSpacesAvailable"
                    },
                    "updating":{  
                       "value":"2018-10-06T12:10:00+02:00"
                    }
                 },
                 {  
                    "capacity":{  
                       "value":"490"
                    },
                    "freeParkingLots":{  
                       "value":"286"
                    },
                    "occupiedParkingLots":{  
                       "value":"199"
                    },
                    "occupancy":{  
                       "value":"84"
                    },
                    "status":{  
                       "value":"enoughSpacesAvailable"
                    },
                    "updating":{  
                       "value":"2018-10-06T12:02:25+02:00"
                    }
                 },
                 {  
                    "capacity":{  
                       "value":"570"
                    },
                    "freeParkingLots":{  
                       "value":"313"
                    },
                    "occupiedParkingLots":{  
                       "value":"302"
                    },
                    "occupancy":{  
                       "value":"68"
                    },
                    "status":{  
                       "value":"enoughSpacesAvailable"
                    },
                    "updating":{  
                       "value":"2018-10-06T11:47:23+02:00"
                    }
                 },
                 {  
                    "capacity":{  
                       "value":"623"
                    },
                    "freeParkingLots":{  
                       "value":"265"
                    },
                    "occupiedParkingLots":{  
                       "value":"204"
                    },
                    "occupancy":{  
                       "value":"59"
                    },
                    "status":{  
                       "value":"enoughSpacesAvailable"
                    },
                    "updating":{  
                       "value":"2018-10-06T11:38:26+02:00"
                    }
                 },
                 {  
                    "capacity":{  
                       "value":"600"
                    },
                    "freeParkingLots":{  
                       "value":"258"
                    },
                    "occupiedParkingLots":{  
                       "value":"231"
                    },
                    "occupancy":{  
                       "value":"81"
                    },
                    "status":{  
                       "value":"enoughSpacesAvailable"
                    },
                    "updating":{  
                       "value":"2018-10-06T11:26:55+02:00"
                    }
                 },
                 {  
                    "capacity":{  
                       "value":"607.8"
                    },
                    "freeParkingLots":{  
                       "value":"275"
                    },
                    "occupiedParkingLots":{  
                       "value":"245"
                    },
                    "occupancy":{  
                       "value":"90"
                    },
                    "status":{  
                       "value":"enoughSpacesAvailable"
                    },
                    "updating":{  
                       "value":"2018-10-06T11:15:00+02:00"
                    }
                 },
                 {  
                    "capacity":{  
                       "value":"705.4"
                    },
                    "freeParkingLots":{  
                       "value":"306"
                    },
                    "occupiedParkingLots":{  
                       "value":"295"
                    },
                    "occupancy":{  
                       "value":"94"
                    },
                    "status":{  
                       "value":"enoughSpacesAvailable"
                    },
                    "updating":{  
                       "value":"2018-10-06T11:04:07+02:00"
                    }
                 }
              ]
           }
        }
     }';
    
    //Definizioni di funzione
    //Fine definizioni di funzione
    
    if(isset($_REQUEST['getGeoJson']))
    {
        echo $fakeGeoJson;
    }
    
    if(isset($_REQUEST['getSingleGeoJson']))
    {
        $id = $_REQUEST['singleGeoJsonId'];
        echo $fakeSingleGeoJsons[$id];
    }
    
    if(isset($_REQUEST['getSingleGeoJsonTimeTrend']))
    {
        echo $fakeSingleGeoJsonTimeTrend;
    }
    