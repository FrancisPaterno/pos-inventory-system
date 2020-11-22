const state = {
    stockheaders:[],
    allstockheaders:[]
};
const getters = {
    getStockHeaders:(state)=>(state.stockheaders.data),
    getAllStockHeaders:(state)=>(state.allstockheaders)
};
const actions = {};
const mutations = {
    setStockHeaders:(state, stockheaders)=>(state.stockheaders = stockheaders),
    setAllStockHeaders:(state, stockheaders)=>(state.allstockheaders = stockheaders),
    setStockHeader:(state, stockheader)=>(state.stockheaders.data.push(stockheader)),
    updateStockHeader:(state, updStockHeader)=>{
        const index = state.stockheaders.data.findIndex(sh=>sh.id === updStockHeader.id);
        if(index !== -1){
            state.stockheaders.data.splice(index, 1, updStockHeader);
        }
    },
    removeStockHeader:(state, stockHeader)=>(state.stockheaders.data = state.stockheaders.data.filter(sh=>sh.id !== stockHeader.id)),
    setStockHeaderCurrentPage:(state, data)=>(state.stockheaders.current_page = data)
};

export default{
    state,
    getters,
    actions,
    mutations
};