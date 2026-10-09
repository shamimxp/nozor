(function ($) {
    if (window.productRecipeFormReady) {
        return;
    }
    window.productRecipeFormReady = true;

    function materialRow(id, name, type, grade, values) {
        values = values || {};
        var thickness = values.thickness !== undefined ? values.thickness : 2;
        var width = values.width !== undefined ? values.width : '';
        var height = values.height !== undefined ? values.height : '';
        var area = values.area !== undefined ? values.area : 0;
        var qty = values.qty !== undefined ? values.qty : 0;
        var gradeValue = values.grade_value !== undefined ? values.grade_value : grade;

        return `
            <tr data-type="${type || ''}" data-grade="${gradeValue || 0}">
                <td>${name}<input type="hidden" name="raw_material_id[]" value="${id}"></td>
                <td><input type="number" step="0.01" name="thickness[]" class="form-control form-control-sm text-center row-thickness" value="${thickness}" readonly></td>
                <td><input type="number" step="0.01" name="width[]" placeholder="0" class="form-control form-control-sm text-center row-width" value="${width}"></td>
                <td><input type="number" step="0.01" name="height[]" placeholder="0" class="form-control form-control-sm text-center row-height" value="${height}"></td>
                <td><input type="number" step="0.01" name="area[]" class="form-control form-control-sm text-center row-area" value="${area}" readonly></td>
                <td><input type="number" step="any" name="grade_value[]" class="form-control form-control-sm text-center" value="${gradeValue || 0}" readonly></td>
                <td><input type="number" step="0.01" name="qty[]" class="form-control form-control-sm text-center row-qty" value="${qty}" readonly></td>
            </tr>
        `;
    }

    function currentRowIds() {
        var ids = [];
        $('#items_table_body tr').each(function () {
            var id = $(this).find('input[name="raw_material_id[]"]').val();
            if (id) {
                ids.push(id.toString());
            }
        });
        return ids;
    }

    function populateExistingItems() {
        var items = window.productRecipeExistingItems || [];
        var tbody = $('#items_table_body');
        if (!tbody.length || tbody.children().length) {
            return;
        }

        items.forEach(function (item) {
            tbody.append(materialRow(
                item.raw_material_product_id,
                item.name || '',
                (item.type || '').toLowerCase(),
                item.grade_value || 0,
                item
            ));
        });
    }

    window.ensureRawMaterialSelect = function () {
        var $el = $('#raw_materials_select');
        if (!$el.length || !$el.is(':visible')) {
            return;
        }

        var selected = [];
        $el.find('option').each(function () {
            if (this.selected || this.hasAttribute('selected')) {
                selected.push(this.value);
            }
        });

        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }

        if (selected.length) {
            $el.val(selected);
        }

        $el.select2({
            width: '100%',
            placeholder: 'Select raw materials',
            closeOnSelect: false
        });
    };

    $(document).on('change', '#raw_materials_select', function () {
        var selectedOptions = $(this).find('option:selected');
        var tbody = $('#items_table_body');
        var currentIds = currentRowIds();
        var selectedIds = [];

        selectedOptions.each(function () {
            selectedIds.push($(this).val().toString());
        });

        tbody.find('tr').each(function () {
            var id = $(this).find('input[name="raw_material_id[]"]').val();
            if (id && selectedIds.indexOf(id.toString()) === -1) {
                $(this).remove();
            }
        });

        currentIds = currentRowIds();
        selectedOptions.each(function () {
            var option = $(this);
            var id = option.val().toString();
            if (currentIds.indexOf(id) !== -1) {
                return;
            }
            var name = option.text().replace(/\s*\([^)]*\)$/, '');
            var type = (option.data('type') || '').toString().toLowerCase();
            var grade = option.data('grade') || 0;
            tbody.append(materialRow(id, name, type, grade));
        });
    });

    $(document).on('input', '.row-width, .row-height, .row-thickness', function () {
        var tr = $(this).closest('tr');
        var type = (tr.data('type') || '').toString();
        var grade = parseFloat(tr.data('grade')) || 0;
        var t = parseFloat(tr.find('.row-thickness').val()) || 0;
        var w = parseFloat(tr.find('.row-width').val()) || 0;
        var h = parseFloat(tr.find('.row-height').val()) || 0;
        var area = 0;

        if (type.indexOf('iron') !== -1) {
            area = w * w * h;
        } else {
            area = t * w * h;
        }

        tr.find('.row-area').val(area.toFixed(2));
        tr.find('.row-qty').val(Math.round(area * grade));
    });

    var selectedFiles = new DataTransfer();

    $(document).on('change', '#manufacture_images', function (e) {
        var files = e.target.files;
        if (!files || !files.length) {
            return;
        }
        $.each(files, function (i, file) {
            selectedFiles.items.add(file);
        });
        this.files = selectedFiles.files;
        renderNewImagePreviews();
    });

    function renderNewImagePreviews() {
        var container = $('#image_preview_container');
        container.empty();
        $.each(selectedFiles.files, function (i, file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = $('<img>').attr('src', e.target.result).addClass('rounded border').css({
                    width: '80px',
                    height: '80px',
                    objectFit: 'cover'
                });
                var removeBtn = $('<button>')
                    .attr('type', 'button')
                    .addClass('btn btn-sm btn-danger position-absolute remove-new-image')
                    .css({ top: '-5px', right: '-5px', padding: '2px 5px', fontSize: '10px' })
                    .html('&times;')
                    .data('index', i);
                container.append($('<div>').addClass('position-relative').append(img).append(removeBtn));
            };
            reader.readAsDataURL(file);
        });
    }

    $(document).on('click', '.remove-new-image', function () {
        var index = $(this).data('index');
        var newFiles = new DataTransfer();
        $.each(selectedFiles.files, function (i, file) {
            if (i !== index) {
                newFiles.items.add(file);
            }
        });
        selectedFiles = newFiles;
        $('#manufacture_images')[0].files = selectedFiles.files;
        renderNewImagePreviews();
    });

    $(document).on('click', '.remove-existing-image', function () {
        $(this).closest('.existing-image-wrapper').remove();
    });

    $(function () {
        populateExistingItems();
    });
})(jQuery);
