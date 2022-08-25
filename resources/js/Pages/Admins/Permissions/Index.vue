<template>
    <div>
        <admin-layout>
            <section class="content">
                <div class="container-flu">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Permissions</h3>
                                    <div class="card-tools" v-if="$page.props.auth.hasRole.superAdmin || $page.props.auth.hasRole.admin">
                                        <button type="button" class="btn btn-info text-uppercase" style="letter-spaciing: 01em;" @click="openModal()">
                                            Create
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <td class="text-capitalize">Name</td>
                                                <td class="text-capitalize">Description</td>
                                                <td class="text-capitalize">Created</td>
                                                <td class="text-capitalize text-right" v-if="$page.props.auth.hasRole.superAdmin || $page.props.auth.hasRole.admin">Actions</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(permission, index) in permissions.data" :key="index">
                                                <td>{{permission.name}}</td>
                                                <td>{{permission.description}}</td>
                                                <td>{{permission.created_at}}</td>
                                                <td class="text-right" v-if="$page.props.auth.hasRole.superAdmin || $page.props.auth.hasRole.admin">
                                                    <button class="btn btn-success text-uppercase" style="letter-spacing: 0.1em;" @click="editModal(permission)">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-danger text-uppercase ml-1" style="letter-spacing: 0.1em;" @click="deletePermission(permission)">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer clearfix">
                                    <Pagination :links="permissions.links"></Pagination>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div class="modal fade" id="modal-lg">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-tilte">{{ formTitle}}</h4>
                            <button type="button" class="close" @click="closeModal()" aria-label="close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body overflow-hidden">
                            <p class="h6 text-warning">
                                ** All permission must start with "create: ", "read: ", "update: ", or "delete: "
                            </p>
                            <div class="d-flex flex-column h4">
                                <span>Preview name: <span class="text-capitalize">{{ form.name }}</span></span>
                                <span class="mt-3">Preview description: <span class="text-capitalize">{{ form.description }}</span></span> 
                            </div>
                            <div class="card card-primary">
                                <form @submit.prevent="checkMode">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="name" class="h4">Permission Name</label>
                                            <input type="text" class="form-control" id="name" placeholder="Permission Name" v-model="form.name" :class="{'is-invalid': form.errors.name}" autofocus="autofocus" autocomplete="off">
                                        </div>
                                        <div class="invalid-feedback mb-3" :class="{'d-block': form.errors.name}">
                                            {{ form.errors.name }}
                                        </div>

                                        <div class="form-group">
                                            <label for="description" class="h4">Permission Description</label>
                                            <input type="text" class="form-control" id="description" placeholder="Permission Description" v-model="form.description" :class="{'is-invalid': form.errors.description}" autofocus="autofocus" autocomplete="off">
                                        </div>
                                        <div class="invalid-feedback mb-3" :class="{'d-block': form.errors.description}">
                                            {{ form.errors.description }}
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-danger text-uppercase" style="letter-spacing: 0.1em;" @click="closeModal()">Cancel</button>
                                            <button type="submit" class="btn btn-info text-uppercase" style="letter-spacing: 0.1em;" :disabled="!form.name || !form.description || form.processing">{{ buttonText }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </admin-layout>
    </div>
</template>

<script>
    import AdminLayout from '@/Layouts/AdminLayout';
    import Pagination from '@/Components/Pagination';
    export default {
        props: ['permissions'],
        components: { 
            AdminLayout,
            Pagination
        },
        data() {
            return {
                editedIndex: -1,
                editMode: false,
                form: this.$inertia.form({
                    id: '',
                    name: '',
                    description: ''
                }),
            }
        },
        computed: {
            formTitle() {
                return this.editedIndex === -1 ? "Create New Permission" : "Edit Current Permission";
            },
            buttonText() {
                return this.editedIndex === -1 ? "CREATE" : "EDIT";
            },
            checkMode() {
                return this.editMode === false ? this.createPermission : this.editPermission;
            },
        },
        methods: {
            openModal() {
                this.editMode = false;
                this.editedIndex = -1;
                this.form.clearErrors();
                this.form.reset();
                $('#modal-lg').modal('show');
            },
            closeModal() {
                this.form.clearErrors();
                this.form.reset();
                this.editMode = false;
                $('#modal-lg').modal('hide');
            },
            editModal(permission) {
                this.form.clearErrors();
                this.editMode = true;
                this.editedIndex = this.permissions.data.indexOf(permission);
                this.form.id = permission.id;
                this.form.name = permission.name;
                this.form.description = permission.description;
                $('#modal-lg').modal('show');
            },
            createPermission() {
                this.form.post(this.route('admin.permissions.store'), {
                    preserveScroll: true,
                    onSuccess:() => {
                        this.closeModal();
                        Toast.fire({
                            icon: 'success',
                            title: 'New Permission Created!'
                        });
                    }
                });
            },
            editPermission() {
                this.form.patch(this.route('admin.permissions.update', this.form.id, this.form), {
                    preserveScroll: true,
                    onSuccess:() => {
                        this.closeModal();
                        Toast.fire({
                            icon: 'success',
                            title: 'Permission has been updated!'
                        });
                    }
                });
            },
            deletePermission(permission) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.form.delete(this.route('admin.permissions.destroy', permission), {
                            preserveScroll: true,
                            onSuccess:() => {
                                this.closeModal();
                                Swal.fire(
                                    'Deleted!',
                                    'Permission has been deleted.',
                                    'success'
                                );
                            }
                        });
                    }
                });
            }
        }
    }
</script>