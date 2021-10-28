import { data } from "jquery";

const state = {
    items:[],
    allitems:[]
};
const getters = {
    items:(state)=>(state.items.data),
    getAllItems:(state)=>(state.allitems)
};
const actions = {};
const mutations = {
    setItems:(state, items)=>(state.items = items),
    setAllItems:(state, items)=>(state.allitems = items),
    setItem:(state, item)=>(state.items.data.push(item)),
    setItemCurrentPage:(state, data)=>(state.items.current_page = data),
    updateItem:(state, item)=>{
        const index = state.items.data.findIndex(xitem=>xitem.id === item.id);
        if(index !== -1){
            state.items.data.splice(index, 1, item);
        }
    },
    removeItem:(state, item)=>(state.items.data = state.items.data.filter(itm=>(itm.id !== item.id)))
};

export default{
    state,
    getters,
    actions,
    mutations
};