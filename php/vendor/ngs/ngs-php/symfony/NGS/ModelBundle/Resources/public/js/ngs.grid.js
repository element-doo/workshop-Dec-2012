jQuery.prototype.formRemove = function() {
    return jQuery.each(this, function() {
        $(this).click(function(e) {
            e.preventDefault();
            $(this).tooltip('hide');
            $(this).closest('li').remove();
        }).tooltip();
    });
}

jQuery.prototype.formCollection = function(trigger) {
    return jQuery.each(this, function() {
        var $list = $(this);
        $(trigger).click(function(e) {
            e.preventDefault();
            var $form = $($list.attr('data-prototype').replace(/__name__/g, $list.children().length));
            $form.closest('.remove').formRemove();
            $('<li></li>').append($form).appendTo($list);
        })
        // fix Symfony generates *0, *1... before each list item
        $list.find('li>div').each(function(){
            $(this).find('label:first').hide();
        });
        $list.find('.remove').formRemove();
    })
}

if(typeof NGS === 'undefined') var NGS = {};

NGS.grid = {};
NGS.modal = {};

NGS.urlToId = function(url) {
    return url.replace('/', '-', 'gi')
                .replace ('.','_', 'gi')
                .replace ('#', '', 'gi');
}

NGS.modal.create = function(id) {
    return $('<div tabIndex="-1" class="modal hide">').attr('id', id).appendTo('body');
}

NGS.ajaxForm = function(selector) {
    return $(selector).each(function() {
        var $form = $(this);
        
        $form.find('.cancel').click(function(event) {
            event.preventDefault();
            $form.closest('.modal').modal('hide');
        });
        
        $form.submit(function(event) {
            event.preventDefault();
            $.post(
                $form.attr('action'),
                $form.serialize(),
                function(response) {
                    $form.closest('.modal').modal('hide');
                    // @todo move to callback
                    if(typeof(response.data.item)!=='undefined') {
                        $(NGS).trigger('update', response.data.item);
                    }
                    else {
                        
                    }
                },
                'json'
            );
        });
    });
}

NGS.grid.init = function(selector, options) {
    return $(selector).each(function() {
        var $grid = $(selector);

        NGS.grid.ajaxEdit($grid.find('.action-add'));

        var editContent = function(data) {
            var content = $(data).find('.grid-content');
            var rows = content.find('tbody tr').css('cursor', 'pointer');
            // hide actions columns
            content.find('thead th:last').hide();
            rows.find('td:last').hide();
            // temporary disabled handlers for delete and edit actions
            // NGS.grid.ajaxRow(rows);
            return content;
        };

        var searchForm = $grid.find ('.search form');
        if (searchForm.length > 0) {
            searchForm.submit(function(event) {
                event.preventDefault();
                var $form = $(this);
                var $submitButton = $form.find('[type=submit]').addClass('disabled');

                $.get(
                    $form.attr('action'),
                    // @todo remove limit
                    $form.serialize()+'&limit=10',
                    function (data) {
                        var content = editContent (data);
                        $grid.find('.grid-content').replaceWith(content);
                        $submitButton.removeClass('disabled');
                    },
                    'html'
                );
            });
        } else editContent (this);

        $grid.find('.clear-search').click(function(event){
            event.preventDefault();
            $grid.find('.search input').val('');
        })
    });
}

NGS.grid.ajaxEdit = function(selector) {
    return $(selector).each(function() {
        $(this).click(function(event) {
            event.preventDefault();
            var $this = $(this);
            var url = $this.attr('href');
            $.get(
                url,
                null,
                function(response) {
                    $this.closest('.modal').modal('hide');
                    var modal = NGS.modal.create(NGS.urlToId(url))
                        .html(response);
                    NGS.ajaxForm(modal.find('form'))
                    modal.modal('show');
                },
                'html'
            );
         });
     });
}

NGS.grid.ajaxRow = function(selector, options) {
    return $(selector).each(function() {
        var $row = $(this);
        NGS.grid.ajaxEdit($row.find('.action-edit'));
        // @todo implement delete
        $row.find('.action-delete').click(function(ev) {
            ev.preventDefault();
        });
    });
}

NGS.grid.lookup = function(event) {
    event.preventDefault();
    $this = $(this);
    var target = $this.attr('lookup-target');
    
    var url = $this.attr('href');
    if($this.hasClass('is-loaded')) {
        $('#'+NGS.urlToId(url)).modal('show');
        return false;
    }           
    $.get(
        url,
        null,
        function(response) {
            $this.addClass('is-loaded');
            var grid = NGS.modal.create(NGS.urlToId(url))
                .html(response);
            NGS.grid.init(grid);
            
            grid.on('click', 'tr', function() {
                $(target).val($(this).find('.key').html());
                $(this).closest('.modal').modal('hide');
            })
            
            grid.modal('show');
        },
    'html');
}
