let f = (method, link, data, cb) => {
    let options = {
        method: method,
        headers: new Headers()
    };

    if(data instanceof FormData) {
        options.body = data;
    } else if(data) {
        let fd = new FormData();
        for(let key in data) fd.append(key, data[key]);
        options.body = fd;
    }
    if(vue.bearerEnabled)
        options.headers.append('Authorization', 'Bearer ' + vue.bearer);

    let status = false;
    fetch(link, options)
        .then(r => {
            if(r.status == '404') { //404
                cb(null, r);
            }

            status = r;
            return r.json();
        }).then(r => {
            cb(r, status);
        });
};
let vue;

window.onload = _ => {
    vue = new Vue({
        el: '#app',
        data: {
            url: '/php/',
            isStart: false,
            tests: [],
            bearer: '',
            bearerEnabled: false
        },

        methods: {
            appStart() {
                if (this.url !== '') {
                    this.isStart = true;
                    let newTests = {};

                    for(cat in tests) {
                        newTests[cat] = {};
                        newTests[cat].name = tests[cat].name;
                        newTests[cat].items = {};

                        for(id in tests[cat].items) {
                            newTests[cat].items[id] = tests[cat].items[id];
                            newTests[cat].items[id].sended = false;
                            newTests[cat].items[id].status = null;
                            newTests[cat].items[id].response = {
                                statusCode: null,
                                statusText: null,
                                body: null
                            };

                            newTests[cat].items[id].ok = false;
                        }
                    }

                    this.tests = newTests
                }
            },

            test(cat, id) {
                let item = this.tests[cat].items[id];
                let data = item.custom ? new FormData(document.querySelector('[data-form=' + cat + '_' + id + ']')) : item.request.data;
                let url = this.url + item.request.link;

                if(item.request.customLink) {
                    for(let key in item.request.customLink) {
                        url = url.replace('{' + key + '}', document.querySelector('[data-value="' + cat + '_' + id + '_' + key + '"]').value);
                    }
                }

                f(item.request.method, url, data, (result, status) => {
                    this.tests[cat].items[id].status = item.test(result, status);

                    this.tests[cat].items[id].sended = true;
                    this.tests[cat].items[id].originalUrl = url;
                    this.tests[cat].items[id].response.statusCode = status.status;
                    this.tests[cat].items[id].response.statusText = status.statusText;

                    this.tests[cat].items[id].response.body = JSON.stringify(result, null, 4);
                });
            },

        }
    });
};
