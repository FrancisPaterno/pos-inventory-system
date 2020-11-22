<template>
  <v-card>
    <v-app-bar flat app color="#43a047">
      <v-app-bar-nav-icon
        class="white--text"
        @click="drawer = !drawer"
      ></v-app-bar-nav-icon
      ><v-toolbar-title class="white--text">Title</v-toolbar-title
      ><v-spacer></v-spacer>
      <v-btn text color="white" @click="logout" v-if="isLoggedIn"
        ><span>Sign Out</span><v-icon right>exit_to_app</v-icon></v-btn
      >
      <v-menu left bottom v-if="!isLoggedIn">
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon v-bind="attrs" v-on="on">
            <v-icon color="white">more_vert</v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-item
            v-for="(link, index) in navlinks"
            :key="index"
            route
            :to="link.route"
          >
            <v-list-item-icon
              ><v-icon>{{ link.icon }}</v-icon></v-list-item-icon
            >
            <v-list-item-title>{{ link.text }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>
    <v-navigation-drawer app color="#43a047" v-model="drawer">
      <v-list nav dense>
        <v-list-item-group active-class="deep-purple--text text--accent-4">
          <v-list-item route to="/">
            <v-list-item-icon>
              <v-icon>dashboard</v-icon>
            </v-list-item-icon>
            <v-list-item-title class="white--text">Dashboard</v-list-item-title>
          </v-list-item>
          <v-divider></v-divider>
          <v-list-group :value = true prepend-icon="list"
            ><template v-slot:activator
              ><v-list-item-title>Master files</v-list-item-title></template
            >
            <v-list-item
              v-for="masterfile in masterfiles"
              :key="masterfile.text"
              route
              :to="masterfile.route"
              ><v-list-item-icon
                ><v-icon v-text="masterfile.icon" left></v-icon
                ><v-list-item-title>{{
                  masterfile.text
                }}</v-list-item-title></v-list-item-icon
              >
              </v-list-item
            >
            <v-divider></v-divider>
            <v-list-item v-for="masterfile in masterfiles_1"
              :key="masterfile.text"
              route
              :to="masterfile.route"
            >
            <v-list-item-icon>
              <v-icon v-text="masterfile.icon" left></v-icon>
              <v-list-item-title>{{masterfile.text}}</v-list-item-title>
            </v-list-item-icon>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item v-for="masterfile in masterfiles_2"
              :key="masterfile.text"
              route
              :to="masterfile.route"
            >
            <v-list-item-icon>
              <v-icon v-text="masterfile.icon" left></v-icon>
              <v-list-item-title>{{masterfile.text}}</v-list-item-title>
            </v-list-item-icon>
            </v-list-item>
            <v-divider></v-divider>
            </v-list-group>
            <v-list-group
            :value="true" prepend-icon="home_work"
            >
            <template
            v-slot:activator
            >
            <v-list-item-title>Inventory</v-list-item-title>
            </template>
            <v-list-item v-for="item in stockMenu"
            :key="item.text"
            route
            :to="item.route"
            >
            <v-list-item-icon>
              <v-icon v-text="item.icon" left></v-icon>
              <v-list-item-title>{{item.text}}</v-list-item-title>
            </v-list-item-icon>
            </v-list-item>
            </v-list-group>
        </v-list-item-group></v-list>
        </v-navigation-drawer
    >
  </v-card>
</template>

<script>
import User from "../apis/User";
export default {
  data() {
    return {
      drawer: false,
      links: [
        { icon: "dashboard", text: "Dashboard", route: "/" },
        {
          icon: "list",
          text: "Masterfiles",
          route: "/masterfile",
        },
      ],
      masterfiles: [
        { icon: "category", text: "Item Categories", route: "/itemcategories" },
        {
          icon: "branding_watermark",
          text: "Item Brands",
          route: "/itembrands",
        },
        { icon: "straighten", text: "Item Units", route: "/itemunits" },
        { icon:"local_offer", text:"Items", route:"/items"},
      ],
      masterfiles_1:[
        {icon:'support_agent', text: 'Customers', route:'/customers'}
      ],
      masterfiles_2:[
        {icon:"store", text:"Warehouses", route:"/warehouses"},
        {icon:"contact_page", text:"Suppliers", route:"/suppliers"}
      ],
      stockMenu:[
        {icon:'add_business', text:'Stocks', route:'/stocks'}
      ],
      navlinks: [
        { icon: "login", text: "Login", route: "/login" },
        { icon: "fingerprint", text: "Register", route: "/register" },
      ],
      isLoggedIn: false,
    };
  },
  methods: {
    logout() {
      User.logout().then(() => {
        localStorage.removeItem("token");
        this.$router.push({ name: "Login" });
        this.isLoggedIn = false;
      });
    },
  },
  mounted() {
    this.isLoggedIn = !!localStorage.getItem("token");
    this.$root.$on("login", () => {
      this.isLoggedIn = true;
    });
  },
};
</script>

<style></style>
