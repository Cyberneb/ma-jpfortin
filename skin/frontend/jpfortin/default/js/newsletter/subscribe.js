document.observe('dom:loaded', function() {
    ['newsletterPopupSubscriberFormDetail', 'newsletterSubscriberFormDetail'].each(function(key) {
        var form = window[key];
        if (window[key]) {
            var form = window[key].form;
            Event.stopObserving(form, 'submit');
            Event.observe(form, 'submit', function(ev) {
                if (this.validate()) {
                    new Ajax.Request(form.action, {
                        method: form.method,
                        parameters: form.serialize(true),
                        onSuccess: function(response) {
                            form.up('.block-subscribe').down('.message').update(response.responseText);
                        }.bind(this)
                    });
                }
                Event.stop(ev);
            }.bind(window[key].validator));
        }
    });
});