<template>
  <div>
    <a class="icon-btn" href="#"
       data-toggle="modal"
       :data-target="modal"
       @click.prevent="clickItem(rowData.id)" v-if="icon">
        <i :class="icon"></i>
    </a>
    <a href="#"
       data-toggle="modal"
       :data-target="modal"
       @click.prevent="clickItem(rowData.id)" v-else-if="num" v-text="content? content : 0">
    </a>
    <a href="#"
       data-toggle="modal"
       :data-target="modal"
       @click.prevent="clickItem(rowData.id)" v-else-if="content">
        {{content}}
    </a>
  </div>
</template>
<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon.js';

    export default {
        extends: axiosGetPost,
        props: ['rowData', 'icon', 'content', 'modalId', 'num'],
        methods: {
            clickItem(id) {
                this.$hub.$emit('invoker', id);
            },
        },
        computed: {
          modal: function() {
            return '#' + this.modalId;
          }
        }
    }
</script>
<style>
  a.icon-btn {
    font-size: 24px;
  }
</style>
