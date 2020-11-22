<template>
    <v-container>
        <v-data-table
            class="ma-5"
            :headers="headers"
            :items="getCategories"
            :loading="loading"
            :items-per-page="getPerPage"
            hide-default-footer
        >
            <template v-slot:top>
                <v-card flat>
                    <v-container>
                        <v-row>
                            <v-col>
                                <v-card-title class="px-1">Item Categories</v-card-title>
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
                            <v-col  class="d-flex flex-row-reverse">
                                <v-dialog v-model="dialog" max-width="500px">
                                <template v-slot:activator="{on, attrs}">
                                    <v-btn color="success" v-bind="attrs" v-on="on">New Item</v-btn>
                                </template>
                                <v-card>
                                    <v-card-title><span class="headline"><span class="headline">{{formTitle}}</span> </span></v-card-title>
                                <v-card-text>
                                    <v-form ref="form">
                                        <v-text-field label="Name" v-model="form.name" 
                                        @input="$v.form.name.$touch()"
                                        @blur="$v.form.name.$touch()" 
                                        :error-messages="nameErrors">
                                        </v-text-field>
                                        <span v-if="errors.name" class="error--text" >{{errors.name[0]}}</span>
                                        <v-text-field label="Description" v-model="form.description" 
                                        @input="$v.form.description.$touch()"
                                        @blur="$v.form.description.$touch()" 
                                        :error-messages="descriptionErrors">
                                        </v-text-field>
                                        <span v-if="errors.description" class="error--text">{{errors.description[0]}}</span>
                                        <v-card-actions>
                                        <v-spacer></v-spacer>
                                            <v-btn color="success darken-1" text @click="close">Cancel</v-btn>
                                            <v-btn color="success darken-1" text @click.prevent="saveCategory" :loading="saving">Save</v-btn>
                                        </v-card-actions>
                                    </v-form>
                                    <span v-if="errors.message" class="error--text">{{errors.message}}</span>
                                </v-card-text>
                                </v-card>
                                </v-dialog>
                            </v-col>
                        </v-row>
                    <v-divider></v-divider>
                    </v-container>
                <v-dialog v-model="dialogDelete" max-width="500px">
                    <v-card>
                        <v-card-title class="headline">
                            Are you sure you want to delete this item?
                        </v-card-title>
                        <v-card-text>
                            <span v-if="errors.message" class="error--text">{{errors.message}}</span>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="success darken-1" text @click="closeDelete">Cancel</v-btn>
                            <v-btn color="success darken-1" text @click="deleteConfirm" :loading="deleting">OK</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-card>
        </template>
            <template v-slot:item.actions="{ item }">
                <v-icon small class="mr-2" @click="editItem(item)">
                    edit
                </v-icon>
                <v-icon small @click="deleteItem(item)"> 
                    delete
                </v-icon>
            </template>
            <template v-slot:body.append>
                <perPage v-on:updaterow="displayData"  
                collection="itemcategories"
                store="category" />
            </template>
        </v-data-table>
        <Paginate
            collection="itemcategories"
            store="category"
            :search="search"
            :getItem="getItemCategories"
            moduleCurrentPage = 'setCategoryCurrentPage'
        />
    </v-container>
</template>

<script>
import ItemCategory from "../../apis/ItemCategory";
import { mapGetters } from "vuex";
import Paginate from "../paginate";
import perPage from '../rowperpage';
import VueTypes from 'vue-types';
import {validationMixin} from 'vuelidate'
import {required, maxLength} from 'vuelidate/lib/validators';

export default {
    mixins:[validationMixin],
    validations:{
    form:{
        name:{required,maxLength:maxLength(100)},
        description:{maxLength:maxLength(500)}
        }
    },
    data() {
        return {
            headers: [
                { text: "ID", align: "start", value: "id" },
                { text: "Name", value: "name" },
                { text: "Description", value: "description" },
                { text: "Actions", value: "actions", sortable: false }
            ], 
            loading:false,
            rows:5,
            search:'',
            dialog:false,
            dialogDelete:false,
            itemIndex:-1,
            errors:[],
            form:{
                id:0,
                name:'',
                description:''
            }, 
            defaultItem: {
                id:0,
                name: '',
                description:''
            },
            loading:false,
            saving:false,
            deleting:false
        };
    },
    components: { Paginate, perPage },
    computed: { 
        ...mapGetters(["getCategories","getPerPage"]),
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
    methods: {
        getItemCategories(param){
            this.loading = true;
            ItemCategory.getItemCategories({params:param})
            .then((response)=>{
                this.$store.commit('setCategories', response.data);
                this.loading = false;
            })
            .catch((error)=>{
                if(error.response.status === 422){
                    this.errors = error.response.data.errors;
                }

                if(error.response.status === 500){
                    this.errors = error.response.data;
                }
                thid.loading = false;
            })
        },
        displayData(data){
           this.getItemCategories({page:1,rows:this.getPerPage, search:this.search});
        },
        editItem(item){
            this.itemIndex = this.getCategories.indexOf(item);
            this.form = Object.assign({}, item);
            this.dialog = true;
        },
        close(){
            this.dialog = false;
            this.errors = [];
            this.$nextTick(()=>{
                this.form = Object.assign({}, this.defaultItem);
                this.itemIndex = -1;
                this.$v.$reset();
            });
            
        },
        saveCategory(){
            this.errors = [];
            this.$v.$touch();
     
            if(!this.$v.form.$invalid){
                this.saving = true;
                if(this.itemIndex === -1){
                     ItemCategory.addItem(this.form)
                        .then((response)=>{
                        this.$store.commit('setCategory', response.data);
                        this.saving = false;
                        this.close();
                    })
                    .catch((error)=>{
                        if(error.response.status === 422){
                            this.errors = error.response.data.errors;
                        }

                        if(error.response.status === 500){
                            this.errors = error.response.data;
                        }
                        this.saving = false;
                    });
                }
                else{
                    ItemCategory.editItem(this.form)
                    .then((response)=>{
                        this.$store.commit('updateCategory', response.data);
                        this.saving = false;
                        this.close();
                    })
                    .catch((error)=>{
                        if(error.response.status === 422){
                            this.errors = error.response.data.errors;
                        }

                        if(error.response.status === 500){
                            this.errors = error.response.data;
                        }
                        this.saving = false;
                    })
                }
            }
        
        },
        deleteConfirm(){
            this.deleting = true;
            ItemCategory.deleteItem(this.form.id).then((response)=>{
                this.$store.commit('removeCategory',response.data);
                console.log(response.data);
                this.closeDelete();
            })
            .catch((error)=>{
                if(error.response.status === 422){
                    this.errors = error.response.data.errors;
                }

                if(error.response.status === 500){
                    this.errors = error.response.data;
                }
                this.deleting = false;
            })
        },
        deleteItem(item){
            this.itemIndex = this.getCategories.indexOf(item);
            this.form = Object.assign({}, item);
            this.dialogDelete = true;
        },
        closeDelete(){
            this.dialogDelete = false;
            this.errors = [];
            this.$nextTick(()=>{
                this.form = Object.assign({}, this.defaultItem);
                this.itemIndex = -1;
            })
        }
    },
    watch:{
        dialog(val){
            val || this.close();
        },
        dialogDelete(val){
            val || this.closeDelete();
        }
    },
    created() {
        console.log('ItemCategory created');
        this.getItemCategories({page:1,rows:this.getPerPage, search:this.search});
    },
};
</script>

<style></style>
