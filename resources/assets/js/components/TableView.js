import View from "./View";

export default class TableView extends View {

    onCreate() {
        if (this.props.data && this.props.data.length > 0)
            this.setData(this.props.data);

        if (this.props.header && this.props.header.length > 0)
            this.addHeader(this.props.header);
    }

    addRow() {
        return this.dom.insertRow();
    }

    addCell(row, data, header = false) {
        let tag = header? 'th' : 'td';
        let cell = $(`<${tag}></${tag}>`).html(data);
        $(row).append(cell);

        return cell;
    }

    addHeader(data) {
        let header = this.dom.createTHead();
        let row = header.insertRow();

        for (let d of data)
            this.addCell(row, d, true);
    }

    setData(data) {
        this.container.html('');
        for (let i in data) {
            let row = this.addRow();

            for (let j in data[i]) {
                let cell = this.addCell(row, data[i][j]);
                this.onBuildCell(cell, row, i, j);
            }
        }
    }

    onBuildCell(cell, row, i, j) {
        //
    }

}