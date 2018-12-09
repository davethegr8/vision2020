$('#entry-submit').on('submit', function (e) {
    e.preventDefault();

    var form = this;

    var images = $(form).find('input[type=file]');
    for (var i=0;i<images.length;i++) {
        if ($(images[i]).hasClass('invalid')) {
            $(images[i]).parent().tooltip('show')
            $('html, body').animate({
                scrollTop: $(images[i]).parent().offset().top
            }, 200);
            return;
        }
    }

    form.submit();
});

function priceListeners() {
    $('.price').on('keypress', function(e) {
      return e.metaKey || // cmd/ctrl
        e.which <= 0 || // arrow keys
        e.which == 8 || // delete key
        /[0-9]/.test(String.fromCharCode(e.which)); // numbers
    }).on('keyup', function (e) {
        var $elem = $(e.target),
            $container = $elem.closest('.form-group'),
            num = parseInt($elem.val() / 2, 10);

        $container.find('.bid-portion').text(num.toLocaleString());
    });
}

priceListeners();


$('input[type=file]').on('change', function (e) {
    var elem = $(e.target).get(0),
        file = elem.files && elem.files[0],
        img,
        size;

    if (file) {
        img = new Image();
        img.src = window.URL.createObjectURL( file );

        img.onload = function() {
            var width = img.naturalWidth,
                height = img.naturalHeight;

            window.URL.revokeObjectURL( img.src );

            if( width <= 1200 && height <= 1200 ) {
                $(elem).addClass('valid').removeClass('invalid');
                $(elem).attr('title', '')
            }
            else {
                $(elem).addClass('invalid').removeClass('valid');
                $(elem).parent()
                    .attr('title', "Please choose an image that is at most 1200x1200")
                    .data('toggle', 'tooltip')
                    .tooltip('show')
            }
        };
    }
});

$('#add-another').on('click', function (e) {
    var template = $('.auction-entry')[0],
        html = $(template.outerHTML);

    html.find('input').each(function (i, elem) {
        $(elem).val('');
    });

    $('.auction-entry').last().after(html);

    priceListeners();
});

$('.voter label').on('click', function (e) {
    var $elem = $(e.target),
        $input = $elem.find('input'),
        $form = $elem.closest('form'),
        data = {
            "score": $input.val()
        };

    $('.alert').remove();

    $.ajax({
        "url": $form.attr('action'),
        "method": "POST",
        "data": data
    }).done(function (response) {
        var alert = '';
        alert += '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        alert += 'Score saved. <a href="/2018/jury/submissions">Back to submissions &raquo;</a>';
        alert += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        alert += '<span aria-hidden="true">&times;</span>';
        alert += '</button>';
        alert += '</div>';

        $form.before(alert);

        $('.voter label').removeClass('btn-success').addClass('btn-info');
        $elem.removeClass('btn-info').addClass('btn-success')
    })

});

$('.save-status select').on('change', function (event) {
    var $target = $(event.target),
        $form = $target.closest('form'),
        data = $form.serialize();

    $form.closest('td').removeClass('bg-success bg-danger');

    $.ajax({
        "url": $form.attr('action'),
        "method": $form.attr('method'),
        "data": data
    }).done(function (response) {
        $form.data('status', response.status);

        if (response.ok) {
            $form.closest('td').addClass('bg-success');
        }
        else {
            $form.closest('td').addClass('bg-danger');
        }
    });


});
