<template>
  <v-container>
    <v-data-table
    class="ma-5"
    :headers="headers"
    hide-default-footer
    :items-per-page="getPerPage"
    :items="getSuppliers"
    >
    <template v-slot:top>
      <v-card flat>
        <v-container>
          <v-row>
            <v-col>
              <v-card-title>Suppliers</v-card-title>
            </v-col>
            <v-col>
              <v-text-field append-icon="search" label="Search" single-line hide-details v-model="search" @keyup.enter="displayData"></v-text-field>
              <v-chip v-if="errors.message" class="error--text mt-2" large close @click:close="errors = []" style="word-wrap: break-word;" >
                      {{errors.message}}
              </v-chip>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row>
            <v-col class="d flex flex-row-reverse">
              <v-dialog v-model="dialog" max-width="500px">
                <template v-slot:activator="{on, attrs}">
                  <v-btn v-bind="attrs" v-on="on" color="success">New Item</v-btn>
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
                    </v-text-field>
                    <span v-if="errors.name" class="error--text">{{errors.name[0]}}</span>
                    <v-text-field label="Business Registration Number"
                    v-model="form.BRN"
                    @input="$v.form.BRN.$touch()" 
                    @blur="$v.form.BRN.$touch()" 
                    :error-messages="brnErrors">
                    >
                    </v-text-field>
                    <v-text-field 
                    label="Address" 
                    v-model="form.address"
                    @input="$v.form.address.$touch()" 
                    @blur="$v.form.address.$touch()" 
                    :error-messages="addressErrors">
                    >
                    </v-text-field>
                    <span v-if="errors.address" class="error--text">{{errors.address[0]}}</span>
                    <v-text-field 
                    label="Contact" 
                    v-model="form.contact"
                    @input="$v.form.contact.$touch()" 
                    @blur="$v.form.contact.$touch()" 
                    :error-messages="contactErrors">
                    >
                    </v-text-field>
                    <span v-if="errors.contact" class="error--text">{{errors.contact[0]}}</span>
                    <v-text-field 
                    label="Email" 
                    v-model="form.email"
                    @input="$v.form.email.$touch()" 
                    @blur="$v.form.email.$touch()" 
                    :error-messages="emailErrors">
                    >
                    </v-text-field>
                    <span v-if="errors.email" class="error--text">{{errors.email[0]}}</span>
                  </v-form>
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn small color="success darken-1" text @click="close">Cancel</v-btn>
                    <v-btn small color="success darken-1" text :loading="saving" @click="saveSupplier">OK</v-btn>
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
    <template v-slot:item.actions={item}>
      <v-icon small class="mr-2" @click="editItem(item)">edit</v-icon>
      <v-icon small @click="deleteItem(item)">delete</v-icon>
    </template>
    <template
    v-slot:body.append
    >
    <perPage v-on:updaterow="displayData"
    collection="suppliers"
    store="supplier" />
    </template>
    </v-data-table>
    <Paginate
        collection="suppliers"
        store="supplier"
        :search="search"
        :getItem="getSupplierLists"
        moduleCurrentPage = 'setSupplierCurrentPage'
    />
  </v-container>
</template>

<script>
import Supplier from '../../apis/Supplier';
import {mapGetters} from 'vuex';
import perPage from '../rowperpage';
import Paginate from '../paginate';
import {validationMixin} from 'vuelidate';
import {required, maxLength, email} from 'vuelidate/lib/validators';
export default {
  mixins:[validationMixin],
  validations:{
    form:{
        name:{required, maxLength:maxLength(150)},
        BRN:{required, maxLength:maxLength(50)},
        address:{required, maxLength:maxLength(400)},
        contact:{required, maxLength:maxLength(30)},
        email:{maxLength:maxLength(150), email}
      }
  },
  data(){
    return{
      headers:[
        {text:'ID', align:'start', value:'id'},
        {text:'Name', value:'name'},
        {text:'Business Registration No.', value:'BRN'},
        {text:'Address', value:'address'},
        {text:'Contact', value:'contact'},
        {text:'Email', value:'email'},
        {text:'Actions', value:'actions', sortable:false}
      ],
      search:'',
      loading:false,
      saving:false,
      deleting:false,
      errors:[],
      dialog:false,
      dialogDelete:false,
      itemIndex:-1,
      form:{
        id:0,
        name:'',
        BRN:'',
        address:'',
        contact:'',
        email:''
      },
      defaultItem:{
        id:0,
        name:'',
        BRN:'',
        address:'',
        contact:'',
        email:''
      }
    }
  },
  components:{
    perPage,
    Paginate
  },
  computed:{
    ...mapGetters(['getSuppliers', 'getPerPage']),
    formTitle(){
      return this.itemIndex === -1?'New Item':'Edit Item';
    },
    nameErrors(){
      const errors = [];
      if(!this.$v.form.name.$dirty) return errors;

      !this.$v.form.name.required && errors.push('Name field is required.');
      !this.$v.form.name.maxLength && errors.push('Name must be at most 150 characters long.');

      return errors;
    },
    brnErrors(){
      const errors = [];
      if(!this.$v.form.BRN.$dirty) return errors;

      !this.$v.form.BRN.required && errors.push('Business Registration Number field is required.');
      !this.$v.form.BRN.maxLength && errors.push('Business Registration Number must be at most 50 characters long.');

      return errors;
    },
    addressErrors(){
      const errors = [];
      if(!this.$v.form.address.$dirty) return errors;

      !this.$v.form.address.required && errors.push('Address field is required.');
      !this.$v.form.address.maxLength && errors.push('Address must be at most 400 characters long.');

      return errors;
    },
    contactErrors(){
      const errors = [];
      if(!this.$v.form.contact.$dirty) return errors;

      !this.$v.form.contact.required && errors.push('Contact field is required.');
      !this.$v.form.contact.maxLength && errors.push('Contact must be at most 30 characters long.');

      return errors;
    },
    emailErrors(){
      const errors = [];
      if(!this.$v.form.email.$dirty) return errors;

      !this.$v.form.email.email && errors.push('Email is not valid.');
      !this.$v.form.email.maxLength && errors.push('Email must be at most 150 characters long.');

      return errors;
    }
  },
  methods:{
    displayData(){
      this.getSupplierLists({page:1, rows:this.getPerPage, search:this.search});
    },
    getSupplierLists(params){
      this.loading = true;
      Supplier.getSuppliers({params:params})
      .then(response=>{
        this.$store.commit('setSuppliers', response.data);
        this.loading = false;
      })
      .catch((error)=>{
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
        this.form = Object.assign({}, this.defaultItem);
        this.itemIndex = -1;
        this.errors = [];
        this.$v.$reset();
        this.saving = false;
      });
    },
    closeDelete(){
      this.dialogDelete = false;
      this.$nextTick(()=>{
        this.form = Object.assign({}, this.defaultItem);
        this.errors = [];
        this.itemIndex = -1;
        this.deleting = false;
      });
    },
    editItem(item){
      this.itemIndex = this.getSuppliers.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
    },
    deleteItem(item){
      this.itemIndex = this.getSuppliers.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialogDelete = true;
    },
    confirmDelete(){
      Supplier.deleteSupplier(this.form.id)
      .then(response=>{
        this.$store.commit('removeSupplier', response.data);
        this.deleting = false;
        this.closeDelete();
      })
    },
    saveSupplier(){
      this.$v.$touch();
      if(!this.$v.form.$invalid){
        if(this.itemIndex === -1){
          Supplier.addSupplier(this.form)
          .then(response=>{
            this.$store.commit('setSupplier', response.data);
            this.saving = false;
            this.close();
          })
          .catch((errors)=>{
            if(errors.response.status === 422){
                this.errors = errors.response.data.errors;
                console.log('errors', this.errors);
            }
            if(errors.response.status === 500){
                this.errors = errors.response.data;
                console.log('errors', this.errors);
            }
            this.saving = false;
          })
        }
        else{
          Supplier.updateSupplier(this.form)
          .then(response=>{
            this.$store.commit('updateSupplier', response.data);
            this.saving = false;
            this.close();
          })
          .catch((errors)=>{
            if(errors.response.status === 422){
                this.errors = errors.response.data.errors;
                console.log('errors', this.errors);
            }
            if(errors.response.status === 500){
                this.errors = errors.response.data;
                console.log('errors', this.errors);
            }
            this.saving = false;
          })
        }
      }
    }
  },
  created(){
    this.getSupplierLists({page:1, rows:this.getPerPage, search:this.search});
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