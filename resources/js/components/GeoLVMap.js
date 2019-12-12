import View from "./View";
import ResultMarkerView from "./ResultMarkerView";
import MarkerClusterer from "@google/markerclusterer";

export default class GeoLVMap extends View {

    constructor(jQueryElement, properties) {
        super(jQueryElement, properties);
        this.colors = {
            0: 'e6194b',
            1: '3cb44b',
            2: 'ffe119',
            3: '0082c8',
            4: 'f58231',
            5: '911eb4',
            6: '46f0f0',
            7: 'f032e6',
            8: 'd2f53c',
            9: 'fabebe',
            10: '008080',
            11: 'aa6e28',
            12: 'acE5ee',
            13: 'de5d83',
            14: '66ff00',
            15: 'c32148'
        };
    }

    get center() {
        let center = this.get('map').data('center');
        if (center) {
            center = center.split(',');
            return {lat: parseFloat(center[0]), lng: parseFloat(center[1])};
        } else {
            return {lat: 0, lng: 0};
        }
    }

    get zoom() {
        return this.get('map').data('zoom') || 8
    }

    get locality() {
        let values = this.get('map').data('locality');
        if (values) {
            values = values.split('|').map((i) => parseFloat(i));
            return {min_lat: values[0], min_lng: values[1], max_lat: values[2], max_lng: values[3]}
        } else {
            return null
        }
    }

    get results() {
        let addresses = [];
        let alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        let results = this.get('result');
        let total = results.length;

        results.each((i, result) => {
            let address = $(result).data();
            address.position = new google.maps.LatLng(address.latitude, address.longitude);
            if (total < 26)
                address.label = alphabet[i];

            addresses.push(address);
        });

        return addresses;
    }

    onCreate() {


        this.map = new google.maps.Map(this.get('map').get(0), {
            center: this.center,
            zoom: this.zoom,
            disableDefaultUI: true,
            zoomControl: true,
            scaleControl: true,
            //<editor-fold defaultstate="collapsed" desc="styles: [...]">
            styles: [
                {
                    "featureType": "administrative",
                    "elementType": "labels.text.fill",
                    "stylers": [
                        {
                            "color": "#444444"
                        }
                    ]
                },
                {
                    "featureType": "landscape",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#f2f2f2"
                        }
                    ]
                },
                {
                    "featureType": "poi",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "road",
                    "elementType": "all",
                    "stylers": [
                        {
                            "saturation": -100
                        },
                        {
                            "lightness": 45
                        }
                    ]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "simplified"
                        }
                    ]
                },
                {
                    "featureType": "road.arterial",
                    "elementType": "labels.icon",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "transit",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "water",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#46bcec"
                        },
                        {
                            "visibility": "on"
                        }
                    ]
                }
            ]
            //</editor-fold>
        });

        this.drawLocality();
        this.drawMarkers();
    }

    getColor(group) {
        if (!this.colors.hasOwnProperty(group)) {
            this.colors[group] = (0x1000000 + (Math.random()) * 0xffffff).toString(16).substr(1, 6);
        }

        return this.colors[group];
    }

    drawLocality() {
        let bounds = this.locality;
        if (bounds) {
            let min = new google.maps.LatLng(bounds.min_lat, bounds.min_lng);
            let max = new google.maps.LatLng(bounds.max_lat, bounds.max_lng);
            new google.maps.Rectangle({
                strokeColor: '#003366',
                strokeOpacity: 0.6,
                strokeWeight: 2,
                fillColor: '#006699',
                fillOpacity: 0.1,
                map: this.map,
                bounds: new google.maps.LatLngBounds(min, max)
            });
        }
    }

    drawMarkers() {
        let bounds = new google.maps.LatLngBounds();
        let markers = this.results.map((address) => {
            bounds.extend(address.position);
            return this.createMarker(address);
        });

        new MarkerClusterer(this.map, markers,
            {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});

        this.map.fitBounds(bounds);
    }

    createMarker(address) {
        let pinColor = this.getColor(address.cluster);
        let pinImage = {
            url: "https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
            scaledSize: new google.maps.Size(21, 34),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(10, 34),
            labelOrigin: new google.maps.Point(10, 10),
        };
        let pinShadow = {
            url: "https://chart.apis.google.com/chart?chst=d_map_pin_shadow",
            scaledSize: new google.maps.Size(40, 37),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(12, 35),
        };

        let marker = new google.maps.Marker({
            map: this.map,
            icon: pinImage,
            shadow: pinShadow,
            title: address.streetName,
            position: address.position,
            label: {text: address.label, color: "white", fontWeight: "bold"}
        });

        let info = new google.maps.InfoWindow({
            content: View.render(ResultMarkerView, address).dom
        });

        marker.addListener('click', () => {
            info.open(this.map, marker);
        });

        if (address.isFocus) {
            this.map.setCenter(address.position);
            info.open(this.map, marker);
        }

        return marker;
    }
}