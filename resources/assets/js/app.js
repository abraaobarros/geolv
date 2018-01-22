require('./bootstrap');

import View from "./components/View";
import GeoLVMap from "./components/GeoLVMap";
import PreloadView from "./components/preload/PreloadView";

window.initMap = function () {

    View.render(GeoLVMap, '#geolv-container', {
        map: '.geolv-map',
        result: '.geolv-result'
    });

};


View.render(PreloadView, '#preload-container', {
    input: 'input[name=geocode_file]',
    indexes: 'input[name=indexes]',
    label: '.form-control-file',
    table: '.result-table',
    preview: '.preview-container',
    exampleContainer: '.example-container',
    exampleTable: '.example-table',
    radioAddress: '#modeRadioAddress',
    radioLocality: '#modeRadioLocality',
    radioCEP: '#modeRadioCEP',
    geocodeExamplesBtn: '#geocodeExamples'
});
