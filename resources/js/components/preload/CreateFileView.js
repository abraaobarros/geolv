import View from "../View";
import PreviewTableView from "./PreviewTableView";
import TableView from "../TableView";
import GeocodeBtnView from "./GeocodeBtnView";

export default class CreateFileView extends View {

    get hasHeader() {
        return this.input('header').prop('checked');
    }

    onCreate() {
        this.results = [];
        this.count = 0;
        this.indexes = {'text': [], 'locality': [], 'state': [], 'postal_code': []};
        this.input('geocode_file').change(() => this.parse());
        this.input('delimiter').change(() => this.parse());
        this.input('header').change(() => this.displayResults());
        this.input('providers[]').change(() => { this.displayCount(); this.onAddressUpdated() });
        this.input('mode').click(() => this.updateMode());
    }

    parse() {
        this.get('preview').fadeIn('slow');
        this.get('preview_hide').hide();
        this.input('geocode_file').parse({
            before: (file) => this.beforeParsing(file),
            error: (err, file, inputElem, reason) => this.onParsingError(err, file, reason),
            config: {
                preview: 6,
                skipEmptyLines: true,
                quote: true,
                header: false,
                skip: this.hasHeader ? 1 : 0,
                delimiter: this.input('delimiter').val(),
                complete: (results, file) => this.onCompletedParsing(results, file)
            }
        });
        this.get('exampleContainer').hide();
    }

    countLines() {
        this.count = 0;
        this.displayCount();
        this.input('geocode_file').parse({
            config: {
                chunk: (results) => {
                    this.count += results.data.length;
                    this.displayCount();
                }
            }
        });
    }

    displayCount() {
        let providers = this.input('providers[]').filter(':checked').length;
        let price = 2.0 * (this.count / 1000) * providers;

        this.get('price').html(price.toFixed(2));
        this.get('providers_count').html(providers);
        this.input('count').val(this.count);

        let time = Math.ceil(this.count / 10);
        if (time === 0)
            this.get('time').html('-');
        else
            this.get('time').html(time);
    }

    beforeParsing(file) {
        this.get('label').html(file.name);
        return {action: "continue"};
    }

    getParsedAddress(row, indexes) {
        try {
            let fields = [];
            for (let idx of indexes) {
                fields.push(this.results[row][idx]);
            }

            return fields.join(', ').trim();
        } catch (e) {
            return '';
        }
    }

    getAddressesView() {
        let addresses = [];
        let providers = this.input('providers[]').filter(':checked').map((i, o) => $(o).val()).toArray();

        for (let i in this.results) {
            if (this.hasHeader && i == 0) continue;

            let address = {
                text: this.getParsedAddress(i, this.indexes['text']),
                locality: this.getParsedAddress(i, this.indexes['locality']),
                state: this.getParsedAddress(i, this.indexes['state']),
                postal_code: this.getParsedAddress(i, this.indexes['postal_code']),
                providers: providers
            };
            let data = [];

            if (this.indexes.text.length > 0)
                data.push(address.text);

            if (this.indexes.locality.length > 0)
                data.push(address.locality);

            if (this.indexes.state.length > 0)
                data.push(address.state);

            if (this.indexes.postal_code.length > 0)
                data.push(address.postal_code);

            data.push(View.render(GeocodeBtnView, {address}).container);
            addresses.push(data);
        }

        return addresses;
    }

    getHeadersView() {
        let headers = [];
        if (this.indexes.text.length > 0)
            headers.push('Endereço');

        if (this.indexes.locality.length > 0)
            headers.push('Cidade');

        if (this.indexes.state.length > 0)
            headers.push('Estado');

        if (this.indexes.postal_code.length > 0)
            headers.push('CEP');

        headers.push('Resultado');

        return headers;
    }

    onParsingError(err, file, reason) {
        console.log(err, file, reason);
    }

    onAddressUpdated() {
        let address = this.indexes.text.length > 0;
        //let locality = this.indexes.locality.length > 0;
        //let state = this.indexes.state.length > 0;
        let postal_code = this.indexes.postal_code.length > 0;

        this.input('indexes').val(JSON.stringify(this.indexes));

        if (address || postal_code > 0) {
            this.get('exampleContainer').fadeIn();
            View.render(TableView, this.get('exampleTable'), {
                data: this.getAddressesView(),
                header: this.getHeadersView()
            });
        } else {
            this.get('exampleContainer').fadeOut();
        }
    }

    updateMode() {
        let mode = this.input('mode').filter(':checked').val();
        this.previewTable.setMode(mode);
    }

    onCompletedParsing(results) {
        this.results = results.data;
        this.displayResults();
        this.countLines();
    }

    displayResults() {
        let start = (this.hasHeader)? 1: 0;
        let end = this.results.length - ((this.hasHeader)? 0: 1);
        let header = (this.hasHeader)? this.results[0] : [];
        this.previewTable = View.render(PreviewTableView, this.get('table'), {
            data: this.results.slice(start, end),
            header: header,
            indexes: this.indexes,
            selected: () => this.onAddressUpdated()
        });
        this.onAddressUpdated();
        this.updateMode();
    }
}
