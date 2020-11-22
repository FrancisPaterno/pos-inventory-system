import Api from './Api';

export default{
    getCustomers(param){
        return Api().get('customer', param);
    },
    addCustomer(customer){
        return Api().post('customer', customer);
    },
    updateCustomer(customer){
        return Api().put(`customer/${customer.id}`,customer);
    },
    deleteCustomer(id){
        return Api().delete(`customer/${id}`);
    }
};