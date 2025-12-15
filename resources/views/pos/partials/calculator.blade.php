<div class="calculator-dropdown">
    <div class="d-flex flex-column gap-3">
        <input type="text" class="form-control" id="display" disabled />
        <div class="calculator-btns d-flex flex-column gap-3">
            <div class="d-flex gap-3 justify-content-between">
                <button type="button" class="btn btn--secondary w-100" data-value="7">7</button>
                <button type="button" class="btn btn--secondary w-100" data-value="8">8</button>
                <button type="button" class="btn btn--secondary w-100" data-value="9">9</button>
                <button type="button" class="btn btn--warning w-100" data-value="/">
                    <i class="fas fa-divide"></i>
                </button>
            </div>
            <div class="d-flex gap-3 justify-content-between">
                <button type="button" class="btn btn--secondary w-100" data-value="4">4</button>
                <button type="button" class="btn btn--secondary w-100" data-value="5">5</button>
                <button type="button" class="btn btn--secondary w-100" data-value="6">6</button>
                <button type="button" class="btn btn--warning w-100" data-value="*">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="d-flex gap-3 justify-content-between">
                <button type="button" class="btn btn--secondary w-100" data-value="1">1</button>
                <button type="button" class="btn btn--secondary w-100" data-value="2">2</button>
                <button type="button" class="btn btn--secondary w-100" data-value="3">3</button>
                <button type="button" class="btn btn--warning w-100" data-value="-">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <div class="d-flex gap-3 justify-content-between">
                <button type="button" class="btn btn--secondary w-100" data-value="0">0</button>
                <button type="button" class="btn btn--secondary w-100" data-value=".">.</button>
                <button type="button" class="btn btn--danger w-100" id="clear">C</button>
                <button type="button" class="btn btn--warning w-100" data-value="+">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <button type="button" class="btn btn--success w-100" id="equals">
            <i class="fas fa-equals"></i>
        </button>
    </div>
</div>

@push('script')
    <script>
        "use strict";
        (function($) {
            const $calculatorWrapper = $('.calculator-dropdown');
            const $displayElement = $calculatorWrapper.find("#display");

            function safeEval(expression) {
                try {
                    return Function(`"use strict"; return (${expression})`)();
                } catch {
                    return 'Error';
                }
            }
            function appendToDisplay(value) {
                const currentValue = $displayElement.val();
                const lastChar = currentValue.charAt(currentValue.length - 1);
                const operators = ['+', '-', 'x', '÷', '.'];
                if (operators.includes(lastChar) && operators.includes(value)) {
                    return;
                }
                $displayElement.val(currentValue + value);
            }

            function clearDisplay() {
                $displayElement.val('');
            }
            $calculatorWrapper.on('click', '.btn--secondary, .btn--warning', function() {

                let buttonValue = $(this).data('value');
                if (buttonValue === '*') {
                    appendToDisplay('x');
                } else if (buttonValue === '/') {
                    appendToDisplay('÷');
                } else {
                    appendToDisplay(buttonValue);
                }
            });

            $calculatorWrapper.on('click', '#clear', function() {
                clearDisplay();
            });

            $calculatorWrapper.on('click', "#equals", function() {
                let expression = $displayElement.val();
                expression = expression.replace(/x/g, '*').replace(/÷/g, '/');
                const result = safeEval(expression);
                $displayElement.val(result);
            });

            $(document).on('keydown', function(e) {
                //only work when calculator dropdown is open
                if ($('body').find('.calculator--dropdown').find('.dropdown-menu').hasClass('show')) {
                    const key = e.key;
                    if ((key >= '0' && key <= '9') || ['+', '-', '*', '/', '.'].includes(key)) {
                        if (key === '*') {
                            appendToDisplay('x');
                        } else if (key === '/') {
                            appendToDisplay('÷');
                        } else {
                            appendToDisplay(key);
                        }
                    } else if (key === 'Enter' || key === '=') {
                        e.preventDefault();
                        $('#equals').click();
                    } else if (key === 'Backspace') {
                        const currentValue = $displayElement.val();
                        $displayElement.val(currentValue.slice(0, -1));
                    } else if (key === 'Escape') {
                        $('#clear').click();
                    }
                }
            });

        })(jQuery);
    </script>
@endpush
