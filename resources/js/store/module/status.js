const state = {allitemstatus:[]};
const getters = {
    getAllItemStatus:(state)=>(state.allitemstatus)
};
const actions = {};
const mutations = {
    setAllItemStatus:(state, itemstatuses)=>(state.allitemstatus = itemstatuses)
};
export default{
    state,
    getters,
    actions,
    mutations
};