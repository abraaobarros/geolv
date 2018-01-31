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

window.initPreload = function () {

    View.render(PreloadView, '#preload-container', {
        label: '.form-control-file',
        table: '.result-table',
        price: '.price-value',
        time: '.time-value',
        preview: '.preview-container',
        preview_hide: '.preview-container-hide',
        exampleContainer: '.example-container',
        exampleTable: '.example-table',
        radioAddress: '#modeRadioAddress',
        radioLocality: '#modeRadioLocality',
        radioCEP: '#modeRadioCEP',
        geocodeExamplesBtn: '#geocodeExamples'
    });

};


