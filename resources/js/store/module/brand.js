const state = {itembrands:[],allitembrands:[]};
const getters = {
    getBrands:state=>state.itembrands.data,
    getAllBrands:state=>state.allitembrands
};
const actions = {
};
const mutations = {
    setItemBrands :(state, brands)=>(state.itembrands = brands),
    setAllItemBrands:(state, brands)=>(state.allitembrands = brands),
    setItemBrand:(state, brand)=>(state.itembrands.data.push(brand)),
    updateItemBrand:(state, updbrand)=>{
        const index = state.itembrands.data.findIndex(brand => brand.id === updbrand.id);
        if(index !== -1){
            state.itembrands.data.splice(index, 1, updbrand);
        }
    },
    removeItemBrand:(state, data)=>( state.itembrands.data = state.itembrands.data.filter(brand=>brand.id !== data.id)),
    setBrandCurrentPage: (state, data) => (state.itembrands.current_page = data),
};

export default {
    state,
    getters,
    actions,
    mutations
};