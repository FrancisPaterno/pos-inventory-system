const state = { itemcategories: [], allitemcategories:[], perpage:10};
const getters = {   getCategories: state => state.itemcategories.data, 
                    getPerPage:state=>state.perpage,
                    getAllCategories: state=>state.allitemcategories 
                };
const actions = {
};
const mutations = {
    setCategories: (state, categories) => (state.itemcategories = categories),
    setAllCategories:(state, categories)=>(state.allitemcategories = categories),
    setCategory:(state,category)=>( state.itemcategories.data.push(category)),
    updateCategory:(state, category)=>{
        const index = state.itemcategories.data.findIndex(cat=>cat.id === category.id);
        if(index !== -1){
            state.itemcategories.data.splice(index, 1, category);
        }
    },
    removeCategory:(state, data)=>(state.itemcategories.data = state.itemcategories.data.filter(cat=>cat.id !== data.id)),
    setCategoryCurrentPage: (state, data) => (state.itemcategories.current_page = data),
    setPerPage:(state, rows)=>(state.perpage = rows),
};

export default { state, getters, actions, mutations };
