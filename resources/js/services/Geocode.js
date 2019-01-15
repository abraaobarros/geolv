export default class Geocode {

    static get(text, locality, postal_code) {
        return axios.get('/api/geocode', { params: {text, locality, postal_code} })
    }

}