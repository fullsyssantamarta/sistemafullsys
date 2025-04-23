<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @open="create" width="30%" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" append-to-body>

        <div class="row mb-4">
            <div class="col-md-12">
                <el-alert
                    :title="`Remisión generada con éxito: ${form.number_full}`"
                    type="success"
                    show-icon>
                </el-alert>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 text-center font-weight-bold mt-3">
                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickToPrint('a4')">
                    <i class="fa fa-print"></i>
                </button>
                <p>A4</p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 text-center font-weight-bold mt-3">
                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickToPrint('ticket')">
                    <i class="fa fa-print"></i>
                </button>
                <p>Ticket</p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="form-group">
                    <label class="control-label">Enviar por WhatsApp</label>
                    <div class="input-group">
                        <el-input v-model="whatsapp_number" placeholder="Número WhatsApp">
                            <el-button slot="append" icon="el-icon-chat-dot-round" @click="clickSendWhatsapp" :loading="loadingWhatsapp">
                                Enviar
                            </el-button>
                        </el-input>
                    </div>
                </div>
            </div>
        </div>

        <span slot="footer" class="dialog-footer">
            <div class="row w-100">
                <div class="col-md-12 text-right">
                    <template v-if="showClose">
                        <el-button @click="clickClose">Cerrar</el-button>
                    </template>
                    <template v-else>
                        <el-button class="list" @click="clickFinalize">Ir al listado</el-button>
                        <el-button type="primary" @click="clickNewDocument">Nueva remisión</el-button>
                    </template>
                </div>
            </div>
        </span>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'recordId', 'showClose', 'showDownload'],
        data() {
            return {
                titleDialog: null,
                loading: false,
                resource: 'co-remissions',
                errors: {},
                form: {},
                company: {},
                locked_emission:{},
                whatsapp_number: "",
                loadingWhatsapp: false,
            }
        },
        async created() {
            this.initForm()
            await this.$http.get(`/companies/record`)
                .then(response => {
                    if (response.data !== '') {
                        this.company = response.data.data
                    }
                })
        },
        methods: {
            clickToPrint(format){
                window.open(`/${this.resource}/print/${this.form.external_id}/${format}`, '_blank');
            },
            initForm() {
                this.errors = {};
                this.form = {
                    id: null,
                    number_full:null,
                    customer_email:null,
                    customer_phone:null,
                    correlative_api:null,
                    message_text: null,
                    response_api_message: null,
                    download_pdf: null,
                    download_xml: null,
                    external_id: null,
                    identifier: null,
                    date_of_issue:null,
                    print_ticket: null,
                    print_a4: null,
                    print_a5: null,
                    print_html: null,
                    series:null,
                    number:null,
                    whatsapp_number: null,
                };
            },
            async create() {
                await this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                    this.form = response.data.data;
                    this.titleDialog = 'Remisión: '+this.form.number_full;
                });

            },
            clickPrint(format){
                window.open(`/print/document/${this.form.external_id}/${format}`, '_blank');
            },
            clickSendEmail() {
                this.loading = true
                this.$http.post(`/${this.resource}/sendEmail`, {
                    email: this.form.customer_email,
                    number: this.form.correlative_api,
                    number_full: this.form.number_full
                })
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success('El correo fue enviado satisfactoriamente')
                        } else {
                            this.$message.error('Error al enviar el correo')
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors
                        } else {
                            this.$message.error(error.response.data.message)
                        }
                    })
                    .then(() => {
                        this.loading = false
                    })
            },
            async getPdfAsBase64(url) {
                try {
                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Error al obtener el PDF');
                    
                    const blob = await response.blob();
                    return new Promise((resolve) => {
                        const reader = new FileReader();
                        reader.onloadend = () => resolve(reader.result.split(',')[1]);
                        reader.readAsDataURL(blob);
                    });
                } catch (error) {
                    console.error('Error al convertir PDF a base64:', error);
                    throw error;
                }
            },

            async clickSendWhatsapp() {
                if (!this.whatsapp_number) {
                    this.$message.error('Ingrese un número de WhatsApp');
                    return;
                }

                this.loadingWhatsapp = true;
                
                try {
                    const pdfUrl = `/${this.resource}/print/${this.form.external_id}/a4`;
                    const pdfBase64 = await this.getPdfAsBase64(pdfUrl);

                    const payload = {
                        number: this.whatsapp_number,
                        message: `Remisión: ${this.form.number_full}`,
                        pdf_base64: pdfBase64,
                        filename: `remision_${this.form.number_full}.pdf`
                    };

                    const { data } = await this.$http.post('/pos/whatsapp/send', payload);

                    if (data.success) {
                        this.$message.success('Documento enviado por WhatsApp exitosamente');
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    this.$message.error(error.response?.data?.message || error.message);
                } finally {
                    this.loadingWhatsapp = false;
                }
            },
            clickFinalize() {
                location.href = `/${this.resource}`
            },
            clickNewDocument() {
                this.clickClose()
            },
            clickClose() {
                this.$emit('update:showDialog', false)
                this.$emit("triggerBack");
                this.initForm()
                this.$eventHub.$emit('cancelSale')
            },
        }
    }
</script>
