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
        this.map = new google.maps.Map(this.get('map').get(0), {
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

        this.colors = {
            'far': '006699',
            'near': '009900',
            'A': 'FFFF00',
            'B': '009933',
            'C': '006699',
        };

        this.drawLocality();
        this.drawMarkers();
    }

    drawLocality() {
        let bounds = this.locality;
        if (bounds) {
            new google.maps.Rectangle({
                strokeColor: '#003366',
                strokeOpacity: 0.6,
                strokeWeight: 2,
                fillColor: '#006699',
                fillOpacity: 0.1,
                map: this.map,
                bounds: new google.maps.LatLngBounds(
                    new google.maps.LatLng(bounds.min_lat, bounds.min_lng),
                    new google.maps.LatLng(bounds.max_lat, bounds.max_lng)
                )
            });
        }
    }

    drawMarkers() {
        let bounds = new google.maps.LatLngBounds();
        let results = this.results;

        for (let address of results) {

            let color = this.colors[address.group];
            let icon = new google.maps.MarkerImage(`http://www.googlemapsmarkers.com/v1/${color}/`);
            let marker = new google.maps.Marker({
                map: this.map,
                icon: icon,
                title: address.streetName,
                position: address.position,
                label: address.label,
            });

            let info = new google.maps.InfoWindow({
                content: View.render(ResultMarkerView, address).dom
            });

            marker.addListener('click', () => {
                info.open(this.map, marker);
            });

            bounds.extend(address.position);

            if (address.isFocus) {
                this.map.setCenter(address.position);
                info.open(this.map, marker);
            }
        }

        this.map.fitBounds(bounds);
    }
}