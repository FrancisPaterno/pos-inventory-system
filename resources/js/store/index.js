import Vue from "vue";
import Vuex from "vuex";
import category from "./module/category";
import brand from './module/brand';
import unit from './module/unit';
import item from './module/item';
import status from './module/status';
import customer from './module/customer';
import warehouse from './module/warehouse';
import supplier from './module/supplier';
import stockheader from './module/stockheader';
import stockitem from './module/stockitem';

Vue.use(Vuex);

export default new Vuex.Store({ modules: { category, brand, unit, item, status, customer, warehouse, supplier, stockheader, stockitem}});
