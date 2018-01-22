import View from "./View";
import ResultMarkerView from "./ResultMarkerView";

export default class GeoLVMap extends View {

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

        this.get('result').each((i, result) => {
            let address = $(result).data();
            address.position = new google.maps.LatLng(address.latitude, address.longitude);
            address.label = alphabet[i % 26];

            addresses.push(address);
        });

        return addresses;
    }

    onCreate() {
        let map = new google.maps.Map(this.get('map').get(0), {
            center: this.center,
            zoom: this.zoom,
            disableDefaultUI: true,
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

        this.drawLocality(map);
        this.drawMarkers(map);
    }

    drawLocality(map) {
        let bounds = this.locality;
        if (bounds) {
            new google.maps.Rectangle({
                strokeColor: '#003366',
                strokeOpacity: 0.6,
                strokeWeight: 2,
                fillColor: '#006699',
                fillOpacity: 0.1,
                map: map,
                bounds: new google.maps.LatLngBounds(
                    new google.maps.LatLng(bounds.min_lat, bounds.min_lng),
                    new google.maps.LatLng(bounds.max_lat, bounds.max_lng)
                )
            });
        }
    }

    drawMarkers(map) {
        let bounds = new google.maps.LatLngBounds();
        let results = this.results;

        for (let address of results) {

            let marker = new google.maps.Marker({
                map: map,
                title: address.streetName,
                position: address.position,
                label: address.label
            });

            let info = new google.maps.InfoWindow({
                content: View.render(ResultMarkerView, address).dom
            });

            marker.addListener('click', () => {
                info.open(map, marker);
            });

            bounds.extend(address.position);

            if (address.isFocus) {
                map.setCenter(address.position);
                info.open(map, marker);
            }
        }

        map.fitBounds(bounds);
    }
}