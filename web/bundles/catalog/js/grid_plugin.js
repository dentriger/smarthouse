(function ($) {
    $.fn.gridPlugin = function (settings) {
        var that = this;
        var options = {
            rootURL: settings.rootURL,
            ajaxURL: settings.ajaxURL,
            sortableColumn: settings.sortableColumns[0],
            itemsPerPage: settings.itemsPerPage,
            page: 1,
            direction: 'DESC',
            filterableColumn: 'all',
            column: 1,
            count: 0
        }

        function setSearchWithFilters(){
            var div_group = document.createElement('div');
            $(div_group).attr('class',"input-group");

            var div = document.createElement('div');
            $(div).attr('class',"input-group-btn search-panel");
            var button = document.createElement('button');
            $(button).attr('type',"button");
            $(button).attr('class',"btn btn-default dropdown-toggle");
            $(button).attr('data-toggle',"dropdown");
            var span_1 = document.createElement('span');
            $(span_1).attr('id',"search_concept");
            $(span_1).text('Filtered by');
            var span_2 = document.createElement('span');
            $(span_2).attr('class',"caret");
            $(span_1).appendTo(button);
            $(span_2).appendTo(button);
            $(button).appendTo(div);

            var ul = document.createElement('ul');
            $(ul).attr('class',"dropdown-menu");
            $(ul).attr('role',"menu");
            for (var i = 0, len = settings.filterableColumns.length; i < len; i++){
                var li = document.createElement('li');
                var a = document.createElement('a');
                $(a).attr('value',settings.filterableColumns[i]);
                $(a).text(settings.filterableColumns[i]);
                $(a).appendTo(li);
                $(li).appendTo(ul);
            }
            var li_nothing = document.createElement('li');
            var a_nothing = document.createElement('a');
            $(a_nothing).attr('value','all');
            $(a_nothing).text('Nothing');
            $(a_nothing).appendTo(li_nothing);
            $(li_nothing).appendTo(ul);

            $(ul).appendTo(div);
            $(div).appendTo(div_group);

            var input = document.createElement('input');
            $(input).attr('type',"hidden");
            $(input).attr('name',"search_param");
            $(input).attr('value',"all");
            $(input).attr('id',"search_param");
            $(input).appendTo(div_group);

            var input_2 = document.createElement('input');
            $(input_2).attr('disabled',"disabled");
            // $(input_2).removeAttr('disabled');
            $(input_2).attr('type',"text");
            $(input_2).attr('name',"x");
            $(input_2).attr('class',"form-control");
            $(input_2).attr('placeholder',"Search term...");
            $(input_2).appendTo(div_group);

            $(div_group).appendTo(that);

            $('.search-panel .dropdown-menu').find('a').click(function(e) {
                e.preventDefault();
                var param = $(this).attr("value");
                if (param === 'all') {
                    $(input_2).attr('disabled',"disabled");
                    options.filterableColumn = 'all';
                    countRequest(options);
                    ajaxRequest(options);
                }
                else $(input_2).removeAttr('disabled');
                var concept = $(this).text();
                $('.search-panel span#search_concept').text('Filtered by ' + concept);
                $('.input-group #search_param').val(param);
            });

            $('.form-control').keyup(function(event){
                if(event.keyCode === 13){
                    options.filterableColumn = document.getElementById('search_param').value;
                    options.column = document.getElementsByClassName('form-control')[0].value;
                    ajaxRequest(options);
                }
            });
        }

        function setTable() {
            that.append('<table></table>');
            this.table = $('table');
            this.table.addClass('table table-striped');
        }

        function setHeaders(header) {
            this.table.append('<thead>');
            var thead = $('table thead');
            for(var field in header){
                if (field === options.sortableColumn && options.direction === 'ASC') {
                    thead.append('<th class="active-filter">'+field+'</th>');
                } else if(field === options.sortableColumn && options.direction === 'DESC') {
                    thead.append('<th class="active-filter-desc">'+field+'</th>');
                } else if(settings.sortableColumns.indexOf(field) !== -1) {
                    thead.append('<th>'+field+'</th>');
                } else {
                    thead.append('<th class="disabled-header">'+field+'</th>');
                }
            }
            if (settings.columnEdit) {
                thead.append('<th class="disabled-header">Edit</th>');
            }
            this.table.append('</thead>');
        }

        function setBody(data, from) {
            if (!from) {
                this.table.append('<tbody>');
            }
            var tbody = $('table tbody');
            data.forEach(function (item, i) {
                tbody.append('<tr id="titem'+item['id']+'">');
                var tr = $('#titem'+item['id']+'');
                for (var field in item){
                    if (field === 'category') {
                        tr.append('<td>' + item['category']['id'] + '(' + item['category']['name'] + ')</td>');
                    } else {
                        tr.append('<td>' + (item[field].toString()).slice(0,20) + '</td>');
                    }
                }

                if (settings.columnEdit) {
                    tr.append('<td>' +
                        '<div class="btn-group" role="group" >' +
                        '<a href="' + options.rootURL + item['id'] + '"  id="view' + item['id'] +
                        '" class="btn btn-info view-btn" >' +
                        '<span class="glyphicon glyphicon-book" ></span></a>' +
                        '<a href="' + options.rootURL + item['id'] + '/edit" id="edit' + item['id'] +
                        '" class="btn btn-primary edit-btn">' +
                        '<span class="glyphicon glyphicon-pencil"></span></a>' +
                        '<button id="' + item['id'] + '" class="btn btn-danger remove-btn">' +
                        '<span class="glyphicon glyphicon-remove"></span></button>' +
                        '</div>' +
                        '</td>');
                }

                tbody.append('</tr>');
            });

            if (!from) {
                this.table.append('</tbody>');
            }
        }

        function ajaxRequest(options) {
            countRequest(options);
            $('table').remove();
            $('.pagination').remove();
            $.ajax({
                url: options.ajaxURL
                + '?page=' + options.page+'&per_page='+
                options.itemsPerPage+'&ordered_by='+options.sortableColumn+'&direction='+
                options.direction+'&filtered_by='+options.filterableColumn+'&column='+options.column,
                success: function(data){
                    countRequest(options);
                    setTable();
                    setHeaders(data[0]);
                    setBody(data);
                    setSortable();
                    setButtons();
                    setButtonsWorkable();
                },
            });
        }

        function countRequest(options){
            $.ajax({
                url: options.ajaxURL
                + '/count' + '?filtered_by='+options.filterableColumn+'&column='+options.column,
                success:function(data){
                    options.count = data['count'];
                }
            });
        }

        function setSortable() {
            $('th').click(function () {
                if (!$(this).hasClass('disabled-header')) {
                    if ($(this).hasClass('active-filter')) {
                        $('th').removeClass('active-filter');
                        $('th').removeClass('active-filter-desc');
                        $(this).addClass('active-filter-desc');
                        options.sortableColumn = $(this)[0].outerText;
                        options.direction = 'DESC';
                        ajaxRequest(options);
                    } else {
                        $('th').removeClass('active-filter');
                        $('th').removeClass('active-filter-desc');
                        $(this).addClass('active-filter');
                        options.sortableColumn = $(this)[0].outerText;
                        options.direction = 'ASC';
                        ajaxRequest(options);
                }
            }
            });
        }

        function setButtons() {
            var ul = document.createElement('ul');
            $(ul).attr('class',"pagination");
            var li_prev = document.createElement('li');
            var a_prev = document.createElement('a');
            $(a_prev).attr('id',"prev-btn");
            $(a_prev).appendTo(li_prev);
            $(a_prev).text('Prev');
            $(li_prev).appendTo(ul);

            for (var i = 0; i < options.count/options.itemsPerPage; i++){
                var li = document.createElement('li');
                if (options.page == i+1) $(li).attr('class','active');
                var a = document.createElement('a');
                $(a).appendTo(li);
                $(a).attr('class','page');
                $(a).attr('value', i + 1);
                $(a).text(i + 1);
                $(li).appendTo(ul);
            }

            var li_next = document.createElement('li');
            var a_next = document.createElement('a');
            $(a_next).attr('id',"next-btn");
            $(a_next).appendTo(li_next);
            $(a_next).text('Next');
            $(li_next).appendTo(ul);

            $(ul).appendTo(that);

            $('#prev-btn').click(function () {
                if (options.page > 1) {
                    options.page--;
                    ajaxRequest(options);
                }
            });

            $('#next-btn').click(function () {
                if (options.page < options.count/options.itemsPerPage) {
                    options.page++;
                    ajaxRequest(options);
                }
            });

            $('.page').click(function () {
                options.page = $(this).attr('value');
                ajaxRequest(options);
            });
        }

        function setButtonsWorkable() {
            $('.remove-btn').click(function () {
                var id = $(this).attr('id');
                    removeAjax(id);
            });
        }
        
        function removeAjax(id) {
            $.ajax({
                url: options.rootURL + id + '/remove',
                success: function () {
                    $('#titem' + id).remove();
                }
            });
            ajaxRequest(options);

        }
        setSearchWithFilters();
        ajaxRequest(options);
    }

})(jQuery);