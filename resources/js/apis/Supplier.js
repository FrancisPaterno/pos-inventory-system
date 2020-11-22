import Api from './Api';

export default{
    getSuppliers(param){
        return Api().get('supplier', param);
    },
    addSupplier(supplier){
        return Api().post('supplier', supplier);
    },
    updateSupplier(supplier){
        return Api().put(`supplier/${supplier.id}`, supplier);
    },
    deleteSupplier(id){
        return Api().delete(`supplier/${id}`);
    }
};