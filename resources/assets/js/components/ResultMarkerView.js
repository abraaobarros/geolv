import View from "./View";

export default class ResultMarkerView extends View {

    initialize() {
        this.container.html(`
            <div class="card" style="margin-bottom: 10px">
                <div class="card-body">
                    <h4 class="card-title">${ this.props.streetName }</h4>
                    <h6 class="card-subtitle mb-2 text-muted">${ this.props.provider }</h6>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item list-group-item-{{ $result->relevance > 0? 'default' : 'danger' }}">
                        Relevância: ${ this.props.relevance }%
                    </li>
                    <li class="list-group-item">Número: ${ this.props.streetNumber || "" }</li>
                    <li class="list-group-item">Localidade: ${ this.props.locality || "" }</li>
                    <li class="list-group-item">Sub-localidade: ${ this.props.subLocality || "" }</li>
                    <li class="list-group-item">
                        País: (${ this.props.countryCode || "" }) ${ this.props.countryName || "" }
                    </li>
                    <li class="list-group-item">Latitude: ${ this.props.latitude }</li>
                    <li class="list-group-item">Longitude: ${ this.props.longitude }</li>
                    <li class="list-group-item">CEP: ${ this.props.postalCode || "" }</li>
                </ul>
            </div>
        `)
    }
}