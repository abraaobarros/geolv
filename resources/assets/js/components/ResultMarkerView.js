import View from "./View";

export default class ResultMarkerView extends View {

    onCreate() {
        this.container.html(`
            <div class="card" style="margin-bottom: 10px">
                <div class="card-body">
                    <h4 class="card-title">${ this.props.streetName }</h4>
                    <h6 class="card-subtitle mb-2 text-muted">${ this.props.provider }</h6>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <span class="badge mr-1">Número:</span> ${ this.props.streetNumber || "" }
                    </li>
                    <li class="list-group-item">
                        <span class="badge mr-1">Localidade:</span> ${ this.props.locality || "" }
                    </li>
                    <li class="list-group-item">
                        <span class="badge mr-1">Sub-localidade:</span> ${ this.props.subLocality || "" }
                    </li>
                    <li class="list-group-item">
                        <span class="badge mr-1">País:</span> (${ this.props.countryCode || "" }) ${ this.props.countryName || "" }
                    </li>
                    <li class="list-group-item">
                        <span class="badge mr-1">Latitude:</span> ${ this.props.latitude }
                    </li>
                    <li class="list-group-item">
                        <span class="badge mr-1">Longitude:</span> ${ this.props.longitude }
                    </li>
                    <li class="list-group-item">
                        <span class="badge mr-1">CEP:</span> ${ this.props.postalCode || "" }
                    </li>
                </ul>
            </div>
        `)
    }

}