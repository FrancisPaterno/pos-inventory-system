<template>
  <tr>
    <td><v-select style="maxWidth:100px" v-model="select" v-bind:items="items" item-text="text" item-value="value" label="Rows per page:"  height="50" @change.once="updateRowPage" ></v-select></td>
    <td><span>Showing {{fromPage}} to {{toPage}} of {{total}}</span></td>
    <td></td>
    <td ></v-row></td>
  </tr>
</template>

<script>
import VueTypes from 'vue-types';
import { mapGetters } from 'vuex';
export default {
props:{store:VueTypes.string.isRequired,
collection:VueTypes.string.isRequired},
data(){
    return {
      select:
      {text:'10', value:10}, 
      items:[
        {text:'5', value:5},
        {text:'10', value:10},
        {text:'15', value:15}, 
        {text:'20', value:20}, 
        {text:'50', value:50}]
        }},
    methods:{
      updateRowPage(){
        this.$emit('updaterow', this.select);
      }
    },
    computed:{
      ...mapGetters(['getPerPage']),
      fromPage:{
        get(){
          return this.$store.state[this.store][this.collection].from;
        }
      },
      toPage:{
        get(){
          return this.$store.state[this.store][this.collection].to;
        }
      },
      total:{
        get(){
          return this.$store.state[this.store][this.collection].total;
        }
      }
    },
    watch:{
        select(newVal, oldVal){
            this.$store.commit('setPerPage', this.select);
            this.$emit('updaterow');
        }
    }
}

</script>

<style>

</style>