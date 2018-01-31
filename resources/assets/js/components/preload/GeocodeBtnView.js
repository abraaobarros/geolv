import View from "../View";
import Geocode from "../../services/Geocode";

export default class GeocodeBtnView extends View {

    onCreate() {
        let address = this.props.address;
        this.container.html(`
            <a href="/geocode?text=${address.text}&locality=${address.locality}&postal_code=${address.postal_code}" 
                class="btn btn-outline-success" target="_blank">
                Visualizar <i class="fa fa-external-link ml-2"></i>
            </a>
        `);
    }

}