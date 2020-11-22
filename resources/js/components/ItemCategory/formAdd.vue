<template></template>

<script>
import ItemCategory from '../../apis/ItemCategory';
import {validationMixin} from 'vuelidate'
import {required, maxLength} from 'vuelidate/lib/validators';
import {mapActions} from 'vuex';
import VueTypes from 'vue-types';

export default {
  mixins:[validationMixin],
  validations:{
    form:{name:{required,maxLength:maxLength(100)},
    description:{required, maxLength:maxLength(500)}}
  },
  name:'add-category',
  data(){
    return {
      form:{
        name:'',
        description:''
        }, 
    loading:false, 
    errors:[],
    defaultItem: {
        name: '',
        description:''
      },
    }
  },
  props:{
    editedIndex:VueTypes.number.isRequired,
    dialog:VueTypes.bool.isRequired,
    item:VueTypes.object.isRequired
  },
  methods:{
  ...mapActions(['addItemCategory']),
  addCategory(){
      this.errors = [];
      this.$v.$touch();
     
      if(!this.$v.form.$invalid){
        this.addItemCategory(this.form).catch((error)=>{
          if(error.response.status === 422){
              this.errors = error.response.data.errors;
          }
        });
      }
    },
    close(){
      this.dialog = false;
      this.$nextTick(()=>{
        this.form = Object.assign({}, this.defaultItem);
        this.editItem = -1;
      });
    },
    editItem(){
      this.$nextTick(()=>{
        this.form = Object.assign({}, this.item);
      });
    }
},
watch:{
  dialog(val){
    val || this.close();
  }
},
computed:{
  formTitle(){
    return this.editedIndex===-1?'New Item':'Edit Item';
  },
  xdialog:{
    get(){
      return this.dialog;
    },
    set(val){
      this.$emit('hide-dialog', val);
    }
  },
  nameErrors(){
    const errors = [];
    if(!this.$v.form.name.$dirty) return errors
  
    !this.$v.form.name.maxLength && errors.push('Name must be at most 100 characters long.')
    !this.$v.form.name.required && errors.push('Name is required.')

    return errors
  },
  descriptionErrors(){
    const errors = [];

    if(!this.$v.form.description.$dirty) return errors

    !this.$v.form.description.maxLength && errors.push('Description must be at most 500 characters long.')
    !this.$v.form.description.required && errors.push('Description is required.')

    return errors
  }
}
}
</script>

<style>

</style>