const state = {
    suppliers:[],
    allsupplier:[]
};
const getters = {
    getSuppliers:(state)=>(state.suppliers.data),
    getAllSuppliers:(state)=>(state.allsupplier)
};
const actions = {};
const mutations = {
    setSuppliers:(state, suppliers)=>(state.suppliers = suppliers),
    setAllSuppliers:(state, suppliers)=>(state.allsupplier = suppliers),
    setSupplier:(state, supplier)=>(state.suppliers.data.push(supplier)),
    updateSupplier:(state, updSupplier)=>{
        const index = state.suppliers.data.findIndex(sup=>sup.id === updSupplier.id);
        if(index !== -1){
            state.suppliers.data.splice(index, 1, updSupplier);
        }
    },
    removeSupplier:(state, supplier)=>(state.suppliers.data = state.suppliers.data.filter(sup=>sup.id !== supplier.id)),
    setSupplierCurrentPage:(state, data)=>(state.suppliers.current_page = data)
};

export default{
    state,
    getters,
    actions,
    mutations
}