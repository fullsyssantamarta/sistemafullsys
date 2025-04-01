<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active">
                    <span>Cuentas Contables</span>
                </li>
            </ol>
            <div class="right-wrapper pull-right">
                <button type="button" class="btn btn-custom btn-sm mt-2 mr-2" @click.prevent="openForm()">
                    <i class="fa fa-plus-circle"></i> Nueva Cuenta
                </button>
            </div>
        </div>

        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">Listado de Asientos Contables</h3>
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th class="text-right">Acciones</th>
                    </tr>

                    <tr slot-scope="{ index, row }">
                        <td>{{ index + 1 }}</td>
                        <td>{{ row.code }}</td>
                        <td>{{ row.name }}</td>
                        <td>{{ translateType(row.type) }}</td>
                        <td class="text-right">
                            <button class="btn btn-sm btn-info" @click="openForm(row.id)">Editar</button>
                            <button class="btn btn-sm btn-danger" @click="deleteRecord(row.id)">Eliminar</button>
                        </td>
                    </tr>
                </data-table>
            </div>
        </div>

        <chart-account-form :showDialog.sync="showDialog" :recordId="recordId"></chart-account-form>
    </div>
</template>

<script>
import ChartAccountForm from "./form.vue";
import DataTable from "../components/DataTable.vue";

export default {
    components: { ChartAccountForm, DataTable },
    data() {
        return {
            showDialog: false,
            resource: "accounting/charts",
            recordId: null,
        };
    },
    methods: {
        openForm(recordId = null) {
            this.recordId = recordId;
            this.showDialog = true;
        },
        deleteRecord(id) {
            this.$http.delete(`/${this.resource}/${id}`).then(() => {
                this.$eventHub.$emit("reloadData");
            });
        },
        translateType(type) {
            const typeMap = {
                Asset: "Activo",
                Liability: "Pasivo",
                Equity: "Patrimonio",
                Revenue: "Ingreso",
                Expense: "Gasto",
            };
            return typeMap[type] || "Desconocido"; // Valor predeterminado si no coincide
        },
    },
};
</script>
