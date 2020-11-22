import Api from './Api';

export default{
    getAllItemStatus(){
        return Api().get('itemstatus');
    }
};