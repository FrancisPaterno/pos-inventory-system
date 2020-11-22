import Api from './Api';

export default{
    getItems(params){
        return Api().get('item', params);
    },

    getAllItems(){
        return Api().get('item');
    },

    addItem(param){
        return Api().post('item', param);
    },
    getItemImage(param){
        return Api().get('itemImage', param);
    },
    editItem(item){
        return Api().put(`item/${item.id}`, item);
    },
    deleteItem(id){
        return Api().delete(`item/${id}`);
    }
}