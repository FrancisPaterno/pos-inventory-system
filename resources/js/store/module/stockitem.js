const state = {
    stockitems:[]
};

const getters = {
    getStockItems:(state)=>(state.stockitems)
};

const actions = {};

const mutations = {
    setStockItems:(state, stockitems)=>(state.stockitems = stockitems),
    setStockItem:(state, stockitem)=>(state.stockitems.push(stockitem)),
    updateStockItem:(state, updstockitem)=>{
        const index = state.stockitems.findIndex()

        if(index !== -1){
            state.stockitems.splice(index, 1, updstockitem)
        }
    },
    removeStockItem:(state, stockitem)=>(state.stockitems = state.stockitems.filter(si=>si.id !== stockitem.id)),
    setStockItemCurrentPage:(state, data)=>(state.stockitems.current_page = data)
};

export default{
    state,
    getters,
    actions,
    mutations
};