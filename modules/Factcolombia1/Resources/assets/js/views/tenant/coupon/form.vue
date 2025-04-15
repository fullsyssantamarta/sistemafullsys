<template>
    <el-dialog width="65%" :title="titleDialog" :visible="showDialog" :close-on-click-modal="false" @close="close" @open="create" append-to-body top="7vh">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.title}">
                            <label class="control-label">Título *</label>
                            <el-input v-model="form.title"></el-input>
                            <small class="form-control-feedback" v-if="errors.title" v-text="errors.title[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.description}">
                            <label class="control-label">Descripción *</label>
                            <el-input v-model="form.description"></el-input>
                            <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.minimum_purchase_amount}">
                            <label class="control-label">Monto mínimo compras *</label>
                            <el-input v-model="form.minimum_purchase_amount"></el-input>
                            <small class="form-control-feedback" v-if="errors.minimum_purchase_amount" v-text="errors.minimum_purchase_amount[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.establishment}">
                            <label class="control-label">Tienda *</label>
                            <el-input v-model="form.establishment"></el-input>
                            <small class="form-control-feedback" v-if="errors.establishment" v-text="errors.establishment[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Fecha *</label>
                            <el-date-picker v-model="form.coupon_date" type="date"
                                            value-format="yyyy-MM-dd" format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                        </div>
                    </div>
                    
                </div>
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.status}">
                            <label class="control-label">Estado </label>
                            <div>
                                <el-switch
                                    v-model="form.status"
                                    active-text="Activo"
                                    inactive-text="Inactivo">
                                </el-switch>
                                <small class="form-control-feedback" v-if="errors.status" v-text="errors.status[0]"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right pt-2">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit" >Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>
    import moment from 'moment'

    export default {
        props: ['showDialog', 'recordId', 'external'],
        data() {
            return {
                showDialogLots:false,
                loading_submit: false,
                titleDialog: 'Nuevo Cupón',
                resource: 'co-coupon',
                errors: {},
                headers: headers_token,
                form: {
                },
            }
        },

        async created() {
            await this.initForm()
        },

        computed: {
            urlData() {
                return (this.form.id) ? {method: 'put', url: `/${this.form.id}`} : {method: 'post', url: '/'};
            }
        },

        methods: {
            initForm() {
                this.loading_submit = false;
                this.errors = {
                };
                this.form = {
                    id: null,
                    title: 'CUPÓN DE COMPRA - ',
                    description: null,
                    minimum_purchase_amount:null,
                    establishment: null,
                    coupon_date: null,
                    status: false,
                }
            },
            resetForm() {
                this.initForm()
            },

            create() {
                this.titleDialog = (this.recordId)? 'Editar Cupón':'Nuevo Cupón'
                if (this.recordId) {
                    this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then(response => {
                            this.form = response.data
                        })
                }
            },

            async submit() {
                this.loading_submit = true
                if(this.form.id){
                    await this.$http.put(`/${this.resource}/${this.form.id}`, this.form)
                        .then(response => {
                            if (response.data.success) {
                                this.$message.success(response.data.message)
                                if (this.external) {
                                    this.$eventHub.$emit('reloadDataItems', response.data.id)
                                } else {
                                    this.$eventHub.$emit('reloadData')
                                }
                                this.close()
                            } else {
                                this.$message.error(response.data.message)
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 422) {
                                this.errors = error.response.data
                            } else {
                                console.log(error)
                            }
                        })
                        .then(() => {
                            this.loading_submit = false
                        })
                }else{
                    await this.$http.post(`/${this.resource}`, this.form)
                        .then(response => {
                            if (response.data.success) {
                                this.$message.success(response.data.message)
                                if (this.external) {
                                    this.$eventHub.$emit('reloadDataItems', response.data.id)
                                } else {
                                    this.$eventHub.$emit('reloadData')
                                }
                                this.close()
                            } else {
                                this.$message.error(response.data.message)
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 422) {
                                this.errors = error.response.data
                            } else {
                                console.log(error)
                            }
                        })
                        .then(() => {
                            this.loading_submit = false
                        })
                }
            },
            close() {
                this.$emit('update:showDialog', false)
                this.resetForm()
            }
        }
    }
</script>
