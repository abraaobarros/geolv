import View from "./View";

export default class ResultMarkerView extends View {

    addItem(name, value) {
        if (value) {
            this.find('.list-group').append(`
                <li class="list-group-item">
                    <span class="badge mr-1">${name}:</span> ${ value }
                </li>
            `)
        }
    }

    onCreate() {
        this.container.html(`
            <div class="card" style="margin-bottom: 10px">
                <div class="card-body">
                    <h4 class="card-title">${ this.props.formattedAddress }</h4>
                    <h6 class="card-subtitle mb-2 text-muted">${ this.props.provider } <small>(Cluster: ${this.props.cluster})</small></h6>
                </div>
                <ul class="list-group list-group-flush"></ul>
            </div>
        `);

        this.addItem('Número', this.props.streetNumber);
        this.addItem('Localidade', this.props.locality);
        this.addItem('Sub-localidade', this.props.postalCode);
        this.addItem('CEP', this.props.subLocality);
        this.addItem('País', this.props.countryName);
        this.addItem('Latitude', this.props.latitude.toFixed(8));
        this.addItem('Longitude', this.props.longitude.toFixed(8));
    }

}