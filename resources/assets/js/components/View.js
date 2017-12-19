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

    initialize () {
        throw new Error('Unimplemented method')
    }

    static render(component, selector, properties) {
        const element = (selector instanceof Object)? new component(undefined, selector) : new component(jQuery(selector), properties);
        $(document).ready(() => element.initialize());

        return element;
    }
}