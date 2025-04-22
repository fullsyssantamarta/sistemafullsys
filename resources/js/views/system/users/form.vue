<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">Datos del Administrador <small>Acceso al sistema</small></h3>
        </div>
        <div class="card-body">
            <form autocomplete="off" @submit.prevent="submit">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.name}">
                                <label class="control-label">Nombre</label>
                                <el-input v-model="form.name"></el-input>
                                <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.email}">
                                <label class="control-label">Correo Electrónico</label>
                                <el-input v-model="form.email" :disabled="form.id !== null"></el-input>
                                <small class="form-control-feedback" v-if="errors.email" v-text="errors.email[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.password}">
                                <label class="control-label">Contraseña</label>
                                <el-input v-model="form.password"></el-input>
                                <small class="form-control-feedback" v-if="errors.password" v-text="errors.password[0]"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.password_confirmation}">
                                <label class="control-label">Confirmar Contraseña</label>
                                <el-input v-model="form.password_confirmation"></el-input>
                                <small class="form-control-feedback" v-if="errors.password_confirmation" v-text="errors.password_confirmation[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" :class="{'has-danger': errors.phone}">
                                <label class="control-label">Teléfono</label>
                                <el-input v-model="form.phone"></el-input>
                                <small class="form-control-feedback text-muted">Se mostrará un icono de Whatsapp en cada cliente. Agregar codigo de pais, ejemplo; 51955955955</small>
                                <small class="form-control-feedback" v-if="errors.phone" v-text="errors.phone[0]"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions text-right pt-2">
                    <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header bg-info">
                <h3 class="my-0">Lista de Usuarios</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.phone }}</td>
                            <td>
                                <button @click.stop="editUser(user)">Editar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</template>

<script>

    export default {
        data() {
            return {
                loading_submit: false,
                headers: null,
                users: [],
                resource: 'users',
                errors: {},
                form: {},
            }
        },
        created() {
            this.initForm(); // Inicializa el formulario en blanco
            this.getUsers(); // Obtiene la lista de usuarios para la tabla
        },
        methods: {
            selectUser(user) {
                this.form = { ...user };
                this.errors = {};
            },
            getUsers() {
                this.$http.get(`/${this.resource}/records`)
                    .then(response => {
                        this.users = response.data.data; // Simplemente asigna los usuarios directamente
                        console.log( this.users);
                    })
                    .catch(error => {
                        console.error('Error al obtener la lista de usuarios:', error);
                    });
            },
            editUser(user) {
                // Clona el objeto usuario para evitar referencias directas, excluyendo la contraseña
                this.form = { ...user, password: '', password_confirmation: '' };
                this.errors = {};
            },
            initForm() {
                this.errors = {};
                this.form = {
                    id: null, // Asegúrate de que 'id' esté en null
                    name: '', // Campos en blanco
                    email: '',
                    password: '',
                    password_confirmation: '',
                    phone: '',
                };
            },
            submit() {
                // Determina si se está creando o actualizando un usuario y configura la URL y el método
                const url = this.form.id ? `/${this.resource}/${this.form.id}` : `/${this.resource}`;
                const method = this.form.id ? 'put' : 'post';
                const data = this.form.id ? { ...this.form, _method: 'PUT' } : this.form;

                this.loading_submit = true;
                this.$http[method](url, data)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message);
                            this.getUsers(); // Recarga la lista de usuarios para reflejar los cambios
                            this.resetForm(); // Limpia el formulario
                        } else {
                            this.$message.error(response.data.message);
                        }
                    })
                    .catch(error => {
                        this.$message.error('Error al realizar la operación');
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors;
                        } else {
                            console.error(error.response);
                        }
                    })
                    .finally(() => {
                        this.loading_submit = false;
                    });
            },
            resetForm() {
                this.form = {
                    id: null,
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    phone: '',
                };
                this.errors = {};
            },

            getUsers() {
                this.$http.get(`/${this.resource}/records`)
                    .then(response => {
                        this.users = response.data.data; // Actualiza la lista de usuarios
                    })
                    .catch(error => {
                        console.error('Error al obtener la lista de usuarios:', error);
                    });
            }

        }
    }
</script>
