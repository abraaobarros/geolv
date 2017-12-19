import View from "./View";
import ResultMarkerView from "./ResultMarkerView";

class GeoLVMap extends View {

    get center() {
        let center = this.get('map').data('center').split(',');
        if (center.length)
            return {lat: parseFloat(center[0]), lng: parseFloat(center[1])};
        else
            return undefined;
    }

    get zoom() {
        return this.data.zoom || 8
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

    initialize() {
        let map = new google.maps.Map(this.get('map').get(0), {
            center: this.center,
            zoom: this.zoom,
            disableDefaultUI: true,
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
        });

        let bounds = new google.maps.LatLngBounds();
        let results = this.results;

        for (let address of results) {

            let marker = new google.maps.Marker({
                map: map,
                title: address.streetName,
                position: address.position,
                label: address.label,
            });

            let info = new google.maps.InfoWindow({
                content: View.render(ResultMarkerView, address).dom
            });

            marker.addListener('click', () => {
                info.open(map, marker);
            });

            bounds.extend(address.position);
        }

        map.fitBounds(bounds);

        if (results.length)
            map.setCenter(results[0].position)
    }

}

window.initMap = function () {

    View.render(GeoLVMap, '#geolv-container', {
        map: '.geolv-map',
        result: '.geolv-result'
    });

};
