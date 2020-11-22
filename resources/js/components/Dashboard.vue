<template>
  <v-card
    ><v-card-title><h1 class="mx-auto">Dashboard</h1></v-card-title
    ><v-card-text v-if="myUser"
      ><h3>Hello {{ myUser.name }}</h3>
      <span>{{ myUser.email }}</span></v-card-text
    ></v-card
  >
</template>

<script>
import User from "../apis/User";
export default {
  data() {
    return { user: null };
  },
  computed: {
    myUser: function () {
      return this.user;
    },
  },
  mounted() {
    User.auth().then((response) => {
      this.user = response.data;
    });
  },
};
</script>

<style></style>
