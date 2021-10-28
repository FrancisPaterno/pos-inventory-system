<template>
<v-container>
  <v-data-table
  :headers="headers"
  :items="getStockItems"
  >
    <template v-slot:top>
      <v-card flat>
        <v-card-title>
          <v-icon @click="$router.go(-1)">arrow_back</v-icon>
          <h2 class="mx-auto">Details</h2>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col>
              <h2>Delivery Receipt:{{shDetails.delivery_receipt_no}}</h2>
            </v-col>
            <v-col class="d flex flex-row-reverse">
              <div>
                <h2>Date:{{formattedDate}}</h2>
              </div>
            </v-col>
          </v-row>
          <v-row class="my-5">
            <v-col>
              <v-textarea label="Description" readonly v-model="shDetails.description" auto-grow height="50"></v-textarea>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-text-field v-if="shDetails.warehouse" label="Warehouse" v-model="shDetails.warehouse.name" readonly></v-text-field>
            </v-col>
            <v-col>
              <v-text-field v-if="shDetails.supplier" label="Supplier" v-model="shDetails.supplier.name" readonly></v-text-field>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row>
            <v-col class="d flex flex-row-reverse">
              <v-dialog max-width="500px" v-model="dialog">
                <template v-slot:activator="{on, attrs}">
                  <v-btn v-on="on" v-bind="attrs" color="success">New Item</v-btn>
                </template>
                <v-card>
                  <v-card-title>{{formTitle}}</v-card-title>
                  <v-card-text>
                    <v-form>
                      <v-autocomplete :loading="loadingItems"
                      label="Items"
                      chips
                      v-model="form.item_id"
                      :items="getAllItems"
                      item-text="name"
                      item-value="id"
                      @input="$v.form.item_id.$touch()" 
                      @blur="$v.form.item_id.$touch()" 
                      :error-messages="itemErrors"
                      >
                      </v-autocomplete>
                      <span v-if="errors.item_id" class="error--text">{{errors.item_id[0]}}</span>
                      <v-textarea
                      label="Description"
                      v-model="form.description"
                      @input="$v.form.description.$touch()" 
                      @blur="$v.form.description.$touch()" 
                      :error-messages="descriptionErrors"
                      >
                      </v-textarea>
                      <span v-if="errors.description" class="error--text">{{errors.description[0]}}</span>
                      <v-text-field
                      label="Quantity"
                      v-model="form.qty"
                      @input="$v.form.qty.$touch()" 
                      @blur="$v.form.qty.$touch()" 
                      :error-messages="quantityErrors"
                      >
                      </v-text-field>
                      <span v-if="errors.qty" class="error--text">{{errors.qty[0]}}</span>
                      <v-text-field
                      label="Wholesale Price"
                      v-model="form.wholesale_price"
                      @input="$v.form.wholesale_price.$touch()" 
                      @blur="$v.form.wholesale_price.$touch()" 
                      :error-messages="wholesaleErrors"
                      >
                      </v-text-field>
                      <span v-if="errors.wholesale_price" class="error--text">{{errors.wholesale_price[0]}}</span>
                      <v-text-field
                      label="Retail Price"
                      v-model="form.retail_price"
                      @input="$v.form.retail_price.$touch()" 
                      @blur="$v.form.retail_price.$touch()" 
                      :error-messages="retailErrors"
                      >
                      </v-text-field>
                      <span v-if="errors.retail_price" class="error--text">{{errors.retail_price[0]}}</span>
                    </v-form>
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn small color="success darken-1" text @click="close">Cancel</v-btn>
                    <v-btn small color="success darken-1" text :loading="saving" @click="saveStockItem">OK</v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </v-col>
          </v-row>
          <v-divider></v-divider>
        </v-card-text>
      </v-card>
    </template>
    <template v-slot:item.actions={item}>
      <v-icon small class="mr-2" @click="editItem(item)">edit</v-icon>
      <v-icon small>delete</v-icon>
    </template>
  </v-data-table>
</v-container>
</template>

<script>
import StockHeader from '../../apis/StockHeader';
import StockItem from '../../apis/StockItem';
import Item from '../../apis/Item';
import moment from 'moment';
import {mapGetters} from 'vuex';
import {validationMixin} from 'vuelidate';
import {required, maxLength, integer, decimal} from 'vuelidate/lib/validators';

export default {
  mixins:[validationMixin],
  validations:{
    form:{
      stock_header_id:{required},
      item_id:{required},
      description:{maxLength:maxLength(500)},
      qty:{required, integer},
      wholesale_price:{required, decimal},
      retail_price:{required, decimal}
    }
  },
  data(){
    return {
      shDetails:{},
      errors:[],
      itemIndex:-1,
      headers:[
        {text:'ID', align:'start', value:'id'},
        {text:'Item', value:'item.name'},
        {text:'Quantity', value:'qty'},
        {text:'Wholesale Price', value:'wholesale_price'},
        {text:'Retail Price', value:'retail_price'},
        {text:'Actions', value:'actions', sortable:false}
      ],
      loadingItems:false,
      saving:false,
      form:{
        id:null,
        stock_header_id:null,
        item_id:null,
        description:'',
        qty:null,
        wholesale_price:null,
        retail_price:null,
      },
      defaultitem:{
        id:null,
        stock_header_id:null,
        item_id:null,
        description:'',
        qty:null,
        wholesale_price:null,
        retail_price:null,
      },
      dialog:false
    }
  },
  props:{
    stockHeader:{
      required:true
    }
  },
  computed:{
    ...mapGetters(['getStockItems','getAllItems']),
    formattedDate(){
      return moment(this.shDetails.date_received).format('MMM DD, YYYY');
    },
    getSHDetails(){
      return this.shDetails;
    },
    formTitle(){
      return this.itemIndex === -1?'New Item':'Edit Item';
    },
    itemErrors(){
      const errors = [];
      if(!this.$v.form.item_id.$dirty) return errors;

      !this.$v.form.item_id.required && errors.push('Item is required.');

      return errors;
    },
    descriptionErrors(){
       const errors = [];
      if(!this.$v.form.description.$dirty) return errors;

      !this.$v.form.description.maxLength && errors.push('Description must be at most 500 characters long.');

      return errors;
    },
    quantityErrors(){
      const errors = [];
      if(!this.$v.form.qty.$dirty) return errors;

      !this.$v.form.qty.required && errors.push('Quantity is required.');
      !this.$v.form.qty.integer && errors.push('Quantity must be an integer.');

      return errors;
    },
    wholesaleErrors(){
      const errors = [];
      if(!this.$v.form.wholesale_price.$dirty) return errors;

      !this.$v.form.wholesale_price.required && errors.push('Wholesale Price is required.');
      !this.$v.form.wholesale_price.decimal && errors.push('Wholesale Price must be a decimal.');

      return errors;
    },
    retailErrors(){
      const errors = [];
      if(!this.$v.form.retail_price.$dirty) return errors;

      !this.$v.form.retail_price.required && errors.push('Retail Price is required.');
      !this.$v.form.retail_price.decimal && errors.push('Retail Price must be a decimal.');

      return errors;
    }
  },
  methods:{
    getStockHeader(){
      StockHeader.getStockHeader(this.stockHeader)
      .then(response=>{
        this.shDetails = response.data;
        this.form.stock_header_id = this.shDetails.id;
        this.getStockItemsList();
      })
      .catch((error)=>{
        if(error.response.status === 422){
          this.errors.error.response.data.errors;
        }

        if(error.response.status === 500){
            this.errors = error.response.data;
        }
      })
    },
    getStockItemsList(){
      StockItem.getStockItems({params:{shid:this.shDetails.id}})
      .then((response)=>{
        console.log('shDetails', this.shDetails);
        this.$store.commit('setStockItems', response.data)
      })
      .catch((error)=>{
        if(error.response.status === 422){
          this.errors.error.response.data.errors;
        }

        if(error.response.status === 500){
            this.errors = error.response.data;
        }
      })
    },
    getAllItemLists(){
      this.loadingItems = true;
      Item.getAllItems()
      .then((response)=>{
        this.$store.commit('setAllItems', response.data);
        this.loadingItems = false;
      })
      .catch((error)=>{
        if(error.response.status === 422){
          this.errors.error.response.data.errors;
        }

        if(error.response.status === 500){
            this.errors = error.response.data;
        }
      })
    },
    close(){
      this.dialog = false;
      this.$nextTick(()=>{
        this.$v.$reset();
        this.form = Object.assign({}, this.defaultitem);
        this.errors = [];
        this.itemIndex = -1;
        this.form.stock_header_id = this.shDetails.id;
      })
    },
    saveStockItem(){
      this.$v.$touch();
      console.log('add form', this.form);
      if(!this.$v.form.$invalid){
        this.saving = true;
        if(this.itemIndex === -1){
          StockItem.addStockItem(this.form)
          .then((response)=>{
            this.$store.commit('setStockItem', response.data);
            this.close();
          })
          .catch((error)=>{
            if(error.response.status === 422){
                this.errors = error.response.data.errors;
                console.log('errors', this.errors);
            }
            if(error.response.status === 500){
                this.errors = error.response.data;
                console.log('errors', this.errors);
            }
          })
          .finally(()=>{
            this.saving = false;
          })
        }
        else{
          console.log('edit form', this.form);
        }
      }
    },
    editItem(item){
      this.itemIndex = this.getStockItems.indexOf(item);
      this.form = Object.assign({},item);
      this.dialog = true;
    }
  },
  created(){
    this.getStockHeader();
  },
  watch:{
    dialog(val){
      if(val){
        this.getAllItemLists();
      }
      else{
        this.close();
      }
    }
  }
}
</script>

<style>

</style>