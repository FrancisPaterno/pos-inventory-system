<template>
    <v-container>
        <v-data-table 
        class="ma-5"
        :headers="headers"
        hide-default-footer
        :items="getStockHeaders"
        :items-per-page="getPerPage"
        >
        <template v-slot:top>
            <v-card flat>
                <v-container>
                    <v-row>
                        <v-col>
                            <v-card-title>Stock Lists</v-card-title>
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
                                            v-model="form.delivery_receipt_no" 
                                            label="Receipt No"
                                            :error-messages="receiptErrors"
                                            @input="$v.form.delivery_receipt_no.$touch()" 
                                            @blur="$v.form.delivery_receipt_no.$touch()"
                                            >
                                            </v-text-field>
                                            <span v-if="errors.delivery_receipt_no" class="error--text">{{errors.delivery_receipt_no[0]}}</span>
                                            <v-textarea label="Description" v-model="form.description"
                                            @input="$v.form.description.$touch()" 
                                            @blur="$v.form.description.$touch()"
                                            :error-messages="descriptionErrors"
                                            >
                                            </v-textarea>
                                            <span v-if="errors.description" class="error--text">{{errors.description[0]}}</span>
                                            <v-menu
                                            ref="menu"
                                            v-model="date_menu"
                                            :close-on-content-click="false"
                                            :return-value.sync="form.date_received"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="290px"
                                            >
                                            <template v-slot:activator="{ on, attrs }">
                                            <v-text-field
                                            v-model="form.date_received"
                                            label="Date"
                                            prepend-icon="today"
                                            readonly
                                            v-bind="attrs"
                                            v-on="on"
                                            @input="$v.form.date_received.$touch()" 
                                            @blur="$v.form.date_received.$touch()"
                                            :error-messages="dateReceivedErrors"
                                            >
                                            </v-text-field>
                                            </template>
                                            <v-date-picker
                                            v-model="form.date_received"
                                            no-title
                                            scrollable
                                            >
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="date_menu = false"
                                            >
                                                Cancel
                                            </v-btn>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="$refs.menu.save(form.date_received)"
                                            >
                                                OK
                                            </v-btn>
                                            </v-date-picker>
                                            </v-menu>
                                            <span v-if="errors.date_received" class="error--text">{{errors.date_received[0]}}</span>
                                            <v-autocomplete
                                            label="Warehouse" 
                                            chips 
                                            v-model="form.warehouse_id"
                                            :loading="loadWarehouses"
                                            :items="getAllWarehouses"
                                            item-text="name"
                                            item-value="id"
                                            @input="$v.form.warehouse_id.$touch()" 
                                            @blur="$v.form.warehouse_id.$touch()"
                                            :error-messages="warehouseErrors"
                                            >
                                            </v-autocomplete>
                                            <span v-if="errors.warehouse_id" class="error--text">{{errors.warehouse_id[0]}}</span>
                                            <v-autocomplete
                                            label="Supplier"
                                            chips
                                            v-model="form.supplier_id"
                                            :loading="loadSuppliers"
                                            :items="getAllSuppliers"
                                            item-text="name"
                                            item-value="id"
                                            @input="$v.form.supplier_id.$touch()" 
                                            @blur="$v.form.supplier_id.$touch()"
                                            :error-messages="supplierErrors"
                                            >
                                            </v-autocomplete>
                                            <span v-if="errors.supplier_id" class="error--text">{{errors.supplier_id[0]}}</span>
                                            <v-text-field label="Total"
                                            v-model="form.total"
                                            type="number"
                                            @input="$v.form.total.$touch()" 
                                            @blur="$v.form.total.$touch()"
                                            :error-messages="totalErrors"
                                            >
                                            </v-text-field>
                                            <span v-if="errors.total" class="error--text">{{errors.total[0]}}</span>
                                        </v-form>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn color="success darken-1" text @click="close">Cancel</v-btn>
                                        <v-btn color="success darken-1" text @click="saveStockHeader">OK</v-btn>
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
        <template v-slot:item.date_received="{item}">
            <span>{{formatDate(item.date_received)}}</span>
        </template>
        <template v-slot:item.actions={item}>
            <v-icon small class="mr-2" @click="editItem(item)">edit</v-icon>
            <v-icon small class="mr-2" @click="deleteItem(item)">delete</v-icon>
            <router-link :key="$route.path" :to="{name:'StockDetails', params:{stockHeader:item.id}}">
             <v-icon small>text_snippet</v-icon>
            </router-link>
        </template>
        <template v-slot:body.append>
            <perPage v-on:updaterow="displayData"
            collection="stockheaders"
            store="stockheader" />
        </template>
        </v-data-table>
        <Paginate
        collection="stockheaders"
        store="stockheader"
        :search="search"
        :getItem="getStockHeaderLists"
        moduleCurrentPage = 'setStockHeaderCurrentPage'
    />
    </v-container>
</template>

<script>
import {mapGetters} from 'vuex'
import perPage from '../rowperpage';
import Paginate from '../paginate';
import {validationMixin} from 'vuelidate';
import {required, maxLength, decimal} from 'vuelidate/lib/validators';
import StockHeader from '../../apis/StockHeader';
import Warehouse from '../../apis/Warehouse';
import Supplier from '../../apis/Supplier';
import moment from 'moment';

export default {
    mixins:[validationMixin],
    validations:{
        form:{
            delivery_receipt_no:{required, maxLength:maxLength(30)},
            description:{maxLength:maxLength(500)},
            date_received:{required},
            warehouse_id:{required},
            supplier_id:{required},
            total:{required, decimal}
        }
    },
    data(){
        return {
            headers:[
                {text:'ID', align:'start', value:'id'},
                {text:'Receipt No', value:'delivery_receipt_no'},
                {text:'Description', value:'description'},
                {text:'Date', value:'date_received'},
                {text:'Warehouse', value:'warehouse.name'},
                {text:'Supplier', value:'supplier.name'},
                {text:'Total', value:'total'},
                {text:'Actions', value:'actions', sortable:false}
            ],
            search:'',
            errors:[],
            dialog:false,
            dialogDelete:false,
            itemIndex:-1,
            loading:false,
            form:{
                id:0,
                delivery_receipt_no:'',
                description:'',
                date_received:new Date().toISOString().substr(0,10),
                warehouse_id:null,
                supplier_id:null,
                total:null
            },
            defaultItem:{
                id:0,
                delivery_receipt_no:'',
                description:'',
                date_received:new Date().toISOString().substr(0,10),
                warehouse_id:null,
                supplier_id:null,
                total:null
            },
            date_menu:false,
            loadWarehouses:false,
            loadSuppliers:false,
            saving:false,
            deleting:false
        }
    },
    components:{
        perPage,
        Paginate
    },
    computed:{
        ...mapGetters(['getStockHeaders', 'getPerPage', 'getAllWarehouses', 'getAllSuppliers']),
        formTitle(){
            return this.itemIndex === -1?'New Item':'Edit Item';
        },
        receiptErrors(){
            const errors = [];
            if(!this.$v.form.delivery_receipt_no.$dirty) return errors;

            !this.$v.form.delivery_receipt_no.required && errors.push('Delivery receipt field is required.');
            !this.$v.form.delivery_receipt_no.maxLength && errors.push('Delivery receipt must be at most 30 characters long.');

            return errors;
        },
        descriptionErrors(){
            const errors = [];
            if(!this.$v.form.description.$dirty) return errors;

            !this.$v.form.description.maxLength && errors.push('Description must be at most 500 characters long.');

            return errors;
        },
        dateReceivedErrors(){
            const errors = [];
            if(!this.$v.form.date_received.$dirty) return errors;

            !this.$v.form.date_received.required && errors.push('Date received is required.');

            return errors;
        },
        warehouseErrors(){
            const errors = [];
            if(!this.$v.form.warehouse_id.$dirty) return errors;

            !this.$v.form.warehouse_id.required && errors.push('Warehouse is required.');

            return errors;
        },
        supplierErrors(){
            const errors = [];
            if(!this.$v.form.supplier_id.$dirty) return errors;

            !this.$v.form.supplier_id.required && errors.push('Supplier is required.');

            return errors;
        },
        totalErrors(){
            const errors = [];
            if(!this.$v.form.total.$dirty) return errors;

            !this.$v.form.total.required && errors.push('Total is required.');
            !this.$v.form.total.decimal && errors.push('Total is not valid');

            return errors;
        },
    },
    methods:{
        formatDate(date){
            return moment(date).format("MMM DD, YYYY");
        },
        displayData(){
            this.getStockHeaderLists({page:1, rows:this.getPerPage, search:this.search});
        },
        getAllWarehousesList(){
            this.loadWarehouses = true;
            Warehouse.getAllWarehouse()
            .then(response=>{
                this.$store.commit('setAllWarehouses', response.data);
            })
            .catch((error)=>{
                if(error.response.status === 422){
                this.errors.error.response.data.errors;
                }

                if(error.response.status === 500){
                    this.errors = error.response.data;
                }
            })
            .finally(()=>{this.loadWarehouses = false;})
        },
        getAllSupplierList(){
            this.loadSuppliers = true;
            Supplier.getSuppliers({})
            .then(response=>{
                this.$store.commit('setAllSuppliers', response.data)
            })
            .catch((error)=>{
                if(error.response.status === 422){
                this.errors.error.response.data.errors;
                }

                if(error.response.status === 500){
                    this.errors = error.response.data;
                }
            })
            .finally(()=>{this.loadSuppliers = false;})
        },
        getStockHeaderLists(params){
            this.loading = true;
            StockHeader.getStockHeaders({params:params})
            .then(response=>{
                this.$store.commit('setStockHeaders', response.data);
            })
            .catch((error)=>{
                if(error.response.status === 422){
                this.errors.error.response.data.errors;
                }

                if(error.response.status === 500){
                    this.errors = error.response.data;
                }
            })
            .finally(()=>{
                this.loading = false;
            })
        },
        saveStockHeader(){
            this.$v.$touch();
            if(!this.$v.form.$invalid){
                this.saving = true;
                if(this.itemIndex === -1){
                    StockHeader.addStockHeader(this.form)
                    .then((response)=>{
                        this.$store.commit('setStockHeader',response.data);
                        this.close();
                    })
                    .catch((error)=>{
                        console.log('save stock', error);
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
                    StockHeader.updateStockHeader(this.form)
                    .then(response=>{
                        this.$store.commit('updateStockHeader', response.data);
                        this.close();
                    })
                    .catch((error)=>{
                        console.log('save stock', error);
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
            }
        },
        close(){
            this.dialog = false;
            this.$nextTick(()=>{
                this.form = Object.assign({}, this.defaultItem);
                this.errors = [];
                this.itemIndex = -1;
                this.$v.$reset();
            })
        },
        closeDelete(){
            this.dialogDelete = false;
            this.$nextTick(()=>{
                this.form = Object.assign({}, this.defaultItem);
                this.errors = [];
                this.itemIndex = -1;
                this.$v.$reset();
            });
        },
        editItem(item){
            this.itemIndex = this.getStockHeaders.indexOf(item);
            this.form = Object.assign({},item);
            this.dialog = true;
        },
        deleteItem(item){
            this.itemIndex = this.getStockHeaders.indexOf(item);
            this.form = Object.assign({},item);
            this.dialogDelete = true;
        },
        confirmDelete(){
            this.deleting = true;
            StockHeader.removeStockHeader(this.form.id)
            .then(response=>{
                this.$store.commit('removeStockHeader', response.data);
                this.deleting = false;
                this.closeDelete();
            })
        }
    },
    created(){
        this.getStockHeaderLists({page:1, rows:this.getPerPage, search:this.search});
    },
    watch:{
        dialog(val){
            if(val){
                this.getAllWarehousesList();
                this.getAllSupplierList();
            }
            else{
                this.close();
            }
        },
        dialogDelete(val){
            val || this.closeDelete();
        }
    }
}
</script>

<style>

</style>