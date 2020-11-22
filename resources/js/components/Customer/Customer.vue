<template>
  <v-container>
      <v-data-table
        class="ma-5"
        :headers="headers"
        hide-default-footer
        :items="getCustomers"
        :loading="loading"
        :items-per-page="getPerPage"
      >
        <template v-slot:top>
            <v-card flat>
                <v-container>
                    <v-row>
                        <v-col>
                            <v-card-title>Customers</v-card-title>
                        </v-col>
                        <v-col>
                            <v-text-field
                                append-icon="search"
                                label="Search"
                                single-line
                                hide-details
                                v-model="search"
                                @keyup.enter="displayData"
                            >
                            </v-text-field>
                            <v-chip v-if="errors.message" class="error--text mt-2" large close @click:close="errors = []" style="word-wrap: break-word;" >
                            {{errors.message}}
                            </v-chip>
                        </v-col>
                    </v-row>
                    <v-divider></v-divider>
                    <v-row>
                        <v-col class="d flex flex-row-reverse">
                            <v-dialog v-model="dialog" max-width="500">
                                <template v-slot:activator="{on, attrs}">
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
                                            </v-text-field>
                                            <span v-if="errors.name" class="error--text">{{errors.name[0]}}</span>
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
                                        <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn small text color="success darken-1" @click="close">Cancel</v-btn>
                                            <v-btn small text color="success darken-1" :loading="saving" @click="saveCustomer">OK</v-btn>
                                        </v-card-actions>
                                    </v-card-text>
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
            <v-icon small class="mr-2" @click="editCustomer(item)">edit</v-icon>
            <v-icon small @click="deleteCustomer(item)">delete</v-icon>
        </template>
        <template v-slot:body.append>
            <perPage v-on:updaterow="displayData"
                collection="customers"
                store="customer"
             />
        </template>
      </v-data-table>
      <Paginate
        collection="customers"
        store="customer"
        :search="search"
        :getItem="getCustomersLists"
        moduleCurrentPage = 'setCustomerCurrentPage'
      />
  </v-container>
</template>

<script>
import {mapGetters} from 'vuex';
import Customer from '../../apis/Customer';
import perPage from '../rowperpage';
import Paginate from '../paginate';
import {validationMixin} from 'vuelidate';
import {required, maxLength, email} from 'vuelidate/lib/validators';

export default {
    mixins:[validationMixin],
    validations:{
        form:{
            name: {required, maxLength:maxLength(255)},
            address:{required, maxLength:maxLength(255)},
            contact:{required, maxLength:maxLength(255)},
            email:{email, maxLength:maxLength(255)}
        }
    },
    data(){
        return {
            headers:[
                {text:'ID', align:'start', value:'id'},
                {text:'Name', value:'name'},
                {text:'Address', value:'address'},
                {text:'Contact', value:'contact'},
                {text:'Email', value:'email'},
                {text:'Actions', value:'actions', sortable:false}
            ],
            loading:false,
            search:'',
            dialog:false,
            dialogDelete:false,
            form:{
                id:0,
                name:'',
                address:'',
                contact:'',
                email:''
            },
            errors:[],
            defaultItem:{
                id:0,
                name:'',
                address:'',
                contact:'',
                email:''
            },
            itemIndex:-1,
            saving:false,
            deleting:false
        }
    },
    components:{
        perPage,
        Paginate
    },
    computed:{
        ...mapGetters(['getCustomers', 'getPerPage']),
        formTitle(){
            return this.itemIndex === -1?'New Item':'Edit Item';
        },
        nameErrors(){
            const errors = [];
            if(!this.$v.form.name.$dirty) return errors;

            !this.$v.form.name.required && errors.push('Name field is required.');
            !this.$v.form.name.maxLength && errors.push('Name must be at most 255 characters long.');

            return errors;
        },
        addressErrors(){
            const errors = [];
            if(!this.$v.form.address.$dirty) return errors;

            !this.$v.form.address.required && errors.push('Address field is required.');
            !this.$v.form.address.maxLength && errors.push('Address must be at most 255 characters long.');

            return errors;
        },
        contactErrors(){
            const errors = [];
            if(!this.$v.form.contact.$dirty) return errors;

            !this.$v.form.contact.required && errors.push('Contact field is required.');
            !this.$v.form.contact.maxLength && errors.push('Contact must be at most 255 characters long.');

            return errors;
        },
        emailErrors(){
            const errors = [];
            if(!this.$v.form.email.$dirty) return errors;

            !this.$v.form.email.email && errors.push('Email is not valid.');
            !this.$v.form.email.maxLength && errors.push('Email must be at most 255 characters long.');

            return errors;
        }
    },
    methods:{
        getCustomersLists(params){
            this.loading = true;
            Customer.getCustomers({params:params})
            .then((response)=>{
                this.$store.commit('setCustomers', response.data)
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
        displayData(){
            this.getCustomersLists({page:1, rows:this.getPerPage, search:this.search})
        },
        close(){
            this.dialog = false;
            this.$nextTick(()=>{
                this.form = Object.assign({}, this.defaultItem);
                this.errors = [];
                this.itemIndex = -1;
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
            });
        },
        saveCustomer(){
            this.$v.$touch();
            if(!this.$v.form.$invalid){
                this.saving = true;
                if(this.itemIndex === -1){
                    Customer.addCustomer(this.form)
                    .then((response)=>{
                        this.$store.commit('setCustomer', response.data);
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
                    Customer.updateCustomer(this.form)
                    .then((response)=>{
                        this.$store.commit('updateCustomer', response.data);
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
        editCustomer(item){
            this.itemIndex = this.getCustomers.indexOf(item);
            this.form = Object.assign({}, item);
            this.dialog = true;
        },
        deleteCustomer(item){
            this.itemIndex = this.getCustomers.indexOf(item);
            this.form = Object.assign({}, item);
            this.dialogDelete = true;
        },
        confirmDelete(){
            this.deleting = true;
            Customer.deleteCustomer(this.form.id)
            .then((response)=>{
                this.$store.commit('removeCustomer', response.data);
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
        }
    },
    created(){
        this.getCustomersLists({page:1, rows:this.getPerPage, seach:this.search})
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