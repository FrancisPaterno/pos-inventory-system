import axios from "axios";

let BaseApi = axios.create({ baseURL: "http://127.0.0.1:8000/api" });

let Api = function() {
    let token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        BaseApi.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
    }
    return BaseApi;
};

export default Api;
