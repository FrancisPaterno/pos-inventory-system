<template>
  <v-container>
      <v-data-table class="ma-5"
                    :headers="headers"
                    hide-default-footer
                    :items-per-page="getPerPage"
                    :items="getBrands"
                    :loading="loading"
                    >
        <template v-slot:top>
            <v-card flat>
                <v-container>
                    <v-row>
                        <v-col>
                            <v-card-title>Item Brands</v-card-title>
                        </v-col>
                        <v-col>
                            <v-text-field append-icon="search" label="Search" single-line hide-details v-model="search" @keyup.enter="displayData"></v-text-field>
                            <v-chip v-if="myerrors.message" class="error--text mt-2" large close @click:close="myerrors = []" style="word-wrap: break-word;" >
                                    {{myerrors.message}}
                            </v-chip>
                        </v-col>
                    </v-row>
                    <v-divider></v-divider>
                    <v-row>
                        <v-col class="d-flex flex-row-reverse">
                            <v-dialog max-width="500px" v-model="dialog">
                                <template v-slot:activator="{on, attrs}">
                                    <v-btn color="success" v-bind="attrs" v-on="on">New Item</v-btn>
                                </template>
                                <v-card>
                                    <v-card-title>{{formTitle}}</v-card-title>
                                    <v-card-text>
                                        <v-form ref="form">
                                            <v-text-field label="Name" v-model="form.name" @input="$v.form.name.$touch()" @blur="$v.form.name.$touch()" :error-messages="nameErrors" ></v-text-field>
                                            <span v-if="myerrors.name" class="error--text" >{{myerrors.name[0]}}</span>
                                            <v-text-field label="Description" v-model="form.description" @input="$v.form.description.$touch()" @blur="$v.form.description.$touch()" :error-messages="descriptionErrors" ></v-text-field>
                                            <span v-if="myerrors.description" class="error--text">{{myerrors.description[0]}}</span>
                                        </v-form>
                                        <span v-if="myerrors.message" class="error--text">{{myerrors.message}}</span>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn color="success darken-1" text @click="close">Cancel</v-btn>
                                        <v-btn color="success darken-1" text @click.prevent="saveBrand" :loading="saving">Save</v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                            <v-dialog v-model="dialogDelete" max-width="500px">
                                <v-card>
                                    <v-card-title class="headline">Are you sure you want to delete this item?</v-card-title>
                                    <v-card-text>
                                        <span v-if="myerrors.message" class="error--text">{{myerrors.message}}</span>
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
            <v-icon class="mr-2" small @click="editItem(item)">edit</v-icon>
            <v-icon small @click="deleteItem(item)">delete</v-icon>
        </template>
        <template v-slot:body.append>
            <perPage v-on:updaterow="displayData"  
                collection="itembrands"
                store="brand" />
        </template>
      </v-data-table>
      <Paginate
            collection="itembrands"
            store="brand"
            :search="search"
            :getItem="getItemBrands"
            moduleCurrentPage = 'setBrandCurrentPage'
        />
  </v-container>
</template>

<script>
import {mapGetters} from 'vuex';
import ItemBrand from '../../apis/ItemBrand';
import perPage from '../rowperpage';
import {validationMixin} from 'vuelidate';
import {required, maxLength} from 'vuelidate/lib/validators';
import Paginate from '../paginate';

export default {
    data(){
        return {
            headers:[
                {text:'ID', align:'start', value:'id'},
                {text:'Name', value:'name'},
                {text:'Description', value:'description'},
                {text:'Actions', value:'actions', sortable:false}
            ],
            search:'',
            itemIndex:-1,
            form:{
                id:0,
                name:'',
                description:''
            },
            defaultItem:{
                id:0,
                name:'',
                description:''
            },
            dialog:false,
            dialogDelete:false,
            myerrors:[],
            loading:false,
            saving:false,
            deleting:false
        }
    },
    mixins:[validationMixin],
    validations:{
        form:{
            name:{required, maxLength:maxLength(100)},
            description:{maxLength:maxLength(500)}
        }
    },
    components:{
        perPage,
        Paginate
    },
    methods:{
        getItemBrands(param){
            this.loading = true;
            ItemBrand.getItemBrand({params:param})
            .then((response)=>{
                this.$store.commit('setItemBrands', response.data);
                this.loading = false;
            })
            .catch((error)=>{
                if(error.response.status === 422){
                    this.myerrors.error.response.data.errors;
                }

                if(error.response.status === 500){
                            this.myerrors = error.response.data;
                            console.log('errors', this.myerrors);
                }
            })
        },
        displayData(){
            this.getItemBrands({page:1,rows:this.getPerPage, search:this.search});
        },
        close(){
            this.dialog = false;
            this.myerrors = [];
            this.$nextTick(()=>{
                this.form = Object.assign({}, this.defaultItem);
                this.itemIndex = -1;
                this.$v.$reset();
                }
            )
        },
        closeDelete(){
            this.dialogDelete = false;
            this.myerrors = [];
            this.$nextTick(()=>{
                this.form = Object.assign({}, this.defaultItem);
                this.itemIndex = -1;
                this.$v.$reset();
            })
        },
        editItem(item){
            this.itemIndex = this.getBrands.indexOf(item);
            this.form = Object.assign({},item);
            this.dialog = true;
        },
        deleteItem(item){
            this.itemIndex = this.getBrands.indexOf(item);
            this.form = Object.assign({},item);
            this.dialogDelete = true;
        },
        confirmDelete(){
            this.deleting = true;
            ItemBrand.deleteItemBrand(this.form.id)
            .then((response)=>{
                this.$store.commit('removeItemBrand', response.data);
                this.deleting = false;
                this.closeDelete();
            })
            .catch((error)=>{
                if(error.response.status === 422){
                    this.myerrors = error.response.data.errors;
                }

                if(error.response.status === 500){
                            this.myerrors = error.response.data;
                            console.log('errors', this.myerrors);
                }
                this.deleting = false;
            })
        },
        saveBrand(){
            this.$v.$touch();
            if(!this.$v.form.$invalid){
                this.saving = true;
                if(this.itemIndex === -1){
                    ItemBrand.addItemBrand(this.form)
                    .then((response)=>{
                        this.$store.commit('setItemBrand', response.data);
                        this.saving = false;
                        this.close();
                    })
                    .catch((error)=>{
                        if(error.response.status === 422){
                            this.myerrors = error.response.data.errors;
                            console.log('errors', this.myerrors);
                        }
                        if(error.response.status === 500){
                            this.myerrors = error.response.data;
                            console.log('errors', this.myerrors);
                        }
                        this.saving = false;
                    });
                }
                else{
                    ItemBrand.editItemBrand(this.form)
                    .then((response)=>{
                        this.$store.commit('updateItemBrand', response.data);
                        this.saving = false;
                        this.close();
                    })
                    .catch((error)=>{
                        if(error.response.status === 422){
                            this.myerrors = error.response.data.errors
                        }
                        if(error.response.status === 500){
                            this.myerrors = error.response.data;
                        }
                        this.saving = false;
                    })
                }
                this.saving = true;
            }
        }
    },
    computed:{
        ...mapGetters(['getPerPage', 'getBrands']),
        formTitle(){
            return this.itemIndex===-1?'New Item':'Edit Item';
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

            return errors
        }
    },
    created(){
        console.log('ItemBrand mounted');
        this.getItemBrands({page:1,rows:this.getPerPage, search:this.search});
    },
    watch:{
        dialog(val){
            val || this.close();
        },
        dialogDelete(val){
            val || this.closeDelete();
        }
    }
};
</script>

<style>

</style>