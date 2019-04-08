<?php
$this->title = 'RESTfull TEST';
?>
<div id="app">
    <header>
        <div class="navbar navbar-dark bg-dark box-shadow fixed-top">
            <div class="container d-flex justify-content-between">
                <a href="#" class="navbar-brand d-flex align-items-center">
                    <strong>RESTful TEST</strong>
                </a>
            </div>
            <div class="form-inline mt-2 mt-md-0" v-if="isStart">
                <input class="form-check-input" type="checkbox" value="" id="invalidCheck" v-model="bearerEnabled">
                <input class="form-control mr-sm-2" type="text" v-model="bearer" placeholder="Authorization Bearer" :disabled="!bearerEnabled" :style="!bearerEnabled ? 'background-color: #f9a1a1' : ''">
            </div>
        </div>
    </header>

    
    <br>
    <br>
    <br>
    <br>
    <div class="container">
        <div class="row mainContent">
            <div class="menu col-md-2" v-if="isStart">
                <div class="list-group menu-list">
                    <a :href="'#' + catId" class="list-group-item d-flex justify-content-between lh-condensed" v-for="(cat, catId) in tests">{{ cat.name }}</a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card p-2">
                    <div class="input-group">
                        <input class="form-control" placeholder="Link to api: http://xxx-m1.wsr.ru/" v-model="url">
                        <div class="input-group-append">
                            <button class="btn btn-secondary" @click="appStart()">Start</button>
                        </div>
                    </div>
                </div>
                <br>
                <div v-if="isStart">
                    <template v-for="(cat, catId) in tests">
                        <div :id="catId" class="scrollId"></div>
                        <h4 class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">{{ cat.name }}</span>
                        </h4>
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between lh-condensed"
                                v-for="(item, id) in cat.items"
                                :class="{ 'bg-success': item.status, 'bg-danger': item.status === false ? true : false, 'bg-custom': item.custom, 'finish': item.ok }">
                                <div style="position:relative;">
                                    <div class="ok" :class="{ 'active': item.ok }" @click="item.ok = !item.ok">OK</div>
                                    <h6 class="my-0">{{ item.name }}</h6>
                                    <small class="text-muted">{{ item.subname }}</small>
                                    <button class="btn btn-absolute" @click="test(catId, id)"
                                            v-if="item.custom !== true">Test
                                    </button>
                                    <div class="other">
                                        <!-- <div class="mt-4" v-if="bearerEnabled">
                                            <div class="form-group">
                                                <label>Authorization Bearer:</label>
                                                <input class="form-control form-control-sm" v-model="bearer">
                                            </div>
                                        </div> -->
                                        <div class="mt-4" v-if="item.request.customLink">
                                            <div class="form-group" v-for="(k, key) in item.request.customLink">
                                                <label>{{ key }}:</label>
                                                <input class="form-control form-control-sm"
                                                       :data-value="catId + '_' + id + '_' + key">
                                            </div>
                                        </div>
                                        <form class="mt-4" @submit.prevent="test(catId, id)" v-if="item.custom"
                                              :data-form="catId + '_' + id" method="post" enctype="multipart/form-data">
                                            <div class="form-group" v-if="bearerEnabled">
                                                <label>Authorization Bearer:</label>
                                                <input type="text" name="bearer" class="form-control form-control-sm" v-model="bearer">
                                            </div>
                                            <div class="form-group" v-for="(field, name) in item.request.data">
                                                <label>{{name}}</label>
                                                <input class="form-control form-control-sm" :type="field.type"
                                                       v-if="field.type == 'text' || field.type == 'file'" :name="name"
                                                       :value="field.default">
                                                <textarea class="form-control form-control-sm"
                                                          v-if="field.type == 'textarea'" :name="name"
                                                          v-text="field.default"></textarea>
                                            </div>
                                            <button class="btn btn-sm">Send</button>
                                        </form>
                                        <div class="row mt-4" v-if="item.sended">
                                            <div class="col-md-6">
                                                <div class="alert alert-info">
                                                    URL: {{ item.originalUrl }}<br>
                                                    Method: {{ item.request.method }}<br>
                                                    Data:
                                                    <pre v-if="!item.custom">{{ JSON.stringify(item.request.data, null, 4) }}</pre>
                                                    <template v-if="item.custom">FormData</template>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="alert alert-info">
                                                    Status code: {{ item.response.statusCode }}<br>
                                                    Status text: {{ item.response.statusText }}<br>
                                                    Body:
                                                    <pre>{{ item.response.body }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>