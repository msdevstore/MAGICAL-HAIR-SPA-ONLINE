<template>
    <div class="main-layout-wrapper">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent m-0">
                <li class="breadcrumb-item">
                    <span>{{ trans('lang.clients') }}</span>
                </li>
            </ol>
        </nav>
        <div class="main-layout-card">
            <div class="main-layout-card-header-with-button">
                <div class="main-layout-card-content-wrapper">
                    <div class="main-layout-card-header-contents">
                        <h5 class="bluish-text m-0">{{ trans('lang.clients') }}</h5>
                    </div>
                </div>
            </div>
            <datatable-component class="main-layout-card-content"
                                 :options="tableOptions">
            </datatable-component>
            <!--Pass current modelId and isActive for rerender modal on DOM -->
            <div class="modal fade" id="add-edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered big-modal-dialog" role="document">
                    <client_edit class="modal-content"
                                 v-if="isActive"
                                 :id="selectedItemId"
                                 :modalID="modalID">
                    </client_edit>
                </div>
            </div>
        </div>
        <confirmation-modal id="confirm-delete"
                            :message="'client_will_be_deleted_permanently'"
                            :firstButtonName="'yes'"
                            :secondButtonName="'no'"
                            @confirmationModalButtonAction="confirmationModalButtonAction">
        </confirmation-modal>
        <confirmation-modal id="confirm-key-reset"
                            :message="'password_will_be_changed'"
                            :firstButtonName="'yes'"
                            :secondButtonName="'no'"
                            @confirmationModalButtonAction="confirmationKeyResetAction">
        </confirmation-modal>

        <!-- Service Policy Modal -->
        <div class="modal modal-landing fade" id="manage-point" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-body justify-content-center">
                        <h4 class="modal-title text-center mb-4" id="exampleModalLabel">
                            {{ trans('lang.point_management') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="point">Point</label>
                                    <input class="form-control" name="point" id="point" v-model="point">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="reason">Why?</label>
                                    <input class="form-control" name="reason" id="reason" v-model="reason" placeholder="As bonus">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-primary" @click="save">Save</button>
                                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';

    export default {
        extends: axiosGetPost,
        data() {
            return {
                modalID: '#add-edit-modal',
                isActive: false,
                selectedItemId: '',
                hasData: value => {
                    return !_.isEmpty(value) ? true : false
                },
                tableOptions: {
                    tableName: 'clients',
                    columns: [
                        {
                            title: 'lang.name',
                            key: 'full_name',
                            type: 'clickable_link',
                            source: 'client',
                            uniquefield: 'id',
                            sortable: true
                        },
                        {title: 'lang.emails', key: 'email', type: 'text', sortable: true},
                        {title: 'lang.phone', key: 'phone', type: 'text', sortable: true},
                        {
                            title: 'lang.point',
                            content: 'point',
                            type: 'custom_component',
                            componentName: 'custom_component',
                            modalId: 'manage-point',
                            num: true
                        },
                        {
                            title: 'lang.key_reset',
                            icon: 'la la-key',
                            type: 'custom_component',
                            componentName: 'custom_component',
                            modalId: 'confirm-key-reset'
                        },
                        {
                            title: 'lang.action',
                            key: 'action',
                            type: 'component',
                            componentName: 'action_component'
                        }
                    ],
                    right_align: ['action'],
                    source: '/clientlist',
                    search: true,
                },
                point: 0,
                reason: '',
            }
        },
        mounted() {
            let instance = this;
            instance.$hub.$on('viewEdit', function (id) {
                instance.addEditAction(id);
            });
            instance.modalCloseAction(instance.modalID);
            instance.$hub.$on('invoker', function (id) {
                instance.selectedItemId = id;
            });
        },
        methods: {
            confirmationModalButtonAction() {
                // set delete route with global deleteID && deleteIndex.
                let instance = this;
                instance.deleteDataMethod('/deleteclient/' + instance.deleteID, instance.deleteIndex);
            },
            confirmationKeyResetAction() {
                let instance = this;
                // instance.deleteDataMethod('/deleteclient/' + instance.deleteID, instance.deleteIndex);
                instance.axiosGet('/reset-password/' + instance.selectedItemId,
                    function (response) {
                        location.reload();
                    },
                    function (error) {
                        console.log(error);
                    },
                );
            },
            save() {
                console.log(this.reason);
                if(this.point == 0) {
                    alert("Please input the number of point you want to add or deduct!");
                } else {
                    this.axiosPost('/point-manage/' + this.selectedItemId,
                        {
                            point: this.point,
                            reason: this.reason
                        },
                        function (response) {
                            location.reload();
                        },
                        function (error) {
                            console.log(error);
                        },
                    );
                }
            }
        }
    }
</script>