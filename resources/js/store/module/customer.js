const state = {
    customers:[]
};
const getters = {
    getCustomers:(state)=>(state.customers.data)
};
const actions = {};
const mutations = {
    setCustomers:(state, customers)=>(state.customers = customers),
    setCustomerCurrentPage: (state, data) => (state.customers.current_page = data),
    setCustomer:(state, customer)=>(state.customers.data.push(customer)),
    updateCustomer:(state, updCustomer)=>{
        const index = state.customers.data.findIndex(cust=>cust.id === updCustomer.id);
        if(index !== -1){
            state.customers.data.splice(index, 1, updCustomer);
        }
    },
    removeCustomer:(state, customer)=>(state.customers.data = state.customers.data.filter(cust=>cust.id !== customer.id))
};

export default {
    state,
    getters,
    actions,
    mutations
}