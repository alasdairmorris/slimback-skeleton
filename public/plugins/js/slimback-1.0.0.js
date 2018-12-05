var Slimback = (function ($, Backbone, _) {
    var Slimback = {};

    Slimback.VERSION = '1.0.0';

    $( document ).ajaxError(function(event, jqxhr, settings, thrownError) {
        switch(jqxhr.status) {
            case 0: // Unspecified error, so probably a connectivity problem
                alert( "ERROR: Failed to connect to server.\n\nPlease try again." );
                location.reload(true);
                break;
            case 401: // Permission denied, so redirect to login
                window.location = app.loginUrl;
                break;
            default:
                break;
        }
    });

    // Underscore.js isDefined mixin
    // Returns true if value is defined.
    _.mixin({
        isDefined: function(reference) {
            return !_.isUndefined(reference);
        },
        nl2br: function(str) {
            return str.replace(/(\r\n|\n\r|\r|\n)/g, "<br>");
        }
    });

    // Zombie management - see https://lostechies.com/derickbailey/2011/09/15/zombies-run-managing-page-transitions-in-backbone-apps/
    Slimback.BaseView = Backbone.View.extend({
        close: function(event) {
            if (event) {
                event.preventDefault();
            }
            this.preClose();
            this.trigger('done');
            this.stopListening();
            this.undelegateEvents();
            this.unbind();
            this.onClose();
        },
        preClose: function() {
            // by default, do nothing.
            // Override with custom code where necessary
        },
        onClose: function() {
            // by default, do nothing.
            // Override with custom code where necessary
        },
        addEvents: function(events) {
            this.delegateEvents( _.extend({}, _.clone(this.events), events) );
        },
    });

    Slimback.BaseModel = Backbone.Model.extend({
        // helper to simply convert a field's value to a boolean
        getBool: function(fieldName) {
            return s.toBoolean(this.get(fieldName));
        },
        getInt: function(fieldName) {
            return parseInt(this.get(fieldName));
        },
        getFloat: function(fieldName) {
            return parseFloat(s.isBlank(this.get(fieldName)) ? '0.00' : this.get(fieldName));
        },
        getNumber: function(fieldName, decimalPlaces, isZeroBlank) {
            var val = parseFloat(this.get(fieldName));

            if(_.isUndefined(decimalPlaces)) {
                decimalPlaces = 0;
            }
            if(_.isUndefined(isZeroBlank)) {
                isZeroBlank = true;
            }

            if(val === 0) {
                return isZeroBlank ? '' : s.numberFormat(val, decimalPlaces);
            } else {
                return s.numberFormat(val, decimalPlaces);
            }
        },
        getDecimal: function(fieldName) {
            var value = this.get(fieldName);
            if(value.length === 0) {
                value = '0';
            }
            return new Decimal(value);
        }
        // url: function () {
        //     var links = this.get('links'),
        //         url = links && links.self;
        //     if (!url) {
        //         url = Backbone.Model.prototype.url.call(this);
        //     }
        //     return url;
        // }
    });

    Slimback.BaseCollection = Backbone.Collection.extend({
        getOrFetch: function (id) {
            var result = new $.Deferred(),
                model = this.get(id);
            if (!model) {
                model = this.push({id: id});
                model.fetch({
                    success: function (model, response, options) {
                        result.resolve(model);
                    },
                    error: function (model, response, options) {
                        result.reject(model, response);
                    }
                });
            } else {
                result.resolve(model);
            }
            return result;
        },
        filteredCollection: function(predicate, context) {
            return new this.constructor(this.filter(predicate, context));
        }
    });

    Slimback.TemplateView = Slimback.BaseView.extend({
        templateName: '',
        initialize: function() {
            this.template = _.template($(this.templateName).html());
        },
        render: function() {
            var context = this.getContext(),
                html = this.template(context);
            this.$el.html(html);
        },
        getContext: function() {
            return {};
        }
    });

    Slimback.InfoBox = Slimback.BaseView.extend({
        className: 'modal',
        id: 'modal-dialog',
        events: {
            'click .action-button': function() {this.$el.modal('hide');},
            'hidden.bs.modal': 'close'
        },
        initialize: function (options) {
            var self = this;
            this.title = options.title || "";
            this.buttonLabel = options.buttonLabel || "OK";
            this.html = options.html || "Content goes here";
            this.onConfirm = options.onConfirm || null;
            this.modalTemplate = _.template($('#modal-dialog-content').html());
            $('body').append(this.el);
        },
        render: function () {
            var modalContext = {title: this.title, content: this.html, labels: {action: this.buttonLabel}};
            this.$el.html(this.modalTemplate(modalContext));
            this.$el.modal({
                backdrop: 'static',
                show: true
            });
        },
        onClose: function () {
            if(this.onConfirm)
            {
                this.onConfirm();
            }
            this.remove();
        }
    });

    Slimback.ConfirmBox = Slimback.BaseView.extend({
        className: 'modal',
        id: 'modal-dialog',
        events: {
            'click .cancel-button': 'cancel',
            'click .action-button': 'confirm'
        },
        initialize: function (options) {
            var self = this;
            this.title = options.title || "";
            this.cancelButtonLabel = options.cancelButtonLabel || "Cancel";
            this.confirmButtonLabel = options.confirmButtonLabel || "OK";
            this.html = options.html || "Content goes here";
            this.onCancel = options.onCancel || null;
            this.onConfirm = options.onConfirm || null;
            this.modalTemplate = _.template($('#modal-dialog-content').html());
            $('body').append(this.el);
        },
        render: function () {
            var modalContext = {title: this.title, content: this.html, labels: {cancel: this.cancelButtonLabel, action: this.confirmButtonLabel}};
            this.$el.html(this.modalTemplate(modalContext));
            this.$el.modal({
                backdrop: 'static',
                show: true
            });
        },
        cancel: function (event) {
            this.close();
            if(this.onCancel)
            {
                this.onCancel();
            }
        },
        confirm: function (event) {
            this.close();
            if(this.onConfirm)
            {
                this.onConfirm();
            }
        },
        onClose: function () {
            this.$el.modal('hide');
            this.remove();
        }
    });

    Slimback.PleaseWaitBox = Slimback.BaseView.extend({
        className: 'modal bs-example-modal-sm',
        id: 'pleaseWaitBox',
        initialize: function (options) {
            this.message = (options && options.message) || "...please wait...";
            $('body').append(this.el);
            this.render();
        },
        render: function () {
            this.$el.html(sprintf(
                '<div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-body"><div class="text-center">%s</div></div></div></div>',
                this.message
            ));
            this.$el.modal({
                backdrop: 'static',
                show: true
            });
        },
        onClose: function () {
            this.$el.modal('hide');
            this.remove();
        }
    });

    Slimback.ModalFormView = Slimback.BaseView.extend({
        className: 'modal',
        id: 'modal-dialog',
        events: {
            'click .action-button': 'submit',
            'click .cancel-button': 'close'
        },
        errorTemplate: _.template('<span class="help-block field-error-message"><%- msg %></span>'),
        clearErrors: function () {
            $('.has-error', this.form).removeClass('has-error');
            $('.field-error-message', this.form).remove();
            $('.non-field-errors', this.$el).remove();
        },
        initialize: function (options) {
            var self = this;
            this.modalTemplate = _.template($('#modal-dialog-content').html());
        },
        getButtonLabels: function() {
            return {
                cancel: 'Close',
                action: 'Save'
            };
        },
        render: function () {
            var context = this.getContext(),
                html = this.template(context),
                modalContext = {title: this.title, content: html, labels: this.getButtonLabels()};
            this.$el.html(this.modalTemplate(modalContext));
            this.$el.modal({
                backdrop: 'static',
                show: true
            });
        },
        serializeForm: function (form) {
            return _.object(_.map(form.serializeArray(), function (item) {
                // Convert object to tuple of (name, value)
                return [item.name, item.value];
            }));
        },
        failure: function (xhr, status, error) {
            var errors = jQuery.parseJSON(xhr.responseText);
            this.showErrors(errors);
        },
        modelFailure: function (model, xhr, options) {
            if(xhr.status !== 0 && xhr.status != 401) {
                var errors = jQuery.parseJSON(xhr.responseText);
                this.showErrors(errors);
            }
        },
        success: function (model, resp, options) {
            this.close();
        },
        showErrors: function (errors) {
            var generalErrors = [];
            _.map(errors, function (fieldErrors, name) {
                var field = $(':input[name=' + name + ']', this.form),
                    label = $('label[for=' + field.attr('id') + ']', this.form);
                field.closest('.form-group').addClass('has-error');

                function appendError(msg) {
                    field.after(this.errorTemplate({msg: msg}));
                }

                if (field.length) {
                    _.map(fieldErrors, appendError, this);
                }
                else
                {
                    generalErrors = generalErrors.concat(fieldErrors);
                }
            }, this);

            if(generalErrors.length)
            {
                this.form.before('<div class="alert alert-danger non-field-errors" role="alert"><ul></ul></div>');
                _.each(generalErrors, function(msg) {
                    this.form.parent().find('div.alert ul').append("<li>"+msg+"</li>");
                }, this);
            }
        },
        preClose: function() {
            this.$el.modal('hide');
        },
        onClose: function () {
            this.remove();
        },
    });

    Slimback.ModalFormViewWithPleaseWaitBox = Slimback.ModalFormView.extend({
        initialize: function (options) {
            Slimback.ModalFormView.prototype.initialize.apply(this, arguments);
            this.pleaseWaitBox = null;
        },
        modelFailure: function (model, xhr, options) {
            if(xhr.status !== 0 && xhr.status != 401) {
                var errors = jQuery.parseJSON(xhr.responseText);

                if(this.pleaseWaitBox) {
                    this.pleaseWaitBox.close();
                    this.pleaseWaitBox = null;
                }

                if(!this.$el.data('bs.modal').isShown) {
                    this.$el.modal('show');
                }

                this.showErrors(errors);
            }
        },
        onClose: function() {
            if(this.pleaseWaitBox) {
                this.pleaseWaitBox.close();
            }
            this.remove();
        },

    });

    Slimback.convertIsoToPrettyDate = function(dateStr) {
        return moment(dateStr, ["YYYY-MM-DD"], true).format("DD/MM/YYYY");
    };

    Slimback.convertPrettyDateToIso = function(dateStr) {
        return moment(dateStr, ["DD/MM/YYYY"], true).format("YYYY-MM-DD");
    };

    return Slimback;
})(jQuery, Backbone, _);
