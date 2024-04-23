"use strict";

// Class definition
var KTAppEcommerceSaveProduct = function () {
    var cancelButton;
    // Private functions

    // Init quill editor
    const initQuillProduct = () => {

        
        // Define all elements for quill editor

        var pro_eleme = '#kt_ecommerce_add_product_short_description';
        var quill_product_desc = document.querySelector('#kt_ecommerce_add_product_short_description');
        
        var meta_eleme = '#kt_ecommerce_add_product_meta_description';
        var quill_meta_desc = document.querySelector('#kt_ecommerce_add_product_meta_description');
        //meta descripton
        quill_meta_desc = new Quill(meta_eleme, {
            modules: {
                toolbar: [
                    [{
                        header: [1, 2, 3, 4, 5, 6, false]
                    }],
                    ['bold', 'italic', 'underline'],
                    ['image', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            },
            placeholder: 'Type your text here...',
            theme: 'snow' // or 'bubble'
        });
        quill_meta_desc.on('text-change', function(delta, oldDelta, source) {
            $('#meta_description').val(quill_meta_desc.container.firstChild.innerHTML);
        });


        // prodcut desctionpt 
        quill_product_desc = new Quill(pro_eleme, {
            modules: {
                toolbar: [
                    [{
                        header: [1, 2, 3, 4, 5, 6, false]
                    }],
                    ['bold', 'italic', 'underline'],
                    ['image', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            },
            placeholder: 'Type your text here...',
            theme: 'snow' // or 'bubble'
        });
        quill_product_desc.on('text-change', function(delta, oldDelta, source) {
            $('#product_description').val(quill_product_desc.container.firstChild.innerHTML);
        });       


        const elements = [
            '#kt_ecommerce_add_product_meta_description',
            '#kt_ecommerce_add_product_short_description',
            '#kt_ecommerce_add_product_long_description',
            '#kt_ecommerce_add_product_technical_specification',
            '#kt_ecommerce_add_product_specification',
            '#kt_ecommerce_add_product_features',
            '#kt_ecommerce_add_product_benefits',
            '#kt_ecommerce_add_product_content',

        ];

        $('#kt_product_sale_end_date').flatpickr({
            altInput: true,
            altFormat: "d F, Y",
            dateFormat: "Y-m-d",
        });
        $('#kt_product_sale_start_date').flatpickr({
            altInput: true,
            altFormat: "d F, Y",
            dateFormat: "Y-m-d",
        });

        
        
    }

    // Init tagify
    const initTagifyProduct = () => {
        // Define all elements for tagify
        const elements = [
            '#kt_ecommerce_add_product_category',
            '#kt_ecommerce_add_product_tags'
        ];
        // Loop all elements
        elements.forEach(element => {
            // Get tagify element
            const tagify = document.querySelector(element);
            // Break if element not found
            if (!tagify) {
                return;
            }
            // Init tagify --- more info: https://yaireo.github.io/tagify/
            new Tagify(tagify, {
                whitelist: ["new", "trending", "sale", "discounted", "selling fast", "last 10"],
                dropdown: {
                    maxItems: 20,           // <- mixumum allowed rendered suggestions
                    classname: "tagify__inline__suggestions", // <- custom classname for this dropdown, so it could be targeted
                    enabled: 0,             // <- show suggestions on focus
                    closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
                }
            });
        });
    }
    // Init noUIslider
    const handleTaxDiscount = () => {
        const discountOptions = document.querySelectorAll('input[name="discount_tax_option"]');
        const percentageEl = document.getElementById('kt_ecommerce_add_product_tax_fixed');
        // const fixedEl = document.getElementById('kt_ecommerce_add_product_discount_fixed');

        discountOptions.forEach(option => {
            option.addEventListener('change', e => {
                const value = e.target.value;

                switch (value) {
                    case '2': {
                        percentageEl.classList.remove('d-none');
                        // fixedEl.classList.add('d-none');
                        break;
                    }
                    default: {
                        percentageEl.classList.add('d-none');
                        // fixedEl.classList.add('d-none');
                        break;
                    }
                }
            });
        });
    }

    // Shipping option handler
    const handleShipping = () => {
        
    }

    // Category status handler
    const handleStatus = () => {
        const target = document.getElementById('kt_ecommerce_add_product_status');
        const select = document.getElementById('kt_ecommerce_add_product_status_select');
        const statusClasses = ['bg-success', 'bg-warning', 'bg-danger'];

        $(select).on('change', function (e) {
            const value = e.target.value;

            switch (value) {
                case "published": {
                    target.classList.remove(...statusClasses);
                    target.classList.add('bg-success');
                    hideDatepicker();
                    break;
                }
                case "scheduled": {
                    target.classList.remove(...statusClasses);
                    target.classList.add('bg-warning');
                    showDatepicker();
                    break;
                }
                case "inactive": {
                    target.classList.remove(...statusClasses);
                    target.classList.add('bg-danger');
                    hideDatepicker();
                    break;
                }
                case "draft": {
                    target.classList.remove(...statusClasses);
                    target.classList.add('bg-primary');
                    hideDatepicker();
                    break;
                }
                default:
                    break;
            }
        });


        // Handle datepicker
        const datepicker = document.getElementById('kt_ecommerce_add_product_status_datepicker');

        // Init flatpickr --- more info: https://flatpickr.js.org/
        $('#kt_ecommerce_add_product_status_datepicker').flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        const showDatepicker = () => {
            datepicker.parentNode.classList.remove('d-none');
        }

        const hideDatepicker = () => {
            datepicker.parentNode.classList.add('d-none');
        }
    }

    // Condition type handler
    const handleConditions = () => {
        const allConditions = document.querySelectorAll('[name="method"][type="radio"]');
        const conditionMatch = document.querySelector('[data-kt-ecommerce-catalog-add-category="auto-options"]');
        allConditions.forEach(radio => {
            radio.addEventListener('change', e => {
                if (e.target.value === '1') {
                    conditionMatch.classList.remove('d-none');
                } else {
                    conditionMatch.classList.add('d-none');
                }
            });
        })
    }

    // Submit form handler
    

    

    // Public methods
    return {
        init: function () {
            // Init forms
            initQuillProduct();
            initTagifyProduct();
            // initSlider();
            // Handle forms
            handleStatus();
            handleConditions();
            handleTaxDiscount();
            handleShipping();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTAppEcommerceSaveProduct.init();
});
