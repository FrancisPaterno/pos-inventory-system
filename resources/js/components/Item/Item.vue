<template>
  <v-container>
      <v-data-table
        class="ma-5"
        :headers="headers"
        hide-default-footer
        :items="items"
        :items-per-page="getPerPage"
        :loading="loading"
        :expanded.sync="expanded"
        show-expand
      >
          <template v-slot:top>
            <v-card flat>
              <v-container>
                <v-row>
                  <v-col>
                    <v-card-title>Items</v-card-title>
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
                    <v-dialog v-model="dialog" max-width="1000px">
                      <template v-slot:activator="{on, attrs}">
                        <v-btn color="success" v-bind="attrs" v-on="on">New Item</v-btn>
                      </template>
                      <v-card>
                        <v-card-title>{{formTitle}}</v-card-title>
                        <v-card-text>
                          <v-row>
                            <v-col>
                              <v-form ref="form" >
                                <v-text-field label="Barcode" v-model="form.barcode" @input="$v.form.barcode.$touch()" @blur="$v.form.barcode.$touch()" :error-messages="barcodeErrors"></v-text-field>
                                <span v-if="errors.barcode" class="error--text">{{errors.barcode[0]}}</span>
                                <v-text-field label="Name" v-model="form.name" @input="$v.form.name.$touch()" @blur="$v.form.name.$touch()" :error-messages="nameErrors"></v-text-field>
                                <span v-if="errors.name" class="error--text">{{errors.name[0]}}</span>
                                <v-text-field label="SKU" v-model="form.sku" @input="$v.form.sku.$touch()" @blur="$v.form.sku.$touch()" :error-messages="skuErrors"></v-text-field>
                                <span v-if="errors.sku" class="error--text">{{errors.sku[0]}}</span>
                                <v-autocomplete label="Category" chips v-model="form.item_category_id" :loading="loadingCategory" :items="getAllCategories" item-text="name" item-value="id" @input="$v.form.item_category_id.$touch()" @blur="$v.form.item_category_id.$touch()" :error-messages="itemCategoryErrors"></v-autocomplete>
                                <span v-if="errors.item_category_id" class="error--text">{{errors.item_category_id[0]}}</span>
                                <v-autocomplete label="Brand" chips v-model="form.item_brand_id" :loading="loadingBrand" :items="getAllBrands" item-text="name" item-value="id" @input="$v.form.item_brand_id.$touch()" @blur="$v.form.item_brand_id.$touch()" :error-messages="itemBrandErrors"></v-autocomplete>
                                <span v-if="errors.item_brand_id" class="error--text">{{errors.item_brand_id[0]}}</span>
                                <v-autocomplete label="Uom" chips v-model="form.item_unit_id" :loading="loadingUnit" :items="getAllUnits" item-text="name" item-value="id" @input="$v.form.item_unit_id.$touch()" @blur="$v.form.item_unit_id.$touch()" :error-messages="itemUnitErrors"></v-autocomplete>
                                <span v-if="errors.item_unit_id" class="error--text">{{errors.item_unit_id[0]}}</span>
                                <v-autocomplete label="Status" chips v-model="form.item_status_id" :loading="loadingStatus" :items="getAllItemStatus" item-text="name" item-value="id" @input="$v.form.item_status_id.$touch()" @blur="$v.form.item_status_id.$touch()" :error-messages="itemStatusErrors"></v-autocomplete>
                                <span v-if="errors.item_status_id" class="error--text">{{errors.item_status_id[0]}}</span>
                              </v-form>
                            </v-col>
                            <v-divider vertical></v-divider>
                            <v-col>
                              <v-row>
                                <v-img class="mx-auto" v-bind:src="formImg" max-width="400" max-height="400" :lazy-src="defaultImage" ref="img">
                                  <template v-slot:placeholder>
                                    <v-row
                                      class="fill-height ma-0"
                                      align="center"
                                      justify="center"
                                    >
                                      <v-progress-circular
                                        indeterminate
                                        color="grey lighten-5"
                                      ></v-progress-circular>
                                    </v-row>
                                  </template>
                                </v-img>
                              </v-row>
                              <v-row justify="center">
                                <input type="file" style="display:none" accept="image/*" ref="fileInput" @change="onFileSelected"></input>
                                <v-btn class="ma-2" color="primary darken-1" text small @click="$refs.fileInput.click()" >Select Image</v-btn>
                                <v-btn class="ma-2" color="primary darken-1" text small @click="onClearImage">Clear</v-btn>
                              </v-row>
                              <v-row>
                                <v-textarea class="ma-2" v-model="form.description" label="Description" @input="$v.form.description.$touch()" @blur="$v.form.description.$touch()" :error-messages="descriptionErrors"></v-textarea>
                              </v-row>
                            </v-col>
                            
                          </v-row>
                          <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn small color="success darken-1" text @click="close">Cancel</v-btn>
                            <v-btn small color="success darken-1" text :loading="saving" @click="saveItem">OK</v-btn>
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
          <template v-slot:expanded-item="{headers, item}">
            <td :colspan="headers.length">
              <v-container>
                <v-row>
                  <v-col>
                    <v-img class="mx-auto" max-width="400" max-height="400" v-bind:src="item.image" alt="No Image"></v-img>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col>
                    <span> <strong>Description:</strong></span> <br/>
                    <p>{{item.description}}</p>
                  </v-col>
                </v-row>
              </v-container>
            </td>
          </template>
          <template v-slot:item.actions={item}>
            <v-icon small class="mr-2" @click="editItem(item)">edit</v-icon>
            <v-icon small @click="deleteItem(item)">delete</v-icon>
          </template>
          <template v-slot:body.append>
            <perPage v-on:updaterow="displayData"
            collection="items"
            store="item" />
          </template>
      </v-data-table>
      <Paginate
        collection="items"
        store="item"
        :search="search"
        :getItem="getItems"
        moduleCurrentPage = 'setItemCurrentPage'
      />
  </v-container>
</template>

<script>
import { mapGetters } from 'vuex';
import Item from '../../apis/Item';
import ItemCategory from '../../apis/ItemCategory';
import ItemBrand from '../../apis/ItemBrand';
import ItemUnit from '../../apis/ItemUnit';
import ItemStatus from '../../apis/ItemStatus';
import {validationMixin} from 'vuelidate';
import {required, maxLength} from 'vuelidate/lib/validators';
import perPage from '../rowperpage';
import Paginate from '../paginate';

export default {
  mixins:[validationMixin],
  validations:{
    form:{
        barcode:{required, maxLength:maxLength(13)},
        name:{required, maxLength:maxLength(150)},
        sku:{maxLength:maxLength(100)},
        description:{maxLength:maxLength(500)},
        item_category_id:{required},
        item_brand_id:{required},
        item_unit_id:{required},
        item_status_id:{required},
      },
  },
  data(){
    return{
      headers:[
        {text:'ID', align:'start', value:'id'},
        {text:'Barcode', value:'barcode'},
        {text:'Name', value:'name'},
        {text:'SKU', value:'sku'},
        {text:'Category', value:'item_category.name'},
        {text:'Brand', value:'item_brand.name'},
        {text:'Uom', value:'item_unit.name'},
        {text:'Status', value:'item_status.name'},
        {text:'Actions', sortable:false, value:'actions'},
        {text: '', value: 'data-table-expand' },
      ],
      search:'',
      loading:false,
      loadingCategory:false,
      loadingBrand:false,
      loadingUnit:false,
      loadingStatus:false,
      saving:false,
      deleting:false,
      errors:[],
      dialog:false,
      dialogDelete:false,
      itemIndex:-1,
      form:{
        id:0,
        barcode:'',
        name:'',
        sku:'',
        image:null,
        description:'',
        item_category_id:{},
        item_brand_id:{},
        item_unit_id:{},
        item_status_id:{},
      },
      defaultItem:{
        id:0,
        barcode:'',
        name:'',
        sku:'',
        image:null,
        description:'',
        item_category_id:{},
        item_brand_id:{},
        item_unit_id:{},
        item_status_id:{},
      },
      defaultImage:"/images/default.jpg",
      selectedFile:null,
      expanded:[],
      itemImage:null
    }
  },
  components:{
    perPage,
    Paginate
  },
  methods:{
    getImage(event){
      console.log('expanded', event);
      const name = event[0].name;
      Item.getItemImage({params:{item:name}})
      .then((response)=>{
        this.itemImage = response.data;
      })
      .catch((error)=>{
        this.itemImage = this.defaultImage;
      })
    },
    getItems(params){
      this.loading = true;
      Item.getItems({params:params})
      .then((response)=>{
        this.$store.commit('setItems', response.data);
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
    getAllItemCategories(){
      this.loadingCategory = true;
      ItemCategory.getAllItemCategory()
      .then((response)=>{
        this.$store.commit('setAllCategories', response.data);
        this.loadingCategory = false;
      })
      .catch((error)=>{
        if(error.response.status === 422){
          this.errors.error.response.data.errors;
        }

        if(error.response.status === 500){
            this.errors = error.response.data;
        }
        this.loadingCategory = false;
      })
    },
    getAllItemBrands(){
      this.loadingBrand = true;
      ItemBrand.getAllItemBrand()
      .then((response)=>{
        this.$store.commit('setAllItemBrands', response.data);
        this.loadingBrand = false
      })
      .catch((error)=>{
        if(error.response.status === 422){
          this.errors.error.response.data.errors;
        }

        if(error.response.status === 500){
            this.errors = error.response.data;
        }
        this.loadingBrand = false;
      })
    },
    getAllItemUnits(){
      this.loadingUnit = true;
      ItemUnit.getAllItemUnits()
      .then((response)=>{
        this.$store.commit('setAllItemUnits', response.data);
        this.loadingUnit = false;
      })
      .catch((error)=>{
        if(error.response.status === 422){
          this.errors.error.response.data.errors;
        }

        if(error.response.status === 500){
            this.errors = error.response.data;
        }
        this.loadingUnit = false;
      })
    },
    getAllItemStatuses(){
      this.loadingStatus = true;
      ItemStatus.getAllItemStatus()
      .then((response)=>{
        this.$store.commit('setAllItemStatus', response.data);
        this.loadingStatus = false;
      })
      .catch((error)=>{
        if(error.response.status === 422){
          this.errors.error.response.data.errors;
        }

        if(error.response.status === 500){
            this.errors = error.response.data;
        }
        this.loadingStatus = false;
      })
    },
    saveItem(){
      this.$v.$touch();
      if(!this.$v.form.$invalid){
        this.saving = true;
        if(this.itemIndex === -1){
        Item.addItem(this.form)
        .then((response)=>{
          this.$store.commit('setItem', response.data);
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
        Item.editItem(this.form)
        .then((response)=>{
          this.$store.commit('updateItem', response.data);
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
    onFileSelected(event){
      const reader = new FileReader();

      reader.addEventListener('load',()=>{
        this.selectedFile = reader.result;
        this.form.image = reader.result;
      });
      reader.readAsDataURL(event.target.files[0]);
    },
    onClearImage(){
      this.selectedFile = this.defaultImage;
      this.form.image = null;
    },
    editItem(item){
      this.itemIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.selectedFile = item.image;
      this.dialog = true;
    },
    deleteItem(item){
      this.itemIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialogDelete = true;
    },
    close(){
      this.dialog = false;
      this.$nextTick(()=>{
        this.$v.$reset();
        this.form = Object.assign({},this.defaultItem);
        this.errors = [];
        this.itemIndex = -1;
        this.selectedFile = this.defaultImage;
      });
    },
    closeDelete(){
      this.dialogDelete = false;
      this.$nextTick(()=>{
        this.form = Object.assign({}, this.defaultItem);
        this.errors = [];
        this.itemIndex = -1;
        this.selectedFile = this.defaultImage;
      });
    },
    confirmDelete(){
      this.deleting = true;
      Item.deleteItem(this.form.id)
      .then((response)=>{
        this.$store.commit('removeItem', response.data);
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
    displayData(){
      this.getItems({page:1, rows:this.getPerPage,search:this.search});
    }
  },
  computed:{
    ...mapGetters(['getPerPage', 'items','getAllCategories', 'getAllBrands', 'getAllUnits', 'getAllItemStatus']),
    formTitle(){
      return this.itemIndex ===-1?'New Item':'Edit Item'
    },
    formImg(){
      return this.selectedFile;
    },
    getItemImage(){
      return this.itemImage;
    },
    barcodeErrors(){
      const errors = [];
      if(!this.$v.form.barcode.$dirty) return errors;

      !this.$v.form.barcode.required && errors.push('Barcode field is required.');
      !this.$v.form.barcode.maxLength && errors.push('Barcode must be at most 13 characters long.');

      return errors;
    },
    nameErrors(){
      const errors = [];
      if(!this.$v.form.name.$dirty) return errors;

      !this.$v.form.name.required && errors.push('Name field is required.');
      !this.$v.form.name.maxLength && errors.push('Name must be at most 150 characters long.');

      return errors;
    },
    skuErrors(){
      const errors = [];
      if(!this.$v.form.sku.$dirty) return errors;

      !this.$v.form.sku.maxLength && errors.push('SKU must be at most 100 characters long.');

      return errors;
    },
    descriptionErrors(){
      const errors = [];
      if(!this.$v.form.description.$dirty) return errors;

      !this.$v.form.description.maxLength && errors.push('Description must be at most 500 characters long.');

      return errors;
    },
    itemCategoryErrors(){
      const errors = [];
      if(!this.$v.form.item_category_id.$dirty) return errors;

      !this.$v.form.item_category_id.required && errors.push('Category is required.');

      return errors;
    },
    itemBrandErrors(){
      const errors = [];
      if(!this.$v.form.item_brand_id.$dirty) return errors;

      !this.$v.form.item_brand_id.required && errors.push('Brand is required.');

      return errors;
    },
    itemUnitErrors(){
      const errors = [];
      if(!this.$v.form.item_unit_id.$dirty) return errors;

      !this.$v.form.item_unit_id.required && errors.push('Uom is required.');

      return errors;
    },
    itemStatusErrors(){
      const errors = [];
      if(!this.$v.form.item_status_id.$dirty) return errors;

      !this.$v.form.item_status_id.required && errors.push('Status is required.');

      return errors;
    },
  },
  created(){
    this.getItems({page:1, rows:this.getPerPage,search:this.search});
    this.selectedFile = this.defaultImage;
  },
  watch:{
    dialog(val){
      if(val){
        this.getAllItemCategories();
        this.getAllItemBrands();
        this.getAllItemUnits();
        this.getAllItemStatuses();
      }
      else{
        this.close();
      }
    },
    dialogDelete(val){
      val|| this.closeDelete();
    }
  }
}
</script>

<style>

</style>