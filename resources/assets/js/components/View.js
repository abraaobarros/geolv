export default class View {

    constructor(jQueryElement, properties) {
        this.container = jQueryElement || jQuery("<div></div>");
        this.props = properties;
    }

    get dom() {
        return this.container.get(0)
    }

    get data() {
        return this.container.data()
    }

    get(subElement) {
        return this.find(this.props[subElement])
    }

    find (selector) {
        return this.container.find(selector)
    }

    // noinspection JSUnusedGlobalSymbols
    onCreate () {
        throw new Error('Unimplemented method')
    }

    static render(component, selector, properties) {
        let element;

        if (selector instanceof jQuery)
            element = new component(selector, properties);
        else if (selector instanceof Object)
            element = new component(jQuery('<div></div>'), selector);
        else
            element = new component(jQuery(selector), properties);

        $(document).ready(() => element.onCreate());

        return element;
    }

}