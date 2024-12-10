<template>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <el-switch v-model="form.enable_qz_tray" active-text="Si" inactive-text="No" @change="enableQzTray"></el-switch>
            </div>
            <template v-if="form.enable_qz_tray">
                <p>Ingresar los dos archivos generados en los certificados de Qz Tray</p>
                <div class="row text-center mx-auto">
                    <div class="col-md-6 col-12">
                        <label>Digital Certificate</label>
                        <el-input v-model="form.digital_qztray"
                            :readonly="true">
                            <el-upload slot="append"
                                ref="digitalqz"
                                :name="'digital_qztray'"
                                :headers="headers"
                                :on-success="successUpload"
                                :on-error="errorUpload"
                                :show-file-list="false"
                                :multiple="false"
                                action="/certificates-qztray/uploads">
                                <el-button icon="el-icon-upload"
                                    type="primary"></el-button>
                            </el-upload>
                        </el-input>
                    </div>
                    <div class="col-lg-6 col-12">
                        <span>Private Key</span>
                        <el-input v-model="form.private_qztray"
                            :readonly="true">
                            <el-upload slot="append"
                                ref="privateqz"
                                :data="{'digital_qztray': null}"
                                :name="'private_qztray'"
                                :headers="headers"
                                :on-success="successUpload"
                                :on-error="errorUpload"
                                :show-file-list="false"
                                :multiple="false"
                                action="/certificates-qztray/uploads">
                                <el-button icon="el-icon-upload"
                                    type="primary"></el-button>
                            </el-upload>
                        </el-input>
                    </div>
                    <div class="row mt-4" v-if="showButtonDelete">
                        <div class="col-md-12 text-right">
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-danger"
                                        @click.prevent="removeCertificateQzTray">Eliminar</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import {deletable} from '@mixins/deletable'

export default {
    mixins: [deletable],
    data() {
        return {
            headers: headers_token,
            showButtonDelete : false,
            resources: 'certificates-qztray',
            form: {},
        }
    },
    created() {
        this.initForm()
        this.getRecordCertificatesQzTray()
    },
    methods: {
        successUpload(response, file, fileList) {

            if (response.success) {
                this.$message.success(response.message)
                this.form[response.type] = response.name
                this.showButtonDelete = true;
            } else {
                this.$message({message: 'Error al subir el archivo', type: 'error'})
            }
        },
        errorUpload(error)
        {
            this.$message({message: 'Error al subir el archivo', type: 'error'})
        },
        initForm(){
            this.form = {
                digital_qztray: null,
                private_qztray: null,
                enable_qz_tray: false
            }
        },
        getRecordCertificatesQzTray() {
            this.$http
                .get(`/${this.resources}/record`)
                .then(response => {
                    let certificates = response.data

                    if (certificates.digital_certificate_qztray || certificates.private_certificate_qztray ) {
                        this.showButtonDelete = true
                    }
                    this.form.digital_qztray = certificates.digital_certificate_qztray ? certificates.digital_certificate_qztray : null;
                    this.form.private_qztray = certificates.private_certificate_qztray ? certificates.private_certificate_qztray : null;
                    this.form.enable_qz_tray = certificates.enable_qz_tray;

                })
        },
        enableQzTray() {
            let params = {
                enable_qz_tray: this.form.enable_qz_tray
            }
            this.$http
                .post(`/${this.resources}/change-status`, params)
                .then(response => {
                    this.form.enable_qz_tray = response.data.enable_qz_tray;
                    this.$message({message: 'Configuración de impresión actualizada', type: 'success'})
                })
                .catch(error => {
                    console.log(error)
                    this.$message({message: 'Configuración de impresión presenta errores', type: 'error'})
                })
        },
        async removeCertificateQzTray() {
            await this.destroy(`/${this.resources}`)
            this.showButtonDelete = false;
            this.getRecordCertificatesQzTray()
        }
    }
}
</script>