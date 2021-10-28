import Api from './Api'

export default{
    getStockItems(param){
        console.log('params', param);
        return Api().get('stockItems', param);
    },
    getStockItem(id){
        return Api().get(`stockItems/${id}`);
    },
    addStockItem(stockItem){
        return Api().post('stockItems', stockItem);
    },
    updateStockItem(stockItem){
        return Api().put(`stockItems/${stockItem.id}`,stockItem);
    },
    removeStockItem(id){
        return Api().delete(`stockItems/${id}`);
    }
};