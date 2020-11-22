import Api from './Api';

export default{
    getItemUnits(param){
        return Api().get('itemunit', param);
    },
    getAllItemUnits(){
        return Api().get('itemunit');
    },
    addItemUnit(brand){
        return Api().post('itemunit', brand);
    },
    editItemUnit(itemunit){
        return Api().put(`itemunit/${itemunit.id}`, itemunit);
    },
    deleteItemUnit(id){
        return Api().delete(`itemunit/${id}`);
    }
};