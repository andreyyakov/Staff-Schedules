$(document).ready(function () {
    $('.submit-selections').click(function () {

        let rotaId = $('#inputSelectRota option:selected').val();
        let shopId = $('#inputSelectShop option:selected').val();
        if (!rotaId || !shopId) {
            $('.errors').show().html('Please verify the selected data and try again.');
            $('.staff-info').hide();
            return;
        }

        $('.errors').hide();
        $.ajax({
            url: window.location.origin + window.location.pathname,
            type: 'post',
            data: {'rotaId': rotaId, 'shopId': shopId},
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.status === 'success') {
                    let trElem = '';
                    let weekDays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thrusday", "Friday", "Saturday"];
                    $.each(response.data, function (date, values) {
                        trElem += `<tr>`;
                        trElem += `<td> ${weekDays[new Date(date).getDay()]} </td><td>`;
                        $.each(values, function (key, value) {
                            trElem += `${key} : ${value} <br />`;
                        });
                        trElem += `</td></tr>`
                    });
                    $('.staff-info tbody').html(trElem);
                    $('.rota-date').text(`Week start at ${$('#inputSelectRota option:selected').text()}`);
                    $('.staff-info').show();
                }
            },
            error: function (xhr, status, error) {
                $('.errors').show().html('Please verify the selected data and try again.');
                $('.staff-info').hide();
            }
        });

    });
});
