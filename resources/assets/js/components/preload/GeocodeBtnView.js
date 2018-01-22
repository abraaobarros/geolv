import View from "../View";
import Geocode from "../../services/Geocode";

export default class GeocodeBtnView extends View {

    onCreate() {
        this.container.html(`
            <button class="btn btn-outline-success">
                Visualizar <i class="fa fa-map-pin ml-2"></i>
            </button>
        `);

        this.btn = this.find('.btn');
        this.btn.click((e) => this.onBtnClick(e));
    }

    onBtnClick(e) {
        e.preventDefault();
        this.container.html('<i class="fa fa-spinner fa-pulse fa-fw text-success"></i>');

        Geocode
            .get(this.props.address.text, this.props.address.locality, this.props.address.postal_code)
            .then((response) => this.onGeocodeResult(response.data))
            .catch((e) => this.onGeocodeError(e));
    }

    onGeocodeResult(response) {
        console.log(response);
        if (response.data.length > 0)
            this.container.html('<code>' + JSON.stringify(response.data[0], null, 4) + '</code>');
        else
            this.container.html('Nenhum resultado encontrado.');
    }

    onGeocodeError(error) {
        if (error.response.status == 422) {
            this.container.html('');
            for (let message of _.values(error.response.data.errors)) {
                this.container.append(`<span class="text-danger">${message}</span>`);
            }
        } else {
            this.container.html(error.message);
        }
    }
}