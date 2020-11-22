<template>
  <div>
      <v-dialog v-model="xdialog" max-width="500px">
          <template v-slot:activator="{on, attrs}">
              <v-btn color="success" v-bind="attrs" v-on="on">New Item</v-btn>
          </template>
          <component v-bind:is="form" :editedIndex='editedIndex'></component>
        </v-dialog>
  </div>
</template>

<script>
import Vuetypes from 'vue-types'

export default {
props:{form:Vuetypes.string.isRequired,
    dialog:Vuetypes.bool.isRequired,
    editedIndex: Vuetypes.number.isRequired
},
computed:{
  xdialog:{
    get(){
      return this.dialog;
    },
    set(val){
      this.$emit('hide-dialog', val);
    }
  }
}
}
</script>

<style>

</style>