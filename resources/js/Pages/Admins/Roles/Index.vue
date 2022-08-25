<template>
    <div>
        <admin-layout>
            <section class="content">
                <div class="container-flu">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Roles & Permisstions</h3>
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
                                                <td class="text-capitalize">Role Name</td>
                                                <td class="text-capitalize">Permissions</td>
                                                <td class="text-capitalize">Created</td>
                                                <td class="text-capitalize text-right" v-if="$page.props.auth.hasRole.superAdmin || $page.props.auth.hasRole.admin">Actions</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(role, index) in roles.data" :key="index">
                                                <td>{{role.name}}</td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span v-for="(permission, index) in role.permissions " :key="index">
                                                            {{ permission.name }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>{{role.created_at}}</td>
                                                <td class="text-right" v-if="$page.props.auth.hasRole.superAdmin || $page.props.auth.hasRole.admin">
                                                    <button class="btn btn-success text-uppercase" style="letter-spacing: 0.1em;" @click="editModal(role)">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-danger text-uppercase ml-1" style="letter-spacing: 0.1em;" @click="deleteRole(role)">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer clearfix">
                                    <Pagination :links="roles.links"></Pagination>
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
                            <div class="h4">
                                <span>Preview: <span class="text-capitalize">{{ form.name }}</span></span>
                            </div>
                            <div class="card card-primary">
                                <form @submit.prevent="checkMode">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="role" class="h4">Role Name</label>
                                            <input type="text" class="form-control" id="role" placeholder="Role Name" v-model="form.name" :class="{'is-invalid': form.errors.name}" autofocus="autofocus" autocomplete="off">
                                        </div>
                                        <div class="invalid-feedback mb-3" :class="{'d-block': form.errors.name}">
                                            {{ form.errors.name }}
                                        </div>

                                        <div class="form-group">
                                            <label for="Permissions" class="h4">Permissions</label>
                                            <multiselect 
                                                v-model="form.permissions"
                                                :options="permissionOptions"
                                                :multiple="true"
                                                :taggable="true"
                                                placeholder="Choose permission(s)"
                                                @addPermissions="addPermissions"
                                                label="name"
                                                track-by="id">
                                            </multiselect>
                                        </div>
                                        <div class="invalid-feedback" :class="{'d-block': form.errors.permissions }">
                                            {{ form.errors.permissions }}
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-danger text-uppercase" style="letter-spacing: 0.1em;" @click="closeModal()">Cancel</button>
                                            <button type="submit" class="btn btn-info text-uppercase" style="letter-spacing: 0.1em;" :disabled="!form.name || form.processing">{{ buttonText }}</button>
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
        props: ['roles', 'permissions'],
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
                    permissions: []
                }),
                permissionOptions: this.permissions,
            }
        },
        computed: {
            formTitle() {
                return this.editedIndex === -1 ? "Create New Role" : "Edit Current Role";
            },
            buttonText() {
                return this.editedIndex === -1 ? "CREATE" : "EDIT";
            },
            checkMode() {
                return this.editMode === false ? this.createRole : this.editRole;
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
            editModal(role) {
                this.form.clearErrors();
                this.editMode = true;
                this.editedIndex = this.roles.data.indexOf(role);
                this.form.id = role.id;
                this.form.name = role.name;
                this.form.permissions = role.permissions;
                $('#modal-lg').modal('show');
            },
            addPermissions(newPermission) {
                let permission = {
                    name: newPermission,
                };
                this.permissionOptions.push(permission);
                this.form.permissions.push(permission);
            },
            createRole() {
                this.form.post(this.route('admin.roles.store'), {
                    preserveScroll: true,
                    onSuccess:() => {
                        this.closeModal();
                        Toast.fire({
                            icon: 'success',
                            title: 'New Role Created!'
                        });
                    }
                });
            },
            editRole() {
                this.form.patch(this.route('admin.roles.update', this.form.id, this.form), {
                    preserveScroll: true,
                    onSuccess:() => {
                        this.closeModal();
                        Toast.fire({
                            icon: 'success',
                            title: 'Role has been updated!'
                        });
                    }
                });
            },
            deleteRole(role) {
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
                        this.form.delete(this.route('admin.roles.destroy', role), {
                            preserveScroll: true,
                            onSuccess:() => {
                                this.closeModal();
                                Swal.fire(
                                    'Deleted!',
                                    'Role has been deleted.',
                                    'success'
                                );
                            }
                        });
                    }
                })
                
            }
        }
    }
</script>