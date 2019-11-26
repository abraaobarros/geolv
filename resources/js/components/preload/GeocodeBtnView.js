import View from "../View";

export default class GeocodeBtnView extends View {

    onCreate() {
        let {text, locality, state, postal_code, providers} = this.props.address;
        locality = state.length > 0? `${locality} - ${state}` : locality;
        let query_string = $.param({
            text,
            locality,
            postal_code,
            providers
        });

        this.container.html(`
            <a href="${window.location.origin}/geocode?${query_string}" 
                class="btn btn-outline-success btn-block" target="_blank">
                <i class="fa fa-external-link"></i>
            </a>
        `);
    }

}