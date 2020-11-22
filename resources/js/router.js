import Vue from "vue";
import VueRouter from "vue-router";
import Dashboard from "./components/Dashboard";
import Login from "./components/auth/Login";
import Register from "./components/auth/Register";
import ItemCategory from "./components/ItemCategory/ItemCategory";
import ItemBrand from './components/ItemBrand/ItemBrand';
import ItemUnit from './components/ItemUnit/ItemUnit';
import Item from './components/Item/Item';
import Customer from './components/Customer/Customer';
import Warehouse from './components/Warehouse/Warehouse';
import Supplier from './components/Supplier/Supplier';
import Stocks from './components/StockHeader/Main';
import StockList from './components/StockHeader/List';
import StockDetails from './components/StockHeader/Details';

const routes = [
    {
        path: "/",
        name: "Dashboard",
        component: Dashboard,
        meta: { authOnly: true }
    },
    {
        path: "/login",
        name: "Login",
        component: Login,
        meta: { guestOnly: true }
    },
    {
        path: "/register",
        name: "Register",
        component: Register,
        meta: { guestOnly: true }
    },
    {
        path: "/itemcategories",
        name: "ItemCategory",
        component: ItemCategory,
        meta: { authOnly: true }
    },
    {
        path:'/itembrands',
        name:'ItemBrand',
        component:ItemBrand,
        meta:{authOnly:true}
    },
    {
        path:'/itemunits',
        name:'ItemUnit',
        component:ItemUnit,
        meta:{authOnly:true}
    },
    {
        path:'/items',
        name:'Item',
        component:Item,
        meta:{authOnly:true}
    },
    {
        path:'/customers',
        name:'Customers',
        component:Customer,
        meta:{authOnly:true}
    },
    {
        path:'/warehouses',
        name:'Warehouses',
        component:Warehouse,
        meta:{authOnly:true}
    },
    {
        path:'/suppliers',
        name:'Suppliers',
        component:Supplier,
        meta:{authOnly:true}
    },
    {
        path:'/stocks',
        name:'Stocks',
        component:Stocks,
        meta:{authOnly:true},
        redirect:{name:'StockLists'},
        children:[
            {path:'stocklists', name:'StockLists', component:StockList, meta:{authOnly:true}},
            {path:'stockdetails/:stockHeader', name:'StockDetails', component:StockDetails, props:true, meta:{authOnly:true}},
        ]
    }
];

Vue.use(VueRouter);

const router = new VueRouter({ routes });

function isLoggedIn() {
    return localStorage.getItem("token");
}

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.authOnly)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (!isLoggedIn()) {
            next({
                path: "/login",
                query: { redirect: to.fullPath }
            });
        } else {
            next();
        }
    } else if (to.matched.some(record => record.meta.guestOnly)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (isLoggedIn()) {
            next({
                path: "/",
                query: { redirect: to.fullPath }
            });
        } else {
            next();
        }
    } else {
        next(); // make sure to always call next()!
    }
});

export default router;
