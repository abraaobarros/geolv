import View from "../View";
import PreviewTableView from "./PreviewTableView";
import TableView from "../TableView";
import GeocodeBtnView from "./GeocodeBtnView";

export default class PreloadView extends View {

    get hasHeader() {
        return this.input('header').prop('checked');
    }

    onCreate() {
        this.results = [];
        this.count = 0;
        this.indexes = {'text': [], 'locality': [], 'postal_code': []};
        this.input('geocode_file').change(() => this.parse());
        this.input('delimiter').change(() => this.parse());
        this.input('header').change(() => this.displayResults());
        this.get('radioAddress').click(() => this.updateMode());
        this.get('radioLocality').click(() => this.updateMode());
        this.get('radioCEP').click(() => this.updateMode());
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
        let price = this.count / 1000;
        this.get('price').html(price.toFixed(2));
        this.input('count').val(this.count);

        let time = Math.ceil(this.count / 20);
        if (time == 0)
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

    getAddresses() {
        let addresses = [];
        for (let i in this.results) {
            if (this.hasHeader && i == 0) continue;

            let address = {
                text: this.getParsedAddress(i, this.indexes['text']),
                locality: this.getParsedAddress(i, this.indexes['locality']),
                postal_code: this.getParsedAddress(i, this.indexes['postal_code'])
            };
            addresses.push([
                address.text,
                address.locality,
                address.postal_code,
                View.render(GeocodeBtnView, {address}).container,
            ]);
        }

        return addresses;
    }

    getCepAddresses() {
        let addresses = [];
        for (let i in this.results) {
            if (this.hasHeader && i == 0) continue;

            let address = {
                postal_code: this.getParsedAddress(i, this.indexes['postal_code'])
            };
            addresses.push([
                address.postal_code,
                View.render(GeocodeBtnView, {address}).container,
            ]);
        }

        return addresses;
    }

    onParsingError(err, file, reason) {
        console.log(err, file, reason);
    }

    onAddressUpdated() {
        let address = this.getParsedAddress(0, this.indexes.text);
        let locality = this.getParsedAddress(0, this.indexes.locality);
        let postal_code = this.getParsedAddress(0, this.indexes.postal_code);

        this.input('indexes').val(JSON.stringify(this.indexes));

        if (address.length > 0 && locality.length > 0) {
            this.get('exampleContainer').fadeIn();
            View.render(TableView, this.get('exampleTable'), {
                data: this.getAddresses(),
                header: ['Endereço', 'Cidade', 'CEP', 'Resultado']
            });
        } else if (address.length == 0 && locality.length == 0 && postal_code.length > 0) {
            this.get('exampleContainer').fadeIn();
            View.render(TableView, this.get('exampleTable'), {
                data: this.getCepAddresses(),
                header: ['CEP', 'Resultado']
            });
        } else {
            this.get('exampleContainer').fadeOut();
        }
    }

    updateMode() {
        let address = this.get('radioAddress').prop('checked');
        let locality = this.get('radioLocality').prop('checked');
        let postal_code = this.get('radioCEP').prop('checked');

        if (address)
            this.previewTable.setMode('text');
        else if (locality)
            this.previewTable.setMode('locality');
        else if (postal_code)
            this.previewTable.setMode('postal_code');
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
