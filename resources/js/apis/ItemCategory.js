import Api from "./Api";

export default {
    getItemCategories(param) {
        return Api().get("itemcategory", param);
    },
    getAllItemCategory(){
        return Api().get('itemcategory');
    },
    addItem(category){
        return Api().post('itemcategory',category);
    },
    editItem(param){
        return Api().put(`itemcategory/${param.id}`, param);
    },
    deleteItem(id){
        return Api().delete(`itemcategory/${id}`);
    }
};
