import Api from './Api';

export default{

    getItemBrand(param){
        return Api().get('itembrand', param);
    },
    getAllItemBrand(){
        return Api().get('itembrand');
    },
    addItemBrand(brand){
        return Api().post('itembrand', brand);
    },
    editItemBrand(param){
        return Api().put(`itembrand/${param.id}`, param);
    },
    deleteItemBrand(id){
        return Api().delete(`itembrand/${id}`);
    }
};