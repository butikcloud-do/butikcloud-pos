{{-- Supplier Import Modal - Matching Customer/Category/Brand Import Modal Style --}}
{{-- Hidden by default to prevent flash during page load --}}
<div id="importSupplierModal" class="modal fade custom--modal" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Import Suppliers')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('user.supplier.import.store') }}" method="POST" enctype="multipart/form-data" class="supplier-import-form">
                @csrf
                <div class="modal-body">
                    {{-- Instructions Section --}}
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading mb-2">
                            <i class="las la-info-circle"></i> @lang('Bulk Upload Instructions')
                        </h6>
                        <ul class="mb-0 ps-3">
                            <li>@lang('Download the sample Excel file to understand the required format')</li>
                            <li>@lang('Required fields: Company Name, Name, Mobile')</li>
                            <li>@lang('Optional fields: Email, Address, City, State, Zip, Postcode, Country')</li>
                            <li>@lang('Company Name, Mobile, and Email (if provided) must be unique')</li>
                            <li><strong>@lang('Transaction Rule:')</strong> @lang('If ANY supplier fails validation, the ENTIRE import will be rejected (no partial imports)')</li>
                        </ul>
                    </div>

                    {{-- Sample File Download --}}
                    <div class="form-group mb-4">
                        <label class="form-label d-block mb-2">
                            <i class="las la-download"></i> @lang('Step 1: Download Sample File')
                        </label>
                        <a href="{{ route('user.supplier.import.sample') }}" class="btn btn-outline--success btn-sm">
                            <i class="las la-file-excel me-1"></i> @lang('Download Sample Excel File')
                        </a>
                        <p class="text-muted fs-14 mt-2 mb-0">
                            @lang('The sample file contains all required columns with example data.')
                        </p>
                    </div>

                    {{-- File Upload Section --}}
                    <div class="form-group mb-3">
                        <label class="form-label">
                            <i class="las la-upload"></i> @lang('Step 2: Upload Your Excel File')
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" 
                               class="form-control" 
                               name="excel_file" 
                               accept=".xlsx,.xls" 
                               required>
                        <small class="text-muted">
                            @lang('Accepted formats: .xlsx, .xls | Maximum file size: 10MB')
                        </small>
                    </div>

                    {{-- Validation Summary (Initially Hidden) --}}
                    <div class="import-validation-summary d-none">
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">
                                <i class="las la-exclamation-triangle"></i> @lang('Validation Errors Found')
                            </h6>
                            <p class="mb-2">@lang('Please fix the following errors and retry:'):</p>
                            <ul class="validation-errors mb-0"></ul>
                        </div>
                    </div>

                    {{-- Success Message (Initially Hidden) --}}
                    <div class="import-success-message d-none">
                        <div class="alert alert-success">
                            <h6 class="alert-heading">
                                <i class="las la-check-circle"></i> @lang('Import Successful!')
                            </h6>
                            <p class="success-text mb-0"></p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary btn-large" data-bs-dismiss="modal">
                        @lang('Close')
                    </button>
                    <button type="submit" class="btn btn--primary btn-large import-submit-btn">
                        <i class="las la-file-import me-1"></i> @lang('Import Suppliers')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
<script>
    "use strict";
    (function($) {
        const $importForm = $('.supplier-import-form');
        const $submitBtn = $('.import-submit-btn');
        const $validationSummary = $('.import-validation-summary');
        const $successMessage = $('.import-success-message');
        const $validationErrors = $('.validation-errors');
        const $successText = $('.success-text');

        // Safe Translation Strings
        const processingText = @json(__('Processing...'));
        const importText = @json(__('Import Suppliers'));
        const errorText = @json(__('An error occurred while processing the import'));
        const importFailedText = @json(__('Import failed. Please check the errors and try again.'));

        // Ensure button is enabled on load
        enableSubmitButton();

        // Handle import form submission
        $importForm.on('submit', function(e) {
            e.preventDefault();
            
            // Reset messages
            $validationSummary.addClass('d-none');
            $successMessage.addClass('d-none');
            $validationErrors.empty();
            
            const formData = new FormData(this);
            
            // Disable submit button and show loading state
            $submitBtn.prop('disabled', true).addClass('disabled').html('<i class="las la-spinner la-spin me-1"></i> ' + processingText);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        // Show success message
                        $successText.text(response.message.join(', '));
                        $successMessage.removeClass('d-none');
                        
                        // Reset form
                        $importForm[0].reset();
                        
                        // Reload page after 2 seconds
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                        
                        notify('success', response.message.join(', '));
                    } else {
                        handleImportErrors(response.message);
                    }
                },
                error: function(xhr) {
                    let errors = [];
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        if (Array.isArray(xhr.responseJSON.message)) {
                            errors = xhr.responseJSON.message;
                        } else {
                            errors = [xhr.responseJSON.message];
                        }
                    } else {
                        errors = [errorText];
                    }
                    
                    handleImportErrors(errors);
                },
                complete: function() {
                    // ALWAYS reset button state when request finishes
                    enableSubmitButton();
                }
            });
        });

        function enableSubmitButton() {
            $submitBtn.prop('disabled', false)
                      .removeAttr('disabled')
                      .removeClass('disabled')
                      .html('<i class="las la-file-import me-1"></i> ' + importText);
        }

        function handleImportErrors(errors) {
            // Show validation errors
            $validationErrors.empty();
            
            if (!Array.isArray(errors)) {
                errors = [errors];
            }

            errors.forEach(function(error) {
                $validationErrors.append('<li>' + error + '</li>');
            });
            $validationSummary.removeClass('d-none');
            
            if ($validationSummary.length > 0) {
                $validationSummary[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
            
            notify('error', importFailedText);
        }

        // Reset form and button when modal is closed
        $('#importSupplierModal').on('hidden.bs.modal', function() {
            resetModalState();
        });

        // Force reset button when modal is opened
        $('#importSupplierModal').on('show.bs.modal', function() {
            resetModalState();
        });

        function resetModalState() {
            $importForm[0].reset();
            $validationSummary.addClass('d-none');
            $successMessage.addClass('d-none');
            $validationErrors.empty();
            enableSubmitButton();
        }

    })(jQuery);
</script>
@endpush

@push('style')
<style>
    /* Ensure import modal is hidden until explicitly triggered */
    #importSupplierModal {
        display: none !important;
    }
    
    /* Allow Bootstrap to show it when triggered */
    #importSupplierModal.show {
        display: block !important;
    }
</style>
@endpush
