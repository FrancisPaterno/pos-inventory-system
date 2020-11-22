<template>
<v-container>
  <v-data-table>
    <template v-slot:top>
      <v-card flat>
        <v-card-title>
          <v-icon @click="$router.go(-1)">arrow_back</v-icon>
          <h1 class="mx-auto">Details</h1>
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
        </v-card-text>
      </v-card>
    </template>
  </v-data-table>
</v-container>
</template>

<script>
import StockHeader from '../../apis/StockHeader';
import moment from 'moment';
export default {
  data(){
    return {
      shDetails:{}
    }
  },
  props:{
    stockHeader:{
      required:true
    }
  },
  computed:{
    formattedDate(){
      return moment(this.shDetails.date_received).format('MMM DD, YYYY');
    },
    getSHDetails(){
      return this.shDetails;
    }
  },
  methods:{
    getStockHeader(){
      StockHeader.getStockHeader(this.stockHeader)
      .then(response=>{
        this.shDetails = response.data;
      })
      .catch((error)=>{
        if(error.response.status === 422){
          this.errors.error.response.data.errors;
        }

        if(error.response.status === 500){
            this.errors = error.response.data;
        }
      })
    }
  },
  created(){
    this.getStockHeader();
  }
}
</script>

<style>

</style>