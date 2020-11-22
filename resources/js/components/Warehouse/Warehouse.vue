<template>
  <v-container>
    <v-data-table
    class="ma-5"
    :headers="headers"
    hide-default-footer
    :items="getWarehouses"
    :items-per-page="getPerPage"
    :loading="loading"
    >
    <template v-slot:top>
      <v-card flat>
        <v-container>
          <v-row>
            <v-col>
              <v-card-title>Warehouses</v-card-title>
            </v-col>
            <v-col>
              <v-text-field
              append-icon="search"
              label="Search"
              single-line
              hide-details=""
              v-model="search"
              @keyup.enter="displayData"
              >
              </v-text-field>
              <v-chip v-if="errors.message" class="error--text" large close @click:close="errors=[]" style="word-wrap:break-word">
                {{errors.message}}
              </v-chip>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row>
            <v-col class="d flex flex-row-reverse">
              <v-dialog v-model="dialog" max-width="500">
                <template v-slot:activator="{attrs, on}">
                  <v-btn color="success" v-bind="attrs" v-on="on">New Item</v-btn>
                </template>
                <v-card>
                  <v-card-title>{{formTitle}}</v-card-title>
                  <v-card-text>
                    <v-form>
                      <v-text-field
                      label="Name"
                      v-model="form.name"
                      @input="$v.form.name.$touch()" 
                      @blur="$v.form.name.$touch()" 
                      :error-messages="nameErrors">
                      >
                      </v-text-field>
                      <span v-if="errors.name" class="error--text">{{errors.name[0]}}</span>
                      <v-text-field
                      label="Address"
                      v-model="form.address"
                      @input="$v.form.address.$touch()" 
                      @blur="$v.form.address.$touch()" 
                      :error-messages="addressErrors"
                      >
                      </v-text-field>
                      <span v-if="errors.address" class="error--text">{{errors.address[0]}}</span>
                      <v-text-field
                      label="Contact"
                      v-model="form.contact"
                      @input="$v.form.contact.$touch()" 
                      @blur="$v.form.contact.$touch()" 
                      :error-messages="contactErrors"
                      >
                      </v-text-field>
                      <span v-if="errors.contact" class="error--text">{{errors.contact[0]}}</span>
                    </v-form>
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn small text color="success darken-1" @click="close">Cancel</v-btn>
                    <v-btn small text color="success darken-1" :loading="saving" @click="saveWarehouse">OK</v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
              <v-dialog v-model="dialogDelete" max-width="500px">
                  <v-card>
                  <v-card-title class="headline">Are you sure you want to delete this item?</v-card-title>
                  <v-card-text>
                  <span v-if="errors.message" class="error--text">{{errors.message}}</span>
                  </v-card-text>
                  <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn color="success darken-1" text @click="closeDelete">Cancel</v-btn>
                      <v-btn color="success darken-1" text @click="confirmDelete" :loading="deleting">OK</v-btn>
                  </v-card-actions>
                  </v-card>
              </v-dialog>
            </v-col>
          </v-row>
          <v-divider></v-divider>
        </v-container>
      </v-card>
    </template>
    <template v-slot:item.actions="{item}">
      <v-icon small class="mr-2" @click="editItem(item)">edit</v-icon>
      <v-icon small @click="deleteItem(item)">delete</v-icon>
    </template>
    <template v-slot:body.append>
      <perPage v-on:updaterow="displayData"
      collection="warehouses"
      store="warehouse"
      />
    </template>
    </v-data-table>
    <Paginate
    collection="warehouses"
    store="warehouse"
    :search="search"
    :getItem="getWarehousesList"
    moduleCurrentPage="setWarehouseCurrentPage"
    />
  </v-container>
</template>

<script>
import {mapGetters} from 'vuex';
import Warehouse from '../../apis/Warehouse';
import perPage from '../rowperpage';
import Paginate from '../paginate';
import {validationMixin} from 'vuelidate';
import {required, maxLength} from 'vuelidate/lib/validators';

export default {
  mixins:[validationMixin],
  validations:{
    form:{
          name: {required, maxLength:maxLength(100)},
          address:{required, maxLength:maxLength(300)},
          contact:{required, maxLength:maxLength(30)},
        }
  },
  data(){
    return {
      headers:[
        {text:'ID', align:'start', value:'id'},
        {text:'Name', value:'name'},
        {text:'Address', value:'address'},
        {text:'Contact', value:'contact'},
        {text:'Actions', value:'actions', sortable:false}
      ],
      loading:false,
      saving:false,
      deleting:false,
      search:'',
      errors:[],
      dialog:false,
      dialogDelete:false,
      itemIndex:-1,
      form:{
        id:0,
        name:'',
        address:'',
        contact:''
      },
      defaultItem:{
         id:0,
        name:'',
        address:'',
        contact:''
      }
    }
  },
  components:{
    perPage,
    Paginate
  },
  computed:{
    ...mapGetters(['getWarehouses', 'getPerPage']),
    formTitle(){
      return this.itemIndex === -1?'New Item':'Edit Item';
    },
    nameErrors(){
      const errors = [];
      if(!this.$v.form.name.$dirty) return errors;

      !this.$v.form.name.required && errors.push('Name field is required.');
      !this.$v.form.name.maxLength && errors.push('Name must be at most 100 characters long.');

      return errors;
    },
    addressErrors(){
      const errors = [];
      if(!this.$v.form.address.$dirty) return errors;

      !this.$v.form.address.required && errors.push('Address field is required.');
      !this.$v.form.address.maxLength && errors.push('Address must be at most 300 characters long.');

      return errors;
    },
    contactErrors(){
      const errors = [];
      if(!this.$v.form.contact.$dirty) return errors;

      !this.$v.form.contact.required && errors.push('Contact field is required.');
      !this.$v.form.contact.maxLength && errors.push('Contact must be at most 30 characters long.');

      return errors;
    },
  },
  methods:{
    displayData(){
      this.getWarehousesList({page:1, rows:this.getPerPage,search:this.search});
    },
    getWarehousesList(params){
      this.loading = true;
      Warehouse.getWarehouses({params:params})
      .then((response)=>{
        this.$store.commit('setWarehouses', response.data);
        this.loading = false;
      })
      .catch((errors)=>{
        if(error.response.status === 422){
            this.errors.error.response.data.errors;
        }

        if(error.response.status === 500){
            this.errors = error.response.data;
        }
        this.loading = false;
      })
    },
    close(){
      this.dialog = false;
      this.$nextTick(()=>{
        this.form= Object.assign({}, this.defaultItem);
        this.itemIndex = -1;
        this.errors = [];
        this.$v.$reset();
        this.saving = false;
      });
    },
    closeDelete(){
      this.dialogDelete = false;
      this.$nextTick(()=>{
          this.itemIndex = -1;
          this.form = Object.assign({}, this.defaultItem);
          this.errors = [];
      })
    },
    saveWarehouse(){
      this.$v.$touch();
      if(!this.$v.form.$invalid){
        this.saving = true;
        if(this.itemIndex === -1){
          Warehouse.addWarehouse(this.form)
          .then((response)=>{
            this.$store.commit('setWarehouse', response.data);
            this.saving = false;
            this.close();
          })
          .catch((errors)=>{
            this.saving = false;
            if(errors.response.status === 422){
                this.errors = errors.response.data.errors;
            }
            if(errors.response.status === 500){
                this.errors = errors.response.data;
            }
          });
        }
        else{
          Warehouse.updateWarehouse(this.form)
          .then(response=>{
            this.$store.commit('updateWarehouse', response.data)
            this.saving = false;
            this.close();
          })
          .catch(errors=>{
              this.saving = false;
              if(errors.response.status === 422){
                  this.errors = errors.response.data.errors;
              }
              if(errors.response.status === 500){
                  this.errors = errors.response.data;
              }
          })
        }
      }
    },
    confirmDelete(){
      this.deleting = true;
      Warehouse.deleteWarehouse(this.form.id)
      .then(response=>{
        this.$store.commit('removeWarehouse', response.data);
        this.deleting = false;
        this.closeDelete();
      })
      .catch((errors)=>{
          this.deleting = false;
              if(errors.response.status === 422){
                  this.errors = errors.response.data.errors;
              }
              if(errors.response.status === 500){
                  this.errors = errors.response.data;
              }
      })
    },
    editItem(item){
      this.itemIndex = this.getWarehouses.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
    },
    deleteItem(item){
      this.itemIndex = this.getWarehouses.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialogDelete = true;
    }
  },
  created(){
    this.getWarehousesList({page:1, rows:this.getPerPage,search:this.search});
  },
  watch:{
    dialog(val){
      val || this.close();
    },
    dialogDelete(val){
      val || this.closeDelete();
    }
  }
}
</script>

<style>

</style>