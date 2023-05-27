$(document).ready(function() {
    $('#send_link').click(function() {
        $('#downloading-image').css('display', 'inline');
        $.ajax({
            url: 'getLinks.php',
            method: 'post',
            data: {
                url: $('#url').val(),
                depth: $('#depth').val()
            },
            dataType: 'json'
        })
            .done(function(data) {
                $('#downloading-image').css('display', 'none');
                if (data['result'] == 1) {
                    alert(data['msg']);
                    return false;
                }
                $('#links_table').html('');
                var html = '';
                var count_records = 0;
                $.each(data['all_sub_links'], function(sub_link_key, sub_link_value) {
                    count_records++;
                    html += '<tr>';
                    html += '<td>' + count_records + '</td>';
                    html += '<td>' + sub_link_value + '</td>';
                    html += '</tr>';
                });
                $('#links_table').append(html);
            });
    });
});
