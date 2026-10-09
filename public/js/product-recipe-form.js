(function ($) {
    if (window.productRecipeFormReady) {
        return;
    }
    window.productRecipeFormReady = true;

    function getMaterialPrice(materialId) {
        var opt = $('#raw_materials_select option[value="' + materialId + '"]');
        if (opt.length) {
            var p = parseFloat(opt.data('price'));
            if (!isNaN(p)) {
                return p;
            }
        }
        return 0;
    }

    function materialRow(id, name, type, grade, values, unitPrice) {
        values = values || {};
        var thickness = values.thickness !== undefined ? values.thickness : 2;
        var width = values.width !== undefined ? values.width : '';
        var height = values.height !== undefined ? values.height : '';
        var area = values.area !== undefined ? values.area : 0;
        var qty = values.qty !== undefined ? values.qty : 0;
        var gradeValue = values.grade_value !== undefined ? values.grade_value : grade;
        var price = (values.price_per_unit !== undefined && values.price_per_unit !== null)
            ? values.price_per_unit
            : (unitPrice !== undefined ? unitPrice : getMaterialPrice(id));

        return `
            <tr data-type="${type || ''}" data-grade="${gradeValue || 0}" data-price="${price || 0}">
                <td>${name}<input type="hidden" name="raw_material_id[]" value="${id}"></td>
                <td><input type="number" step="0.01" name="thickness[]" class="form-control form-control-sm text-center row-thickness" value="${thickness}"></td>
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
            var unitPrice = (item.price_per_unit !== undefined && item.price_per_unit !== null)
                ? item.price_per_unit
                : getMaterialPrice(item.raw_material_product_id);

            tbody.append(materialRow(
                item.raw_material_product_id,
                item.name || '',
                (item.type || '').toLowerCase(),
                item.grade_value || 0,
                item,
                unitPrice
            ));
        });
    }

    function calculateProductPricing() {
        var totalMaterialCost = 0;
        $('#items_table_body tr').each(function () {
            var tr = $(this);
            var area = parseFloat(tr.find('.row-area').val()) || 0;
            var grade = parseFloat(tr.data('grade')) || 0;
            var price = parseFloat(tr.data('price')) || 0;
            var qtyVal = tr.find('.row-qty').val();
            var qty = parseFloat(qtyVal);
            if (isNaN(qty)) {
                qty = Math.round(area * grade);
            }
            var cost = qty * price;
            totalMaterialCost += Math.round(cost);
        });

        var chargeFields = [
            'body_charge', 'finishing_charge', 'bearing', 'stone',
            'electric_bill', 'gas_bill', 'box_price', 'carrying_charge',
            'wire_price', 'pitol_price'
        ];
        var totalCharges = 0;
        chargeFields.forEach(function (field) {
            var val = parseFloat($('input[name="' + field + '"]').val()) || 0;
            totalCharges += val;
        });

        var totalProductCost = Math.round((totalMaterialCost + totalCharges) * 100) / 100;

        $('#calc_material_cost').text(totalMaterialCost.toFixed(2));
        $('#calc_charges_total').text(totalCharges.toFixed(2));
        $('#calc_product_cost').text(totalProductCost.toFixed(2));

        // Dealer pricing
        var dealerPercent = parseFloat($('#dealer_profit_percent').val()) || 0;
        var dealerProfitAmount = Math.round((totalProductCost * (dealerPercent / 100)) * 100) / 100;
        var dealerPrice = Math.round((totalProductCost + dealerProfitAmount) * 100) / 100;
        $('#calc_dealer_profit_amount').text(dealerProfitAmount.toFixed(2));
        $('#calc_dealer_price').text(dealerPrice.toFixed(2));

        // Special dealer pricing
        var specialDealerPercent = parseFloat($('#special_dealer_profit_percent').val()) || 0;
        var specialDealerProfitAmount = Math.round((totalProductCost * (specialDealerPercent / 100)) * 100) / 100;
        var specialDealerPrice = Math.round((totalProductCost + specialDealerProfitAmount) * 100) / 100;
        $('#calc_special_dealer_profit_amount').text(specialDealerProfitAmount.toFixed(2));
        $('#calc_special_dealer_price').text(specialDealerPrice.toFixed(2));

        // Normal customer pricing
        var customerPercent = parseFloat($('#normal_customer_profit_percent').val()) || 0;
        var customerProfitAmount = Math.round((totalProductCost * (customerPercent / 100)) * 100) / 100;
        var customerPrice = Math.round((totalProductCost + customerProfitAmount) * 100) / 100;
        $('#calc_customer_profit_amount').text(customerProfitAmount.toFixed(2));
        $('#calc_customer_price').text(customerPrice.toFixed(2));
    }

    window.calculateProductPricing = calculateProductPricing;

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
            var price = option.data('price') || 0;
            tbody.append(materialRow(id, name, type, grade, {}, price));
        });

        calculateProductPricing();
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

        calculateProductPricing();
    });

    $(document).on('input change', 'input[name="body_charge"], input[name="finishing_charge"], input[name="bearing"], input[name="stone"], input[name="electric_bill"], input[name="gas_bill"], input[name="box_price"], input[name="carrying_charge"], input[name="wire_price"], input[name="pitol_price"]', function () {
        calculateProductPricing();
    });

    $(document).on('input change', '#dealer_profit_percent, #special_dealer_profit_percent, #normal_customer_profit_percent', function () {
        calculateProductPricing();
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
        calculateProductPricing();
    });
})(jQuery);
