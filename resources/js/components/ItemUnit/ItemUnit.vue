<template>
  <v-container>
    <v-data-table class="ma-5" 
        :headers="headers"
        hide-default-footer
        :items="getUnits"
        :items-per-page="getPerPage"
        :loading="loading"
    >
    <template v-slot:top>
        <v-card flat>
            <v-container>
                <v-row>
                    <v-col>
                        <v-card-title>Item Units</v-card-title>
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
                    <v-col class="d-flex flex-row-reverse">
                        <v-dialog v-model="dialog" max-width="500px">
                            <template v-slot:activator="{on, attrs}">
                                <v-btn color="success" v-bind="attrs" v-on="on">New Item</v-btn>
                            </template>
                            <v-card>
                                <v-card-title>{{formTitle}}</v-card-title>
                                <v-card-text>
                                    <v-form ref="form">
                                        <v-text-field label="Name" v-model="form.name" @input="$v.form.name.$touch()" @blur="$v.form.name.$touch()" :error-messages="nameErrors" ></v-text-field>
                                        <span v-if="errors.name" class="error--text" >{{errors.name[0]}}</span>
                                        <v-text-field label="Description" v-model="form.description" @input="$v.form.description.$touch()" @blur="$v.form.description.$touch()" :error-messages="descriptionErrors" ></v-text-field>
                                        <span v-if="errors.description" class="error--text">{{errors.description[0]}}</span>
                                    </v-form>
                                    <span v-if="errors.message" class="error--text">{{errors.message}}</span>
                                </v-card-text>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn small color="success darken-1" text @click="close">Cancel</v-btn>
                                    <v-btn small color="success darken-1" text @click="saveItemUnit" :loading="saving">OK</v-btn>
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
    <template v-slot:body.append>
        <perPage v-on:updaterow="displayData"
        collection="itemunits"
        store="unit" />
    </template>
      </v-data-table>
    <Paginate
        collection="itemunits"
        store="unit"
        :search="search"
        :getItem="getItemUnits"
        moduleCurrentPage = 'setItemUnitCurrentPage'
    />
  </v-container>
</template>

<script>
import {mapGetters} from 'vuex';
import ItemUnit from '../../apis/ItemUnit';
import {validationMixin} from 'vuelidate';
import {required, maxLength} from 'vuelidate/lib/validators';
import perPage from '../rowperpage';
import Paginate from '../paginate';

export default {
    mixins:[validationMixin],
    validations:{
        form:{
            name:{required, maxLength:maxLength(20)},
            description:{maxLength:maxLength(100)}
        }
    },
    data(){
        return {
            headers:[{text:'ID', align:'start', value:'id'},
            {text:'Name', value:'name'},
            {text:'Description', value:'description'},
            {text:'Actions', value:'actions', sortable:false}],
            errors:[],
            search:'',
            dialog:false,
            dialogDelete:false,
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
            loading:false,
            saving:false,
            deleting:false
        }
    },
    components:{
        perPage,
        Paginate
    },
    computed:{
        ...mapGetters(['getUnits', 'getPerPage']),
        formTitle(){
            return this.itemIndex===-1?'New Item':'Edit Item';
        },
        nameErrors(){
            const errors = [];
            if(!this.$v.form.name.$dirty) return errors
        
            !this.$v.form.name.maxLength && errors.push('Name must be at most 20 characters long.')
            !this.$v.form.name.required && errors.push('Name is required.')

            return errors
        },
        descriptionErrors(){
            const errors = [];

            if(!this.$v.form.description.$dirty) return errors

            !this.$v.form.description.maxLength && errors.push('Description must be at most 100 characters long.')

            return errors
        }
    },
    methods:{
        getItemUnits(param){
            this.loading = true;
            ItemUnit.getItemUnits({params:param})
            .then((response)=>{
                this.$store.commit('setItemUnits', response.data);
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
        displayData(){
            this.getItemUnits({page:1, rows:this.getPerPage,search:this.search});
        },
        close(){
            this.dialog = false;
            this.$nextTick(()=>{
                this.$v.$reset();
                this.form = Object.assign({}, this.defaultItem);
                this.errors = [];
                this.itemIndex = -1;
            });
        },
        closeDelete(){
            this.dialogDelete = false;
            this.$nextTick(()=>{
                this.$v.$reset();
                this.form = Object.assign({}, this.defaultItem);
                this.errors = [];
                this.itemIndex = -1;
            });
        },
        saveItemUnit(){
            this.$v.$touch();

            if(!this.$v.form.$invalid){
                this.saving = true;
                if(this.itemIndex === -1){
                    ItemUnit.addItemUnit(this.form)
                    .then((response)=>{
                        this.$store.commit('setItemUnit', response.data);
                        this.saving = false;
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
                        this.saving = false;
                    })
                }
                else{
                    ItemUnit.editItemUnit(this.form)
                    .then((response)=>{
                        this.$store.commit('updateItemUnit', response.data);
                        this.saving = false;
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
                        this.saving = false;
                    })
                }
            }
        },
        confirmDelete(){
            this.deleting = true;
            ItemUnit.deleteItemUnit(this.form.id)
            .then((response)=>{
                this.$store.commit('removeItemUnit', response.data);
                this.deleting = false;
                this.closeDelete();
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
                this.deleting = false;
            })
        },
        editItem(item){
            this.itemIndex = this.getUnits.indexOf(item);
            this.form = Object.assign({}, item);
            this.dialog = true;
        },
        deleteItem(item){
            this.itemIndex = this.getUnits.indexOf(item);
            this.form = Object.assign({}, item);
            this.dialogDelete = true;
        }
    },
    created(){
        this.getItemUnits({page:1, rows:this.getPerPage,search:this.search});
        console.log('Item unit created');
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