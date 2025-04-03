<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="loadData">
        <form @submit.prevent="submit">
            <div class="row">
                <!-- Selector de Clase -->
                <div class="col-12">
                    <div class="form-group">
                        <label>Clase</label>
                        <el-select v-model="form.parent_id" placeholder="Seleccione una clase (opcional)" @change="handleParentSelection">
                            <el-option v-for="account in parentAccounts" :key="account.id" :label="account.code + ' - ' + account.name" :value="account.id" />
                        </el-select>
                    </div>
                </div>

                <!-- Selectores de Niveles -->
                <div class="col-12" v-for="(level, index) in hierarchy" :key="index">
                    <div class="form-group">
                        <label>{{ levelTitles[index] }}</label>
                        <el-select v-model="level.selected" @change="handleSublevelSelection(level.selected, index)">
                            <el-option v-for="child in level.children" :key="child.id" :label="child.label" :value="child.id" :disabled="index === 3"/>
                        </el-select>
                    </div>
                </div>

                <!-- Campo de Código -->
                <div class="col-6">
                    <div class="form-group">
                        <label>Código</label>
                        <el-input v-model="form.code" :maxlength="getMaxCodeLength()" show-word-limit />
                    </div>
                </div>

                <!-- Campo de Nombre dinámico -->
                <div class="col-6">
                    <div class="form-group">
                        <label>Nombre de {{ dynamicNameLabel }}</label>
                        <el-input v-model="form.name" />
                    </div>
                </div>

                <!-- Campo de Nivel -->
                <div class="col-6">
                    <div class="form-group">
                        <label>Nivel</label>
                        <el-input v-model="form.level" type="number" readonly />
                    </div>
                </div>

                <!-- Selector de Tipo -->
                <div class="col-6">
                    <div class="form-group">
                        <label>Tipo</label>
                        <el-select v-model="form.type" placeholder="Seleccione">
                            <el-option label="Activo" value="Asset" />
                            <el-option label="Pasivo" value="Liability" />
                            <el-option label="Patrimonio" value="Equity" />
                            <el-option label="Ingreso" value="Revenue" />
                            <el-option label="Gasto" value="Expense" />
                        </el-select>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="col-12 pt-4 text-right">
                <el-button @click="close">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "recordId"],
    data() {
        return {
            loading_submit: false,
            titleDialog: null,
            resource: "accounting/charts",
            form: { code: "", name: "", level: 1, parent_id: null, type: "" },
            parentAccounts: [],
            hierarchy: [],
            parentCode: "",
            errors: {},
            levelTitles: ["Grupo", "Cuenta", "Subcuenta"], // Títulos dinámicos para los niveles
        };
    },
    computed: {
        // Etiqueta dinámica para el campo de Nombre
        dynamicNameLabel() {
            switch (this.form.level) {
                case 1:
                    return "Clase";
                case 2:
                    return "Grupo";
                case 3:
                    return "Cuenta";
                case 4:
                    return "Subcuenta";
                default:
                    return "Nombre";
            }
        }
    },
    methods: {
        async loadData() {
            this.titleDialog = this.recordId ? "Editar Cuenta" : "Nueva Cuenta";
            this.hierarchy = [];

            // Cargar cuentas de nivel 1
            await this.loadParentAccounts();

            if (this.recordId) {
                // Cargar datos del registro a editar
                const { account, parents } = await this.fetchRecord(this.recordId);
                this.setupForm(account);
                if (parents.length > 0) {
                    this.form.parent_id = parents[0].id;
                    await this.handleParentSelection();
                    await this.loadHierarchyForEdit(parents);
                }
                this.form.level = account.level;
                this.form.code = account.code;
            }
        },

        async loadParentAccounts() {
            const response = await this.$http.get(`/${this.resource}/records?column=level&value=1`);
            this.parentAccounts = response.data.data;
        },

        async fetchRecord(id) {
            const response = await this.$http.get(`/${this.resource}/${id}`);
            return response.data.data;
        },

        setupForm(account) {
            this.form = {
                code: account.code,
                name: account.name,
                level: account.level,
                // parent_id: account.parent_id,
                type: account.type
            };
        },

        async loadHierarchyForEdit(parents) {
            for (let i = 1; i < parents.length; i++) {
                const parent = parents[i];

                // Validar si el nivel ya existe en la jerarquía
                if (this.hierarchy[i - 1]) {
                    // Asignar el valor seleccionado en el nivel correspondiente
                    this.hierarchy[i - 1].selected = parent.id;

                    // Si hay un siguiente nivel en parents, cargar sus hijos
                    if (i < parents.length - 1) {
                        const children = await this.fetchChildren(parent.id);
                        if (this.hierarchy[i]) {
                            this.hierarchy[i].children = children;
                        } else {
                            this.hierarchy.push({ selected: null, children });
                        }
                    }
                }
            }
        },

        async fetchChildren(parentId) {
            const response = await this.$http.get(`/${this.resource}/children/${parentId}`);
            // Mapear los hijos a la estructura deseada
            return response.data.children.map(child => ({
                id: child.id,
                label: `${child.code} - ${child.name}`,
                code: child.code
            }));
        },

        async handleParentSelection() {
            if (!this.form.parent_id) {
                this.resetHierarchy();
                return;
            }

            const selectedParent = this.parentAccounts.find(acc => acc.id === this.form.parent_id);
            if (selectedParent) {
                this.parentCode = selectedParent.code;
                this.form.code = this.parentCode;
            }

            const children = await this.fetchChildren(this.form.parent_id);
            this.hierarchy = [{ selected: null, children }];
            this.form.level = 2;
        },

        async handleSublevelSelection(parent_id, index) {
            if (!parent_id) {
                this.hierarchy = this.hierarchy.slice(0, index);
                this.form.level = this.hierarchy.length + 1;
                return;
            }

            const selectedChild = this.hierarchy[index].children.find(child => child.id === parent_id);
            if (selectedChild) {
                this.parentCode = selectedChild.code;
                this.form.code = this.parentCode;
            }

            const children = await this.fetchChildren(parent_id);
            this.hierarchy = this.hierarchy.slice(0, index + 1);
            if (children.length) {
                this.hierarchy.push({ selected: null, children });
            } else {
                if(this.hierarchy.length < 4) {
                    this.hierarchy.push({ selected: null, children: [] });
                }
            }
            this.form.level = this.hierarchy.length + 1;

            this.updateCode();
        },

        updateCode() {
            const lastSelectedLevel = this.hierarchy.findLast(level => level.selected);

            if (lastSelectedLevel) {
                const selectedChild = lastSelectedLevel.children.find(child => child.id === lastSelectedLevel.selected);
                this.form.code = selectedChild ? selectedChild.code : "";
            } else {
                this.form.code = "";
            }
        },

        resetHierarchy() {
            this.hierarchy = [];
            this.form.code = "";
            this.form.level = 1;
        },

        getMaxCodeLength() {
            const levels = [1, 2, 4, 8];
            return levels[this.form.level - 1] || 1;
        },

        async submit() {
            this.loading_submit = true;

            const lastSelectedParentId = this.hierarchy.reduceRight((acc, level) => acc || level.selected, null);
            if (lastSelectedParentId) {
                this.form.parent_id = lastSelectedParentId;
            }

            const method = this.recordId ? "put" : "post";
            const url = this.recordId ? `/${this.resource}/${this.recordId}` : `/${this.resource}`;

            try {
                const response = await this.$http[method](url, this.form);
                this.$message.success(response.data.message);
                this.close();
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data;
                }
            } finally {
                this.loading_submit = false;
            }
        },

        close() {
            this.resetForm();
            this.$emit("update:showDialog", false);
            this.$eventHub.$emit("reloadData");
        },

        resetForm() {
            this.form = { code: "", name: "", level: 1, parent_id: null, type: "" };
            this.hierarchy = [];
            this.parentCode = "";
        }
    }
};
</script>