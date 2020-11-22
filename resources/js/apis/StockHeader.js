import Api from './Api';

export default{
    getStockHeaders(param){
        return Api().get('stockHeaders', param);
    },
    getStockHeader(id){
        return Api().get(`stockHeaders/${id}`);
    },
    addStockHeader(stockheader){
        return Api().post('stockHeaders', stockheader);
    },
    updateStockHeader(stockheader){
        return Api().put(`stockHeaders/${stockheader.id}`, stockheader);
    },
    removeStockHeader(id){
        return Api().delete(`stockHeaders/${id}`);
    }
};