define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/url',
	'mage/translate'
    ],
    function(
        $,
        modal,
        urlBuilder
    ) {
        return function (config) {
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: $.mage.__('Share My Cart - Quote # %1').replace('%1',config.quoteId),
                modalClass: 'custom-modal',
                buttons: [{
                    text: $.mage.__('Send Email'),
                    class: 'action primary',
                    click: function ()
                    {
                        var iqname = $('#iqname').val();
                        var iqmail = $('#iqmail').val();
                        if((iqname!='')&&(iqmail!=''))
                        {
                            $.ajax({
                                type: "POST",
                                url: urlBuilder.build("sharecart/index/index"),
                                dataType: "html",
                                data : {
                                    name :  iqname,
                                    email : iqmail,
                                },
                                success: function(responseq){
                                    $("#responseq").html(responseq);
                                }
                            });
                        }
                        else
                        {
                            alert('All fields required.');
                        }
                    }
                }]
            };

            var popup = modal(options, $('#custom-model-popup'));

            $(".open-modal").click(function() {
                $("#custom-model-popup").modal('openModal');
            });
        }

    }
);
