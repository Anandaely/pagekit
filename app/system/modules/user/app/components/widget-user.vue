<template>

    <form class="pk-panel-teaser uk-form uk-form-stacked" v-if="editing">

        <div class="uk-form-row">
            <span class="uk-form-label">{{ 'User Type' | trans }}</span>

            <div class="uk-form-controls uk-form-controls-text">
                <p class="uk-form-controls-condensed">
                    <label><input type="radio" value="login" v-model="widget.show"> {{ 'Logged in' | trans }}</label>
                </p>

                <p class="uk-form-controls-condensed">
                    <label><input type="radio" value="registered" v-model="widget.show"> {{ 'Last registered' | trans }}</label>
                </p>
            </div>
        </div>

        <div class="uk-form-row">
            <span class="uk-form-label">{{ 'Display' | trans }}</span>

            <div class="uk-form-controls uk-form-controls-text">
                <p class="uk-form-controls-condensed">
                    <label><input type="radio" value="thumbnail" v-model="widget.display"> {{ 'Thumbnail' | trans }}</label>
                </p>

                <p class="uk-form-controls-condensed">
                    <label><input type="radio" value="list" v-model="widget.display"> {{ 'List' | trans }}</label>
                </p>
            </div>
        </div>

        <div class="uk-form-row">
            <span class="uk-form-label">{{ 'Total Users' | trans }}</span>

            <div class="uk-form-controls uk-form-controls-text">
                <p class="uk-form-controls-condensed">
                    <label><input type="radio" value="1" v-model="widget.total"> {{ 'Show' | trans }}</label>
                </p>

                <p class="uk-form-controls-condensed">
                    <label><input type="radio" value="" v-model="widget.total"> {{ 'Hide' | trans }}</label>
                </p>
            </div>
        </div>

        <div class="uk-form-row">
            <label class="uk-form-label" for="form-user-number">{{ 'Number of Users' | trans }}</label>

            <div class="uk-form-controls">
                <select id="form-user-number" class="uk-width-1-1" v-model="widget.count" number>
                    <option value="6">6</option>
                    <option value="12">12</option>
                    <option value="18">18</option>
                    <option value="24">24</option>
                </select>
            </div>
        </div>

    </form>

    <div class="pk-text-large" v-if="widget.total">{{ userscount }}</div>

    <h3 class="uk-panel-title" v-if="widget.show == 'registered' && widget.total">{{ '{0} Registered Users|{1} Registered User|]1,Inf[ Registered Users' | transChoice userscount}}</h3>

    <h3 class="uk-panel-title" v-if="widget.show != 'registered' && widget.total">{{ '{0} Logged in Users|{1} Logged in User|]1,Inf[ Logged in Users' | transChoice userscount}}</h3>

    <h3 class="uk-panel-title" v-if="widget.show == 'registered' && !widget.total">{{ 'Latest registered Users' | trans}}</h3>

    <h3 class="uk-panel-title" v-if="widget.show != 'registered' && !widget.total">{{ 'Latest logged in Users' | trans}}</h3>

    <ul v-show="users.length && widget.display == 'thumbnail'" data-user class="uk-grid uk-grid-small uk-grid-width-1-4 uk-grid-width-small-1-6 uk-grid-width-medium-1-4 uk-grid-width-xlarge-1-6" v-stack-margin="users">
        <li v-repeat="user: users">
            <a href="{{ $url.route('admin/user/edit', {id: user.id}) }}" title="{{ user.username }}">
                <img class="uk-border-rounded" width="200" height="200" alt="{{ user.name }}" v-gravatar="user.email">
            </a>
        </li>
    </ul>

    <ul v-show="users.length && widget.display == 'list'" data-user class="uk-list uk-list-line">
        <li class="uk-flex uk-flex-middle" v-repeat="user: users">
            <img class="uk-border-circle uk-margin-right" width="40" height="40" alt="{{ user.name }}" v-gravatar="user.email">

            <div class="uk-flex-item-1 uk-text-truncate">
                <a href="{{ $url.route('admin/user/edit', {id: user.id}) }}" title="{{ user.name }}">{{ user.username }}</a>
                <br><a class="uk-link-muted" href="mailto:{{ user.email }}">{{ user.email }}</a>
            </div>
        </li>
    </ul>

</template>

<script>

    module.exports = {

        type: {

            id: 'user',
            label: 'User',
            description: function () {

            },
            defaults: {
                show: 'login',
                display: 'thumbnail',
                total: true,
                count: 12
            }

        },

        replace: false,

        props: ['widget', 'editing'],

        watch: {

            'widget.show': {
                handler: 'load',
                immediate: true
            },

            'widget.count': 'load'

        },

        methods: {

            load: function () {

                var query;

                if (this.$get('widget.show') === 'registered') {
                    query = {
                        sort: 'registered',
                        order: 'DESC'
                    }
                } else {
                    query = {
                        filter: {access: 300}
                    }
                }

                //query.limit = this.$get('widget.count');

                this.$resource('api/user/:id').query(query, function (data) {

                    this.$set('userscount', data.users.length);
                    this.$set('users', data.users.slice(0, this.$get('widget.count')));
                });
            }

        }

    };

    window.Dashboard.components['user'] = module.exports;

</script>
