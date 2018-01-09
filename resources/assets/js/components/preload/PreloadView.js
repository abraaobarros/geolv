import View from "../View";
import PreviewTableView from "./PreviewTableView";
import TableView from "../TableView";
import GeocodeBtnView from "./GeocodeBtnView";

export default class PreloadView extends View {

    onCreate() {
        this.results = [];
        this.get('input').change(() => this.parse());
        this.get('radioAddress').click(() => this.updateMode());
        this.get('radioLocality').click(() => this.updateMode());
        this.get('radioCEP').click(() => this.updateMode());
    }

    parse() {
        this.get('preview').fadeIn('slow');
        this.get('input').parse({
            before: (file) => this.beforeParsing(file),
            error: (err, file, inputElem, reason) => this.onParsingError(err, file, reason),
            config: {
                preview: 5,
                skipEmptyLines: true,
                quote: true,
                complete: (results, file) => this.onCompletedParsing(results, file)
            }
        });
    }

    beforeParsing(file) {
        this.get('label').html(file.name);
        return {action: "continue"};
    }

    getParsedAddress(row, selectedIdxList) {
        let fields = [];
        for (let idx of selectedIdxList) {
            fields.push(this.results[row][idx]);
        }

        return fields.join(', ').trim();
    }

    getAddresses(selectedIdxList) {
        let addresses = [];
        for (let i in this.results) {
            let address = {
                street_name: this.getParsedAddress(i, selectedIdxList['street_name']),
                locality: this.getParsedAddress(i, selectedIdxList['locality']),
                cep: this.getParsedAddress(i, selectedIdxList['cep'])
            };
            addresses.push([
                address.street_name,
                address.locality,
                address.cep,
                View.render(GeocodeBtnView, {address}).container,
            ]);
        }

        return addresses;
    }

    onParsingError(err, file, reason) {
        console.log(err, file, reason);
    }

    onAddressUpdated(selectedIdxList) {
        let address = this.getParsedAddress(0, selectedIdxList['street_name']);
        let locality = this.getParsedAddress(0, selectedIdxList['locality']);
        let cep = this.getParsedAddress(0, selectedIdxList['cep']);

        if ((address.length > 0 && locality.length > 0) || cep.length > 0) {
            this.get('exampleContainer').fadeIn();
            View.render(TableView, this.get('exampleTable'), {
                data: this.getAddresses(selectedIdxList),
                header: ['Endereço', 'Cidade', 'CEP', 'Resultado']
            });
        } else {
            this.get('exampleContainer').fadeOut();
        }
    }

    updateMode() {
        let address = this.get('radioAddress').prop('checked');
        let locality = this.get('radioLocality').prop('checked');
        let cep = this.get('radioCEP').prop('checked');

        if (address)
            this.previewTable.setMode('street_name');
        else if (locality)
            this.previewTable.setMode('locality');
        else if (cep)
            this.previewTable.setMode('cep');
    }

    onCompletedParsing(results) {
        this.results = results.data;
        this.previewTable = View.render(PreviewTableView, this.get('table'), {
            data: this.results,
            selected: (list) => this.onAddressUpdated(list)
        });
        this.updateMode();
    }
}
