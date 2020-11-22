const state = {
    warehouses:[],
    allwarehouses:[]
};
const getters = {
    getWarehouses:(state)=>(state.warehouses.data),
    getAllWarehouses:(state)=>(state.allwarehouses)
};
const actions = {

};
const mutations = {
    setWarehouses:(state, warehouses)=>(state.warehouses = warehouses),
    setAllWarehouses:(state, warehouses)=>(state.allwarehouses = warehouses),
    setWarehouse:(state, warehouse)=>(state.warehouses.data.push(warehouse)),
    updateWarehouse:(state, updwarehouse)=>{
        const index = state.warehouses.data.findIndex(wh=>wh.id === updwarehouse.id);
        if(index !== -1){
            state.warehouses.data.splice(index, 1, updwarehouse);
        }
    },
    removeWarehouse:(state, warehouse)=>(state.warehouses.data = state.warehouses.data.filter(wh=>wh.id !== warehouse.id)),
    setWarehouseCurrentPage:(state, data)=>(state.warehouses.current_page = data)
};

export default {
    state,
    getters,
    actions,
    mutations
};