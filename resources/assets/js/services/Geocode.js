export default class Geocode {

    static get(street_name, locality, cep) {
        return axios.get('/api/geocode', { params: {street_name, locality, cep} })
    }

}