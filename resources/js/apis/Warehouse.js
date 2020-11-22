import Api from './Api';

export default{
    getWarehouses(param){
        return Api().get('warehouse', param);
    },
    getAllWarehouse(){
        return Api().get('warehouse');
    },
    addWarehouse(warehouse){
        return Api().post('warehouse', warehouse);
    },
    updateWarehouse(warehouse){
        return Api().put(`warehouse/${warehouse.id}`, warehouse);
    },
    deleteWarehouse(id){
        return Api().delete(`warehouse/${id}`);
    }
};