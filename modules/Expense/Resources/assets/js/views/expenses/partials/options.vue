<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create" width="30%" append-to-body
               :close-on-click-modal="false"
               :close-on-press-escape="false"
               :show-close="false">
    
        <span slot="footer" class="dialog-footer">
            <template v-if="showClose">
                <el-button @click="clickClose">Cerrar</el-button>
            </template>
            <template v-else>
               <!-- <el-button @click="clickFinalize">Ir al listado</el-button> -->
                <el-button type="primary" @click="clickNewDocument">Cerrar</el-button>
            </template>
        </span>
    </el-dialog>
</template>

<script>

    export default {
        props: ['showDialog', 'recordId', 'showClose'],
        data() {
            return {
                titleDialog: null,
                loading: false,
                resource: 'expenses',
                errors: {},
                form: {},
            }
        },
        created() {
            this.initForm()
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    external_id: null,
                    number: null,
                    customer_email: null,
                    download_pdf: null
                }
            },
            create() {
                this.$http.get(`/${this.resource}/record/${this.recordId}`)
                    .then(response => {
                        this.form = response.data.data;
                        // Convertir el valor de payment a número y luego formatearlo con separadores de miles.
                        const paymentAmount = parseFloat(this.form.payments[0].payment).toLocaleString('es', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        this.titleDialog = 'Gasto registrado por valor de $' + paymentAmount;
                        console.log(this.form);
                    });
            }, 
          
            clickFinalize() {
                location.href = `/${this.resource}`
            },
            clickNewDocument() {
                this.clickClose()
            },
            clickClose() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
        }
    }
</script>