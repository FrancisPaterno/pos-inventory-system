<template>
  <v-card class="my-5 mx-auto" max-width="500"
    ><v-card-title>Login User</v-card-title>
    <v-card-text
      ><v-form ref="form"
        ><v-text-field
          label="Email"
          prepend-icon="email"
          v-model="form.email"
          :error-messages="errors.email"
        ></v-text-field
        ><v-text-field
          label="Password"
          prepend-icon="lock"
          :append-icon="showPassword ? 'visibility_off' : 'visibility'"
          :type="showPassword ? 'text' : 'password'"
          @click:append="showPassword = !showPassword"
          v-model="form.password"
          :error-messages="errors.password"
        ></v-text-field>
        <v-btn
          type="submit"
          class="success"
          width="100%"
          @click.prevent="login"
          :loading="loading"
          >Login</v-btn
        >
      </v-form></v-card-text
    >
  </v-card>
</template>

<script>
import User from "../../apis/User";
export default {
  data() {
    return {
      showPassword: false,
      form: { email: "", password: "", device_name: "browser" },
      errors: [],
      loading: false,
    };
  },
  methods: {
    login() {
      this.loading = true;
      User.login(this.form)
        .then((response) => {
          localStorage.setItem("token", response.data);
          this.$root.$emit("login", true);
          this.loading = false;
          this.$router.push({ name: "Dashboard" });
        })
        .catch((error) => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
          }
          this.loading = false;
        });
    },
  },
};
</script>

<style></style>
