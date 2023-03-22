<template>
    <div class="main-layout-wrapper">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent m-0">
                <li class="breadcrumb-item">
                    <span>{{ trans('lang.history') }}</span>
                </li>
            </ol>
        </nav>
        <div class="main-layout-card">
            <div class="main-layout-card-header-with-button">
                <div class="main-layout-card-content-wrapper">
                    <div class="main-layout-card-header-contents">
                        <h5 class="bluish-text m-0">{{ trans('lang.history') }}</h5>
                    </div>
                </div>
            </div>
            <datatable-component class="main-layout-card-content"
                                 :options="tableOptions"/>
        </div>

    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';

    export default {
        extends: axiosGetPost,
        data() {
            return {
                tableOptions: {
                    tableName: 'services',
                    columns: [
                        {title: 'lang.client', key: 'user', type: 'object', subkey: 'first_name', sortable: true},
                        {title: 'lang.total_point', key: 'point', type: 'text', sortable: true},
                        {title: 'lang.amount', key: 'bonus', type: 'text', sortable: true},
                        {title: 'lang.reason', key: 'reason', type: 'text', sortable: true},
                        {title: 'lang.creator', key: 'creator', type: 'object', subkey: 'first_name', sortable: true},
                        {title: 'lang.created_at', key: 'created_at', type: 'text', sortable: true}
                    ],
                    source: '/historyshow',
                    search: true,
                },

            }
        }

    }
</script>