module.exports = {

    data: function () {
        return {opened: false};
    },

    props: {large: Boolean, lightbox: Boolean},

    template: '',

    created: function () {

        this.template = '<content></content>';

        if ('string' !== typeof this.$options.template) {

            var div = document.createElement('div');
            div.appendChild(this.$options.template);

            this.template = div.innerHTML;
        }

        this.$options.template = '<div class="uk-modal">' +
                '<div class="uk-modal-dialog" v-class="\'uk-modal-dialog-large\': large, \'uk-modal-dialog-lightbox\': lightbox">' +
                    '<div v-if="opened">' +
                        this.template +
                    '</div>' +
                '</div>' +
            '</div>';
    },

    ready: function () {

        var vm = this;

        this.$appendTo('body');

        this.modal = UIkit.modal(this.$el, {modal: false});

        this.modal.on('hide.uk.modal', function () {
            _.each(vm.__data, function (value, key) {
                vm.$delete(key);
            });

            vm.opened = false;
        });
    },

    methods: {

        open: function (data) {

            this.__data = data;
            _.each(data, function (value, key) {
                this.$add(key, value);
            }, this);


            this.opened = true;
            this.modal.show();

        },

        close: function () {
            this.modal.hide();
        }

    }

};