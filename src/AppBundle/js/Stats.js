var $ = require('jquery');
import {FormSerializer} from './FormSerializer';

export class Stats {
    constructor() {
        this.handleFormChange = this.handleFormChange.bind(this);
        this.handlePopState = this.handlePopState.bind(this);
        this.handleAjaxSuccess = this.handleAjaxSuccess.bind(this);
        this.handleAjaxFail = this.handleAjaxFail.bind(this);
    }

    init() {
        this._cache = {
            form: $('#statsForm'),
            loader: $('.statsLoader'),
            container: $('.stats-wrapper'),
            exchangesGraph: $('#exchangesGraph'),
            servicesGraph: $('#servicesGraph'),
            usersGraph: $('#usersGraph')
        };

        this.serializer = new FormSerializer(this._cache.form);

        this.search_url = window.location.href;

        this.setupEvents();

        this.serializer.saveFormData();
        let request = "/stats" + this.serializer.getRequest();
        this.makeRequest(request);
    }

    setupEvents() {
        this._cache.form.on('change', 'input, select, textarea', this.handleFormChange);
        window.addEventListener('popstate', this.handlePopState);
    }

    handleFormChange(e) {
        this.serializer.saveFormData();
        let request = "/stats" + this.serializer.getRequest();

        history.pushState(this.serializer.getFormData(), null, request);

        this.makeRequest(request);
    }

    handlePopState(e) {
        e.preventDefault();
        this.search_url = window.location.href;

        this.makeRequest(this.search_url);
        this.serializer.setFormData(e.state);
        this.serializer.restoreFormData();
    }

    handleAjaxSuccess(data) {
        this.unsetLoadingState();

        /*$('#exchangesGraph').remove();
        $('#servicesGraph').remove();
        $('#usersGraph').remove();

        this._cache.exchangesGraphWrapper.append(this._cache.exchangesGraph);
        this._cache.servicesGraphWrapper.append(this._cache.servicesGraph);
        this._cache.usersGraphWrapper.append(this._cache.usersGraph);*/

        this.updateExchangesGraph(data.exchanges);
        this.updateServicesGraph(data.services);
        this.updateUsersGraph(data.users);
    }

    handleAjaxFail() {
        this.unsetLoadingState();
    }

    updateExchangesGraph(data) {
        try {this.exchangesChart.destroy()} catch(e) {}

        var ctx = this._cache.exchangesGraph[0].getContext("2d");
        this.exchangesChart = new Chart(ctx,{
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Echanges',
                    data: data.data1,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255,99,132,1)',
                    borderWidth: 1
                },{
                    label: 'Noeuds',
                    data: data.data2,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        stacked: false,
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    }

    updateServicesGraph(data) {
        console.log(data);
        try {this.servicesChart.destroy()} catch(e) {}

        var ctx = this._cache.servicesGraph[0].getContext("2d");
        this.servicesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Offres',
                    data: data.offre,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255,99,132,1)',
                    borderWidth: 1
                },{
                    label: 'Demandes',
                    data: data.demande,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },{
                    label: 'Offres flash',
                    data: data.offre_flash,
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                },{
                    label: 'Demandes flash',
                    data: data.demande_flash,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero:true,
                            stepSize: 1
                        }
                    }]
                }
            }
        });
    }

    updateUsersGraph(data) {
        try {this.usersChart.destroy()} catch(e) {}

        var ctx = this._cache.usersGraph[0].getContext("2d");
        this.usersChart = new Chart(ctx,{
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Membres',
                    data: data.user,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255,99,132,1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero:true,
                            stepSize: 1
                        }
                    }]
                },
                elements: {
                    line: {
                        tension: 0.1,
                    }
                }
            }
        });
    }

    makeRequest(request) {
        // If an ajax call is running, cancel it
        try {this._xhr.abort();} catch (e) {}

        this.setLoadingState();

        this._xhr = $.ajax({
            url: request,
            cache: false
        }).done(this.handleAjaxSuccess).fail(this.handleAjaxFail);
    }

    setLoadingState() {
        this._cache.container.addClass('loading');
    }

    unsetLoadingState() {
        this._cache.container.removeClass('loading');
    }
}