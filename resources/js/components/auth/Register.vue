<template>
  <v-card class="my-5 mx-auto" max-width="500"
    ><v-card-title>Register User</v-card-title
    ><v-card-text
      ><v-form ref="form"
        ><v-text-field
          label="Name"
          prepend-icon="person"
          v-model="form.name"
          :error-messages="errors.name"
        ></v-text-field
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
        ></v-text-field
        ><v-text-field
          label="Confirm Password"
          prepend-icon="lock"
          :append-icon="showConfirmPassword ? 'visibility_off' : 'visibility'"
          :type="showConfirmPassword ? 'text' : 'password'"
          @click:append="showConfirmPassword = !showConfirmPassword"
          v-model="form.password_confirmation"
          :error-messages="errors.password_confirmation"
        ></v-text-field
        ><v-btn
          type="submit"
          width="100%"
          class="success"
          @click.prevent="register"
          :loading="loading"
          >Register</v-btn
        ></v-form
      ></v-card-text
    ></v-card
  >
</template>

<script>
import User from "../../apis/User";
export default {
  data() {
    return {
      showPassword: false,
      showConfirmPassword: false,
      form: { name: "", email: "", password: "", password_confirmation: "" },
      errors: [],
      loading: false,
    };
  },
  methods: {
    register() {
      this.loading = true;
      User.register(this.form)
        .then(() => {
          this.loading = false;
          this.$router.push({ name: "Login" });
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
