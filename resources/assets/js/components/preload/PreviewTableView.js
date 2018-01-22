import TableView from "../TableView";

export default class PreviewTableView extends TableView {

    onCreate() {
        super.onCreate();
        this.modes = new Map([
            ['text', ['info', 'Endereço']],
            ['locality', ['success', 'Cidade']],
            ['postal_code', ['warning', 'postal_code']],
        ]);
        this.selectedIdxList = {
            'text': [],
            'locality': [],
            'postal_code': []
        };

        let view = this;
        this.find('td')
            .hover(function () {
                view.onCellHover($(this))
            })
            .click(function () {
                view.onCellClick($(this))
            });
    }

    setMode(key) {
        this.currentMode = key;
    }

    getFirstCell(index) {
        return this.find(`tr:first > td:nth-child(${index})`);
    }

    getCol(index) {
        return this.find(`:nth-child(${index})`);
    }

    onCellHover(cell) {
        let index = cell.index() + 1;
        this.getCol(index).toggleClass('active').tooltip('toggle');
    }

    hasSelectedColumn(columnIndex) {
        for (let [modeKey, mode] of this.modes) {
            if (this.selectedIdxList[modeKey].indexOf(columnIndex) !== -1)
                return modeKey;
        }

        return null;
    }

    onCellClick(cell) {
        let index = cell.index();
        let selectedColumn = this.hasSelectedColumn(index);

        if (selectedColumn === null)
            this.selectedIdxList[this.currentMode].push(index);
        else if (selectedColumn === this.currentMode)
            this.selectedIdxList[this.currentMode] = _.pull(this.selectedIdxList[this.currentMode], index);

        this.updateSelectedCells();
    }

    updateSelectedCells() {
        this.find('td > span').remove();
        this.find('td').removeClass('selected');

        for (let [modeKey, mode] of this.modes) {
            for (let i in this.selectedIdxList[modeKey]) {
                let index = this.selectedIdxList[modeKey][i] + 1;
                let order = parseInt(i) + 1;
                let text = this.selectedIdxList[modeKey].length > 1 ? `${mode[1]} #${order}` : mode[1];

                this.getFirstCell(index).prepend(`<span class="badge badge-${mode[0]} badge-selected">${text}</span>`);
                this.getCol(index).addClass('selected');
            }
        }

        this.props.selected(this.selectedIdxList);
    }

}