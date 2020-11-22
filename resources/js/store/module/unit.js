const state = {itemunits:[],allitemunits:[]};

const getters = {
    getUnits:(state)=>(state.itemunits.data),
    getAllUnits:(state)=>(state.allitemunits)
};

const actions = {};

const mutations = {
    setItemUnits:(state, itemunits)=> (state.itemunits = itemunits),
    setAllItemUnits:(state, itemunits)=>(state.allitemunits = itemunits),
    setItemUnit:(state, itemunit)=>(state.itemunits.data.push(itemunit)),
    setItemUnitCurrentPage:(state, data)=>(state.itemunits.current_page = data),
    updateItemUnit:(state, updItemUnit)=>{
        const index = state.itemunits.data.findIndex(unit=>unit.id === updItemUnit.id);
        if(index !== -1){
            state.itemunits.data.splice(index, 1, updItemUnit);
        }
    },
    removeItemUnit:(state, deletedItemUnit)=>(state.itemunits.data = state.itemunits.data.filter(unit=>unit.id !== deletedItemUnit.id))
};

export default{
    state,
    getters,
    actions,
    mutations
};